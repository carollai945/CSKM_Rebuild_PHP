<?php
namespace App\Http\Controllers;
use App\Models\Report;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function index(Request $request): JsonResponse {
        $staffId = Staff::where('user_id', $request->user()->id)->value('id');
        $query = Report::where('staff_id', $staffId)
            ->when($request->report_type, fn($q,$v) => $q->where('report_type',$v))
            ->when($request->status, fn($q,$v) => $q->where('status',$v))
            ->latest('report_date');
        return response()->json(['data' => $query->paginate(20)]);
    }

    public function store(Request $request): JsonResponse {
        $staffId = Staff::where('user_id', $request->user()->id)->value('id');
        $validated = $request->validate([
            'report_type' => 'required|in:DAILY,WEEKLY',
            'report_date' => 'required|date',
            'content'     => 'nullable|string',
        ]);
        $validated['staff_id'] = $staffId;
        $report = Report::create($validated);
        return response()->json(['data' => $report], 201);
    }

    public function show(Report $report): JsonResponse {
        return response()->json(['data' => $report->load('staff')]);
    }

    public function update(Request $request, Report $report): JsonResponse {
        abort_if($report->status !== 'DRAFT', 422, '只能修改草稿狀態的報表。');
        $validated = $request->validate([
            'report_type' => 'sometimes|required|in:DAILY,WEEKLY',
            'report_date' => 'sometimes|required|date',
            'content'     => 'nullable|string',
        ]);
        $report->update($validated);
        return response()->json(['data' => $report->fresh()]);
    }

    public function submit(Report $report): JsonResponse {
        abort_if($report->status !== 'DRAFT', 422, '只能送審草稿狀態的報表。');
        $report->update(['status' => 'SUBMITTED']);
        return response()->json(['data' => $report->fresh()]);
    }
}
