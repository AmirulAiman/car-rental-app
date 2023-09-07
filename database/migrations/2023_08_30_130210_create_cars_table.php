<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id');
            $table->string('name');
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('status')->default('available');
            $table->string('deposit');
            $table->string('rental_charge');
            $table->string('rental_charge_type')->default('daily');
            $table->text('property')->nullable();
            $table->string('img_url')->default('images/cars/default.jpg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
