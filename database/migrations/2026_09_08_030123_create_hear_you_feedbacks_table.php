<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hear_you_feedbacks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Periode selalu disimpan sebagai tanggal pertama dalam bulan.
             * Contoh: September 2026 => 2026-09-01.
             */
            $table->date('period')->index();

            $table->string('category', 30);
            $table->text('message');

            /*
             * Tanggapan dan progres dari Admin.
             */
            $table->text('admin_response')->nullable();
            $table->string('follow_up_status', 30)
                ->default('waiting_response')
                ->index();

            $table->foreignId('responded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('responded_at')->nullable();

            /*
             * Penilaian pegawai pada bulan setelah aspirasi dibuat.
             * good      = terlaksana dengan baik
             * less_good = terlaksana tetapi kurang baik
             * same      = sama saja / belum ada perubahan berarti
             */
            $table->string('employee_evaluation', 30)->nullable();
            $table->text('evaluation_note')->nullable();
            $table->timestamp('evaluated_at')->nullable();

            $table->timestamps();

            /*
             * Satu pegawai hanya boleh membuat satu Hear You per bulan.
             */
            $table->unique(
                ['user_id', 'period'],
                'hear_you_user_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hear_you_feedbacks');
    }
};
