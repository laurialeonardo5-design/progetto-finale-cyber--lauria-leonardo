<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\HttpService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $httpService;

    public function __construct(HttpService $httpService)
    {
        $this->httpService = $httpService;
    } 

    public function dashboard(){
        $adminRequests = User::where('is_admin', NULL)->get();
        $revisorRequests = User::where('is_revisor', NULL)->get();
        $writerRequests = User::where('is_writer', NULL)->get();

        //$financialData = json_decode($this->httpService->getRequest('http://localhost:8001/financialApp/user-data.php'));
        
        try {
            // Effettua la richiesta HTTP
            $response = $this->httpService->getRequest('http://internal.finance:8001/user-data.php');
            // Controlla se la risposta è vuota o non valida
            if (empty($response)) {
                throw new Exception('La risposta dalla richiesta HTTP è vuota.');
            }
           
            // Decodifica il JSON
            $financialData = json_decode($response, true);

            // Controlla se ci sono errori nella decodifica del JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Errore nella decodifica del JSON: ' . json_last_error_msg());
            }
        
            // A questo punto, $financialData è un array associativo con i dati finanziari
            // Puoi procedere con l'elaborazione dei dati
        } catch (Exception $e) {
            // Gestisci l'eccezione
            echo 'Errore: ' . $e->getMessage();
            // Puoi anche registrare l'errore in un log file o eseguire altre azioni di recupero
        }
        
        return view('admin.dashboard', compact('adminRequests', 'revisorRequests', 'writerRequests','financialData'));
    }

   public function setAdmin(User $user) {
    $user->is_admin = true;
    $user->save();

    // 📝 LOG: Recuperiamo chi sta facendo l'operazione
    $operator = Auth::user();
    Log::info("Cambio ruolo: L'utente {$operator->name} (ID: {$operator->id}) ha promosso l'utente {$user->name} (ID: {$user->id}) ad Amministratore.", [
        'operator_id' => $operator->id,
        'target_id'   => $user->id,
        'ip_address'  => request()->ip()
    ]);

    return redirect(route('admin.dashboard'))->with('message', "$user->name is now administrator");
}

public function setRevisor(User $user) {
    $user->is_revisor = true;
    $user->save();

    // 📝 LOG
    $operator = Auth::user();
    Log::info("Cambio ruolo: L'utente {$operator->name} (ID: {$operator->id}) ha promosso l'utente {$user->name} (ID: {$user->id}) a Revisore.", [
        'operator_id' => $operator->id,
        'target_id'   => $user->id,
        'ip_address'  => request()->ip()
    ]);

    return redirect(route('admin.dashboard'))->with('message', "$user->name is now revisor");
}

public function setWriter(User $user) {
    $user->is_writer = true;
    $user->save();

    // 📝 LOG
    $operator = Auth::user();
    Log::info("Cambio ruolo: L'utente {$operator->name} (ID: {$operator->id}) ha promosso l'utente {$user->name} (ID: {$user->id}) a Scrittore.", [
        'operator_id' => $operator->id,
        'target_id'   => $user->id,
        'ip_address'  => request()->ip()
    ]);

    return redirect(route('admin.dashboard'))->with('message', "$user->name is now writer");
}

    public function editTag(Request $request, Tag $tag){
        $request->validate([
            'name' => 'required|unique:tags',
        ]);
        $tag->update([
            'name' => strtolower($request->name),
        ]);
        return redirect()->back()->with('message', 'Tag successfully updated');
    }

    public function deleteTag(Tag $tag){
        foreach($tag->articles as $article){
            $article->tags()->detach($tag);
        }
        $tag->delete();

        return redirect()->back()->with('message', 'Tag successfully deleted');
    }

    public function editCategory(Request $request, Category $category){
        $request->validate([
            'name' => 'required|unique:categories',
        ]);
        $category->update([
            'name' => strtolower($request->name),
        ]);

        return redirect()->back()->with('message', 'Category successfully updated');
    }

    public function deleteCategory(Category $category){
        $category->delete();

        return redirect()->back()->with('message', 'Category successfully deleted');
    }

    public function storeCategory(Request $request){
        $category = Category::create([
            'name' => strtolower($request->name),
        ]);
        
        return redirect()->back()->with('message', 'Category successfully created');
    }

    public function storeTag(Request $request){
        $tag = Tag::create([
            'name' => strtolower($request->name),
        ]);
        
        return redirect()->back()->with('message', 'Tag successfully created');
    }
}
