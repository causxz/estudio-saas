<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona a trava (Interruptor) no Estúdio
        Schema::table('studios', function (Blueprint $table) {
            $table->boolean('has_commissions')->default(false)->after('name');
        });

        // 2. Adiciona a porcentagem de comissão no Serviço
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('price'); // Ex: 40.00
        });

        // 3. Adiciona o Profissional e o Valor no Agendamento
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('professional_id')->nullable()->constrained('users')->nullOnDelete()->after('client_id');
            $table->decimal('commission_amount', 10, 2)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('has_commissions');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('commission_percentage');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn(['professional_id', 'commission_amount']);
        });
    }
};