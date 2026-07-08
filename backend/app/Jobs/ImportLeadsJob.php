<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLS\Reader as XlsReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Throwable;

class ImportLeadsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        private readonly int $importJobId,
        private readonly string $filePath,
    ) {
    }

    public function handle(): void
    {
        $importJob = ImportJob::query()->findOrFail($this->importJobId);
        $importJob->update([
            'status' => ImportJob::STATUS_PROCESSING,
            'errors' => $importJob->errors ?? [],
        ]);

        try {
            if (!Storage::disk('local')->exists($this->filePath)) {
                throw new \RuntimeException('匯入檔案不存在。');
            }

            $absolutePath = Storage::disk('local')->path($this->filePath);

            $importJob->update([
                'total' => $this->countDataRows($absolutePath),
            ]);

            $this->importRows($importJob, $absolutePath);

            $importJob->update([
                'status' => ImportJob::STATUS_DONE,
            ]);
        } catch (Throwable $e) {
            $errors = $importJob->errors ?? [];
            $errors[] = $e->getMessage();

            $importJob->update([
                'status' => ImportJob::STATUS_FAILED,
                'errors' => $errors,
            ]);
        } finally {
            Storage::disk('local')->delete($this->filePath);
        }
    }

    public function failed(Throwable $exception): void
    {
        $importJob = ImportJob::query()->find($this->importJobId);

        if (!$importJob) {
            return;
        }

        $errors = $importJob->errors ?? [];
        $errors[] = $exception->getMessage();

        $importJob->update([
            'status' => ImportJob::STATUS_FAILED,
            'errors' => $errors,
        ]);
    }

    private function countDataRows(string $absolutePath): int
    {
        $reader = $this->makeReader($absolutePath);
        $reader->open($absolutePath);

        try {
            $count = 0;
            $isHeader = true;

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $values = $this->normalizeValues($row->toArray());

                    if ($this->isEmptyRow($values)) {
                        continue;
                    }

                    if ($isHeader) {
                        $isHeader = false;
                        continue;
                    }

                    $count++;
                }

                break;
            }

            return $count;
        } finally {
            $reader->close();
        }
    }

    private function importRows(ImportJob $importJob, string $absolutePath): void
    {
        $reader = $this->makeReader($absolutePath);
        $reader->open($absolutePath);

        try {
            $rowNumber = 0;
            $processed = 0;
            $errors = $importJob->errors ?? [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    $values = $this->normalizeValues($row->toArray());

                    if ($this->isEmptyRow($values)) {
                        continue;
                    }

                    if ($rowNumber === 1) {
                        continue;
                    }

                    $processed++;

                    try {
                        $this->createLead($values, $importJob->id);
                    } catch (Throwable $e) {
                        $errors[] = "第 {$rowNumber} 列：{$e->getMessage()}";
                    }

                    $importJob->update([
                        'processed' => $processed,
                        'errors' => $errors,
                    ]);
                }

                break;
            }
        } finally {
            $reader->close();
        }
    }

    private function createLead(array $values, int $importJobId): void
    {
        $name = $values[0] ?? null;
        $phone = $values[1] ?? null;

        if ($name === null || $name === '' || $phone === null || $phone === '') {
            throw new \RuntimeException('姓名與電話為必填欄位。');
        }

        Lead::create([
            'name' => $name,
            'phone' => $phone,
            'source_code' => $values[2] ?? 'IMPORT',
            'import_job_id' => $importJobId,
        ]);
    }

    private function makeReader(string $absolutePath): ReaderInterface
    {
        return match (strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION))) {
            'csv' => new CsvReader(),
            'xls' => new XlsReader(),
            'xlsx' => new XlsxReader(),
            default => throw new \RuntimeException('不支援的匯入檔案格式。'),
        };
    }

    private function normalizeValues(array $values): array
    {
        return array_map(
            fn (mixed $value): ?string => match (true) {
                $value === null => null,
                is_string($value) => trim($value),
                default => trim((string) $value),
            },
            $values,
        );
    }

    private function isEmptyRow(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }
}
