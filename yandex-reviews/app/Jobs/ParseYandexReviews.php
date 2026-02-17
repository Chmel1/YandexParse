<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ParseYandexReviews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    // 1. Принимаем ID пользователя при создании задачи
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        
        $url = Cache::get("yandex_map_url_{$this->userId}");
        
        if (!$url) {
            logger("Ошибка: URL для пользователя {$this->userId} не найден.");
            return;
        }

        
        Artisan::call('parse:yandex', [
            'user_id' => $this->userId
        ]);
        
        logger("Парсинг успешно выполнен для пользователя: {$this->userId}");
    }
}