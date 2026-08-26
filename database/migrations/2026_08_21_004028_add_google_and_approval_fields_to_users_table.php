<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ID unik akun Google
            $table->string('google_id')
                ->nullable()
                ->unique()
                ->after('email');

            // Foto profil dari Google
            $table->string('google_avatar')
                ->nullable()
                ->after('google_id');

            // Status verifikasi karyawan oleh admin
            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected'
            ])
                ->default('approved')
                ->after('is_active');

            // Alasan apabila admin menolak data karyawan
            $table->text('approval_rejection_reason')
                ->nullable()
                ->after('approval_status');

            // Waktu karyawan selesai mengisi profil
            $table->timestamp('profile_completed_at')
                ->nullable()
                ->after('approval_rejection_reason');
        });

        /*
         * Karyawan lama yang datanya sudah lengkap
         * langsung dianggap approved agar tidak ikut
         * menunggu verifikasi ulang.
         */
        DB::table('users')
            ->where('role', 'karyawan')
            ->whereNotNull('nik')
            ->whereNotNull('whatsapp')
            ->whereNotNull('department_id')
            ->whereNotNull('join_date')
            ->update([
                'approval_status' => 'approved',
                'profile_completed_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);

            $table->dropColumn([
                'google_id',
                'google_avatar',
                'approval_status',
                'approval_rejection_reason',
                'profile_completed_at',
            ]);
        });
    }
};
