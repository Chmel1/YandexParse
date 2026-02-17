<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

class ParseYandexReviews extends Command
{
    protected $signature = 'parse:yandex {user_id}';
    protected $description = 'Глубокий парсинг отзывов, рейтинга и статистики из Яндекс Карт';

    public function handle()
    {
        $userId = $this->argument('user_id');
    
        
        $url = Cache::get("yandex_map_url_{$userId}"); 
        
        if (!$url) {
            $this->error("URL для пользователя {$userId} не найден в кэше.");
            return;
        }

        $this->info("Цель: " . $url);

        try {
            $this->info("Запуск Brave и подгрузка данных...");

            $browser = Browsershot::url($url)
                ->setChromePath('C:\Program Files\BraveSoftware\Brave-Browser\Application\brave.exe')
                ->showBrowser() 
                ->setOption('args', [
                    '--no-sandbox', 
                    '--disable-setuid-sandbox', 
                    '--disable-blink-features=AutomationControlled',
                ])
                ->windowSize(1920, 1080);

            $this->info("Начинаем глубокую прокрутку...");

            $jsonResult = $browser->evaluate("(() => {
                return new Promise(async (resolve) => {
                    const scrollContainer = document.querySelector('.reviews-view__content') || 
                                            document.querySelector('.scroll__container');

                    if (scrollContainer) {
                        let lastHeight = 0;
                        for (let i = 0; i < 50; i++) {
                            scrollContainer.scrollBy(0, 2500); 
                            await new Promise(r => setTimeout(r, 2000)); 
                            
                            let currentHeight = scrollContainer.scrollHeight;
                            if (currentHeight === lastHeight) {
                                await new Promise(r => setTimeout(r, 2000));
                                if (scrollContainer.scrollHeight === lastHeight) break;
                            }
                            lastHeight = currentHeight;
                        }
                    }

                    document.querySelectorAll('.business-review-view__expand').forEach(btn => btn.click());

                    resolve(JSON.stringify({
                        html: document.documentElement.outerHTML
                    }));
                });
            })()");

            $payload = json_decode($jsonResult, true);
            $crawler = new Crawler($payload['html']);

            


$averageRating = 0;
$ratingContainer = $crawler->filter('.business-summary-rating-badge-view__rating');

if ($ratingContainer->count()) {
    $ratingParts = $ratingContainer->filter('[class*="rating-text"]')->each(function (Crawler $node) {
        return trim($node->text());
    });
    
    $cleanParts = array_filter($ratingParts, fn($val) => is_numeric($val));
    $averageRating = (float) implode('.', $cleanParts);
}


$totalReviewsCount = 0;


$countNode = $crawler->filter('.business-rating-amount-view');

if ($countNode->count()) {
    $totalReviewsCount = (int) preg_replace('/[^0-9]/', '', $countNode->text());
} 


if ($totalReviewsCount === 0) {
    $crawler->filter('.business-header-rating-view__count, .business-summary-rating-badge-view__rating-count')->each(function (Crawler $node) use (&$totalReviewsCount) {
        $text = $node->text();
        if (preg_match('/(\d+)\s+(оцен|отзыв)/u', str_replace(' ', '', $text), $matches)) {
            $totalReviewsCount = (int) $matches[1];
        }
    });
}


Cache::forever("yandex_average_rating_{$userId}", $averageRating);
Cache::forever("yandex_total_count_{$userId}", $totalReviewsCount);

$this->info("РЕЗУЛЬТАТ: Рейтинг $averageRating | Всего оценок: $totalReviewsCount");

            
            $branchName = 'Предприятие';
            $nameSelectors = ['h1[itemprop="name"]', '.card-title-view__title-link', '.orgpage-header-view__header'];
            foreach ($nameSelectors as $sel) {
                if ($crawler->filter($sel)->count()) {
                    $branchName = trim($crawler->filter($sel)->text());
                    break;
                }
            }

            $this->info("Организация: $branchName | Рейтинг: $averageRating | Всего отзывов: $totalReviewsCount");

            
            $reviewNodes = $crawler->filter('.business-review-view');
            $totalParsed = 0;

            $reviewNodes->each(function (Crawler $node) use ($branchName, &$totalParsed, $userId) {
                try {
                    $author = $node->filter('[itemprop="name"]')->count() ? $node->filter('[itemprop="name"]')->text() : 'Аноним';
                    $text = $node->filter('[itemprop="reviewBody"]')->count() ? $node->filter('[itemprop="reviewBody"]')->text() : '';
                    $text = trim(str_replace(['Ещё', 'Читать дальше'], '', $text));

                    $rating = $node->filter('meta[itemprop="ratingValue"]')->count() 
                        ? $node->filter('meta[itemprop="ratingValue"]')->attr('content') : 5;

                    $dateNode = $node->filter('meta[itemprop="datePublished"]');
                    if ($dateNode->count()) {
                        $rawDate = $dateNode->attr('content'); 
                        $publishedAt = Carbon::parse($rawDate)->toDateTimeString(); 
                    } else {
                        $publishedAt = now()->toDateTimeString();
                    }

                    if (!empty($text)) {
                        Review::updateOrCreate(
                            ['external_id' => 'yandex_' . md5($author . $text . $userId)],
                            [
                                'user_id' => $userId,
                                'author_name' => trim($author),
                                'text' => $text,
                                'rating' => (int)$rating,
                                'published_at' => $publishedAt,
                                'branch_name' => $branchName,
                            ]
                        );
                        $totalParsed++;
                        $this->line("✅ [$publishedAt] $author");
                    }
                } catch (\Exception $e) {
                    $this->error("Ошибка сохранения отзыва: " . $e->getMessage());
                 }
            });

            $this->info("Готово! В базу попало: $totalParsed из $totalReviewsCount");

        } catch (\Exception $e) {
            $this->error("Критическая ошибка: " . $e->getMessage());
        }
    }
}