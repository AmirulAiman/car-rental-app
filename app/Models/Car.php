<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\CarUser;

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

    public function users() {
        return $this->belongsToMany(User::class,'car_users','car_id','user_id')
            ->withPivot(['status','start_date','end_date','return_date','proof_of_payment','other_info']);
    }
}
