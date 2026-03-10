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
