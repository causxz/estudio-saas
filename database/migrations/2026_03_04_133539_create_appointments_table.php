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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            
            // Ligação com a tabela de serviços para puxar preço e duração
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            
            // Controle preciso de tempo para bloquear a agenda
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            
            // Status com opções fixas para evitar erros de digitação
            $table->string('status')->default('agendado'); // agendado, confirmado, concluído, cancelado
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
