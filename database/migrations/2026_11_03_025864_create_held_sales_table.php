<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('held_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('hold_no', 50);
            $table->json('cart_data');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('held_at')->useCurrent();
            $table->timestamps();
            $table->index(['store_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('held_sales');
    }
};
