<?php
namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\StudentFeedback;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class StudentFeedbackController extends Controller {
    public function index(Request $request): JsonResponse {
        $q = StudentFeedback::with(['student','handler'])
            ->when($request->student_id,fn($q,$v)=>$q->where('student_id',$v))
            ->when($request->status,fn($q,$v)=>$q->where('status',$v))
            ->latest();
        return response()->json(['data'=>$q->paginate(20)]);
    }
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['student_id'=>'required|exists:students,id','category'=>'nullable|string|max:50','content'=>'required|string']);
        $fb = StudentFeedback::create($v);
        return response()->json(['data'=>$fb],201);
    }
    public function show(StudentFeedback $studentFeedback): JsonResponse { return response()->json(['data'=>$studentFeedback->load(['student','handler'])]); }
    public function update(Request $request, StudentFeedback $studentFeedback): JsonResponse {
        $v = $request->validate(['status'=>'sometimes|in:OPEN,RESOLVED','reply'=>'nullable|string']);
        if(isset($v['status']) && $v['status']==='RESOLVED') {
            $v['handled_by'] = Staff::where('user_id',$request->user()->id)->value('id');
        }
        $studentFeedback->update($v);
        return response()->json(['data'=>$studentFeedback->fresh()]);
    }
}
