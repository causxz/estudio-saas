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

//Definir a rota 'login' para o welcome.blade.php
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

use App\Http\Controllers\AsaasWebhookController;

// Rota para o Asaas enviar notificações
Route::post('/webhooks/asaas', [AsaasWebhookController::class, 'handle']);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Mantemos o GET para caso algum botão seja apenas um link
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Adicionamos o POST para processar o formulário da sua Landing Page
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        // Redireciona para o painel do Filament
        return redirect()->intended('/admin');
    }

    // Se a senha estiver errada, volta para a página inicial com erro
    return back()->withErrors([
        'email' => 'E-mail ou senha incorretos.',
    ])->onlyInput('email');
});