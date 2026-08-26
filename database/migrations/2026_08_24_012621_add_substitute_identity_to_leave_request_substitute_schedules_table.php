<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'leave_request_substitute_schedules',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | Apakah tanggal ini memakai pengganti
                |--------------------------------------------------------------------------
                */

                $table
                    ->boolean('has_substitute')
                    ->default(false)
                    ->after('schedule_date');


                /*
                |--------------------------------------------------------------------------
                | Identitas Pengganti Per Tanggal
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('substitute_name')
                    ->nullable()
                    ->after('has_substitute');

                $table
                    ->string('substitute_whatsapp', 30)
                    ->nullable()
                    ->after('substitute_name');

                $table
                    ->text('substitute_address')
                    ->nullable()
                    ->after('substitute_whatsapp');


                /*
                |--------------------------------------------------------------------------
                | Rekening Pengganti
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('substitute_bank_name', 100)
                    ->nullable()
                    ->after('substitute_address');

                $table
                    ->string(
                        'substitute_bank_account_number',
                        100
                    )
                    ->nullable()
                    ->after('substitute_bank_name');

                $table
                    ->string(
                        'substitute_bank_account_holder'
                    )
                    ->nullable()
                    ->after(
                        'substitute_bank_account_number'
                    );
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'leave_request_substitute_schedules',
            function (Blueprint $table) {

                $table->dropColumn([
                    'has_substitute',
                    'substitute_name',
                    'substitute_whatsapp',
                    'substitute_address',
                    'substitute_bank_name',
                    'substitute_bank_account_number',
                    'substitute_bank_account_holder',
                ]);
            }
        );
    }
};
