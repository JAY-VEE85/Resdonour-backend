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
        Schema::table('users', function (Blueprint $table) {
            $table->string('fname')->after('id');
            $table->string('lname')->after('fname');
            $table->string('phone_number')->nullable()->after('email');
            $table->string('city')->nullable()->after('phone_number');
            $table->string('barangay')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fname', 'lname', 'phone_number', 'city', 'barangay']);
        });
    }
};
