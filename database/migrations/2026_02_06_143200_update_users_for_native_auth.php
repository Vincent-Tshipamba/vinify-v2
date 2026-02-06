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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->after('email_verified_at');
            }
        });

        if (Schema::hasColumn('users', 'workos_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['workos_id']);
                $table->dropColumn('workos_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'workos_id')) {
                $table->string('workos_id')->unique()->after('email_verified_at');
            }
        });

        if (Schema::hasColumn('users', 'password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }
};
