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
        'isSale',
        'topicons',
        'brands_id',
        'language_id',
        'categories_id',
        'isCashback',
        'cashback_price',
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

    public function brands()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(){
        return $this->belongsTo(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
