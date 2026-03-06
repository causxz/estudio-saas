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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // INTEGRAÇÃO COM A AGENDA: Se for um serviço, fica vinculado ao agendamento
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            
            $table->string('description')->comment('Ex: Manutenção Volume Russo, Compra de Fios...');
            
            // TIPO: Define a cor e a matemática (Entrada soma, Saída subtrai)
            $table->enum('type', ['entrada', 'saida'])->default('entrada');
            
            // VALOR: 10 dígitos totais, 2 casas decimais (Ex: 99999999.99)
            $table->decimal('amount', 10, 2);
            
            // MÉTODO DE PAGAMENTO
            $table->string('payment_method')->nullable()->comment('pix, cartao_credito, cartao_debito, dinheiro');
            
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
