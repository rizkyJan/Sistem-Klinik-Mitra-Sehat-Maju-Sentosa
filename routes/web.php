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
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
