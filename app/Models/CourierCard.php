<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCard extends Model
{
    use HasFactory;
    /**
     * Атрибуты, которые могут быть присвоены массово.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'current_orders',
        'completed_orders',
        'product_count',
        'car_number',
        'photo',
        'document',
        'cash',
        'status',
        'rating',
    ];

    /**
     * Преобразует атрибуты JSON в массивы.
     *
     * @var array
     */
    protected $casts = [
        'current_orders' => 'array',
        'completed_orders' => 'array',
        'rating' => 'array',
    ];

    /**
     * Получить пользователя, которому принадлежит данная карта курьера.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
