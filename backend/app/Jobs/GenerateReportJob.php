<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $reportType,
        private readonly string $reportDate,
        private readonly string $disk = 'local',
        private readonly ?string $outputPath = null,
    ) {
    }

    public function handle(): void
    {
        $date = Carbon::parse($this->reportDate);
        $query = Report::query()->where('report_type', $this->reportType);

        if ($this->reportType === 'WEEKLY') {
            $query->whereBetween('report_date', [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek(),
            ]);
        } else {
            $query->whereDate('report_date', $date);
        }

        $reports = $query->orderBy('report_date')->get([
            'id',
            'staff_id',
            'report_type',
            'report_date',
            'status',
            'content',
        ]);

        $payload = [
            'report_type' => $this->reportType,
            'report_date' => $date->toDateString(),
            'generated_at' => now()->toDateTimeString(),
            'total' => $reports->count(),
            'reports' => $reports->toArray(),
        ];

        Storage::disk($this->disk)->put(
            $this->outputPath ?? sprintf('reports/%s_%s.json', strtolower($this->reportType), $date->format('Ymd')),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }
}
