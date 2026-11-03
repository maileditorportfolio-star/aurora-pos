<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->string('purchase_no', 50);
            $table->string('reference_no', 50)->nullable();
            $table->enum('status', ['draft', 'pending', 'partially_received', 'received', 'cancelled'])->default('draft');
            $table->date('purchase_date');
            $table->date('expected_date')->nullable();
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->enum('discount_type', ['fixed', 'percent'])->nullable();
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('shipping_amount', 15, 4)->default(0);
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->decimal('due_amount', 15, 4)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->unique(['store_id', 'purchase_no']);
            $table->index(['store_id', 'supplier_id', 'status', 'purchase_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
