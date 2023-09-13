<?php

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
        Schema::table('car_user', function (Blueprint $table) {
            $table->string('return_proof_of_payment')->after('proof_of_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_user', function (Blueprint $table) {
            $table->dropColumn('return_proof_of_payment');
        });
    }
};
