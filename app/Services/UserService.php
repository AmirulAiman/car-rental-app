<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Display a listing of the resource.
     */
    public function getUsers() {
        return User::orderBy('role','asc')->get();
    }

    public function getUser(User $user){
        return $user;
    }
}
