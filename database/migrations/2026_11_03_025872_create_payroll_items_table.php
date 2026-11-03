<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->decimal('basic_salary', 15, 4);
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->decimal('leave_days', 4, 1)->default(0);
            $table->integer('absent_days')->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->decimal('overtime_amount', 15, 4)->default(0);
            $table->decimal('bonus', 15, 4)->default(0);
            $table->decimal('allowance', 15, 4)->default(0);
            $table->decimal('deduction', 15, 4)->default(0);
            $table->decimal('advance', 15, 4)->default(0);
            $table->decimal('other_adjustments', 15, 4)->default(0);
            $table->decimal('net_salary', 15, 4);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['payroll_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
