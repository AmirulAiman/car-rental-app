<?php

namespace App\Http\Controllers;

use App\Models\AppLibrary;
use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(!auth()->check()){
            return redirect(route('login'));
        }
        $isAdmin = auth()->check() ? auth()->user()->role == 'admin' : false;
        $cars = [];

        switch(auth()->user()->role){
            case 'admin':
                $cars = Car::orderBy('created_at','desc')->get();
                break;
            case 'owner':
                $cars = auth()->user()->cars;
                break;
            default:
                $cars = Car::where('status','available')->get();
                break;
        }
        $brands = AppLibrary::where('group','car_brand')->get();
        return Inertia::render('Cars/Index',[
            'cars' => $cars,
            'brands' => $brands,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!auth()->check() && auth()->user()->role == 'owner'){
            return redirect(route('login'));
        }

        $brands = AppLibrary::where('group','car_brand')->get();
        return Inertia::render('Cars/Create',[
            'brands' => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'plate_number' => 'required',
            'brand' => 'required',
            'deposit' => 'required',
            'rental_charge' => 'required',
        ]);

        $img_path = '';
        if ($request->hasFile('image')) {
            $img_path = $request->file('image')->store('image', 'images/cars');
        } else {
            $img_path = 'images/cars/default.jpg';
        }

        $car = new Car([
            'name' => $request->name,
            'plate_number' => $request->plate_number,
            'brand' => $request->brand,
            'deposit' => $request->deposit,
            'rental_charge' => $request->rent_price_amount,
            'rental_charge_type' => $request->rent_price_amount,
            'img_url' => $img_path,
        ]);
        auth()->user->save($car);
        return redirect(route('cars.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        if(!auth()->check() && auth()->user()->role == 'owner'){
            return redirect(route('login'));
        }

        $brands = AppLibrary::where('group','car_brand')->get();
        $status = AppLibrary::where('group','car_status')->get();
        return Inertia::render('Cars/Update',[
            'brands' => $brands,
            'status' => $status,
            'car' => $car
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Car $car)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->back();
    }

    public function rent(Car $car){
        if(!auth()->check()){
            return redirect(route('login'));
        }

        return Inertia::render('Cars/Book',[
            'car' => $car
        ]);
    }

    public function book(Request $request){
        if(auth()->check()){
            if(auth()->user()->role == 'customer'){

            } else {
                return redirect(route('dashboard'));
            }
        } else {
            return redirect(route('login'));
        }
    }
}
