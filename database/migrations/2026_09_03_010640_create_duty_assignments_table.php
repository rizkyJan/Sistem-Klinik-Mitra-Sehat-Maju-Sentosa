<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('duty_letter_id')
                ->constrained('duty_letters')
                ->cascadeOnDelete();

            // Penerima boleh Karyawan atau Kabid.
            // nullOnDelete dipakai agar histori surat/laporan tidak hilang
            // apabila akun pegawai suatu saat dihapus.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Snapshot identitas saat surat diterbitkan.
            $table->string('assignee_name');
            $table->string('assignee_role', 30);
            $table->string('assignee_department')->nullable();

            $table->timestamp('assigned_at')->nullable();

            // Workflow laporan:
            // pending   = belum mengirim laporan
            // submitted = laporan sudah dikirim
            // revision  = diminta diperbaiki Admin
            // verified  = sudah diverifikasi Admin
            $table->string('report_status', 30)
                ->default('pending')
                ->index();

            $table->timestamp('report_submitted_at')->nullable();
            $table->timestamp('report_verified_at')->nullable();
            $table->foreignId('report_verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('revision_note')->nullable();

            // Tidak menyimpan nominal fee.
            // Admin hanya mengonfirmasi apakah fee sudah dibayar.
            $table->string('fee_status', 30)
                ->default('unpaid')
                ->index();
            $table->timestamp('fee_paid_at')->nullable();
            $table->foreignId('fee_confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('fee_payment_note')->nullable();

            $table->timestamps();

            // Satu orang hanya boleh ditugaskan sekali pada surat yang sama.
            $table->unique(
                ['duty_letter_id', 'user_id'],
                'duty_assignment_letter_user_unique'
            );

            $table->index(
                ['user_id', 'report_status'],
                'duty_assignment_user_report_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_assignments');
    }
};
