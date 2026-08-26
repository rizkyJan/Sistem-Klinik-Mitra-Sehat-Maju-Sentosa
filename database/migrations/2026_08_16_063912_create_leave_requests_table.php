<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Karyawan & jatah cuti
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('leave_balance_id')
                ->constrained('leave_balances')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Data cuti
            |--------------------------------------------------------------------------
            */

            $table->date('start_date');

            $table->date('end_date');

            $table->unsignedTinyInteger('total_days');

            $table->text('reason');


            /*
            |--------------------------------------------------------------------------
            | Data awal pengganti
            |--------------------------------------------------------------------------
            */

            $table->string('substitute_name');

            $table->string('substitute_whatsapp', 20);

            $table->boolean('substitute_confirmed')
                ->default(false);


            /*
            |--------------------------------------------------------------------------
            | Data pengganti setelah ACC
            |--------------------------------------------------------------------------
            */

            $table->text('substitute_address')
                ->nullable();

            $table->string('substitute_bank_name')
                ->nullable();

            $table->string('substitute_bank_account_number', 100)
                ->nullable();

            $table->string('substitute_bank_account_holder')
                ->nullable();

            $table->timestamp('substitute_completed_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('rejected_at')
                ->nullable();

            $table->text('rejection_reason')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'user_id',
                'status',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
