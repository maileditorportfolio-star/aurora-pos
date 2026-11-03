<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('sku', 100);
            $table->string('barcode', 100)->unique();
            $table->decimal('purchase_price', 15, 4)->default(0);
            $table->decimal('selling_price', 15, 4)->default(0);
            $table->decimal('wholesale_price', 15, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
