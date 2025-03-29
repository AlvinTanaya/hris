<?php

// Isi migration (database/migrations/xxxx_create_employee_evaluation_performance_table.php)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employee_evaluation_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rule_evaluation_performance_id')->constrained()->onDelete('cascade');
            $table->decimal('value', 2, 0)->nullable();
            $table->timestamps(); // created_at dan updated_at
            
            // Index untuk performa query
            $table->index(['user_id', 'rule_evaluation_performance_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_evaluation_performance');
    }
};