<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code', 40)->unique();

            $table->date('purchase_date');
            $table->string('category', 50);
            $table->string('merchant_name')->nullable();
            $table->string('item_name');
            $table->text('purpose');

            /*
             * Nominal disimpan sebagai integer Rupiah.
             * Contoh Rp150.000 disimpan menjadi 150000.
             */
            $table->unsignedBigInteger('amount');

            /*
             * Bukti disimpan di disk "local" (storage/app/private),
             * jadi tidak dapat dibuka langsung tanpa melewati controller.
             */
            $table->string('receipt_path');
            $table->string('receipt_original_name');
            $table->string('receipt_mime', 100)->nullable();
            $table->unsignedBigInteger('receipt_size')->nullable();

            /*
             * Alur status:
             * pending -> approved -> paid
             * pending -> rejected
             */
            $table->string('status', 20)->default('pending')->index();

            $table->text('review_note')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'purchase_date']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};
