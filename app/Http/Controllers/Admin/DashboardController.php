<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\LeaveRequest;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless(
            $user->role === 'admin',
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Pegawai Aktif
        |--------------------------------------------------------------------------
        */
        $activeKaryawanCount = User::query()
            ->where('role', 'karyawan')
            ->where('approval_status', 'approved')
            ->where('is_active', true)
            ->count();

        $activeKabidCount = User::query()
            ->where('role', 'kabid')
            ->where('approval_status', 'approved')
            ->where('is_active', true)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Verifikasi Akun
        |--------------------------------------------------------------------------
        */
        $pendingKaryawanVerificationCount = User::query()
            ->where('role', 'karyawan')
            ->where('approval_status', 'pending')
            ->count();

        $pendingKabidVerificationCount = User::query()
            ->where('role', 'kabid')
            ->where('approval_status', 'pending')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Workflow Cuti
        |--------------------------------------------------------------------------
        |
        | Siap Admin:
        | - sudah ACC Kabid;
        | - atau memang tidak membutuhkan Kabid.
        |
        */
        $readyAdminLeaveCount = LeaveRequest::query()
            ->where('status', 'pending')
            ->whereIn(
                'kabid_status',
                [
                    LeaveRequest::KABID_STATUS_APPROVED,
                    LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                ]
            )
            ->count();

        $waitingKabidLeaveCount = LeaveRequest::query()
            ->where('status', 'pending')
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_PENDING
            )
            ->whereHas(
                'user',
                fn($query) =>
                $query->where('role', 'karyawan')
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Reimburse
        |--------------------------------------------------------------------------
        */
        $pendingReimbursementCount = Reimbursement::query()
            ->where(
                'status',
                Reimbursement::STATUS_PENDING
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pengajuan Cuti Siap Diproses Terbaru
        |--------------------------------------------------------------------------
        */
        $readyAdminLeaveRequests = LeaveRequest::query()
            ->with([
                'user.department',
                'permissionType',
                'kabidReviewer',
            ])
            ->where('status', 'pending')
            ->whereIn(
                'kabid_status',
                [
                    LeaveRequest::KABID_STATUS_APPROVED,
                    LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                ]
            )
            ->latest()
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pendaftar Terbaru Menunggu Verifikasi
        |--------------------------------------------------------------------------
        */
        $pendingUsers = User::query()
            ->with('department')
            ->whereIn(
                'role',
                [
                    'karyawan',
                    'kabid',
                ]
            )
            ->where(
                'approval_status',
                'pending'
            )
            ->latest()
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Surat Dinas
        |--------------------------------------------------------------------------
        */
        $dutyActiveCount = DutyLetter::query()
            ->where('status', DutyLetter::STATUS_PUBLISHED)
            ->whereDate('event_date', '>=', today())
            ->count();

        $dutyWaitingReportCount = DutyAssignment::query()
            ->where('report_status', DutyAssignment::REPORT_PENDING)
            ->whereHas('dutyLetter', fn($query) => $query
                ->where('status', DutyLetter::STATUS_PUBLISHED)
                ->whereDate('event_date', '<=', today()))
            ->count();

        $dutyPendingVerificationCount = DutyAssignment::query()
            ->where('report_status', DutyAssignment::REPORT_SUBMITTED)
            ->count();

        $dutyRevisionCount = DutyAssignment::query()
            ->where('report_status', DutyAssignment::REPORT_REVISION)
            ->count();

        $dutyUnpaidFeeCount = DutyAssignment::query()
            ->where('report_status', DutyAssignment::REPORT_VERIFIED)
            ->where('fee_status', DutyAssignment::FEE_UNPAID)
            ->count();

        $pendingDutyReports = DutyAssignment::query()
            ->with(['dutyLetter', 'user.department'])
            ->where('report_status', DutyAssignment::REPORT_SUBMITTED)
            ->orderByDesc('report_submitted_at')
            ->limit(5)
            ->get();

        $recentDutyNotifications = $user->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->filter(fn($notification) => ($notification->data['module'] ?? null) === 'duty')
            ->take(5)
            ->values();

        return view(
            'admin.dashboard',
            compact(
                'user',
                'activeKaryawanCount',
                'activeKabidCount',
                'pendingKaryawanVerificationCount',
                'pendingKabidVerificationCount',
                'readyAdminLeaveCount',
                'waitingKabidLeaveCount',
                'pendingReimbursementCount',
                'readyAdminLeaveRequests',
                'pendingUsers',
                'dutyActiveCount',
                'dutyWaitingReportCount',
                'dutyPendingVerificationCount',
                'dutyRevisionCount',
                'dutyUnpaidFeeCount',
                'pendingDutyReports',
                'recentDutyNotifications'
            )
        );
    }
}
