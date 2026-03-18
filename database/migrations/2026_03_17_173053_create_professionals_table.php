<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cria a tabela simples de Profissionais
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // 2. Conserta a tabela de agendamentos para apontar para cá em vez de 'Users'
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->foreign('professional_id')->references('id')->on('professionals')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
        });
        Schema::dropIfExists('professionals');
    }
};