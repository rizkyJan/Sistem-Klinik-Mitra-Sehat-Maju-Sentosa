<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'leave_request_substitute_schedules',
            function (Blueprint $table) {
                /*
                |--------------------------------------------------------------
                | Penanggung Biaya Pengganti
                |--------------------------------------------------------------
                |
                | employee = biaya pengganti dibayar oleh pemohon/karyawan
                | company  = biaya pengganti dibayar oleh perusahaan
                |
                | Nullable sengaja dipakai agar data lama tetap aman dan tidak
                | dipaksa dianggap sebagai salah satu pilihan baru.
                |
                */
                $table
                    ->enum('substitute_fee_payer', ['employee', 'company'])
                    ->nullable()
                    ->after('substitute_whatsapp');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'leave_request_substitute_schedules',
            function (Blueprint $table) {
                $table->dropColumn('substitute_fee_payer');
            }
        );
    }
};
