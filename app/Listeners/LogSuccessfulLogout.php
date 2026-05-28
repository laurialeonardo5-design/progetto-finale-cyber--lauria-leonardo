<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log; // Importa il Log

class LogSuccessfulLogout
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
    public function handle(Logout $event): void
    {
        // $event->user contiene l'utente che sta uscendo
        $user = $event->user;

        if ($user) {
            Log::info("Disconnessione: L'utente {$user->name} (ID: {$user->id}) ha effettuato il logout.", [
                'email' => $user->email,
                'ip_address' => request()->ip()
            ]);
        }
    }
}