<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\LeaveRequest;
use App\Models\Petition;
use App\Models\Report;
use App\Models\InvoiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 訊息中心 訊息中心 Dashboard
 *
 * 功能編號：訊息中心
 * 對應文件：docs/sdd/a00-personal-data-sdd.md
 */
class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Approved announcements visible to all
        $announcements = Announcement::where('status', 'APPROVED')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id', 'title', 'content', 'created_at']);

        // Pending items for management/approval roles
        $pendingLeave        = LeaveRequest::where('status', 'PENDING')->count();
        $pendingPetitions    = Petition::where('status', 'PENDING')->count();
        $pendingReports      = Report::where('status', 'PENDING')->count();
        $pendingInvoices     = InvoiceRequest::where('status', 'PENDING')->count();

        return response()->json([
            'data' => [
                'announcements' => $announcements,
                'pending_counts' => [
                    'leave_requests'   => $pendingLeave,
                    'petitions'        => $pendingPetitions,
                    'reports'          => $pendingReports,
                    'invoice_requests' => $pendingInvoices,
                ],
            ],
        ]);
    }

    public function markRead(Request $request, int $announcementId): JsonResponse
    {
        // Mark a specific announcement as read by this user (simple response)
        return response()->json(['data' => ['id' => $announcementId, 'read' => true]]);
    }
}
