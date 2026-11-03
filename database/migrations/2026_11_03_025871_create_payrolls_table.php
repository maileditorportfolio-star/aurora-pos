<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('payroll_no', 50);
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->integer('total_employees')->default(0);
            $table->decimal('total_basic', 15, 4)->default(0);
            $table->decimal('total_overtime', 15, 4)->default(0);
            $table->decimal('total_bonus', 15, 4)->default(0);
            $table->decimal('total_allowance', 15, 4)->default(0);
            $table->decimal('total_deduction', 15, 4)->default(0);
            $table->decimal('total_advance', 15, 4)->default(0);
            $table->decimal('total_net', 15, 4)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['store_id', 'month', 'year']);
            $table->unique(['store_id', 'payroll_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
