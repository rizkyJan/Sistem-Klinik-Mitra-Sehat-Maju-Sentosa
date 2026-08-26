<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'leave_request_substitute_schedules',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Pengajuan
                |--------------------------------------------------------------------------
                */

                $table->foreignId('leave_request_id')
                    ->constrained('leave_requests')
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | Tanggal Penggantian
                |--------------------------------------------------------------------------
                */

                $table->date('schedule_date');


                /*
                |--------------------------------------------------------------------------
                | Jenis Jadwal
                |--------------------------------------------------------------------------
                |
                | full_shift    = satu shift penuh
                | partial_hours = hanya beberapa jam
                |
                */

                $table->enum(
                    'schedule_type',
                    [
                        'full_shift',
                        'partial_hours',
                    ]
                );


                /*
                |--------------------------------------------------------------------------
                | Full Shift
                |--------------------------------------------------------------------------
                */

                $table->foreignId('work_shift_id')
                    ->nullable()
                    ->constrained('work_shifts')
                    ->nullOnDelete();


                /*
                |--------------------------------------------------------------------------
                | Beberapa Jam
                |--------------------------------------------------------------------------
                */

                $table->time('start_time')
                    ->nullable();

                $table->time('end_time')
                    ->nullable();


                $table->timestamps();


                /*
                |--------------------------------------------------------------------------
                | Satu tanggal hanya satu jadwal
                |--------------------------------------------------------------------------
                |
                | Nama index dibuat pendek supaya aman untuk MySQL.
                |
                */

                $table->unique(
                    [
                        'leave_request_id',
                        'schedule_date',
                    ],
                    'lrss_request_date_unique'
                );
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'leave_request_substitute_schedules'
        );
    }
};
