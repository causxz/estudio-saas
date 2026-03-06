<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {        
        
        Schema::table('clients', fn (Blueprint $table) => $table->foreignId('studio_id')->default(1)->constrained()->cascadeOnDelete());
        Schema::table('services', fn (Blueprint $table) => $table->foreignId('studio_id')->default(1)->constrained()->cascadeOnDelete());
        Schema::table('appointments', fn (Blueprint $table) => $table->foreignId('studio_id')->default(1)->constrained()->cascadeOnDelete());
        Schema::table('transactions', fn (Blueprint $table) => $table->foreignId('studio_id')->default(1)->constrained()->cascadeOnDelete());
        Schema::table('anamneses', fn (Blueprint $table) => $table->foreignId('studio_id')->default(1)->constrained()->cascadeOnDelete());
    }

    public function down(): void
    {
        Schema::table('clients', fn (Blueprint $table) => $table->dropForeign(['studio_id'])->dropColumn('studio_id'));
        Schema::table('services', fn (Blueprint $table) => $table->dropForeign(['studio_id'])->dropColumn('studio_id'));
        Schema::table('appointments', fn (Blueprint $table) => $table->dropForeign(['studio_id'])->dropColumn('studio_id'));
        Schema::table('transactions', fn (Blueprint $table) => $table->dropForeign(['studio_id'])->dropColumn('studio_id'));
        Schema::table('anamneses', fn (Blueprint $table) => $table->dropForeign(['studio_id'])->dropColumn('studio_id'));
    }
};