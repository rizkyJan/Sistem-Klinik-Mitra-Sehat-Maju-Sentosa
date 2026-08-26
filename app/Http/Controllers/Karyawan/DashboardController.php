<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | User Login
        |--------------------------------------------------------------------------
        */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Pastikan hanya karyawan
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $user->role === 'karyawan',
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Load bidang
        |--------------------------------------------------------------------------
        */

        $user->load('department');


        /*
        |--------------------------------------------------------------------------
        | Tahun berjalan
        |--------------------------------------------------------------------------
        */

        $year = now()->year;


        /*
        |--------------------------------------------------------------------------
        | Jatah Cuti Tahun Berjalan
        |--------------------------------------------------------------------------
        */

        $balance = $user
            ->leaveBalances()
            ->where('year', $year)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Sisa Cuti
        |--------------------------------------------------------------------------
        |
        | Misalnya:
        |
        | quota_days = 9
        | used_days  = 2
        |
        | remaining = 7
        |
        */

        $remainingLeave = $balance
            ? $balance->remaining_days
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Pengajuan Pending
        |--------------------------------------------------------------------------
        */

        $pendingCount = $user
            ->leaveRequests()
            ->whereYear('start_date', $year)
            ->where('status', 'pending')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Pengajuan Disetujui
        |--------------------------------------------------------------------------
        */

        $approvedCount = $user
            ->leaveRequests()
            ->whereYear('start_date', $year)
            ->where('status', 'approved')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Pengajuan Ditolak
        |--------------------------------------------------------------------------
        */

        $rejectedCount = $user
            ->leaveRequests()
            ->whereYear('start_date', $year)
            ->where('status', 'rejected')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Pengajuan Terakhir
        |--------------------------------------------------------------------------
        */

        $recentLeaveRequests = $user
            ->leaveRequests()
            ->latest()
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'karyawan.dashboard',
            compact(
                'user',
                'year',
                'balance',
                'remainingLeave',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'recentLeaveRequests'
            )
        );
    }
}
