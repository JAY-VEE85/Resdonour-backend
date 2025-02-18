<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'user_id' => 1,
                'image' => '../storage/images/bottle-art.png',
                'title' => 'Blossom Bottle Art: Creative Ways to Reuse Plastic Bottles',
                'content' => 'Maaring gamitin ang mga plastic bottles sa paggawa ng mga creative na art projects. Ito ay isang magandang paraan upang mabawasan ang basura at makatulong sa kalikasan.',
                'category' => 'plastic',
                'status' => 'approved',
            ],
            [
                'user_id' => 2,
                'image' => '../storage/images/parol.jpg',
                'title' => 'Parol for Christmas: Recycled Materials',
                'content' => 'Tignan itong parol mula sa bote ng mountain dew, maaring gawin parol sa paparating na pasko.',
                'category' => 'plastic',
                'status' => 'approved',
            ],
            [
                'user_id' => 3,
                'image' => '../storage/images/plastic-bottles.jpg',
                'title' => 'Pag tanim sa mga plastic bottles',
                'content' => 'Maaari mong gamitin ang mga plastic bottles bilang pots para sa mga halaman. Ito ay isang magandang paraan upang mabawasan ang basura at makatulong sa kalikasan.',
                'category' => 'plastic',
                'status' => 'approved',
            ],
            [
                'user_id' => 4,
                'image' => '../storage/images/plastic-bottle-greenhouse.jpg',
                'title' => 'Composting: Turning Organic Waste into Valuable Soil',
                'content' => 'Composting is a great way to reduce organic waste and create nutrient-rich soil for your garden. Learn how to start your own compost pile.',
                'category' => 'compost',
                'status' => 'approved',
            ],
            [
                'user_id' => 5,
                'image' => 'storage/images/sustainable-fashion.jpg',
                'title' => 'Sustainable Fashion: Reuse and Recycle Clothing',
                'content' => 'Find out how sustainable fashion can help reduce waste by reusing and recycling clothing, and learn about eco-friendly fashion choices.',
                'category' => 'miscellaneous products',
                'status' => 'approved',
            ],
            [
                'user_id' => 6,
                'image' => 'storage/images/upcycling-projects.jpg',
                'title' => 'Upcycling Projects: Turning Trash into Treasure',
                'content' => 'Explore fun upcycling projects that turn everyday trash into creative and useful items, reducing waste in the process.',
                'category' => 'rubber',
                'status' => 'approved',
            ],
            [
                'user_id' => 7,
                'image' => 'storage/images/water-conservation.jpg',
                'title' => 'Reduce Water Waste: Conservation Tips',
                'content' => 'Water is a precious resource. Discover tips for reducing water waste in your home and garden to conserve this essential resource.',
                'category' => 'miscellaneous products',
                'status' => 'approved',
            ],
            [
                'user_id' => 8,
                'image' => '../storage/images/Egg-carton-lamp.jpg',
                'title' => 'Egg Carton Lamp: Creative Upcycling Project',
                'content' => 'Create a unique and eco-friendly lamp using an egg carton, maari itong gawin para mag mukhang aesthetic ang dating hindi lang sa maganda ito nakaka tulong pa tayo upang mapakinabangan ang lahat na nasa paligig natin. This upcycling project is a fun way to repurpose old materials and reduce waste.',
                'category' => 'paper',
                'status' => 'approved',
            ],
            [
                'user_id' => 9,
                'image' => '../storage/images/bottle-case.jpg',
                'title' => 'Gawing lagayan ng mga gamit ang mga plastic bottles',
                'content' => 'Maaari mong gamitin ang mga plastic bottles bilang lagayan ng mga gamit tulad ng mga screw, nail, at iba pa. Ito ay isang magandang paraan upang mabawasan ang basura at makatulong sa kalikasan.',
                'category' => 'plastic',
                'status' => 'approved',
            ],
            [
                'user_id' => 10,
                'image' => 'storage/images/recycling-centers.jpg',
                'title' => 'Recycling Centers: What You Can and Cannot Recycle',
                'content' => 'Not everything can be recycled. Learn what you can and cannot recycle at your local recycling center to reduce contamination.',
                'category' => 'miscellaneous products',
                'status' => 'approved',
            ],
        ];

        foreach ($posts as &$post) {
            // Generate random timestamps within the past year
            $randomDate = Carbon::now()->subDays(rand(1, 365))->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
            $post['created_at'] = $randomDate;
            $post['updated_at'] = $randomDate->copy()->addDays(rand(1, 30));
        }

        DB::table('user_posts')->insert($posts);
    }
}
