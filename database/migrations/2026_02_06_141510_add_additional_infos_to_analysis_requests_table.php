<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analysis_requests', function (Blueprint $table) {
            $table->json('additional_infos')->nullable()->after('submitted_at');
        });

        if (Schema::hasColumn('analysis_requests', 'university_id')) {
            Schema::table('analysis_requests', function (Blueprint $table) {
                $table->dropForeign(['university_id']);
            });

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE analysis_requests MODIFY university_id BIGINT UNSIGNED NULL');
            }

            Schema::table('analysis_requests', function (Blueprint $table) {
                $table->foreign('university_id')->references('id')->on('universities')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_requests', function (Blueprint $table) {
            $table->dropColumn('additional_infos');
        });
    }
};
