<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class LogRegisteredUser
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        Log::info("Nuovo utente registrato: Creato l'account per l'utente '{$event->user->name}' (Email: {$event->user->email}, ID: {$event->user->id}).", [
            'user_id'    => $event->user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}