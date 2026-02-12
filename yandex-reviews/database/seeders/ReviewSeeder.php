<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::create([
            'author_name' => 'Иван Иванов',
            'rating' => 5,
            'text' => 'Отличный сервис! Всё очень понравилось.',
            'branch_name' => 'Филиал 1',
            'published_at' => now(),
            'external_id' => '12345'
        ]);
    }
}
