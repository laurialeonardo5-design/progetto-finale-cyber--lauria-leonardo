<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log; // Importa il Log

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // $event->user contiene l'utente che ha appena fatto il login!
        $user = $event->user;

        // Scriviamo nel log
        Log::info("Accesso eseguito: L'utente {$user->name} (ID: {$user->id}) si è loggato.", [
            'email' => $user->email,
            'ip_address' => request()->ip()
        ]);
    }
}