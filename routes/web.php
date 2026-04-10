<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AsaasWebhookController;
use App\Models\Anamnesis;

// 1. A ROTA PRINCIPAL QUE SERVE A LANDING PAGE E O FORMULÁRIO DE LOGIN
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. FORÇA O NOME 'login' PARA A HOME (O Filament respeitará isto se falhar o Auth)
Route::get('/login-site', function () {
    return redirect('/');
})->name('login');

// 3. A Rota de Processamento do Formulário (O POST)
Route::post('/processar-login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        // Login com sucesso Vai direto para o painel
        return redirect()->to('/admin');
    }

    // Falhou? Volta para a Landing page com o erro.
    return redirect('/')->withErrors([
        'email' => 'As credenciais estão incorretas.',
    ])->onlyInput('email');
})->name('processar.login');

// Outras rotas essenciais
Route::post('/webhooks/asaas', [AsaasWebhookController::class, 'handle']);
Route::get('/anamnese/{id}/imprimir', function ($id) {
    $record = Anamnesis::with('client')->findOrFail($id);
    return view('pdf.anamnese', compact('record'));
})->name('anamnese.imprimir');
