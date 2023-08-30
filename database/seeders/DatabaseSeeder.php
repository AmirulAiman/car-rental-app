<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\AppLibrary;
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
        #Rental Status
        AppLibrary::create(['group' => 'rental_status', 'label' => 'New', 'value' => 'new','sort_index' => 1]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Accepted', 'value' => 'accepted','sort_index' => 2]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Rejected', 'value' => 'rejected','sort_index' => 3]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Pending Proof of Payment', 'value' => 'pending_proof_of_payment','sort_index' => 4]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Booking Approved', 'value' => 'approved','sort_index' => 5]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Booking Rejected', 'value' => 'rejected','sort_index' => 6]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Booking Canceled', 'value' => 'canceled','sort_index' => 7]);
        AppLibrary::create(['group' => 'rental_status', 'label' => 'Finished', 'value' => 'finished','sort_index' => 7]);
    }
}
