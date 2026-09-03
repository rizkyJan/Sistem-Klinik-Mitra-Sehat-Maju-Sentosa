<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profile_update_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Hanya field yang berubah yang disimpan.
             * Data rekening juga bisa masuk ke JSON ini saat nanti
             * pegawai mengajukan perubahan rekening.
             */
            $table->json('old_data');
            $table->json('new_data');

            /*
             * Pas foto baru masih menjadi file calon sampai Admin ACC.
             */
            $table->string('new_photo_path')->nullable();

            $table->string('status', 20)
                ->default('pending')
                ->index();

            $table->text('rejection_reason')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profile_update_requests');
    }
};
