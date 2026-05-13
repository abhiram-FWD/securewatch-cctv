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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->constrained('cameras')->onDelete('cascade');
            $table->foreignId('raised_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['crowd', 'crime', 'worksite', 'emergency']);
            $table->text('description');
            $table->text('instruction')->nullable();
            $table->text('resolution_note')->nullable();
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_emergency')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
