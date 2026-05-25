<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BlockSuspiciousIPs
{
// Rendo le proprietà dinamiche così puoi riutilizzare lo stesso 
// middleware con parametri diversi per le varie rotte!
protected $maxAttempts = 60; 
protected $decayMinutes = 1;
protected $blockMinutes = 10; 
public function handle(Request $request, Closure $next, $maxAttempts = null, $blockMinutes = null): Response
{
// Se passati via rotta, sovrascrivi i valori di default
$this->maxAttempts = $maxAttempts ?? $this->maxAttempts;
$this->blockMinutes = $blockMinutes ?? $this->blockMinutes;

$ip = $request->ip();
$key = $this->throttleKey($ip);

// 1. Controlla se l'IP è attualmente bannato
if (Cache::has($key.':blocked')) {
Log::warning("IP bloccato $ip ha tentato un accesso.");
return response()->json([
'error' => "Your IP has been blocked for {$this->blockMinutes} minute(s) due to flood/spam."
], 429); // Ritorna un vero errore HTTP 429
}

// 2. Gestione del contatore dei tentativi
// Se non esiste, lo inizializziamo a 0 (perché poi incrementiamo subito)
if (!Cache::has($key)) {
Cache::put($key, 0, $this->decayMinutes * 60);
}

// Incrementa il contatore
$attempts = Cache::increment($key);

// 3. Controlla se ha superato la soglia
if ($attempts > $this->maxAttempts) {
// Metti l'IP in blacklist
Cache::put($key.':blocked', true, $this->blockMinutes * 60);

// Elimina il contatore vecchio per resettare i tentativi alla fine del ban
Cache::forget($key);

Log::alert("IP $ip BLOCCATO per {$this->blockMinutes} minuti. Troppe richieste.");

return response()->json([
'error' => "Your IP has been blocked for {$this->blockMinutes} minute(s)."
], 429);
}

return $next($request);
}

protected function throttleKey($ip)
{
return "throttle:" . sha1($ip);
}
}