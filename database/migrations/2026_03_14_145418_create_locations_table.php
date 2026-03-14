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
    Schema::create('locations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('studio_id')->constrained()->cascadeOnDelete();
        $table->string('name'); // Ex: Matriz, Filial
        $table->string('address'); // Ex: Rua Principal, 123 - Centro
        $table->string('maps_link')->nullable(); // Link do Google Maps
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
