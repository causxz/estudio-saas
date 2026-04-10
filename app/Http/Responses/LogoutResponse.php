<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Session;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // 1. Invalida a sessão atual (Apaga os dados do Server-Side)
        Session::invalidate();
        
        // 2. Regera o Token CSRF (Garante a segurança da próxima requisição)
        Session::regenerateToken();

        // 3. Redireciona com caminho absoluto para a Landing Page
        return redirect()->to(url('/'));
    }
}