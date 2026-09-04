<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\KabidController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LeaveBalanceController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\LeaveRequestController as KaryawanLeaveRequestController;
use App\Http\Controllers\Kabid\DashboardController as KabidDashboardController;
use App\Http\Controllers\Kabid\LeaveRequestController as KabidLeaveRequestController;
use App\Http\Controllers\Kabid\ReimbursementController as KabidReimbursementController;
use App\Http\Controllers\Kabid\MemberController as KabidMemberController;
use App\Http\Controllers\Kabid\LeaveApprovalController as KabidLeaveApprovalController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Middleware\ShareAdminSidebarCounts;
use App\Http\Middleware\ShareKabidSidebarCounts;
use App\Http\Controllers\Admin\ReimbursementController as AdminReimbursementController;
use App\Http\Controllers\Karyawan\ReimbursementController as KaryawanReimbursementController;
use App\Http\Controllers\Admin\DutyLetterController as AdminDutyLetterController;
use App\Http\Controllers\Kabid\DutyLetterController as KabidDutyLetterController;
use App\Http\Controllers\Karyawan\DutyLetterController as KaryawanDutyLetterController;
use App\Http\Controllers\Kabid\DutyReportController as KabidDutyReportController;
use App\Http\Controllers\Karyawan\DutyReportController as KaryawanDutyReportController;
use App\Http\Controllers\Admin\DutyReportController as AdminDutyReportController;
use App\Http\Controllers\Admin\DutyFeeController as AdminDutyFeeController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\ShareKaryawanSidebarCounts;
use App\Http\Controllers\Admin\EmployeeProfileUpdateRequestController;
use App\Http\Middleware\ShareAdminProfileUpdateCount;

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'role:admin',
    ShareAdminSidebarCounts::class,
    ShareAdminProfileUpdateCount::class,
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | PERUBAHAN PROFIL PEGAWAI
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/perubahan-profil',
            [
                EmployeeProfileUpdateRequestController::class,
                'index'
            ]
        )->name('profile-updates.index');

        Route::get(
            '/perubahan-profil/{profileUpdateRequest}',
            [
                EmployeeProfileUpdateRequestController::class,
                'show'
            ]
        )->name('profile-updates.show');

        Route::get(
            '/perubahan-profil/{profileUpdateRequest}/foto-aktif',
            [
                EmployeeProfileUpdateRequestController::class,
                'currentPhoto'
            ]
        )->name('profile-updates.current-photo');

        Route::get(
            '/perubahan-profil/{profileUpdateRequest}/foto-baru',
            [
                EmployeeProfileUpdateRequestController::class,
                'proposedPhoto'
            ]
        )->name('profile-updates.proposed-photo');

        Route::patch(
            '/perubahan-profil/{profileUpdateRequest}/approve',
            [
                EmployeeProfileUpdateRequestController::class,
                'approve'
            ]
        )->name('profile-updates.approve');

        Route::patch(
            '/perubahan-profil/{profileUpdateRequest}/reject',
            [
                EmployeeProfileUpdateRequestController::class,
                'reject'
            ]
        )->name('profile-updates.reject');

        Route::get('/notifications/{notification}', [
            NotificationController::class,
            'open'
        ])->name('notifications.open');

        Route::patch('/notifications/read-all', [
            NotificationController::class,
            'readAll'
        ])->name('notifications.read-all');

        Route::resource(
            'admins',
            AdminUserController::class
        )
            ->parameters([
                'admins' => 'adminUser'
            ])
            ->except([
                'show'
            ]);

        Route::get(
            '/admins/{adminUser}/photo',
            [
                AdminUserController::class,
                'photo'
            ]
        )->name('admins.photo');

        /*
        |--------------------------------------------------------------------------
        | KELOLA KABID
        |--------------------------------------------------------------------------
        */
        Route::resource(
            'kabid',
            KabidController::class
        )->except([
            'show'
        ]);

        Route::get(
            '/kabid/{kabid}/photo',
            [
                KabidController::class,
                'photo'
            ]
        )->name('kabid.photo');

        Route::put(
            '/kabid/{kabid}/approve',
            [
                KabidController::class,
                'approve'
            ]
        )->name('kabid.approve');

        Route::put(
            '/kabid/{kabid}/reject',
            [
                KabidController::class,
                'reject'
            ]
        )->name('kabid.reject');


        Route::resource(
            'karyawan',
            KaryawanController::class
        )->except([
            'show'
        ]);

        Route::get(
            '/karyawan/{karyawan}/photo',
            [
                KaryawanController::class,
                'photo'
            ]
        )->name('karyawan.photo');

        Route::put(
            '/karyawan/{karyawan}/approve',
            [
                KaryawanController::class,
                'approve'
            ]
        )->name('karyawan.approve');

        Route::put(
            '/karyawan/{karyawan}/reject',
            [
                KaryawanController::class,
                'reject'
            ]
        )->name('karyawan.reject');

        Route::resource(
            'departments',
            DepartmentController::class
        )->except([
            'show'
        ]);

        Route::get('/jatah-cuti', [
            LeaveBalanceController::class,
            'index'
        ])->name('leave-balances.index');

        Route::post('/jatah-cuti/generate', [
            LeaveBalanceController::class,
            'generate'
        ])->name('leave-balances.generate');

        Route::put('/jatah-cuti/{leaveBalance}', [
            LeaveBalanceController::class,
            'update'
        ])->name('leave-balances.update');

        Route::get('/pengajuan-cuti', [
            AdminLeaveRequestController::class,
            'index'
        ])->name('leave-requests.index');

        Route::put(
            '/pengajuan-cuti/{leaveRequest}/approve',
            [
                AdminLeaveRequestController::class,
                'approve'
            ]
        )->name('leave-requests.approve');

        Route::put(
            '/pengajuan-cuti/{leaveRequest}/reject',
            [
                AdminLeaveRequestController::class,
                'reject'
            ]
        )->name('leave-requests.reject');

        Route::get('/pengajuan-cuti/{leaveRequest}', [
            AdminLeaveRequestController::class,
            'show'
        ])->name('leave-requests.show');

        Route::get('/reimbursements', [
            AdminReimbursementController::class,
            'index'
        ])->name('reimbursements.index');

        Route::get('/reimbursements/{reimbursement}/receipt', [
            AdminReimbursementController::class,
            'receipt'
        ])->name('reimbursements.receipt');

        Route::patch('/reimbursements/{reimbursement}/approve', [
            AdminReimbursementController::class,
            'approve'
        ])->name('reimbursements.approve');

        Route::patch('/reimbursements/{reimbursement}/reject', [
            AdminReimbursementController::class,
            'reject'
        ])->name('reimbursements.reject');

        Route::patch('/reimbursements/{reimbursement}/paid', [
            AdminReimbursementController::class,
            'markPaid'
        ])->name('reimbursements.paid');

        Route::get('/reimbursements/{reimbursement}', [
            AdminReimbursementController::class,
            'show'
        ])->name('reimbursements.show');


        /*
        |--------------------------------------------------------------------------
        | SURAT DINAS
        |--------------------------------------------------------------------------
        |
        | Admin menerbitkan surat langsung kepada satu atau beberapa
        | Karyawan/Kabid. Tidak ada proses ACC dari penerima.
        |
        */
        Route::get('/surat-dinas', [
            AdminDutyLetterController::class,
            'index'
        ])->name('duty-letters.index');

        Route::get('/surat-dinas/create', [
            AdminDutyLetterController::class,
            'create'
        ])->name('duty-letters.create');

        Route::post('/surat-dinas', [
            AdminDutyLetterController::class,
            'store'
        ])->name('duty-letters.store');

        Route::get('/surat-dinas/{dutyLetter}/edit', [
            AdminDutyLetterController::class,
            'edit'
        ])->name('duty-letters.edit');

        Route::put('/surat-dinas/{dutyLetter}', [
            AdminDutyLetterController::class,
            'update'
        ])->name('duty-letters.update');

        Route::get('/surat-dinas/{dutyLetter}/pdf', [
            AdminDutyLetterController::class,
            'pdf'
        ])->name('duty-letters.pdf');

        Route::patch('/surat-dinas/{dutyLetter}/cancel', [
            AdminDutyLetterController::class,
            'cancel'
        ])->name('duty-letters.cancel');



        /*
        |--------------------------------------------------------------------------
        | VERIFIKASI LAPORAN SURAT DINAS - ADMIN
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/surat-dinas/{dutyLetter}/penerima/{dutyAssignment}/laporan',
            [
                AdminDutyReportController::class,
                'show'
            ]
        )->name('duty-reports.show');

        Route::get(
            '/surat-dinas/{dutyLetter}/penerima/{dutyAssignment}/laporan/foto/{dutyReportFile}',
            [
                AdminDutyReportController::class,
                'file'
            ]
        )->name('duty-reports.file');

        Route::patch(
            '/surat-dinas/{dutyLetter}/penerima/{dutyAssignment}/laporan/verify',
            [
                AdminDutyReportController::class,
                'verify'
            ]
        )->name('duty-reports.verify');

        Route::patch(
            '/surat-dinas/{dutyLetter}/penerima/{dutyAssignment}/laporan/revision',
            [
                AdminDutyReportController::class,
                'requestRevision'
            ]
        )->name('duty-reports.revision');


        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI FEE SURAT DINAS - ADMIN
        |--------------------------------------------------------------------------
        |
        | Tidak ada nominal fee di modul ini.
        | Admin hanya mengonfirmasi bahwa fee sudah dibayarkan
        | setelah laporan pegawai berstatus Diverifikasi.
        |
        */
        Route::patch(
            '/surat-dinas/{dutyLetter}/penerima/{dutyAssignment}/fee/paid',
            [
                AdminDutyFeeController::class,
                'markPaid'
            ]
        )->name('duty-fees.paid');


        Route::get('/surat-dinas/{dutyLetter}', [
            AdminDutyLetterController::class,
            'show'
        ])->name('duty-letters.show');
    });


/*
|--------------------------------------------------------------------------
| KABID
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'role:kabid',
    ShareKabidSidebarCounts::class,
])
    ->prefix('kabid')
    ->name('kabid.')
    ->group(function () {

        Route::get('/dashboard', [
            KabidDashboardController::class,
            'index'
        ])->name('dashboard');

        Route::get('/notifications/{notification}', [
            NotificationController::class,
            'open'
        ])->name('notifications.open');

        Route::patch('/notifications/read-all', [
            NotificationController::class,
            'readAll'
        ])->name('notifications.read-all');


        /*
        |--------------------------------------------------------------------------
        | ANGGOTA SAYA
        |--------------------------------------------------------------------------
        |
        | Hanya daftar Karyawan yang:
        | - sudah approved Admin
        | - department_id sama dengan Kabid login
        |
        | Belum ada proses approval izin pada STEP 13.
        |
        */
        Route::get('/anggota', [
            KabidMemberController::class,
            'index'
        ])->name('members.index');


        /*
        |--------------------------------------------------------------------------
        | PERSETUJUAN IZIN ANGGOTA
        |--------------------------------------------------------------------------
        */
        Route::get('/persetujuan-izin', [
            KabidLeaveApprovalController::class,
            'index'
        ])->name('leave-approvals.index');

        Route::get('/persetujuan-izin/{leaveRequest}', [
            KabidLeaveApprovalController::class,
            'show'
        ])->name('leave-approvals.show');

        Route::put('/persetujuan-izin/{leaveRequest}/approve', [
            KabidLeaveApprovalController::class,
            'approve'
        ])->name('leave-approvals.approve');

        Route::put('/persetujuan-izin/{leaveRequest}/reject', [
            KabidLeaveApprovalController::class,
            'reject'
        ])->name('leave-approvals.reject');

        /*
        |--------------------------------------------------------------------------
        | CUTI / PERIZINAN PRIBADI KABID
        |--------------------------------------------------------------------------
        |
        | Pengajuan milik Kabid sendiri langsung diproses Admin.
        | Approval anggota Kabid akan dibuat pada step approval dua tahap.
        |
        */
        Route::get('/cuti', [
            KabidLeaveRequestController::class,
            'index'
        ])->name('leave-requests.index');

        Route::get('/cuti/ajukan', [
            KabidLeaveRequestController::class,
            'create'
        ])->name('leave-requests.create');

        Route::post('/cuti', [
            KabidLeaveRequestController::class,
            'store'
        ])->name('leave-requests.store');


        /*
        |--------------------------------------------------------------------------
        | REIMBURSE PRIBADI KABID
        |--------------------------------------------------------------------------
        */
        Route::get('/reimbursements', [
            KabidReimbursementController::class,
            'index'
        ])->name('reimbursements.index');

        Route::get('/reimbursements/create', [
            KabidReimbursementController::class,
            'create'
        ])->name('reimbursements.create');

        Route::post('/reimbursements', [
            KabidReimbursementController::class,
            'store'
        ])->name('reimbursements.store');

        Route::get('/reimbursements/{reimbursement}/receipt', [
            KabidReimbursementController::class,
            'receipt'
        ])->name('reimbursements.receipt');

        Route::get('/reimbursements/{reimbursement}/edit', [
            KabidReimbursementController::class,
            'edit'
        ])->name('reimbursements.edit');

        Route::put('/reimbursements/{reimbursement}', [
            KabidReimbursementController::class,
            'update'
        ])->name('reimbursements.update');

        Route::delete('/reimbursements/{reimbursement}', [
            KabidReimbursementController::class,
            'destroy'
        ])->name('reimbursements.destroy');

        Route::get('/reimbursements/{reimbursement}', [
            KabidReimbursementController::class,
            'show'
        ])->name('reimbursements.show');


        /*
        |--------------------------------------------------------------------------
        | SURAT DINAS SAYA - KABID
        |--------------------------------------------------------------------------
        |
        | Surat langsung muncul ke Kabid yang ditugaskan Admin.
        | Tidak ada proses terima/tolak/ACC dari penerima.
        |
        */
        Route::get('/surat-dinas', [
            KabidDutyLetterController::class,
            'index'
        ])->name('duty-letters.index');

        Route::get('/surat-dinas/{dutyAssignment}/pdf', [
            KabidDutyLetterController::class,
            'pdf'
        ])->name('duty-letters.pdf');

        /*
        |--------------------------------------------------------------------------
        | LAPORAN HASIL DINAS - KABID
        |--------------------------------------------------------------------------
        */
        Route::get('/surat-dinas/{dutyAssignment}/laporan', [
            KabidDutyReportController::class,
            'edit'
        ])->name('duty-reports.edit');

        Route::post('/surat-dinas/{dutyAssignment}/laporan', [
            KabidDutyReportController::class,
            'store'
        ])->name('duty-reports.store');

        Route::get(
            '/surat-dinas/{dutyAssignment}/laporan/foto/{dutyReportFile}',
            [
                KabidDutyReportController::class,
                'file'
            ]
        )->name('duty-reports.file');

        Route::delete(
            '/surat-dinas/{dutyAssignment}/laporan/foto/{dutyReportFile}',
            [
                KabidDutyReportController::class,
                'destroyFile'
            ]
        )->name('duty-reports.files.destroy');

        Route::get('/surat-dinas/{dutyAssignment}', [
            KabidDutyLetterController::class,
            'show'
        ])->name('duty-letters.show');
    });


/*
|--------------------------------------------------------------------------
| KARYAWAN
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'role:karyawan',
    ShareKaryawanSidebarCounts::class,
])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {

        Route::get('/dashboard', [
            KaryawanDashboardController::class,
            'index'
        ])->name('dashboard');

        Route::get('/notifications/{notification}', [
            NotificationController::class,
            'open'
        ])->name('notifications.open');

        Route::patch('/notifications/read-all', [
            NotificationController::class,
            'readAll'
        ])->name('notifications.read-all');

        Route::get('/cuti', [
            KaryawanLeaveRequestController::class,
            'index'
        ])->name('leave-requests.index');

        Route::get('/cuti/ajukan', [
            KaryawanLeaveRequestController::class,
            'create'
        ])->name('leave-requests.create');

        Route::post('/cuti', [
            KaryawanLeaveRequestController::class,
            'store'
        ])->name('leave-requests.store');

        Route::get('/reimbursements', [
            KaryawanReimbursementController::class,
            'index'
        ])->name('reimbursements.index');

        Route::get('/reimbursements/create', [
            KaryawanReimbursementController::class,
            'create'
        ])->name('reimbursements.create');

        Route::post('/reimbursements', [
            KaryawanReimbursementController::class,
            'store'
        ])->name('reimbursements.store');

        Route::get('/reimbursements/{reimbursement}/receipt', [
            KaryawanReimbursementController::class,
            'receipt'
        ])->name('reimbursements.receipt');

        Route::get('/reimbursements/{reimbursement}/edit', [
            KaryawanReimbursementController::class,
            'edit'
        ])->name('reimbursements.edit');

        Route::put('/reimbursements/{reimbursement}', [
            KaryawanReimbursementController::class,
            'update'
        ])->name('reimbursements.update');

        Route::delete('/reimbursements/{reimbursement}', [
            KaryawanReimbursementController::class,
            'destroy'
        ])->name('reimbursements.destroy');

        Route::get('/reimbursements/{reimbursement}', [
            KaryawanReimbursementController::class,
            'show'
        ])->name('reimbursements.show');


        /*
        |--------------------------------------------------------------------------
        | SURAT DINAS SAYA - KARYAWAN
        |--------------------------------------------------------------------------
        |
        | Surat langsung muncul ke Karyawan yang ditugaskan Admin.
        | Tidak ada proses terima/tolak/ACC dari penerima.
        |
        */
        Route::get('/surat-dinas', [
            KaryawanDutyLetterController::class,
            'index'
        ])->name('duty-letters.index');

        Route::get('/surat-dinas/{dutyAssignment}/pdf', [
            KaryawanDutyLetterController::class,
            'pdf'
        ])->name('duty-letters.pdf');

        /*
        |--------------------------------------------------------------------------
        | LAPORAN HASIL DINAS - KARYAWAN
        |--------------------------------------------------------------------------
        */
        Route::get('/surat-dinas/{dutyAssignment}/laporan', [
            KaryawanDutyReportController::class,
            'edit'
        ])->name('duty-reports.edit');

        Route::post('/surat-dinas/{dutyAssignment}/laporan', [
            KaryawanDutyReportController::class,
            'store'
        ])->name('duty-reports.store');

        Route::get(
            '/surat-dinas/{dutyAssignment}/laporan/foto/{dutyReportFile}',
            [
                KaryawanDutyReportController::class,
                'file'
            ]
        )->name('duty-reports.file');

        Route::delete(
            '/surat-dinas/{dutyAssignment}/laporan/foto/{dutyReportFile}',
            [
                KaryawanDutyReportController::class,
                'destroyFile'
            ]
        )->name('duty-reports.files.destroy');

        Route::get('/surat-dinas/{dutyAssignment}', [
            KaryawanDutyLetterController::class,
            'show'
        ])->name('duty-letters.show');
    });


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
|
| Admin:
| - nama/email diperbarui langsung.
|
| Karyawan/Kabid:
| - perubahan profil masuk ke employee_profile_update_requests
| - data aktif belum berubah sampai ACC Admin.
|
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::get('/profile/foto', [
        ProfileController::class,
        'photo'
    ])->name('profile.photo');

    Route::get(
        '/profile/pengajuan-foto/{profileUpdateRequest}',
        [
            ProfileController::class,
            'pendingPhoto'
        ]
    )->name('profile.pending-photo');

    /*
     * Route delete dipertahankan untuk kompatibilitas,
     * tetapi ProfileController::destroy() menolak self-delete.
     */
    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
