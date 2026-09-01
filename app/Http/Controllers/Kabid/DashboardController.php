<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
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
            $user->role === 'kabid'
                && $user->approval_status === 'approved'
                && $user->is_active,
            403
        );

        $user->load('department');

        $year = now()->year;

        /*
        |--------------------------------------------------------------------------
        | Anggota Bidang
        |--------------------------------------------------------------------------
        */
        $memberCount = 0;
        $pendingMemberApprovalCount = 0;

        if ($user->department_id) {
            $memberCount = User::query()
                ->where(
                    'role',
                    'karyawan'
                )
                ->where(
                    'approval_status',
                    'approved'
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'department_id',
                    $user->department_id
                )
                ->count();

            $pendingMemberApprovalCount =
                LeaveRequest::query()
                ->where(
                    'status',
                    'pending'
                )
                ->where(
                    'kabid_status',
                    LeaveRequest::KABID_STATUS_PENDING
                )
                ->whereHas(
                    'user',
                    function ($query) use ($user) {
                        $query
                            ->where(
                                'role',
                                'karyawan'
                            )
                            ->where(
                                'approval_status',
                                'approved'
                            )
                            ->where(
                                'department_id',
                                $user->department_id
                            );
                    }
                )
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Cuti Pribadi Kabid
        |--------------------------------------------------------------------------
        */
        $ownWaitingAdminLeaveCount =
            LeaveRequest::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                'pending'
            )
            ->count();

        $ownApprovedLeaveCount =
            LeaveRequest::query()
            ->where(
                'user_id',
                $user->id
            )
            ->whereYear(
                'start_date',
                $year
            )
            ->where(
                'status',
                'approved'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Saldo Cuti Pribadi
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
            $pendingAnnualDays =
                LeaveRequest::query()
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
        | Reimburse Pribadi Kabid
        |--------------------------------------------------------------------------
        */
        $pendingReimbursementCount =
            Reimbursement::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                Reimbursement::STATUS_PENDING
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pengajuan Anggota yang Perlu Diperiksa
        |--------------------------------------------------------------------------
        */
        $pendingMemberLeaveRequests =
            LeaveRequest::query()
            ->with([
                'user.department',
                'permissionType',
            ])
            ->where(
                'status',
                'pending'
            )
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_PENDING
            )
            ->whereHas(
                'user',
                function ($query) use ($user) {
                    $query
                        ->where(
                            'role',
                            'karyawan'
                        )
                        ->where(
                            'approval_status',
                            'approved'
                        )
                        ->where(
                            'department_id',
                            $user->department_id
                        );
                }
            )
            ->latest()
            ->limit(5)
            ->get();

        return view(
            'kabid.dashboard',
            compact(
                'user',
                'year',
                'memberCount',
                'pendingMemberApprovalCount',
                'ownWaitingAdminLeaveCount',
                'ownApprovedLeaveCount',
                'leaveBalance',
                'pendingAnnualDays',
                'availableAnnualLeave',
                'pendingReimbursementCount',
                'pendingMemberLeaveRequests'
            )
        );
    }
}
