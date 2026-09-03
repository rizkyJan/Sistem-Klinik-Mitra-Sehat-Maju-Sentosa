<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('duty_assignment_id')
                ->unique()
                ->constrained('duty_assignments')
                ->cascadeOnDelete();

            // Isi laporan hasil kegiatan oleh Karyawan/Kabid.
            $table->text('discussion_summary');
            $table->text('result_summary');
            $table->text('follow_up')->nullable();
            $table->text('additional_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_reports');
    }
};
