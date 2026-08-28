<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedSmallInteger('self_replacement_days')
                ->default(0)
                ->after('unpaid_days');

            $table->boolean('self_replacement_consent')
                ->default(false)
                ->after('self_replacement_days');

            $table->timestamp('self_replacement_consent_at')
                ->nullable()
                ->after('self_replacement_consent');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'self_replacement_days',
                'self_replacement_consent',
                'self_replacement_consent_at',
            ]);
        });
    }
};
