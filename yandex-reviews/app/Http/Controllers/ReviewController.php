<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ReviewController extends Controller
{
    //Список отзывов
    public function index()
{
    $reviews = auth()->user()->reviews()
        ->orderBy('published_at', 'desc')
        ->paginate(10);

    return Inertia::render('Reviews', [
        'reviews' => $reviews,
        'averageRating' => Cache::get('yandex_average_rating_' . auth()->id(), 0),
        'totalYandexReviews' => (int) Cache::get('yandex_total_count_' . auth()->id(), 0),
    ]);
}

    
}
