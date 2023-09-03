<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\CarUser;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
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
        if(auth()->check()){
            $data = [];
            switch(auth()->user()->role){
                case 'admin':
                    $cars = Car::all();
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
                Car::find($request->car_id)->update(['status' => 'available']);
                break;
            case 'approved':
                $booking->update(['status' => 'accepted']);
                break;
            case 'rejected':
                Car::find($request->car_id)->update(['status' => 'available']);
                $booking->update(['status' => 'rejected']);
        }
        $booking->status = $request->status;
        $booking->save();

        return redirect(route('dashboard'));
    }
}
