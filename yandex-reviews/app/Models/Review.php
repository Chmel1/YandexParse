<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'author_name',
        'rating',
        'text',
        'branch_name',
        'author_phone',
        'published_at',
        'external_id',
    ];
}
