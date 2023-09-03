<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Car;

class CarUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'return_date',
        'status',
        'proof_of_payment',
        'other_info'
    ];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function car(){
        return $this->hasOne(Car::class,'id','car_id');
    }
}
