<?php

namespace App\Http\Controllers;

use App\Mail\Customers\BookingStatusCustomer;
use App\Mail\Owners\BookingStatusOwner;
use App\Mail\TestMail;
use App\Models\AppLibrary;
use App\Models\User;
use App\Models\Car;
use App\Models\CarUser;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

use App\Services\BookingService;

class AppController extends Controller
{
    private $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function dashboard()
    {
        if(auth()->check()){
            $data = [];
            switch(auth()->user()->role){
                case 'admin':
                    $cars = Car::all();
                    $data = User::all();
                    break;
                case 'owner':
                    $data = CarUser::with(['user','car','car.owner'])
                        ->get();
                    break;
                default:
                    $data = CarUser::where('user_id',auth()->id())
                        ->with(['user','car'])
                        ->where('status','<>','completed')
                        ->where('status','<>','booking_cancelled')
                        ->where('status','<>','booking_rejected')
                        ->first();
                    $history = CarUser::where('user_id',auth()->id())
                            ->with(['user','car'])
                            ->where('status','completed')
                            ->orWhere('status','booking_cancelled')
                            ->orWhere('status','booking_rejected')
                            ->get();
                    break;
            }
        } else {
            return redirect(route('login'));
        }
        $statuses = AppLibrary::where('group','rental_status')->get();
        return Inertia::render('Systems/Dashboard',[
            'data' => $data,
            'cars' => $cars ?? [],
            'history' => $history ?? [],
            'statuses' => $statuses ?? []
        ]);
    }

    public function respond(Request $request, CarUser $booking){
        try {
            $validated = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'status' => 'required'
            ]);
            $status = $this->bookingService->getNextBookingStatus($request->status, $request->approved, $booking);
            if($status->value == 'pending_payment_final'){
                $booking->return_date = date('Y-m-d');
            }
            $booking->status = $status->value;
            $booking->save();
            if(env('EMAIL_NOTIFICATION', false)){
                if($request->approved){
                    Mail::to($booking->user->email)->send(new BookingStatusCustomer($booking,$request->approved));
                } else {
                    Mail::to($booking->user->email)->send(new BookingStatusCustomer($booking));
                }
                Mail::to($booking->car->owner->email)->send(new BookingStatusOwner($booking));
            }
            return response(['data' => 'success', 200]);
        } catch (\Throwable $th) {
            return response(['data' => $th->getMessage(), 500]);
        }
    }

    public function sendTestEmail(Request $request){
        try {
            $users = User::where('id','<>',auth()->id())->get();
            if(env('EMAIL_NOTIFICATION', false)){
                Notification::send($users, new TestNotification());
            } else {
                return response(['message' => 'EMAIL_NOTIFICATION not allowed in .env'], 200);
            }
            return response(['message' => 'testing...'],200);
        } catch (\Throwable $th) {
            return response($th->getMessage(),400);
        }
    }

    public function index(){
        try {
            $libraries = AppLibrary::all();
            $groups = DB::table('app_libraries')->select('group')->distinct()->get();

            return Inertia::render('Systems/App/Index',[
                'data' => $libraries,
                'groups' => $groups 
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }

    public function show(AppLibrary $library)
    {
        try {
            return response(['item' => $library], 200);
        } catch (\Throwable $th) {
            return response(['message' => 'Failed', 'error' => $th->getMessage()], 400);
        }
    }
    
    
    public function store(Request $request){
        try {
            $validated = $request->validate([
                'group' => 'required',
                'label' => 'required',
                'value' => 'required',
                'sort_index' => 'numeric'
            ]);
            AppLibrary::create($request->except('id'));
            return to_route('app.index');
        } catch (\Throwable $th) {
            return to_route('app.index')->with('error', $th->getMessage());
        }
    }
    
    public function update(Request $request, AppLibrary $library){
        try {
            $validated = $request->validate([
                'group' => 'required',
                'label' => 'required',
                'value' => 'required'
            ]);
            $library->update($request->except('id'));
            return response(['message' => 'Item updated'], 200);
        } catch (\Throwable $th) {
            return response(['message' => 'Failed', 'error' => $th->getMessage()], 400);
        }
    }

    public function destroy(AppLibrary $library)
    {
        try {
            $library->delete();
            return response(['message' => 'Item deleted'], 200);
        } catch (\Throwable $th) {
            return response(['message' => 'Delete failed', 'error' => $th->getMessage()], 400);
        }
    }
}
