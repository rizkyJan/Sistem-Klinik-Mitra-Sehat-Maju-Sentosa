<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_monthly_reports', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            /* Selalu disimpan sebagai tanggal pertama bulan, contoh 2026-09-01. */
            $table->date('period');

            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->default(0);

            $table->text('employee_note')->nullable();

            /* submitted | revision | verified */
            $table->string('status', 30)->default('submitted');
            $table->text('revision_note')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('admin_edited_at')->nullable();
            $table->foreignId('admin_edited_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['department_id', 'period'],
                'department_monthly_report_period_unique'
            );

            $table->index(['period', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_monthly_reports');
    }
};
