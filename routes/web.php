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
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ])->name('dashboard');

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
    });


/*
|--------------------------------------------------------------------------
| KARYAWAN
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'role:karyawan',
])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {

        Route::get('/dashboard', [
            KaryawanDashboardController::class,
            'index'
        ])->name('dashboard');

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
    });


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
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

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
