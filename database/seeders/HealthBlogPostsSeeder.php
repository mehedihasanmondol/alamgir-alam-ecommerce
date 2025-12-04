<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

class HealthBlogPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Health category
        $healthCategory = BlogCategory::firstOrCreate(
            ['slug' => 'health'],
            [
                'name' => 'Health & Wellness',
                'description' => 'Articles about health, fitness, nutrition, and wellness',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // Get or create tags
        $nutritionTag = Tag::firstOrCreate(['slug' => 'nutrition'], ['name' => 'Nutrition']);
        $fitnessTag = Tag::firstOrCreate(['slug' => 'fitness'], ['name' => 'Fitness']);
        $mentalHealthTag = Tag::firstOrCreate(['slug' => 'mental-health'], ['name' => 'Mental Health']);
        $dietTag = Tag::firstOrCreate(['slug' => 'diet'], ['name' => 'Diet']);
        $exerciseTag = Tag::firstOrCreate(['slug' => 'exercise'], ['name' => 'Exercise']);
        $wellnessTag = Tag::firstOrCreate(['slug' => 'wellness'], ['name' => 'Wellness']);

        // Get first admin user
        $author = User::first();

        if (!$author) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        // Health blog posts data
        $posts = [
            [
                'title' => '10 Simple Ways to Boost Your Immune System Naturally',
                'excerpt' => 'Discover evidence-based strategies to strengthen your immune system through diet, exercise, and lifestyle changes.',
                'content' => '<h2>Introduction</h2><p>Your immune system is your body\'s natural defense against illness. While there\'s no magic pill, there are proven ways to support your immune health naturally.</p><h2>1. Eat Colorful Fruits and Vegetables</h2><p>Aim for at least 5 servings daily of colorful produce packed with antioxidants and vitamins.</p><h2>2. Get Quality Sleep</h2><p>Adults need 7-9 hours per night for optimal immune function.</p><h2>3. Stay Hydrated</h2><p>Drink 8-10 glasses of water daily to flush toxins and support cellular function.</p><h2>4. Exercise Regularly</h2><p>Aim for 150 minutes of moderate activity weekly to boost circulation.</p><h2>5. Manage Stress</h2><p>Practice meditation, yoga, or deep breathing to reduce stress hormones.</p>',
                'tags' => [$wellnessTag, $nutritionTag],
                'is_featured' => true,
            ],
            [
                'title' => 'The Ultimate Guide to Healthy Eating: Nutrition Basics',
                'excerpt' => 'Master the fundamentals of nutrition with this comprehensive guide to eating well.',
                'content' => '<h2>Understanding Macronutrients</h2><p>Your body needs carbohydrates, proteins, and fats for optimal health.</p><h2>Choose Complex Carbs</h2><p>Whole grains, fruits, and vegetables provide sustained energy.</p><h2>Protein Power</h2><p>Include lean meats, fish, eggs, and legumes for muscle repair.</p><h2>Healthy Fats</h2><p>Avocados, nuts, and olive oil support brain health.</p>',
                'tags' => [$nutritionTag, $dietTag],
                'is_featured' => true,
            ],
            [
                'title' => 'Mental Health Matters: 7 Daily Habits for Wellbeing',
                'excerpt' => 'Small daily practices can make a big difference in your mental health.',
                'content' => '<h2>Start with Gratitude</h2><p>Write down three things you\'re grateful for each morning.</p><h2>Move Your Body</h2><p>Even 20 minutes of walking can boost mood.</p><h2>Connect with Others</h2><p>Social connections are vital for mental health.</p><h2>Practice Mindfulness</h2><p>Start with 5 minutes of meditation daily.</p>',
                'tags' => [$mentalHealthTag, $wellnessTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Beginner\'s Guide to Starting a Fitness Journey',
                'excerpt' => 'Ready to get fit? This guide will help you start with confidence.',
                'content' => '<h2>Set Realistic Goals</h2><p>Start with achievable targets like exercising 3 times per week.</p><h2>Choose Enjoyable Activities</h2><p>Try walking, swimming, dancing, or cycling.</p><h2>Start Slow</h2><p>Begin with 20-30 minute sessions and progress gradually.</p><h2>Track Progress</h2><p>Keep a fitness journal to stay motivated.</p>',
                'tags' => [$fitnessTag, $exerciseTag],
                'is_featured' => false,
            ],
            [
                'title' => 'The Science of Sleep: Get Better Rest',
                'excerpt' => 'Quality sleep is essential for health. Learn practical tips for better rest.',
                'content' => '<h2>Why Sleep Matters</h2><p>Sleep repairs your body and regulates hormones.</p><h2>Create Sleep Environment</h2><p>Keep bedroom cool, dark, and quiet.</p><h2>Bedtime Routine</h2><p>Go to bed at the same time daily.</p><h2>Avoid Disruptors</h2><p>Limit caffeine after 2 PM and avoid screens before bed.</p>',
                'tags' => [$wellnessTag],
                'is_featured' => true,
            ],
            [
                'title' => 'Stress Management Techniques That Work',
                'excerpt' => 'Discover proven techniques to manage stress effectively.',
                'content' => '<h2>Deep Breathing</h2><p>Try the 4-7-8 technique for instant calm.</p><h2>Progressive Relaxation</h2><p>Tense and relax muscle groups systematically.</p><h2>Time Management</h2><p>Use calendars and to-do lists to stay organized.</p><h2>Physical Activity</h2><p>Exercise reduces stress hormones naturally.</p>',
                'tags' => [$mentalHealthTag, $wellnessTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Healthy Weight Loss: Sustainable Strategies',
                'excerpt' => 'Forget fad diets. Learn evidence-based strategies for lasting results.',
                'content' => '<h2>Calorie Deficit Basics</h2><p>Aim for 500-750 calorie deficit daily.</p><h2>Whole Foods First</h2><p>Prioritize vegetables, fruits, and lean proteins.</p><h2>Portion Control</h2><p>Use smaller plates and eat mindfully.</p><h2>Stay Active</h2><p>Combine cardio and strength training.</p>',
                'tags' => [$dietTag, $fitnessTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Superfoods: What They Are and Why You Need Them',
                'excerpt' => 'Discover nutrient-dense foods that support optimal health.',
                'content' => '<h2>Berries</h2><p>Rich in antioxidants that fight inflammation.</p><h2>Leafy Greens</h2><p>Packed with vitamins and minerals.</p><h2>Fatty Fish</h2><p>Omega-3s support heart and brain health.</p><h2>Nuts and Seeds</h2><p>Healthy fats and protein in every bite.</p>',
                'tags' => [$nutritionTag, $dietTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Hydration 101: How Much Water Do You Need?',
                'excerpt' => 'Get the facts about proper hydration and water intake.',
                'content' => '<h2>Why Hydration Matters</h2><p>Water is involved in nearly every bodily function.</p><h2>How Much?</h2><p>Drink when thirsty and check urine color.</p><h2>Signs of Dehydration</h2><p>Thirst, dark urine, fatigue, and headaches.</p><h2>Hydration Tips</h2><p>Carry a water bottle and eat water-rich foods.</p>',
                'tags' => [$wellnessTag, $nutritionTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Building Healthy Habits: A Step-by-Step Guide',
                'excerpt' => 'Learn the science of habit formation and build habits that stick.',
                'content' => '<h2>Start Small</h2><p>Begin with one tiny habit like drinking water each morning.</p><h2>Habit Stacking</h2><p>Link new habits to existing ones.</p><h2>Make It Easy</h2><p>Reduce friction for good habits.</p><h2>Track Progress</h2><p>Use a habit tracker to build momentum.</p>',
                'tags' => [$wellnessTag, $mentalHealthTag],
                'is_featured' => true,
            ],
            [
                'title' => 'Yoga for Beginners: Benefits and Getting Started',
                'excerpt' => 'Discover the benefits of yoga and learn how to start your practice.',
                'content' => '<h2>Physical Benefits</h2><p>Improves flexibility, strength, and balance.</p><h2>Mental Benefits</h2><p>Reduces stress and improves focus.</p><h2>Getting Started</h2><p>Try Hatha or Yin yoga for beginners.</p><h2>Essential Equipment</h2><p>All you need is a yoga mat.</p>',
                'tags' => [$fitnessTag, $exerciseTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Heart Health: Protect Your Cardiovascular System',
                'excerpt' => 'Simple, effective strategies to keep your heart healthy.',
                'content' => '<h2>Eat Heart-Healthy</h2><p>Focus on fruits, vegetables, and whole grains.</p><h2>Stay Active</h2><p>Aim for 150 minutes of aerobic activity weekly.</p><h2>Maintain Healthy Weight</h2><p>Even modest weight loss reduces heart disease risk.</p><h2>Don\'t Smoke</h2><p>Quitting is the best thing for your heart.</p>',
                'tags' => [$wellnessTag, $exerciseTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Gut Health: The Key to Overall Wellness',
                'excerpt' => 'Your gut health affects everything. Learn how to support your microbiome.',
                'content' => '<h2>What Is the Microbiome?</h2><p>Trillions of beneficial bacteria in your gut.</p><h2>Eat Probiotic Foods</h2><p>Yogurt, kefir, and fermented foods support gut health.</p><h2>Feed Your Bacteria</h2><p>Fiber-rich foods are prebiotic fuel.</p><h2>Manage Stress</h2><p>The gut-brain connection is real.</p>',
                'tags' => [$nutritionTag, $wellnessTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Intermittent Fasting: Benefits and Methods',
                'excerpt' => 'Learn if intermittent fasting is right for your health goals.',
                'content' => '<h2>What Is IF?</h2><p>An eating pattern cycling between eating and fasting.</p><h2>Popular Methods</h2><p>16/8 method, 5:2 diet, or alternate-day fasting.</p><h2>Potential Benefits</h2><p>May support weight loss and improve insulin sensitivity.</p><h2>Getting Started</h2><p>Start with a 12-hour fast and extend gradually.</p>',
                'tags' => [$dietTag, $nutritionTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Strength Training for Women: Myths Debunked',
                'excerpt' => 'Learn the truth and benefits of lifting weights for women.',
                'content' => '<h2>Myth: Bulky Muscles</h2><p>Women don\'t have enough testosterone to bulk easily.</p><h2>Benefits</h2><p>Builds lean muscle and increases metabolism.</p><h2>Getting Started</h2><p>Begin with bodyweight or light weights.</p><h2>Essential Exercises</h2><p>Squats, lunges, push-ups, and planks.</p>',
                'tags' => [$fitnessTag, $exerciseTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Natural Remedies for Common Ailments',
                'excerpt' => 'Evidence-based natural remedies for everyday health issues.',
                'content' => '<h2>Headaches</h2><p>Try peppermint oil and stay hydrated.</p><h2>Common Cold</h2><p>Rest, hydrate, and consume vitamin C.</p><h2>Digestive Issues</h2><p>Peppermint tea and ginger can help.</p><h2>Insomnia</h2><p>Chamomile tea and lavender aromatherapy.</p>',
                'tags' => [$wellnessTag, $nutritionTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Meal Prep 101: Save Time and Eat Healthier',
                'excerpt' => 'Learn how to meal prep for a healthier, easier week.',
                'content' => '<h2>Benefits</h2><p>Saves time, money, and helps you eat healthier.</p><h2>Getting Started</h2><p>Choose a prep day and plan your menu.</p><h2>Strategies</h2><p>Batch cooking, ingredient prep, or full meals.</p><h2>Storage Tips</h2><p>Most meals last 3-4 days in the fridge.</p>',
                'tags' => [$nutritionTag, $dietTag],
                'is_featured' => true,
            ],
            [
                'title' => 'Understanding Macros: A Beginner\'s Guide',
                'excerpt' => 'Learn what macros are and how to track them for your goals.',
                'content' => '<h2>What Are Macros?</h2><p>Carbohydrates, proteins, and fats your body needs.</p><h2>Carbohydrates</h2><p>Provide quick energy from grains and fruits.</p><h2>Protein</h2><p>Builds and repairs tissues.</p><h2>Fats</h2><p>Support brain health and hormone production.</p>',
                'tags' => [$nutritionTag, $fitnessTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Mindful Eating: Transform Your Relationship with Food',
                'excerpt' => 'Mindful eating helps you enjoy food more and eat less.',
                'content' => '<h2>What Is It?</h2><p>Paying full attention to the eating experience.</p><h2>Benefits</h2><p>Reduces overeating and improves digestion.</p><h2>How to Practice</h2><p>Eat without distractions and chew thoroughly.</p><h2>Recognize Hunger</h2><p>Distinguish physical from emotional hunger.</p>',
                'tags' => [$nutritionTag, $mentalHealthTag],
                'is_featured' => false,
            ],
            [
                'title' => 'Preventing Burnout: Self-Care Strategies',
                'excerpt' => 'Learn to recognize burnout signs and implement self-care strategies.',
                'content' => '<h2>What Is Burnout?</h2><p>Emotional, physical, and mental exhaustion from stress.</p><h2>Signs</h2><p>Chronic fatigue, insomnia, and detachment.</p><h2>Set Boundaries</h2><p>Learn to say no and protect personal time.</p><h2>Prioritize Self-Care</h2><p>Make time for activities you enjoy.</p>',
                'tags' => [$mentalHealthTag, $wellnessTag],
                'is_featured' => false,
            ],
        ];

        // Create posts
        $this->command->info('Creating 20 health-related blog posts...');
        
        foreach ($posts as $index => $postData) {
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'author_id' => $author->id,
                'blog_category_id' => $healthCategory->id,
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'is_featured' => $postData['is_featured'],
                'allow_comments' => true,
                'views_count' => rand(50, 500),
            ]);

            // Attach tags
            $post->tags()->attach($postData['tags']);
            
            // Attach to categories (many-to-many)
            $post->categories()->attach($healthCategory->id);

            $this->command->info("Created: {$post->title}");
        }

        $this->command->info('✅ Successfully created 20 health blog posts!');
    }
}
