<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

class HttpService
{
protected $client;
protected $allowedDomains = ['internal.finance','newsapi.org'];
protected $allowedProtocols = ['http', 'https'];
protected $refererHeader; // Intestazione Referer

public function __construct()
{
$this->refererHeader = config('app.url');
$this->client = new Client();
}

public function getRequest($url)
{
$parsedUrl = parse_url($url);

// Validate protocol
if (!in_array($parsedUrl['scheme'], $this->allowedProtocols)) {
return 'Protocol not allowed';
}

// Validate domain
if (!isset($parsedUrl['host']) || !in_array($parsedUrl['host'], $this->allowedDomains)) {
return 'Domain not allowed';
}
if (isset($parsedUrl['host']) && $parsedUrl['host'] === 'internal.finance') {
$user = Auth::user();

// Se l'utente non è loggato, o se il suo ruolo NON è admin (es. è un 'writer')
if (!$user || $user->role !== 'admin') {
// Interrompiamo immediatamente restituendo un messaggio di errore o un abort(403)
return 'Access Denied: Only administrators can query internal financial data.';
// Nota per l'esame: puoi anche usare: abort(403, 'Privilegi insufficienti');
}
}
// Aggiungi l'intestazione Referer per le richieste al server locale
$options['headers'] = ['Referer' => $this->refererHeader];

try {
$response = $this->client->request('GET', $url, $options);
return $response->getBody()->getContents();
} catch (RequestException $e) {
return 'Something went wrong: ' . $e->getMessage();
}
}

}