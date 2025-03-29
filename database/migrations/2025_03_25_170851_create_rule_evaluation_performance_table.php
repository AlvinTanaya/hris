<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rule_evaluation_performance', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255)->nullable();
            $table->decimal('weight', 2, 0)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_evaluation_performance');
    }
};
