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

        // "user_id": 3,
        // "image": "http://127.0.0.1:8000/storage/storage/images/sustainable-fashion.jpg",
        // "title": "Sustainable Fashion: Reuse and Recycle Clothing",
        // "content": "Find out how sustainable fashion can help reduce waste by reusing and recycling clothing, and learn about eco-friendly fashion choices.",
        // "category": "Reuse",
        // 'materials' => json_encode(['Plastic']),
        // "status": "posted",
        // "total_likes": 0,
        // 'report_count' => 0,
        // "liked_by_user": false,
        // "created_at": "2025-05-26 21:36:08"
        // 'report_reasons' => json_encode([]),
        // 'report_remarks' => null,

            [
                'user_id' => 1,
                'image' => 'images/bottle-art.png',
                'title' => 'Blossom Bottle Art: Creative Ways to Reuse Plastic Bottles',
                'content' => 'Maaring gamitin ang mga plastic bottles sa paggawa ng mga creative na art projects. Ito ay isang magandang paraan upang mabawasan ang basura at makatulong sa kalikasan.',
                'category' => 'Reuse',
                'materials' => json_encode(['Plastic']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 12,
                // done
            ],
            [
                'user_id' => 2,
                'image' => '../storage/images/parol.jpg',
                'title' => 'Parol for Christmas: Recycled Materials',
                'content' => 'Tignan itong parol mula sa bote ng mountain dew, maaring gawin parol sa paparating na pasko.',
                'category' => 'Reuse',
                'materials' => json_encode(['Plastic', 'Mixed Waste']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 9,
                // done
            ],
            [
                'user_id' => 3,
                'image' => '../storage/images/plastic-bottles.jpg',
                'title' => 'Pag tanim sa mga plastic bottles',
                'content' => 'Maaari mong gamitin ang mga plastic bottles bilang pots para sa mga halaman. Ito ay isang magandang paraan upang mabawasan ang basura at makatulong sa kalikasan.',
                'category' => 'Gardening',
                'materials' => json_encode(['Plastic']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 5,
                // done
            ],
            [
                'user_id' => 3,
                'image' => '../storage/images/plastic-bottle-greenhouse.jpg',
                'title' => 'Composting: Turning Organic Waste into Valuable Soil',
                'content' => 'Composting is a great way to reduce organic waste and create nutrient-rich soil for your garden. Learn how to start your own compost pile.',
                'category' => 'Gardening',
                'materials' => json_encode(['Compost', 'Mixed Waste']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 18,
                // done
            ],
            [
                'user_id' => 3,
                'image' => 'images/ISBSSJyYEexA5Zs1bGYBexpUZZJAyDQdOthm7aug.jpg',
                'title' => 'Sustainable Fashion: Reuse and Recycle Clothing',
                'content' => 'Find out how sustainable fashion can help reduce waste by reusing and recycling clothing, and learn about eco-friendly fashion choices.',
                'category' => 'Reuse',
                'materials' => json_encode(['Cloth']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 2,
                // done
            ],
            [
                'user_id' => 3,
                'image' => '../storage/images/jJlnLJIpbMt1WCvQG5oiSHYr2qGtCHA6ldNDBsaK.jpg',
                'title' => 'Upcycling Projects: Turning Trash into Treasure',
                'content' => 'Explore fun upcycling projects that turn everyday trash into creative and useful items, reducing waste in the process.
                
                https://youtu.be/hH_pjQGSees?si=EH0Wjb6Qrvp8bOdu',
                'category' => 'Reuse',
                'materials' => json_encode(['Rubber', 'Wood']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 5,
                // done
            ],
            [
                'user_id' => 3,
                'image' => 'storage/images/bXRPxq4OTu5pKPC4s6TjLgwO1C3p2hPLBXU6ZQrj.jpg',
                'title' => 'Reduce Water Waste: Conservation Tips',
                'content' => 'Water is a precious resource. Discover tips for reducing water waste in your home and garden to conserve this essential resource.
                
                https://youtu.be/5J2Z3XDUw4o?si=BFiYxqr1qeoTDUFb',
                'category' => 'Reduce',
                'materials' => json_encode(['Tips & Tricks']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 1,
                // done
            ],
            [
                'user_id' => 8,
                'image' => '../storage/images/Egg-carton-lamp.jpg',
                'title' => 'Egg Carton Lamp: Creative Upcycling Project',
                'content' => 'Create a unique and eco-friendly lamp using an egg carton, maari itong gawin para mag mukhang aesthetic ang dating hindi lang sa maganda ito nakaka tulong pa tayo upang mapakinabangan ang lahat na nasa paligig natin. This upcycling project is a fun way to repurpose old materials and reduce waste.
                
                https://youtu.be/IaGveGwL3eQ?si=aV8fqOFpwwgdYFaD',
                'category' => 'Reuse',
                'materials' => json_encode(['Paper', 'Boxes']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 20,
            ],
            [
                'user_id' => 9,
                'image' => '../storage/images/bottle-case.jpg',
                'title' => 'Gawing lagayan ng mga gamit ang mga plastic bottles',
                'content' => 'Maaari mong gamitin ang mga plastic bottles bilang lagayan ng mga gamit tulad ng mga screw, nail, at iba pa. Ito ay isang magandang paraan upang mabawasan ang basura at makatulong sa kalikasan.',
                'category' => 'Reuse',
                'materials' => json_encode(['Plastic']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 15,
            ],
            [
                'user_id' => 10,
                'image' => '../storage/app/public/images/recycling-centers.jpg',
                'title' => 'Recycling Centers: What You Can and Cannot Recycle',
                'content' => 'Not everything can be recycled. Learn what you can and cannot recycle at your local recycling center to reduce contamination.',
                'category' => 'Recycle',
                'materials' => json_encode(['Miscellaneous Products']),
                'status' => 'posted',
                'report_count' => 0,
                'report_reasons' => json_encode([]),
                'report_remarks' => null,
                'total_likes' => 3,
            ],


            // reported posts seeder
            [
                'user_id' => 3,
                'image' => '../storage/images/reported1.jpg',
                'title' => 'Offensive Post Example',
                'content' => 'This post contains inappropriate content and has been reported multiple times.',
                'category' => 'Reduce',
                'materials' => json_encode(['Miscellaneous Products']),
                'status' => 'reported',
                'report_count' => 5,
                'report_reasons' => json_encode(['Inappropriate Content', 'Spam']),
                'report_remarks' => 'Pending admin review.',
                'total_likes' => 0,
            ],
            [
                'user_id' => 3,
                'image' => '../storage/images/reported2.jpg',
                'title' => 'Fake News Alert',
                'content' => 'This post has been reported for spreading misinformation.',
                'category' => 'Reuse',
                'materials' => json_encode(['Miscellaneous Products']),
                'status' => 'reported',
                'report_count' => 8,
                'report_reasons' => json_encode(['Misinformation', 'Fake News']),
                'report_remarks' => 'Review needed by fact-check team.',
                'total_likes' => 0,
            ],
            [
                'user_id' => 6,
                'image' => '../storage/images/reported3.jpg',
                'title' => 'Spam Advertisement',
                'content' => 'This post is an unsolicited advertisement and has been flagged by users.',
                'category' => 'Recycle',
                'materials' => json_encode(['Miscellaneous Products']),
                'status' => 'reported',
                'report_count' => 9,
                'report_reasons' => json_encode(['Spam', 'Unwanted Advertisement']),
                'report_remarks' => 'Pending admin removal.',
                'total_likes' => 0,
            ],
        ];

        foreach ($posts as &$post) {
            // Generate a random date starting from January 1st of the current year
            $randomDate = Carbon::createFromDate(now()->year, 1, 1)
                ->addDays(rand(0, now()->dayOfYear - 1)) // Ensures it stays within this year
                ->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
        
            $post['created_at'] = $randomDate;
            $post['updated_at'] = $randomDate->copy()->addDays(rand(1, 30))->min(now()); // Ensures it doesn't go beyond today
        }

        DB::table('user_posts')->insert($posts);
    }
}
