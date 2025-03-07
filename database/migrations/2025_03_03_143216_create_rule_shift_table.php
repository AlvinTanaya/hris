<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rule_shift', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Morning', 'Afternoon', 'Normal']);
            $table->time('hour_start');
            $table->time('hour_end');
            $table->json('days'); // Store days as JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_shift');
    }
};
