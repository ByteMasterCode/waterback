<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',

        'language_id',
        'categories_id',
        'isCashback',
        'cover',
        'description',
        'brief_description',
    ];

    protected $casts = [
        'isSale' => 'boolean',
        'isCashback' => 'boolean',
        'topicons' => 'array',
        'cover' => 'array',
    ];


    public function categories(){
        return $this->belongsTo(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
