<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('code')
                ->unique();

            /*
             * Contoh:
             * sakit = 1
             * menikah = 3
             * keguguran = 7
             */
            $table->unsignedSmallInteger('policy_days')
                ->nullable();

            /*
             * Apakah jenis ini langsung memakai
             * saldo cuti tahunan.
             */
            $table->boolean('uses_annual_balance')
                ->default(false);

            /*
             * Apakah kelebihan hari boleh dialihkan
             * ke saldo cuti tahunan.
             */
            $table->boolean('excess_can_use_annual_leave')
                ->default(false);

            /*
             * Contoh sakit.
             */
            $table->boolean('requires_doctor_letter')
                ->default(false);

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_types');
    }
};
