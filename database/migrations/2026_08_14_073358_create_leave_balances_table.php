<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->year('year');

            $table->unsignedTinyInteger('quota_days')
                ->default(9);

            $table->unsignedTinyInteger('used_days')
                ->default(0);

            $table->timestamps();

            // Satu karyawan hanya boleh punya
            // satu jatah untuk satu tahun.
            $table->unique([
                'user_id',
                'year',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
