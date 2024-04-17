<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover',
        'description',
        'language_id',
        'brief_description',
        'category_id',
        'news_id',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
