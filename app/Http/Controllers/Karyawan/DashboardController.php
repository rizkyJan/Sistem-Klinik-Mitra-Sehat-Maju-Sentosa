<?php

namespace App\Http\Controllers\Karyawan;

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
            $user->role === 'karyawan'
                && $user->approval_status === 'approved'
                && $user->is_active,
            403
        );

        $user->load('department');

        $year = now()->year;

        /*
        |--------------------------------------------------------------------------
        | Workflow Cuti Karyawan Tahun Berjalan
        |--------------------------------------------------------------------------
        */
        $leaveQuery = LeaveRequest::query()
            ->where(
                'user_id',
                $user->id
            )
            ->whereYear(
                'start_date',
                $year
            );

        $waitingKabidCount =
            (clone $leaveQuery)
            ->where('status', 'pending')
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_PENDING
            )
            ->count();

        $waitingAdminCount =
            (clone $leaveQuery)
            ->where('status', 'pending')
            ->whereIn(
                'kabid_status',
                [
                    LeaveRequest::KABID_STATUS_APPROVED,
                    LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                ]
            )
            ->count();

        $approvedLeaveCount =
            (clone $leaveQuery)
            ->where(
                'status',
                'approved'
            )
            ->count();

        $rejectedLeaveCount =
            (clone $leaveQuery)
            ->where(
                'status',
                'rejected'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Saldo Cuti Tahun Berjalan
        |--------------------------------------------------------------------------
        */
        $leaveBalance = $user
            ->leaveBalances()
            ->where(
                'year',
                $year
            )
            ->first();

        $pendingAnnualDays = 0;

        if ($leaveBalance) {
            $pendingAnnualDays = LeaveRequest::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'leave_balance_id',
                    $leaveBalance->id
                )
                ->where(
                    'status',
                    'pending'
                )
                ->sum(
                    'annual_leave_deducted_days'
                );
        }

        $availableAnnualLeave = $leaveBalance
            ? max(
                0,
                $leaveBalance->quota_days
                    - $leaveBalance->used_days
                    - $pendingAnnualDays
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Reimburse Pribadi
        |--------------------------------------------------------------------------
        */
        $pendingReimbursementCount = Reimbursement::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                Reimbursement::STATUS_PENDING
            )
            ->count();

        $paidReimbursementTotal = (int) Reimbursement::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                Reimbursement::STATUS_PAID
            )
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Riwayat Cuti Terbaru
        |--------------------------------------------------------------------------
        */
        $recentLeaveRequests = LeaveRequest::query()
            ->with([
                'permissionType',
                'kabidReviewer',
                'approver',
            ])
            ->where(
                'user_id',
                $user->id
            )
            ->latest()
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Surat Dinas Pribadi
        |--------------------------------------------------------------------------
        */
        $dutyBaseQuery = DutyAssignment::query()
            ->where('user_id', $user->id);

        $dutyUpcomingCount = (clone $dutyBaseQuery)
            ->whereHas('dutyLetter', fn($query) => $query
                ->where('status', DutyLetter::STATUS_PUBLISHED)
                ->whereDate('event_date', '>=', today()))
            ->count();

        $dutyWaitingReportCount = (clone $dutyBaseQuery)
            ->where('report_status', DutyAssignment::REPORT_PENDING)
            ->whereHas('dutyLetter', fn($query) => $query
                ->where('status', DutyLetter::STATUS_PUBLISHED)
                ->whereDate('event_date', '<=', today()))
            ->count();

        $dutyWaitingVerificationCount = (clone $dutyBaseQuery)
            ->where('report_status', DutyAssignment::REPORT_SUBMITTED)
            ->count();

        $dutyUnpaidFeeCount = (clone $dutyBaseQuery)
            ->where('report_status', DutyAssignment::REPORT_VERIFIED)
            ->where('fee_status', DutyAssignment::FEE_UNPAID)
            ->count();

        $todayDutyAssignments = (clone $dutyBaseQuery)
            ->with('dutyLetter')
            ->whereHas('dutyLetter', fn($query) => $query
                ->where('status', DutyLetter::STATUS_PUBLISHED)
                ->whereDate('event_date', today()))
            ->get();

        $recentDutyNotifications = $user->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->filter(fn($notification) => ($notification->data['module'] ?? null) === 'duty')
            ->take(5)
            ->values();

        return view(
            'karyawan.dashboard',
            compact(
                'user',
                'year',
                'waitingKabidCount',
                'waitingAdminCount',
                'approvedLeaveCount',
                'rejectedLeaveCount',
                'leaveBalance',
                'pendingAnnualDays',
                'availableAnnualLeave',
                'pendingReimbursementCount',
                'paidReimbursementTotal',
                'recentLeaveRequests',
                'dutyUpcomingCount',
                'dutyWaitingReportCount',
                'dutyWaitingVerificationCount',
                'dutyUnpaidFeeCount',
                'todayDutyAssignments',
                'recentDutyNotifications'
            )
        );
    }
}
