<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['sale', 'purchase', 'payroll']);
            $table->string('prefix', 10);
            $table->unsignedBigInteger('current_number')->default(0);
            $table->tinyInteger('padding')->default(6);
            $table->timestamps();
            $table->unique(['store_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_sequences');
    }
};
