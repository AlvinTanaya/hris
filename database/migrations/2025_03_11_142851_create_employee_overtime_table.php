<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('total_hours', 5, 2);
            $table->text('reason');
            $table->enum('overtime_type', ['Paid_Overtime', 'Overtime_Leave'])->default('Paid_Overtime');
            $table->enum('approval_status', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->text('declined_reason')->nullable(); // Tambahan alasan jika ditolak
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('answer_user_id')->nullable()->constrained('users')->onDelete('cascade'); // Tambahan user yang memberikan keputusan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_overtime');
    }
};

