<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Adiciona o tempo de buffer (padrão zero se ela não quiser usar em algum serviço)
            $table->integer('buffer_after')->default(0)->after('duration_minutes')->comment('Tempo de limpeza em minutos');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('buffer_after');
        });
    }
};