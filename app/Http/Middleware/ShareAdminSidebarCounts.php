<?php

namespace App\Http\Middleware;

use App\Models\DepartmentMonthlyReport;
use App\Models\DutyAssignment;
use App\Models\HearYouFeedback;
use App\Models\LeaveRequest;
use App\Models\Reimbursement;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShareAdminSidebarCounts
{
    /**
     * Bagikan jumlah notifikasi / pekerjaan yang perlu ditindaklanjuti
     * ke seluruh view Admin.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (
            Auth::check()
            && Auth::user()->role === 'admin'
        ) {
            $pendingEmployeeVerificationCount = User::query()
                ->where('role', 'karyawan')
                ->where('approval_status', 'pending')
                ->count();

            $pendingKabidVerificationCount = User::query()
                ->where('role', 'kabid')
                ->where('approval_status', 'pending')
                ->count();

            $pendingLeaveRequestCount = LeaveRequest::query()
                ->where('status', 'pending')
                ->whereIn(
                    'kabid_status',
                    [
                        LeaveRequest::KABID_STATUS_APPROVED,
                        LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                    ]
                )
                ->count();

            $pendingReimbursementCount = Reimbursement::query()
                ->where('status', Reimbursement::STATUS_PENDING)
                ->count();

            /*
             * Semua Hear You yang belum memperoleh tanggapan Admin.
             * Badge tidak dibatasi bulan agar aspirasi lama tidak terlupakan.
             */
            $pendingHearYouResponseCount = HearYouFeedback::query()
                ->whereNull('admin_response')
                ->count();

            $pendingMonthlyReportVerificationCount = DepartmentMonthlyReport::query()
                ->where('status', DepartmentMonthlyReport::STATUS_SUBMITTED)
                ->count();

            /*
             * Surat Dinas yang benar-benar membutuhkan tindakan Admin:
             * 1. laporan pegawai sudah dikirim dan menunggu verifikasi;
             * 2. laporan sudah diverifikasi tetapi fee belum dikonfirmasi dibayar.
             *
             * Dengan model ini badge tidak hilang hanya karena notifikasi dibaca.
             * Badge baru turun setelah Admin menyelesaikan tindakannya.
             */
            $pendingDutyReportVerificationCount = DutyAssignment::query()
                ->where(
                    'report_status',
                    DutyAssignment::REPORT_SUBMITTED
                )
                ->count();

            $pendingDutyFeePaymentCount = DutyAssignment::query()
                ->where(
                    'report_status',
                    DutyAssignment::REPORT_VERIFIED
                )
                ->where(
                    'fee_status',
                    DutyAssignment::FEE_UNPAID
                )
                ->count();

            $pendingDutyActionCount =
                $pendingDutyReportVerificationCount
                + $pendingDutyFeePaymentCount;

            view()->share([
                'pendingEmployeeVerificationCount' =>
                    $pendingEmployeeVerificationCount,

                'pendingKabidVerificationCount' =>
                    $pendingKabidVerificationCount,

                'pendingLeaveRequestCount' =>
                    $pendingLeaveRequestCount,

                'pendingReimbursementCount' =>
                    $pendingReimbursementCount,

                'pendingHearYouResponseCount' =>
                    $pendingHearYouResponseCount,

                'pendingMonthlyReportVerificationCount' =>
                    $pendingMonthlyReportVerificationCount,

                'pendingDutyReportVerificationCount' =>
                    $pendingDutyReportVerificationCount,

                'pendingDutyFeePaymentCount' =>
                    $pendingDutyFeePaymentCount,

                'pendingDutyActionCount' =>
                    $pendingDutyActionCount,
            ]);
        }

        return $next($request);
    }
}
