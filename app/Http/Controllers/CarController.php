<?php

namespace App\Http\Controllers;

use App\Models\AppLibrary;
use App\Models\Car;
use App\Models\CarUser;
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
        $canBook = true;
        $cars = [];

        switch(auth()->user()->role){
            case 'admin':
                $cars = Car::orderBy('created_at','desc')->get();
                break;
            case 'owner':
                $cars = auth()->user()->owned;
                break;
            default:
                $canBook  = CarUser::where('user_id',auth()->id())
                    ->where('status','<>','completed')
                    ->exists() ? false : true;
                $cars = Car::where('status','available')->get();
                break;
        }
        $brands = AppLibrary::where('group','car_brand')->get();
        return Inertia::render('Cars/Index',[
            'cars' => $cars,
            'brands' => $brands,
            'isAdmin' => $isAdmin,
            'canBook' => $canBook
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
                $validated = $request->validate([
                    'car_id' => 'exists:cars,id',
                    'start_date' => 'required|date|after_or_equal:today',
                    'end_date' => 'required|date|after:today',
                ]);
                $submit_detail = array_merge($request->all(), ['user_id' => auth()->id(),'status' => 'pending_approval']);
                CarUser::create($submit_detail);
                Car::find($request->car_id)->update(['status' => 'booked']);
            } else {
                return redirect(route('dashboard'));
            }
        } else {
            return redirect(route('login'));
        }
    }

    public function payment(Request $request, CarUser $booking)
    {
        $validated = $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        
        //Process recepit
        $img_url = '';
        $img_name = 'receipt_'.time().'.'.$request->receipt->extension();
        $img_url = 'renters/'.'renter_'.$booking->id.'/'.'receipt_'.time().'.'.$request->receipt->extension();
        $request->receipt->move(public_path('images/booking/'.'request_'.$booking->id), $img_name);

        if($img_url != ''){
            //Update db.
            $booking->proof_of_payment = $img_url;
            $booking->status = 'vehicle_received';
            $booking->save();
        } else {
            return redirect(route('dashboard'))->withErrors(['message' => 'Failed to save receipt, please retry!']);
        }

        return redirect(route('dashboard'))->with('success','Receipt received, wait for further notice.');
    }
}
