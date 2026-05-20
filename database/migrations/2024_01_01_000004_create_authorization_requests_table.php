<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorization_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->enum('movement_type', ['entry', 'exit']);
            $table->text('reason');
            $table->enum('status', ['pending_teacher', 'ready_porteiro', 'rejected_teacher', 'completed'])->default('pending_teacher');
            $table->text('teacher_notes')->nullable();
            $table->timestamp('teacher_approved_at')->nullable();
            $table->foreignId('porteiro_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('porteiro_validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorization_requests');
    }
};
