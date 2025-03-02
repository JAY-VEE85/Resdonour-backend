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
        Schema::create('landing_photos', function (Blueprint $table) {
            $table->id();
            $table->string('image1')->nullable();
            $table->string('content1')->nullable();
            $table->string('image2')->nullable();
            $table->string('content2')->nullable();
            $table->string('image3')->nullable();
            $table->string('content3')->nullable();
            $table->string('image4')->nullable();
            $table->string('content4')->nullable();
            $table->string('image5')->nullable();
            $table->string('content5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_photos');
    }
};
