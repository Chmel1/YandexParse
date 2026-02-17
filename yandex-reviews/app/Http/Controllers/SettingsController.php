<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ParseYandexReviews;
use App\Models\Review;
class SettingsController extends Controller
{
    
      
     
    public function edit()
    {
        return Inertia::render('Settings', [
            'currentUrl' => Cache::get('yandex_map_url', ''),
            'reviews_count' => Review::count(),
        ]);
    }

    
    
    
    public function update(Request $request)
{
    $request->validate([
        'url' => [
            'required', 
            'url', 
            
            'regex:/yandex\.ru\/maps\/org\/.*\/[0-9]+\/reviews\/?/i'
        ]
    ], [
        'url.regex' => 'Ссылка должна вести прямо в раздел отзывов организации (содержать /org/ и /reviews/).'
    ]);

    $oldUrl = Cache::get('yandex_map_url');
    $newUrl = $request->url;

    if ($oldUrl !== $newUrl) {
        
        Review::truncate(); 
        
        Cache::put("yandex_map_url_" . auth()->id(), $request->url, now()->addHours(24));

        ParseYandexReviews::dispatch(auth()->id());
        return back()->with('parsing_status', 'started');
    }

    return back()->with('message', 'Ссылка обновлена. Парсинг запущен в фоновом режиме.');
}
}