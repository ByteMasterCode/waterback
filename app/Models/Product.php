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
        'brand_id',
        'language_id',
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

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
