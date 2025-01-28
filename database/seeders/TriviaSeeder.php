<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriviaSeeder extends Seeder
{
    public function run()
    {
        DB::table('trivia_questions')->insert([
            [
                'question' => 'What do the 3Rs of sustainability stand for?',
                'correct_answer' => 'Recycle, Reuse, Reduce',
                'answers' => json_encode([
                    'Recycle, Reuse, Reduce',
                    'Reduce, Renew, Recycle',
                    'Reuse, Revise, Reduce',
                    'Recycle, Renew, Refill'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Which material takes the longest to decompose in a landfill?',
                'correct_answer' => 'Glass bottle',
                'answers' => json_encode([
                    'Plastic bottle',
                    'Glass bottle',
                    'Aluminum can',
                    'Paper'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How much energy can be saved by recycling one aluminum can?',
                'correct_answer' => 'Enough to power a TV for 3 hours',
                'answers' => json_encode([
                    'Enough to power a lightbulb for 1 hour',
                    'Enough to power a computer for 30 minutes',
                    'Enough to power a TV for 3 hours',
                    'Enough to power a fridge for a day'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What percentage of the world’s waste is recyclable?',
                'correct_answer' => '75%',
                'answers' => json_encode([
                    '50%',
                    '65%',
                    '75%',
                    '80%'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is the term for turning organic waste like food scraps into nutrient-rich soil?',
                'correct_answer' => 'Composting',
                'answers' => json_encode([
                    'Decomposition',
                    'Composting',
                    'Mulching',
                    'Recycling'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Which of the following items cannot be recycled in most curbside programs?',
                'correct_answer' => 'Pizza boxes with grease',
                'answers' => json_encode([
                    'Cardboard',
                    'Plastic bottles',
                    'Pizza boxes with grease',
                    'Aluminum cans'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is “upcycling”?',
                'correct_answer' => 'Recycling materials into higher-quality products',
                'answers' => json_encode([
                    'Recycling materials into higher-quality products',
                    'Biking uphill with sustainable materials',
                    'Using waste for energy production',
                    'Reducing the quality of materials for reuse'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Which country is known as the world leader in recycling?',
                'correct_answer' => 'Germany',
                'answers' => json_encode([
                    'Germany',
                    'Japan',
                    'Sweden',
                    'United States'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is the primary benefit of reducing waste at the source?',
                'correct_answer' => 'It reduces the amount of material that needs to be recycled or sent to landfills.',
                'answers' => json_encode([
                    'It saves time.',
                    'It eliminates the need for recycling entirely.',
                    'It reduces the amount of material that needs to be recycled or sent to landfills.',
                    'It creates new materials.'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How can individuals best contribute to a circular economy?',
                'correct_answer' => 'By buying reusable products and reducing single-use items',
                'answers' => json_encode([
                    'By throwing everything into recycling bins',
                    'By buying reusable products and reducing single-use items',
                    'By burning waste materials',
                    'By only recycling plastics'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
