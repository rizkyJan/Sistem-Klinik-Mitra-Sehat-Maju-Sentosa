<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Hanya Admin / Kabid
        |--------------------------------------------------------------------------
        */

        abort_unless(
            in_array($user->role, ['admin', 'kabid'], true),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Statistik Dashboard
        |--------------------------------------------------------------------------
        */

        $totalKaryawan = User::query()
            ->where('role', 'karyawan')
            ->count();


        $pendingCount = LeaveRequest::query()
            ->where('status', 'pending')
            ->count();


        $approvedCount = LeaveRequest::query()
            ->where('status', 'approved')
            ->count();


        $rejectedCount = LeaveRequest::query()
            ->where('status', 'rejected')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Pengajuan Terbaru
        |--------------------------------------------------------------------------
        */

        $recentLeaveRequests = LeaveRequest::query()
            ->with([
                'user.department',
                'approver',
            ])
            ->latest()
            ->limit(5)
            ->get();


        return view(
            'admin.dashboard',
            compact(
                'user',
                'totalKaryawan',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'recentLeaveRequests'
            )
        );
    }
}
