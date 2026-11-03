<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('employee_code', 50);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20);
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->date('joining_date');
            $table->string('designation', 100);
            $table->enum('employment_status', ['active', 'inactive', 'terminated', 'resigned'])->default('active');
            $table->enum('salary_type', ['monthly', 'daily', 'hourly'])->default('monthly');
            $table->decimal('basic_salary', 15, 4)->default(0);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->json('documents')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['store_id', 'employee_code']);
            $table->index(['store_id', 'employment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
