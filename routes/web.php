<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Anamnesis;

// Rota para abrir a ficha de anamnese preenchida
Route::get('/anamnese/{id}/imprimir', function ($id) {
    // Busca a anamnese no banco de dados junto com a cliente
    $record = Anamnesis::with('client')->findOrFail($id);
    
    // Retorna aquela view visual que criamos antes
    return view('pdf.anamnese', compact('record'));
})->name('anamnese.imprimir');


//teste do docker whatsapp
use Illuminate\Support\Facades\Http;

Route::get('/conectar-whatsapp', function () {
    $apiKey = 'ChaveSecretaEstudio123'; // A senha que definimos no Docker
    
    $response = Http::withHeaders([
        'apikey' => $apiKey,
        'Content-Type' => 'application/json'
    ])->post('http://localhost:8080/instance/create', [
        'instanceName' => 'estudio',
        'token' => 'token_aleatorio_123', // Um token interno para esta instância
        'qrcode' => true
    ]);

    return $response->json();
});

// Define a rota 'register' redirecionando para o registro do Filament
Route::get('/register', function () {
    return redirect()->route('filament.admin.auth.register');
})->name('register');

// Também é uma boa prática definir a rota 'login' para o welcome.blade.php
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');