<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_shift_change', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rule_id')->constrained('rule_shift')->onDelete('cascade');
            $table->text('reason_change');
            $table->enum('status_change', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->foreignId('user_exchange')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_shift_change');
    }
};
