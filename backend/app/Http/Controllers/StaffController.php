<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Requests\UpdateStaffStatusRequest;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $staff = Staff::query()
            ->when($request->filled('keyword'), function (Builder $query) use ($request): void {
                $keyword = $request->string('keyword')->trim()->value();

                $query->where(function (Builder $subQuery) use ($keyword): void {
                    $subQuery->where('staff_no', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('abbr', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->value()))
            ->when($request->filled('region_id'), fn (Builder $query) => $query->where('region_id', $request->integer('region_id')))
            ->orderBy('staff_no')
            ->get();

        return response()->json([
            'data' => $staff->map(fn (Staff $member) => $this->serializeStaff($member))->all(),
        ]);
    }

    public function store(StoreStaffRequest $request): JsonResponse
    {
        $staff = Staff::create($request->validated());

        return response()->json(['data' => $this->serializeStaff($staff)], 201);
    }

    public function show(Staff $staff): JsonResponse
    {
        return response()->json(['data' => $this->serializeStaff($staff)]);
    }

    public function update(UpdateStaffRequest $request, Staff $staff): JsonResponse
    {
        $staff->update($request->validated());

        return response()->json(['data' => $this->serializeStaff($staff->fresh())]);
    }

    public function updateStatus(UpdateStaffStatusRequest $request, Staff $staff): JsonResponse
    {
        $staff->update($request->validated());

        return response()->json(['data' => $this->serializeStaff($staff->fresh())]);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $staff = Staff::query()
            ->when($request->filled('keyword'), function (Builder $query) use ($request): void {
                $keyword = $request->string('keyword')->trim()->value();

                $query->where(function (Builder $subQuery) use ($keyword): void {
                    $subQuery->where('staff_no', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('abbr', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('staff_no')
            ->limit(20)
            ->get(['id', 'name', 'staff_no']);

        return response()->json(['data' => $staff]);
    }

    private function serializeStaff(Staff $staff): array
    {
        return [
            'id' => $staff->id,
            'staff_no' => $staff->staff_no,
            'name' => $staff->name,
            'abbr' => $staff->abbr,
            'region_id' => $staff->region_id,
            'department_id' => $staff->department_id,
            'title_id' => $staff->title_id,
            'join_date' => $staff->join_date?->toDateString(),
            'leave_date' => $staff->leave_date?->toDateString(),
            'status' => $staff->status,
        ];
    }
}
