<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->restrictOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('payment_no', 50);
            $table->decimal('amount', 15, 4);
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'bank_transfer', 'cheque', 'other']);
            $table->string('reference_no', 100)->nullable();
            $table->timestamp('paid_at');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['store_id', 'payment_no']);
            $table->index('purchase_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
