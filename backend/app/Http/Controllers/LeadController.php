<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignLeadsRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\LeadAssignmentHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Lead::with(['region', 'assignedStaff'])->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('assigned_staff_id')) {
            $query->where('assigned_staff_id', $request->assigned_staff_id);
        }

        if ($request->filled('keyword')) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                  ->orWhere('phone', 'like', $keyword)
                  ->orWhere('mobile', 'like', $keyword);
            });
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreLeadRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $lead = Lead::create($data);

        return response()->json(['data' => $lead->load(['region', 'assignedStaff'])], 201);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): JsonResponse
    {
        $lead->update($request->validated());

        return response()->json(['data' => $lead->load(['region', 'assignedStaff'])]);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json(null, 204);
    }

    public function assign(AssignLeadsRequest $request): JsonResponse
    {
        $leads = Lead::whereIn('id', $request->lead_ids)->get();
        $toStaffId = $request->to_staff_id;
        $assignedBy = $request->user()->id;

        foreach ($leads as $lead) {
            LeadAssignmentHistory::create([
                'lead_id'       => $lead->id,
                'from_staff_id' => $lead->assigned_staff_id,
                'to_staff_id'   => $toStaffId,
                'assigned_by'   => $assignedBy,
            ]);

            $lead->update(['assigned_staff_id' => $toStaffId]);
        }

        return response()->json(['data' => ['assigned_count' => $leads->count()]]);
    }
}
