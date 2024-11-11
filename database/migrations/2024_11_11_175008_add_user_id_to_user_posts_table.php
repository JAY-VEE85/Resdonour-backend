<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
        {
            Schema::table('user_posts', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->after('id');  // Add user_id column after the 'id' field
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');  // Foreign key constraint
            });
        }

    public function down()
        {
            Schema::table('user_posts', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
};
