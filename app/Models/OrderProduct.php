<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'count',
        'serial',
    ];

    // Определяем отношение с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Определяем отношение с продуктом
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
