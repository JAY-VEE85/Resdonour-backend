<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Add the 'badges' column if it does not exist yet
        Schema::table('users', function (Blueprint $table) {
            $table->json('badges')->nullable()->default(json_encode([]));
        });

        // Add the "top poster" badge to users who are top posters without affecting existing badges.
        // This can be done after the column is added.
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $badges = json_decode($user->badges, true) ?? [];

            // Ensure "top poster" badge is added only if it's not already there
            if (!in_array('top poster', $badges)) {
                $badges[] = 'top poster';  // Add the top poster badge
            }

            // Update the badges column for each user
            DB::table('users')
                ->where('id', $user->id)
                ->update(['badges' => json_encode($badges)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Optionally, you could drop the badges column in the down method
            $table->dropColumn('badges');
        });
    }
};

