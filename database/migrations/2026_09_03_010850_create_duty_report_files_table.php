<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_report_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('duty_report_id')
                ->constrained('duty_reports')
                ->cascadeOnDelete();

            // Foto dokumentasi / bukti kehadiran.
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('caption')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_report_files');
    }
};
