<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksToUserPostsTable extends Migration
{
    public function up()
    {
        Schema::table('user_posts', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('user_posts', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
