<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('trivia_questions', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // (Reduce, Reuse, Recycle, Gardening)
            $table->string('title');
            $table->text('facts'); // the trivia mismo
            $table->string('question'); // about trivia
            $table->string('correct_answer');
            $table->json('answers');
            $table->integer('correct_count')->default(0); // Count of correct answers
            $table->integer('wrong_count')->default(0); // Count of wrong answers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trivia_questions');
    }
};
