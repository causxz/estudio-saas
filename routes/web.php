<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AsaasWebhookController;
use App\Models\Anamnesis;

// 1. A ROTA PRINCIPAL 
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. FORÇA O NOME 'login' PARA A  HOME (O Filament respeitará isto se falhar o Auth)
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
        return redirect()->to('/admin');
    }

    return redirect('/')->withErrors([
        'email' => 'As credenciais estão incorretas.',
    ])->onlyInput('email');
})->name('processar.login');

// 4. Redirecionar os botões de "Assinar" para o Registro do Filament
Route::get('/register', function () {
    return redirect()->route('filament.admin.auth.register');
})->name('register');

//outras rotas essenciais
Route::post('/webhooks/asaas', [AsaasWebhookController::class, 'handle']);
Route::get('/anamnese/{id}/imprimir', function ($id) {
    $record = Anamnesis::with('client')->findOrFail($id);
    return view('pdf.anamnese', compact('record'));
})->name('anamnese.imprimir');