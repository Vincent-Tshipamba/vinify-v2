<?php

use App\Models\Document;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('text_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('analysis_request_id')->constrained('analysis_requests')->cascadeOnDelete();
            $table->foreignIdFor(Document::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('corpus_id')->constrained('corpus')->restrictOnDelete();
            $table->json('similarities')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->float('plagiarism_percentage')->default(0);
            $table->float('ai_generated_probability')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_analyses');
    }
};
