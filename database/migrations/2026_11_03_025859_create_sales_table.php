<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('invoice_no', 50);
            $table->enum('status', ['completed', 'held', 'cancelled', 'returned'])->default('completed');
            $table->dateTime('sale_date');
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->enum('discount_type', ['fixed', 'percent'])->nullable();
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('shipping_amount', 15, 4)->default(0);
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->decimal('due_amount', 15, 4)->default(0);
            $table->decimal('change_amount', 15, 4)->default(0);
            $table->text('note')->nullable();
            $table->string('hold_reference', 50)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
            $table->unique(['store_id', 'invoice_no']);
            $table->index(['store_id', 'user_id', 'sale_date']);
            $table->index(['store_id', 'customer_id', 'sale_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
