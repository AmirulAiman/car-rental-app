<?php

namespace App\Services;

use App\Models\Car;
use App\Models\AppLibrary;
class BookingService
{
    /**
     * Display a listing of the resource.
     */
    public function getCars() {
        return Car::where('status','available')->get();
    }

    public function getCar(Car $car){
        return $car;
    }

    /**
     * Return the next status for booking process
     * @param $currentStatus: string => current booking status
     * @param $approved: bool => in case the status depends on the user decision
     * @return AppLibrary => return next booking status, from APpLibrar model
     */
    public function getNextBookingStatus(String $currentStatus, $approved = true): AppLibrary
    {
        switch($currentStatus){
            case 'pending_owner_approval':
                if($approved){
                    return AppLibrary::where('group','rental_status')
                        ->where('value','pending_payment_deposit')->first();
                }
                return AppLibrary::where('group','rental_status')
                        ->where('value','booking_rejected')->first();
                break;
            case 'pending_payment_deposit':
                return AppLibrary::where('group','rental_status')
                        ->where('value','pending_validation')->first();
                break;
            case 'pending_validation':
                if($approved){
                    return AppLibrary::where('group','rental_status')
                        ->where('value','waiting_vehicle')->first();
                }
                return AppLibrary::where('group','rental_status')
                        ->where('value','booking_rejected')->first();
                break;
            case 'waiting_vehicle':
                return AppLibrary::where('group','rental_status')
                        ->where('value','vehicle_received')->first();
                break;
            case 'vehicle_received':
                return AppLibrary::where('group','rental_status')
                        ->where('value','vehicle_returned')->first();
                break;
            case 'vehicle_returned':
                return AppLibrary::where('group','rental_status')
                        ->where('value','pending_payment_final')->first();
                break;
            case 'pending_payment_final':
                if($approved){
                    return AppLibrary::where('group', 'rental_status')
                    ->where('value', 'completed')->first();
                }
                break;
            default:
                return AppLibrary::where('group','rental_status')
                    ->where('value','completed')->first();
                break;
        }
    }
}
