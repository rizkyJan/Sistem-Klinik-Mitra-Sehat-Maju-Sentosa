<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->unsignedSmallInteger('unpaid_days')
                ->default(0)
                ->after('annual_leave_deducted_days');

            $table->boolean('salary_deduction_consent')
                ->default(false)
                ->after('unpaid_days');

            $table->timestamp('salary_deduction_consent_at')
                ->nullable()
                ->after('salary_deduction_consent');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->dropColumn([
                'unpaid_days',
                'salary_deduction_consent',
                'salary_deduction_consent_at',
            ]);
        });
    }
};
