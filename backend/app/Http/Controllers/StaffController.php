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

    public function overview(Request $request): JsonResponse
    {
        $staff = Staff::with(['region', 'department', 'title'])
            ->when($request->filled('region_id'), fn (Builder $q) => $q->where('region_id', $request->region_id))
            ->when($request->filled('department_id'), fn (Builder $q) => $q->where('department_id', $request->department_id))
            ->when($request->filled('title_id'), fn (Builder $q) => $q->where('title_id', $request->title_id))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->status))
            ->when($request->filled('keyword'), function (Builder $q) use ($request): void {
                $kw = '%' . $request->string('keyword')->trim()->value() . '%';
                $q->where(fn (Builder $sq) => $sq->where('name', 'like', $kw)->orWhere('staff_no', 'like', $kw));
            })
            ->orderBy('staff_no')
            ->get();

        return response()->json([
            'data' => $staff->map(fn (Staff $member) => [
                'id'            => $member->id,
                'staff_no'      => $member->staff_no,
                'name'          => $member->name,
                'region'        => $member->region?->only(['id', 'name']),
                'department'    => $member->department?->only(['id', 'name']),
                'title'         => $member->title?->only(['id', 'name']),
                'join_date'     => $member->join_date?->toDateString(),
                'leave_date'    => $member->leave_date?->toDateString(),
                'status'        => $member->status,
            ])->all(),
        ]);
    }
}
