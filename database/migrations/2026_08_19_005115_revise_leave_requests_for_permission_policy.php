<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Field lama menjadi nullable
        |--------------------------------------------------------------------------
        */

        Schema::table('leave_requests', function (Blueprint $table) {

            /*
             * Tidak semua izin memakai saldo cuti.
             */
            $table->unsignedBigInteger('leave_balance_id')
                ->nullable()
                ->change();

            /*
             * Pengganti sekarang opsional.
             */
            $table->string('substitute_name')
                ->nullable()
                ->change();

            $table->string('substitute_whatsapp', 20)
                ->nullable()
                ->change();
        });


        /*
        |--------------------------------------------------------------------------
        | Field baru
        |--------------------------------------------------------------------------
        */

        Schema::table('leave_requests', function (Blueprint $table) {

            $table->foreignId('permission_type_id')
                ->nullable()
                ->after('leave_balance_id')
                ->constrained('permission_types')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Kebijakan Perizinan
            |--------------------------------------------------------------------------
            */

            $table->unsignedSmallInteger('policy_covered_days')
                ->nullable();

            $table->unsignedSmallInteger('excess_days')
                ->default(0);

            $table->enum('excess_handling', [
                'none',
                'annual_leave',
                'unpaid',
            ])->default('none');

            /*
             * Berapa hari yang benar-benar akan
             * mengurangi saldo cuti tahunan saat ACC.
             */
            $table->unsignedSmallInteger(
                'annual_leave_deducted_days'
            )->default(0);


            /*
            |--------------------------------------------------------------------------
            | Surat / Dokumen Pendukung
            |--------------------------------------------------------------------------
            */

            $table->string('supporting_document')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Khusus Melahirkan
            |--------------------------------------------------------------------------
            */

            $table->date('expected_delivery_date')
                ->nullable();

            $table->enum('maternity_salary_status', [
                'paid_base_salary',
                'unpaid',
            ])->nullable();


            /*
            |--------------------------------------------------------------------------
            | Pengganti
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_substitute')
                ->default(false);

            /*
             * Data alamat/bank lama sudah ada.
             * Sekarang langsung digunakan ketika submit.
             */

            $table->enum('substitute_schedule_type', [
                'full_shift',
                'partial_hours',
            ])->nullable();

            $table->foreignId('substitute_shift_id')
                ->nullable()
                ->constrained('work_shifts')
                ->nullOnDelete();

            $table->time('substitute_start_time')
                ->nullable();

            $table->time('substitute_end_time')
                ->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->dropForeign(['permission_type_id']);
            $table->dropForeign(['substitute_shift_id']);

            $table->dropColumn([
                'permission_type_id',
                'policy_covered_days',
                'excess_days',
                'excess_handling',
                'annual_leave_deducted_days',
                'supporting_document',
                'expected_delivery_date',
                'maternity_salary_status',
                'has_substitute',
                'substitute_schedule_type',
                'substitute_shift_id',
                'substitute_start_time',
                'substitute_end_time',
            ]);
        });
    }
};
