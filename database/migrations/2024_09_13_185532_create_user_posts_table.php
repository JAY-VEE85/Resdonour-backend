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
        Schema::create('user_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('image')->nullable();
            $table->string('title');
            $table->text('content');
            $table->text('category');
            $table->json('materials')->nullable();

            $table->string('status')->default('posted');
            $table->text('remarks')->nullable();
            $table->integer('report_count')->default(0);
            $table->json('report_reasons')->nullable();
            $table->text('report_remarks')->nullable();
            $table->json('reported_by_users')->nullable();

            $table->string('badge')->nullable();
            $table->integer('total_likes')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('user_posts');
    }
};
