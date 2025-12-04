<?php

namespace Database\Seeders;

use App\Modules\Blog\Models\Comment;
use App\Modules\Blog\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BlogCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all posts
        $posts = Post::all();
        
        // Get all users
        $users = User::all();
        
        if ($posts->isEmpty()) {
            $this->command->info('No blog posts found. Please create posts first.');
            return;
        }
        
        $this->command->info('Creating 20 comments for each post...');
        
        foreach ($posts as $post) {
            // Create 15 top-level comments
            for ($i = 0; $i < 15; $i++) {
                $isGuest = $faker->boolean(30); // 30% chance of guest comment
                
                $comment = Comment::create([
                    'blog_post_id' => $post->id,
                    'user_id' => $isGuest ? null : $users->random()->id,
                    'guest_name' => $isGuest ? $faker->name : null,
                    'guest_email' => $isGuest ? $faker->safeEmail : null,
                    'content' => $faker->paragraph(rand(2, 5)),
                    'status' => $faker->randomElement(['pending', 'approved', 'approved', 'approved', 'spam']), // More approved
                    'approved_at' => $faker->boolean(70) ? now() : null,
                    'approved_by' => $faker->boolean(70) ? $users->random()->id : null,
                    'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
                ]);
                
                // 30% chance to add a reply to this comment
                if ($faker->boolean(30) && $comment->status === 'approved') {
                    $isGuestReply = $faker->boolean(20);
                    
                    Comment::create([
                        'blog_post_id' => $post->id,
                        'parent_id' => $comment->id,
                        'user_id' => $isGuestReply ? null : $users->random()->id,
                        'guest_name' => $isGuestReply ? $faker->name : null,
                        'guest_email' => $isGuestReply ? $faker->safeEmail : null,
                        'content' => $faker->paragraph(rand(1, 3)),
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => $users->random()->id,
                        'created_at' => $faker->dateTimeBetween($comment->created_at, 'now'),
                    ]);
                }
            }
            
            $this->command->info("Created comments for post: {$post->title}");
        }
        
        $this->command->info('Blog comments seeded successfully!');
    }
}
