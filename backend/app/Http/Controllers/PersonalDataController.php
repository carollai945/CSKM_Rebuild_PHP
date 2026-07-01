<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PersonalDataController extends Controller
{
    private function formatStaff(Staff $staff, string $status, array $actions): array
    {
        return [
            'id'             => $staff->id,
            'staff_no'       => $staff->staff_no,
            'name'           => $staff->name,
            'phone'          => $staff->phone,
            'gender'         => $staff->gender,
            'blood_type'     => $staff->blood_type,
            'birth_date'     => $staff->birth_date,
            'photo_url'      => $staff->photo_url,
            'region'         => $staff->region?->only(['id', 'name']),
            'department'     => $staff->department?->only(['id', 'name']),
            'title'          => $staff->title?->only(['id', 'name']),
            'status'         => $staff->status,
            'currentStatus'  => $status,
            'allowedActions' => $actions,
        ];
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $staff = Staff::where('user_id', $user->id)->with(['region', 'department', 'title'])->firstOrFail();

        return response()->json(['data' => $this->formatStaff($staff, 'EDITABLE', ['SAVE', 'UPLOAD_PHOTO'])]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'sometimes|required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|in:M,F,OTHER',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'birth_date' => 'nullable|date',
        ]);

        $staff = Staff::where('user_id', $request->user()->id)->firstOrFail();
        $staff->update($validated);

        return response()->json(['data' => $staff->fresh()->load(['region', 'department', 'title'])]);
    }

    public function showByStaffId(Request $request, Staff $staff): JsonResponse
    {
        Gate::authorize('management');

        $staff->load(['region', 'department', 'title']);

        return response()->json(['data' => $this->formatStaff($staff, 'READONLY', [])]);
    }
}
