<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type','language_id','icon'];

    public function categories()
    {
        return $this->hasMany(Category::class, 'type_id');
    }
    public function language(){
        return $this->belongsTo(Language::class , 'language_id');
    }
}
