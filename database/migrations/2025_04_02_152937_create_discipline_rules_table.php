<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_type'); // attendance, late, early_leave, st, sp, etc.
            $table->string('category'); // Used for grouping rules (e.g., "attendance_percentage")
            $table->decimal('min_value', 10, 2)->nullable(); // Minimum value (e.g., 95%)
            $table->decimal('max_value', 10, 2)->nullable(); // Maximum value (e.g., 100%)
            $table->integer('occurrence')->nullable(); // Number of occurrences (e.g., 1 time, 2 times)
            $table->decimal('score_value', 10, 2); // The score or deduction value
            $table->string('operation', 10)->default('set'); // 'set', 'add', 'subtract', 'multiply'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discipline_rules');
    }
};
