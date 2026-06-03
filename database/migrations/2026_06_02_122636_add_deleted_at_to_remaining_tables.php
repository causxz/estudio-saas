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
        if (!Schema::hasColumn('appointments', 'deleted_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('anamnesis', 'deleted_at')) {
            Schema::table('anamnesis', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('transactions', 'deleted_at')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('personal_transactions', 'deleted_at')) {
            Schema::table('personal_transactions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('anamneses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('personal_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
