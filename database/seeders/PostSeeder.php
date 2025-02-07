<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_posts')->insert([
            [
                'user_id' => 2,
                'image' => 'reduce-waste.jpg',
                'title' => 'Reduce Waste: How to Minimize Trash',
                'content' => 'Learn practical ways to reduce waste in your daily life by minimizing the use of non-recyclable materials and cutting down on disposable products.',
                'category' => 'paper',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, 
                'image' => 'reuse-containers.jpg',
                'title' => 'Reuse Containers: Creative Ways to Reuse Household Items',
                'content' => 'Discover creative ideas for reusing containers and household items instead of throwing them away. This helps reduce waste and saves money.',
                'category' => 'plastic',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'recycle-plastics.jpg',
                'title' => 'Recycle Plastics: The Importance of Proper Sorting',
                'content' => 'Proper recycling of plastics is essential for reducing pollution. Learn how to sort plastics and ensure they get recycled properly.',
                'category' => 'plastic',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'composting-organic-waste.jpg',
                'title' => 'Composting: Turning Organic Waste into Valuable Soil',
                'content' => 'Composting is a great way to reduce organic waste and create nutrient-rich soil for your garden. Learn how to start your own compost pile.',
                'category' => 'compost',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'sustainable-fashion.jpg',
                'title' => 'Sustainable Fashion: Reuse and Recycle Clothing',
                'content' => 'Find out how sustainable fashion can help reduce waste by reusing and recycling clothing, and learn about eco-friendly fashion choices.',
                'category' => 'miscellaneous',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'upcycling-projects.jpg',
                'title' => 'Upcycling Projects: Turning Trash into Treasure',
                'content' => 'Explore fun upcycling projects that turn everyday trash into creative and useful items, reducing waste in the process.',
                'category' => 'rubber',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'water-conservation.jpg',
                'title' => 'Reduce Water Waste: Conservation Tips',
                'content' => 'Water is a precious resource. Discover tips for reducing water waste in your home and garden to conserve this essential resource.',
                'category' => 'miscellaneous',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'recycle-paper.jpg',
                'title' => 'Recycle Paper: Simple Steps for a Greener Planet',
                'content' => 'Recycling paper is one of the easiest ways to help the environment. Learn the simple steps you can take to recycle paper effectively.',
                'category' => 'paper',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'eco-friendly-products.jpg',
                'title' => 'Reduce and Reuse: Eco-Friendly Products to Try',
                'content' => 'Switching to eco-friendly products can help reduce waste and encourage reuse. Check out our list of sustainable products worth trying.',
                'category' => 'plastic',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'image' => 'recycling-centers.jpg',
                'title' => 'Recycling Centers: What You Can and Cannot Recycle',
                'content' => 'Not everything can be recycled. Learn what you can and cannot recycle at your local recycling center to reduce contamination.',
                'category' => 'miscellaneous',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
