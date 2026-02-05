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
        Schema::create('corpus', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['GLOBAL', 'UNIVERSITY'])->default('GLOBAL');
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['type', 'university_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corpus');
    }
};
