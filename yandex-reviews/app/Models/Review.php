<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'author_name',
        'rating',
        'text',
        'branch_name',
        'author_phone',
        'published_at',
        'external_id',
    ];

    protected $casts = [
    'published_at' => 'datetime',
];

public function user()
{
    return $this->belongsTo(User::class);
}
}
