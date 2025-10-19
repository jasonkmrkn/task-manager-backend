<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // <-- This creates a BIGINT
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();

            // This is the modern, correct way to do foreign keys
            // It automatically creates a BIGINT and links them
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('status')->onDelete('restrict');
            $table->foreignId('priority_id')->constrained('priority')->onDelete('restrict');

            $table->timestamps(); // <-- Always good to have!
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks'); // <-- Must be plural "tasks"
    }
};
