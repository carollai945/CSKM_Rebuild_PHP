<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Region;
use App\Models\Title;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['region_code' => 'RGN-N', 'region_name' => '北區', 'region_english_name' => 'North Region', 'abbr' => 'NR', 'status' => 'active'],
            ['region_code' => 'RGN-C', 'region_name' => '中區', 'region_english_name' => 'Central Region', 'abbr' => 'CR', 'status' => 'active'],
            ['region_code' => 'RGN-S', 'region_name' => '南區', 'region_english_name' => 'South Region', 'abbr' => 'SR', 'status' => 'active'],
        ];

        foreach ($regions as $region) {
            Region::query()->updateOrCreate(['region_code' => $region['region_code']], $region);
        }

        $regionIds = Region::query()->pluck('id', 'region_code');

        $departments = [
            ['region_code' => 'RGN-N', 'department_no' => 'D001', 'department_name' => '業務部', 'status' => 'active'],
            ['region_code' => 'RGN-C', 'department_no' => 'D002', 'department_name' => '行政部', 'status' => 'active'],
            ['region_code' => 'RGN-S', 'department_no' => 'D003', 'department_name' => '教學部', 'status' => 'active'],
            ['region_code' => 'RGN-N', 'department_no' => 'D004', 'department_name' => '財務部', 'status' => 'active'],
            ['region_code' => 'RGN-C', 'department_no' => 'D005', 'department_name' => '招生部', 'status' => 'active'],
        ];

        foreach ($departments as $department) {
            Department::query()->updateOrCreate(
                ['department_no' => $department['department_no']],
                [
                    'region_id' => $regionIds[$department['region_code']] ?? null,
                    'department_name' => $department['department_name'],
                    'status' => $department['status'],
                ]
            );
        }

        $departmentsByNumber = Department::query()->get(['id', 'department_no', 'region_id'])->keyBy('department_no');

        $titles = [
            ['department_no' => 'D001', 'title_no' => 'T001', 'title_name' => '業務專員', 'status' => 'active'],
            ['department_no' => 'D002', 'title_no' => 'T002', 'title_name' => '行政專員', 'status' => 'active'],
            ['department_no' => 'D003', 'title_no' => 'T003', 'title_name' => '教務主任', 'status' => 'active'],
            ['department_no' => 'D004', 'title_no' => 'T004', 'title_name' => '財務專員', 'status' => 'active'],
            ['department_no' => 'D005', 'title_no' => 'T005', 'title_name' => '招生顧問', 'status' => 'active'],
        ];

        foreach ($titles as $title) {
            $department = $departmentsByNumber->get($title['department_no']);

            if ($department === null) {
                continue;
            }

            Title::query()->updateOrCreate(
                [
                    'department_id' => $department->id,
                    'title_no' => $title['title_no'],
                ],
                [
                    'region_id' => $department->region_id,
                    'title_name' => $title['title_name'],
                    'status' => $title['status'],
                ]
            );
        }
    }
}
