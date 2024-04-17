<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryManager extends Model
{
    use HasFactory;
    protected $table = 'history_manager';

    protected $fillable = ['actions', 'description'];
}
