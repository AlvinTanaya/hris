<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_resign', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('resign_type');
            $table->text('resign_reason');
            $table->date('resign_date');
            $table->enum('resign_status', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->text('declined_reason')->nullable();
            $table->foreignId('response_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_resign');
    }
};
