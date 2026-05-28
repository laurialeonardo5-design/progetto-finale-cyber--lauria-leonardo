<?php

namespace App\Livewire;

use App\Services\HttpService;
// use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class LatestNews extends Component
{
public $selectedApi;
public $news;
protected $httpService;

public function __construct()
{
$this->httpService = app(HttpService::class);
}

public function fetchNews()
{
    // 1. Mappa delle API permesse (Whitelist)
    $allowedApis = [
        'news_it' => 'https://newsapi.org/v2/top-headlines?country=it',
        'news_uk' => 'https://newsapi.org/v2/top-headlines?country=uk',
        'news_us' => 'https://newsapi.org/v2/top-headlines?country=us',
    ];

    // 2. MITIGAZIONE SSRF: Se la chiave non esiste, blocca subito l'esecuzione
    if (!array_key_exists($this->selectedApi, $allowedApis)) {
        abort(403, 'Tentativo di SSRF rilevato e bloccato!');
    }

    // 3. RECUPERO SICURO: Estraiamo l'URL vero e blindato dalla whitelist
    $targetUrl = $allowedApis[$this->selectedApi];

    // 4. CONTROLLO ROBUSTEZZA (Ottenuto sul $targetUrl, NON su selectedApi)
    if (filter_var($targetUrl, FILTER_VALIDATE_URL) === FALSE) {
        $this->news = 'Invalid URL';
        return;
    }
    
    // 5. ESECUZIONE: Chiamata HTTP eseguita solo sull'URL sicuro della whitelist
    $responseBody = $this->httpService->getRequest($targetUrl);
    $this->news = json_decode($responseBody, true);
}

public function render()
{
return view('livewire.latest-news');
}

}