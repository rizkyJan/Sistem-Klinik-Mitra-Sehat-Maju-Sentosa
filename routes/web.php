<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LeaveBalanceController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\LeaveRequestController as KaryawanLeaveRequestController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Middleware\ShareAdminSidebarCounts;
use App\Http\Controllers\Admin\ReimbursementController as AdminReimbursementController;
use App\Http\Controllers\Karyawan\ReimbursementController as KaryawanReimbursementController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth',
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


Route::middleware(['auth'])
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
