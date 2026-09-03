<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_letters', function (Blueprint $table) {
            $table->id();

            // Informasi surat / kegiatan.
            $table->string('letter_number', 150)->nullable();
            $table->string('title');
            $table->string('organizer')->nullable();
            $table->text('description')->nullable();

            // Waktu kegiatan.
            $table->date('event_date')->index();
            $table->time('start_time');
            $table->time('end_time')->nullable();

            // Lokasi kegiatan.
            $table->string('location_name');
            $table->text('location_address')->nullable();
            $table->text('maps_url')->nullable();

            // File PDF surat dinas yang diunggah Admin.
            $table->string('letter_path');
            $table->string('letter_original_name');
            $table->string('letter_mime', 100)->nullable();
            $table->unsignedBigInteger('letter_size')->nullable();

            // Status surat. Untuk versi awal langsung published saat dibuat.
            $table->string('status', 30)->default('published')->index();
            $table->timestamp('published_at')->nullable();

            // Admin pembuat surat. Jika akun Admin dihapus, histori surat tetap ada.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_letters');
    }
};
