<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
   {
       Schema::table('announcements', function (Blueprint $table) {
           $table->dropColumn('date');
       });
   }

   /**
    * Reverse the migrations.
    */
   public function down()
   {
       Schema::table('announcements', function (Blueprint $table) {
           $table->date('date')->nullable(); // Add the column back if the migration is rolled back
       });
   }
};
