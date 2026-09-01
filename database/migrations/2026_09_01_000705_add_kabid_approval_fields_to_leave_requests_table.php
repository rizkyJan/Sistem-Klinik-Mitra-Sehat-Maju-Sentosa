<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            /*
             * Status approval tahap Kabid.
             *
             * pending      = menunggu Kabid
             * approved     = sudah disetujui Kabid
             * rejected     = ditolak Kabid
             * not_required = tidak membutuhkan approval Kabid
             *                (cuti Kabid sendiri / data legacy)
             */
            $table->string('kabid_status', 20)
                ->default('pending')
                ->after('status')
                ->index();

            $table->foreignId('kabid_reviewed_by')
                ->nullable()
                ->after('kabid_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('kabid_reviewed_at')
                ->nullable()
                ->after('kabid_reviewed_by');

            $table->text('kabid_rejection_reason')
                ->nullable()
                ->after('kabid_reviewed_at');
        });

        /*
         * Semua pengajuan yang sudah ada sebelum fitur dua tahap
         * dianggap data legacy.
         *
         * Dengan begitu histori lama tidak tiba-tiba harus
         * mendapatkan persetujuan Kabid.
         */
        DB::table('leave_requests')
            ->update([
                'kabid_status' => 'not_required',
            ]);
    }


    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign([
                'kabid_reviewed_by',
            ]);

            $table->dropIndex([
                'kabid_status',
            ]);

            $table->dropColumn([
                'kabid_status',
                'kabid_reviewed_by',
                'kabid_reviewed_at',
                'kabid_rejection_reason',
            ]);
        });
    }
};
