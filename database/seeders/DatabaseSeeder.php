<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Verifica se o usuário já existe para não dar erro ao rodar o script duas vezes
        if (!User::where('email', 'admin@admin.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin Estúdio',
                'email' => 'admin@admin.com',
                'password' => Hash::make('12345678'), // Senha padrão
            ]);
        }
    }
}