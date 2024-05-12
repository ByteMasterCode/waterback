<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCard extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'cashback',
        'bottles_count',
        'photo',
        'document',
        'phone_number',
        'second_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
