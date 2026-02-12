<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    //Список отзывов
    public function index(){
        return Review::orderBy('published_at','desc')->paginate(10);
    }

    public function storeSettings(Request $request){
        $request->validate([
            'url'=>'required|url|contains:yandex.ru'
        ]);

        cache(['yandex_map_url' =>$request->url], now()->addYears(1));
        return response()->json(['message'=>'Ссылка сохранена']);
    }
}
