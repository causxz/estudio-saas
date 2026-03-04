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
    Schema::create('anamnesis', function (Blueprint $table) {
        $table->id();
        // Conecta ao cliente - Se o cliente for deletado, a ficha também é (cascade)
        $table->foreignId('client_id')->unique()->constrained()->onDelete('cascade');

        // --- FICHA DIGITAL (Perguntas de Saúde) ---
        $table->boolean('has_allergy')->default(false); // Alergia a esmalte, cianoacrilato, etc.
        $table->boolean('eye_disease')->default(false); // Conjuntivite, blefarite.
        $table->boolean('pregnant_or_lactating')->default(false); // Grávida ou lactante.
        $table->boolean('uses_contact_lenses')->default(false); // Usa lentes?
        $table->boolean('thyroid_problem')->default(false); // Problemas de tireoide afetam a retenção.
        $table->boolean('sleeps_on_stomach')->default(false); // Dorme de bruços? (afeta um lado dos cílios)
        $table->text('observations')->nullable(); // Campo livre para notas.

        // --- ESTILO (Mapeamento) ---
        $table->string('preferred_style')->nullable(); // Ex: Gatinho, Boneca, Esquilo.
        $table->string('mapping_details')->nullable(); // Tamanhos usados (ex: 8, 9, 10, 11).

        // --- FICHA FÍSICA (Upload de Arquivo/Assinatura) ---
        $table->string('physical_file')->nullable(); // Aqui salva o PDF ou a foto da ficha assinada.

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anamneses');
    }
};
