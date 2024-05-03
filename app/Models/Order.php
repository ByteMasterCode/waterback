<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'courier_id',
        'address',
        'location',
        'accept_date',
        'delivered_date',
        'isDenied',
        'description',
        'status',
        'total',
        'cashback',
    ];

    // Определяем отношение с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'courier');
        });
    }
}
