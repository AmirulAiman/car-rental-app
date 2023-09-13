<?php

namespace App\Http\Controllers;

use App\Mail\NewBookingCustomer;
use App\Mail\NewBookingOwner;
use App\Models\AppLibrary;
use App\Models\User;
use App\Models\Car;
use App\Models\CarUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Str;

use App\Services\BookingService;

class CarController extends Controller
{
    private $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $isAdmin = auth()->check() ? auth()->user()->role == 'admin' : false;
        $canBook = true;
        $cars = [];

        if(auth()->check()){
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
        } else {
            $cars = Car::orderBy('created_at','desc')->get();
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
        $properties = AppLibrary::where('group','car_property')->get();
        $status = AppLibrary::where('group','car_status')->get();
        $rental_type = AppLibrary::where('group','rental_type')->get();

        return Inertia::render('Cars/Create',[
            'brands' => $brands,
            'properties' => $properties,
            'statuses' => $status,
            'rental_type' => $rental_type,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'plate_number' => 'required|unique:cars,plate_number',
                'brand' => 'required',
                'deposit' => 'required',
                'rental_charge' => 'required',
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $img_url = '';
            if ($request->hasFile('img')) {
                $img_name = 'car_'.Str::lower(Str::replace($request->plate_number,' ','_')).'.'.$request->img->extension();
                $img_url = 'images/cars/'.'owner_'.auth()->id().'/'.$img_name;
                $request->img->move(public_path('images/cars'), $img_name);
            } else {
                $img_url = 'images/cars/default.jpg';
            }
    
            $car = new Car([
                'name' => $request->name,
                'plate_number' => $request->plate_number,
                'brand' => $request->brand,
                'deposit' => $request->deposit,
                'rental_charge' => $request->rental_charge,
                'rental_charge_type' => $request->rental_charge_type,
                'property' => json_encode($request->property),
                'img_url' => $img_url,
            ]);
            $user = User::find(auth()->id());
            $user->owned()->save($car);
            return to_route('cars.index')->with(['msg'=>'Car registered']);
        } catch (\Throwable $th) {
            return to_route('cars.create')->with(['msg' => $th->getMessage()]);
        }
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

        $car->property = json_decode($car->property);
        $brands = AppLibrary::where('group','car_brand')->get();
        $properties = AppLibrary::where('group','car_property')->get();
        $status = AppLibrary::where('group','car_status')->get();
        $rental_type = AppLibrary::where('group','rental_type')->get();

        return Inertia::render('Cars/Update',[
             'brands' => $brands,
            'properties' => $properties,
            'statuses' => $status,
            'rental_type' => $rental_type,
            'car' => $car
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Car $car)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'plate_number' => 'required|unique:cars,plate_number',
                'brand' => 'required',
                'deposit' => 'required',
                'rental_charge' => 'required',
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $img_url = '';
            if ($request->hasFile('img')) {
                $img_name = 'car_'.Str::lower(Str::replace($request->plate_number,' ','_')).'.'.$request->img->extension();
                $img_url = 'cars/'.'owner_'.auth()->id().'/'.$img_name;
                $request->img->move(public_path('images/cars'), $img_name);
            } else {
                $img_url = 'images/cars/default.jpg';
            }

            $car->update([
                    'name' => $request->name,
                    'plate_number' => $request->plate_number,
                    'brand' => $request->brand,
                    'deposit' => $request->deposit,
                    'rental_charge' => $request->rental_charge,
                    'rental_charge_type' => $request->rental_charge_type,
                    'status' => $request->status,
                    'property' => json_encode($request->property),
                    'img_url' => $img_url,
                ]);
            return to_route('cars.edit', $car->id)->with(['msg' => 'Update success']);
        } catch (\Throwable $th) {
            return to_route('cars.edit', $car->id)->with(['msg' => $th->getMessage()]);
        }
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
                $submit_detail = array_merge($request->all(), ['user_id' => auth()->id(),'status' => 'pending_owner_approval']);
                $booking = CarUser::create($submit_detail);
                $car = Car::find($request->car_id);
                $car->update(['status' => 'booked']);
                Mail::to(auth()->user())->send(new NewBookingCustomer($booking));
                Mail::to($car->owner)->send(new NewBookingOwner($booking));
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
        $img_url = 'booking/'.'booking_'.$booking->id.'/'.'receipt_'.time().'.'.$request->receipt->extension();
        $request->receipt->move(public_path('images/booking/'.'booking_'.$booking->id), $img_name);

        if($img_url != ''){
            //Update db.
            if($booking->status != 'pending_payment_final'){
                $booking->proof_of_payment = $img_url;
                $status = $this->bookingService->getNextBookingStatus('pending_payment_deposit');
                $booking->status = $status->value;
                $booking->save();
            } else {
                $booking->return_proof_of_payment = $img_url;
                $status = $this->bookingService->getNextBookingStatus('pending_payment_final');
                $booking->status = $status->value;
                $booking->save();
            }
        } else {
            return to_route('dashboard');
        }
        #$car = Car::find($booking->car_id)->first();
        
        return to_route('dashboard');
    }
}
