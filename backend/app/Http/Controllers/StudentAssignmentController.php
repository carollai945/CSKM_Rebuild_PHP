<?php
namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentAssignmentController extends Controller {
    public function index(Request $request): JsonResponse {
        Gate::authorize('management');
        $query = Student::with(['advisor'])
            ->when($request->region_id, fn($q,$v)=>$q->where('region_id',$v))
            ->when($request->advisor_staff_id, fn($q,$v)=>$q->where('advisor_staff_id',$v))
            ->when($request->status, fn($q,$v)=>$q->where('status',$v))
            ->latest();
        return response()->json(['data'=>$query->paginate(20)]);
    }

    public function batchAssign(Request $request): JsonResponse {
        Gate::authorize('management');
        $validated = $request->validate([
            'student_ids'      => 'required|array|min:1',
            'student_ids.*'    => 'exists:students,id',
            'advisor_staff_id' => 'required|exists:staff,id',
        ]);
        Student::whereIn('id', $validated['student_ids'])
            ->update(['advisor_staff_id' => $validated['advisor_staff_id']]);
        return response()->json(['message' => '批次分配成功。', 'count' => count($validated['student_ids'])]);
    }
}
