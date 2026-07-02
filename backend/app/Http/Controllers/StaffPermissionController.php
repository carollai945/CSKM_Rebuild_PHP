<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StaffPermissionController extends Controller
{
    /** All functional module codes */
    private const MODULES = [
        'A00','A01','A02','A03','A04','A05','A06',
        'B00','B01','B02',
        'C00','C01','C02','C03','C04','C05',
        'D00','D01','D02','D03','D04',
        'E00','E01','E02','E03',
        'F00','F01','F02','F03','F04','F05',
    ];

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'roles'   => array_map(fn (Role $r) => ['value' => $r->value, 'label' => $r->name], Role::cases()),
                'modules' => self::MODULES,
            ],
        ]);
    }

    public function show(Staff $staff): JsonResponse
    {
        Gate::authorize('management');

        $user = $staff->user;

        return response()->json([
            'data' => [
                'staff_id'    => $staff->id,
                'staff_name'  => $staff->name,
                'role'        => $user?->role,
                'modules'     => $user?->allowed_modules ?? [],
            ],
        ]);
    }

    public function update(Staff $staff, Request $request): JsonResponse
    {
        Gate::authorize('is-admin');

        $request->validate([
            'role'    => ['required', 'string', 'in:' . implode(',', array_column(Role::cases(), 'value'))],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string', 'in:' . implode(',', self::MODULES)],
        ]);

        $user = $staff->user;
        if (! $user) {
            return response()->json(['error' => ['code' => 'NO_USER', 'message' => 'Staff has no user account.']], 422);
        }

        $user->update([
            'role'            => $request->role,
            'allowed_modules' => $request->modules ?? [],
        ]);

        return response()->json([
            'data' => [
                'staff_id' => $staff->id,
                'role'     => $user->fresh()->role,
                'modules'  => $user->fresh()->allowed_modules,
            ],
        ]);
    }
}
