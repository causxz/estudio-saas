<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove a ligação errada
            $table->dropForeign(['professional_id']);
            
            // Cria a ligação correta com a tabela de equipe
            $table->foreign('professional_id')
                  ->references('id')
                  ->on('professionals')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->foreign('professional_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};