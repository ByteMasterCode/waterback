<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type_id', 'icon_id', 'brief_description','language_id'];

    public function type()
    {
        return $this->belongsTo(CategoryType::class, 'type_id');
    }

    public function language(){
        return $this->belongsTo(Language::class , 'language_id');
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class, 'icon_id');
    }
}
