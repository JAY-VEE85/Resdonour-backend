<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriviaSeeder extends Seeder
{
    public function run()
    {
        DB::table('trivia_questions')->insert([
            // Reduce Trivia
            [
                'category' => 'Reduce',
                'title' => 'Plastic pollution',
                'facts' => 'Every year, about 8 million tons of plastic end up in the ocean. Reducing plastic use helps protect marine life!',
                'question' => 'How much plastic waste ends up in the ocean every year?',
                'correct_answer' => '8 million tons',
                'answers' => json_encode([
                    '1 million tons',
                    '8 million tons',
                    '6 million tons',
                    '2 million tons'
                ]),
                'created_at' => '2025-03-03 04:18:09',
            ],
            [
                'category' => 'Reduce',
                'title' => 'Fast fashion waste',
                'facts' => 'The fashion industry produces 10% of global carbon emissions. Choosing sustainable clothing can help reduce this impact!',
                'question' => 'What percentage of global carbon emissions does the fashion industry produce?',
                'correct_answer' => '10%',
                'answers' => json_encode([
                    '5%',
                    '10%',
                    '25%',
                    '50%'
                ]),
                'created_at' => '2025-03-02 04:18:09',
            ],

            // Reuse Trivia
            [
                'category' => 'Reuse',
                'title' => 'Plastic bag usage',
                'facts' => 'A single plastic bag is used for an average of 12 minutes but takes over 500 years to decompose.',
                'question' => 'How long is a plastic bag typically used before being thrown away?',
                'correct_answer' => '12 minutes',
                'answers' => json_encode([
                    '5 minutes',
                    '12 minutes',
                    '1 hour',
                    '1 day'
                ]),
                'created_at' => '2025-03-01 04:18:09',
            ],

            // Recycle Trivia
            [
                'category' => 'Recycle',
                'title' => 'Paper recycling',
                'facts' => 'Recycling one ton of paper saves 17 trees, 26,000 liters of water, and 4,000 kWh of electricity.',
                'question' => 'How many trees can be saved by recycling one ton of paper?',
                'correct_answer' => '17',
                'answers' => json_encode([
                    '5',
                    '10',
                    '17',
                    '25'
                ]),
                'created_at' => '2025-02-28 04:18:09',
            ],

            // Gardening Trivia
            [
                'category' => 'Gardening',
                'title' => 'Trees and oxygen',
                'facts' => 'A single tree can provide oxygen for up to 4 people per day.',
                'question' => 'How many people can a single tree provide oxygen for daily?',
                'correct_answer' => '4',
                'answers' => json_encode([
                    '1',
                    '2',
                    '4',
                    '10'
                ]),
                'created_at' => '2025-02-27 04:18:09',
            ],
            [
                'category' => 'Gardening',
                'title' => 'Companion planting',
                'facts' => 'Basil and tomatoes grow better together because basil helps repel pests that attack tomatoes!',
                'question' => 'Which herb helps tomatoes grow better by repelling pests?',
                'correct_answer' => 'Basil',
                'answers' => json_encode([
                    'Basil',
                    'Mint',
                    'Rosemary',
                    'Cilantro'
                ]),
                'created_at' => '2025-02-26 04:18:09',
            ],
        ]);
    }
}