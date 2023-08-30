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
        Schema::create('app_libraries', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->string('indicator')->nullable();
            $table->integer('sort_index')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_libraries');
    }
};
