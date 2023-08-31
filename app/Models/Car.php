<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'plate_number',
        'brand',
        'status',
        'deposit',
        'rental_charge',
        'rental_charge_type',
        'property',
        'img_url'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
