<?php

namespace App\Http;

use App\Models\AppLibrary;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppService{

    public function getLibraries()
    {
        return AppLibrary::all();
    }

    public function getLibrary(AppLibrary $item)
    {
        return $item;
    }

    public function createNew(Request $request)
    {
        return AppLibrary::create($request->all());
    }

    public function updateItem(Request $request, AppLibrary $item)
    {
        return $item->update($request->all());
    }
}