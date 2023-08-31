<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    public function dashboard()
    {
        $isAdmin = auth()->check() ? auth()->user()->role == 'admin' : false;
        $isOwner = auth()->check() ? auth()->user()->role == 'owner' : false;
        $isCust = auth()->check() ? auth()->user()->role == 'customer' : false;
        return Inertia::render('Systems/Dashboard',[
            'isAdmin' => $isAdmin,
            'isOwner' => $isOwner,
            'isCust' => $isCust,
        ]);
    }
}
