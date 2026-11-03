<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained()->restrictOnDelete();
            $table->string('expense_no', 50);
            $table->decimal('amount', 15, 4);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'bank_transfer', 'cheque', 'other']);
            $table->string('reference_no', 100)->nullable();
            $table->string('vendor', 150)->nullable();
            $table->text('description');
            $table->string('receipt_path', 255)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['store_id', 'expense_no']);
            $table->index(['store_id', 'expense_category_id', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
