<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\AppLibrary;
use App\Models\Car;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        #Generate Users(Customer)
        User::factory(10)->create();
        
        #Generate User(Admin)
        User::factory(1)->create([
            'name' => 'Admin',
            'email' => 'admin@car-rental.dev',
            'role' => 'admin'
        ]);

        #App library
        #User role
        AppLibrary::create(['group' => 'role', 'label' => 'Admin', 'value' => 'admin','sort_index' => 0]);
        AppLibrary::create(['group' => 'role', 'label' => 'Customer', 'value' => 'customer','sort_index' => 1]);
        AppLibrary::create(['group' => 'role', 'label' => 'Owner', 'value' => 'owner','sort_index' => 2]);
        #User status
        AppLibrary::create(['group' => 'user_status', 'label' => 'Active', 'value' => 'active','sort_index' => 0]);
        AppLibrary::create(['group' => 'user_status', 'label' => 'Terminated', 'value' => 'terminated','sort_index' => 1]);
        AppLibrary::create(['group' => 'user_status', 'label' => 'Pending', 'value' => 'pending','sort_index' => 2]);
        AppLibrary::create(['group' => 'user_status', 'label' => 'Approved', 'value' => 'approved','sort_index' => 3]);
        AppLibrary::create(['group' => 'user_status', 'label' => 'Rejected', 'value' => 'rejected','sort_index' => 4]);
        #Car Status
        AppLibrary::create(['group' => 'car_status', 'label' => 'Available', 'value' => 'available','sort_index' => 0]);
        AppLibrary::create(['group' => 'car_status', 'label' => 'Booked', 'value' => 'booked','sort_index' => 1]);
        AppLibrary::create(['group' => 'car_status', 'label' => 'Not Available', 'value' => 'not_available','sort_index' => 2]);
        #Car Brand
        AppLibrary::create(['group' => 'car_brand', 'label' => 'Proton', 'value' => 'proton','sort_index' => 1]);
        AppLibrary::create(['group' => 'car_brand', 'label' => 'Honda', 'value' => 'honda','sort_index' => 2]);
        AppLibrary::create(['group' => 'car_brand', 'label' => 'Perodua', 'value' => 'perodua','sort_index' => 3]);
        AppLibrary::create(['group' => 'car_brand', 'label' => 'Mazda', 'value' => 'mazda','sort_index' => 4]);
        #Rental Status
        AppLibrary::create(['group' => 'rental_status', 'label' => 'New', 'value' => 'new','sort_index' => 1, 'indicator' => 'rounded-md bg-gray-500 text-gray-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Pending Owner Approval', 'value' => 'pending_owner_approval','sort_index' => 2, 'indicator' => 'rounded-md bg-gray-500 text-gray-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Pending Payment (Deposit)', 'value' => 'pending_payment_deposit','sort_index' => 3, 'indicator' => 'rounded-md bg-orange-500 text-orange-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Pending Validation', 'value' => 'pending_validation','sort_index' => 4, 'indicator' => 'rounded-md bg-gray-500 text-gray-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Waiting for Vehicle', 'value' => 'waiting_vehicle','sort_index' => 5, 'indicator' => 'rounded-md bg-yellow-500 text-yellow-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Vehicle Received', 'value' => 'vehicle_received','sort_index' => 6, 'indicator' => 'rounded-md bg-green-500 text-green-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Vehicle Returned', 'value' => 'vehicle_returned','sort_index' => 7, 'indicator' => 'rounded-md bg-green-500 text-green-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Booking Rejected', 'value' => 'booking_rejected','sort_index' => 7, 'indicator' => 'rounded-md bg-red-500 text-red-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Booking cancelled', 'value' => 'booking_cancelled','sort_index' => 8, 'indicator' => 'rounded-md bg-red-500 text-red-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Pending Payment (Final)', 'value' => 'pending_payment_final','sort_index' => 9, 'indicator' => 'rounded-md bg-gray-200 text-gray-900']);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Rental Complete', 'value' => 'completed','sort_index' => 10, 'indicator' => 'rounded-md bg-green-500 text-green-900']);
        #Rental Type
        AppLibrary::create(['group' => 'rental_type', 'label' => 'Daily', 'value' => 'daily','sort_index' => 0]);
        AppLibrary::create(['group' => 'rental_type', 'label' => 'Hourly', 'value' => 'hourly','sort_index' => 1]);

        //Car Property
        AppLibrary::create(['group' => 'car_property', 'label' => 'Automatic', 'value' => 'automatic','sort_index' => 0]);
        AppLibrary::create(['group' => 'car_property', 'label' => 'Manual', 'value' => 'manual','sort_index' => 1]);
        AppLibrary::create(['group' => 'car_property', 'label' => '5 Seats', 'value' => '5_seats','sort_index' => 2]);
        AppLibrary::create(['group' => 'car_property', 'label' => '7 Seats', 'value' => '7_seats','sort_index' => 3]);
        AppLibrary::create(['group' => 'car_property', 'label' => 'Suv', 'value' => 'suv','sort_index' => 4]);
        AppLibrary::create(['group' => 'car_property', 'label' => 'Mpv', 'value' => 'mpv','sort_index' => 5]);

        #Cars
        $car = Car::create(['user_id' => rand(1,10),'name' => 'Proton Exora','plate_number' => 'ABC1234', 'brand' => 'proton', 'status' => 'available','rental_charge' => '20.00','deposit' => '200.0', 'img_url' => 'images/cars/default.jpg']);
        $car->owner()->update(['role' => 'owner']);
        $car = Car::create(['user_id' => rand(1,10),'name' => 'Proton Saga','plate_number' => 'ABC135', 'brand' => 'proton', 'status' => 'available','rental_charge' => '25.00','deposit' => '250.0', 'img_url' => 'images/cars/default.jpg']);
        $car->owner()->update(['role' => 'owner']);
        $car = Car::create(['user_id' => rand(1,10),'name' => 'Perodua Bezza','plate_number' => 'DCA987', 'brand' => 'perodua', 'status' => 'available','rental_charge' => '30.00','deposit' => '250.0', 'img_url' => 'images/cars/default.jpg']);
        $car->owner()->update(['role' => 'owner']);
        $car = Car::create(['user_id' => rand(1,10),'name' => 'Mazda CX-5','plate_number' => 'DFA468', 'brand' => 'mazda', 'status' => 'available','rental_charge' => '50.00','deposit' => '600.0', 'img_url' => 'images/cars/default.jpg']);
        $car->owner()->update(['role' => 'owner']);
        $car = Car::create(['user_id' => rand(1,10),'name' => 'Perodua Myvi','plate_number' => 'DFA345', 'brand' => 'perodua', 'status' => 'available','rental_charge' => '25.00','deposit' => '250.0', 'img_url' => 'images/cars/default.jpg']);
        $car->owner()->update(['role' => 'owner']);
    }
}
