<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\AppLibrary;
use App\Models\User;
use App\Models\Car;
use App\Models\CarUser;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;

class AppController extends Controller
{
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
                        ->first();
                    $history = CarUser::where('user_id',auth()->id())
                            ->with(['user','car'])
                            ->where('status','completed')
                            ->orWhere('status','booking_canceled')
                            ->orWhere('status','rejected')
                            ->get();
                    break;
            }
        } else {
            return redirect(route('login'));
        }

        return Inertia::render('Systems/Dashboard',[
            'data' => $data,
            'cars' => $cars ?? [],
            'history' => $history ?? []
        ]);
    }

    public function respond(Request $request, CarUser $booking){
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'status' => 'required',
        ]);
        switch($request->status){
            case 'booking_cancelled'||'completed':
                $car = Car::find($request->car_id)->update(['status' => 'available']);
                $user = $car->owner;
                break;
            case 'approved':
                $booking->update(['status' => 'accepted']);
                $user = $booking->user;
                break;
            case 'rejected':
                Car::find($request->car_id)->update(['status' => 'available']);
                $booking->update(['status' => 'rejected']);
                $user = $booking->user;
        }
        $booking->status = $request->status;
        $booking->save();

        return redirect(route('dashboard'));
    }

    public function sendTestEmail(Request $request){
        try {
            $users = User::where('id','<>',auth()->id())->get();
            Notification::send($users, new TestNotification());
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
            return response(['message' => 'Item stored'], 200);
        } catch (\Throwable $th) {
            return response(['message' => 'Failed', 'error' => $th->getMessage()], 400);
        }
    }
    
    public function update(Request $request, AppLibrary $library){
        try {
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
