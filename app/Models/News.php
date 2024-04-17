<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $fillable = ['cover', 'description', 'language_id', 'brief_description', 'type'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
