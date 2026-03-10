<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Colocamos o nome exato 'anamnesis' no final da lista
        $tabelas = ['clients', 'services', 'appointments', 'transactions', 'anamnesis'];

        foreach ($tabelas as $tabela) {
            // Só executa se a tabela existir e a coluna NÃO existir
            if (Schema::hasTable($tabela) && !Schema::hasColumn($tabela, 'studio_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    // Usamos constrained('studios') explicitamente para evitar erros de plural do Laravel
                    $table->foreignId('studio_id')->default(1)->constrained('studios')->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tabelas = ['clients', 'services', 'appointments', 'transactions', 'anamnesis'];

        foreach ($tabelas as $tabela) {
            if (Schema::hasTable($tabela) && Schema::hasColumn($tabela, 'studio_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->dropForeign(['studio_id']);
                    $table->dropColumn('studio_id');
                });
            }
        }
    }
};