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
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Studio Cílios de Ouro"
            $table->string('slug')->unique(); // Ex: "cilios-de-ouro" (útil para links no futuro)
            // Aqui no futuro poderemos colocar: logo, cor principal, cnpj, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
