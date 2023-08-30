<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppLibrary extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'label',
        'value',
        'indicator',
        'sort_index',
    ];
}
