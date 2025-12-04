<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Comprehensive Health Blog Seeder
 * Creates 20 health-related blog categories with blog posts
 */
class ComprehensiveHealthBlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first();
        
        if (!$author) {
            $this->command->error('No users found. Please run AdminUserSeeder first.');
            return;
        }

        $this->command->info('Creating health blog categories and posts...');

        // Create 20 health blog categories with posts
        $this->createNutritionCategory($author);
        $this->createFitnessCategory($author);
        $this->createMentalHealthCategory($author);
        $this->createWeightLossCategory($author);
        $this->createYogaCategory($author);
        $this->createSleepCategory($author);
        $this->createImmuneHealthCategory($author);
        $this->createHeartHealthCategory($author);
        $this->createDigestiveHealthCategory($author);
        $this->createSupplementsCategory($author);
        $this->createWomensHealthCategory($author);
        $this->createMensHealthCategory($author);
        $this->createSeniorHealthCategory($author);
        $this->createChildHealthCategory($author);
        $this->createSkinCareCategory($author);
        $this->createDentalHealthCategory($author);
        $this->createEyeHealthCategory($author);
        $this->createBoneHealthCategory($author);
        $this->createDetoxCategory($author);
        $this->createHolisticHealthCategory($author);

        $this->command->info('✅ Successfully created 20 health blog categories with posts!');
    }

    private function createNutritionCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Nutrition & Diet',
            'slug' => 'nutrition-diet',
            'description' => 'Expert advice on nutrition, healthy eating, and balanced diets',
            'is_active' => true,
            'sort_order' => 1,
            'meta_title' => 'Nutrition & Diet Tips - Healthy Eating Guide',
            'meta_description' => 'Discover nutrition tips, diet plans, and healthy eating strategies',
        ]);

        // Create subcategories
        $subcategories = [
            [
                'name' => 'Meal Planning',
                'slug' => 'meal-planning',
                'description' => 'Practical meal planning tips and strategies',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Vitamins & Minerals',
                'slug' => 'vitamins-minerals',
                'description' => 'Essential vitamins and minerals for health',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Special Diets',
                'slug' => 'special-diets',
                'description' => 'Keto, Vegan, Paleo and other diet plans',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($subcategories as $subcat) {
            BlogCategory::create($subcat);
        }

        $posts = [
            ['title' => 'Complete Guide to Balanced Nutrition', 'excerpt' => 'Learn the fundamentals of balanced nutrition for optimal health'],
            ['title' => 'Top 10 Superfoods You Should Eat Daily', 'excerpt' => 'Nutrient-dense foods that boost your health'],
            ['title' => 'Understanding Macronutrients: Carbs, Protein, Fats', 'excerpt' => 'Master the basics of macronutrients for better health'],
        ];

        $this->createPosts($category, $posts, $author, ['nutrition', 'diet', 'healthy-eating']);
    }

    private function createFitnessCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Fitness & Exercise',
            'slug' => 'fitness-exercise',
            'description' => 'Workout routines, exercise tips, and fitness guidance',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Create subcategories
        $subcategories = [
            [
                'name' => 'Strength Training',
                'slug' => 'strength-training',
                'description' => 'Build muscle and increase strength',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cardio Workouts',
                'slug' => 'cardio-workouts',
                'description' => 'Cardiovascular exercises for heart health',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Home Workouts',
                'slug' => 'home-workouts',
                'description' => 'Exercise routines you can do at home',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($subcategories as $subcat) {
            BlogCategory::create($subcat);
        }

        $posts = [
            ['title' => 'Beginner\'s Guide to Starting Your Fitness Journey', 'excerpt' => 'Everything you need to know to start exercising'],
            ['title' => '30-Day Home Workout Challenge', 'excerpt' => 'Transform your body with this home workout plan'],
            ['title' => 'Strength Training vs Cardio: Which is Better?', 'excerpt' => 'Compare different exercise types for your goals'],
        ];

        $this->createPosts($category, $posts, $author, ['fitness', 'exercise', 'workout']);
    }

    private function createMentalHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Mental Health & Wellness',
            'slug' => 'mental-health-wellness',
            'description' => 'Mental health tips, stress management, and emotional wellbeing',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create subcategories
        $subcategories = [
            [
                'name' => 'Stress Management',
                'slug' => 'stress-management',
                'description' => 'Techniques to reduce and manage stress',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Anxiety & Depression',
                'slug' => 'anxiety-depression',
                'description' => 'Understanding and coping with anxiety and depression',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Mindfulness & Meditation',
                'slug' => 'mindfulness-meditation',
                'description' => 'Practices for present moment awareness',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($subcategories as $subcat) {
            BlogCategory::create($subcat);
        }

        $posts = [
            ['title' => '10 Daily Habits for Better Mental Health', 'excerpt' => 'Simple practices to improve your mental wellbeing'],
            ['title' => 'Managing Stress: Effective Techniques That Work', 'excerpt' => 'Proven strategies to reduce stress and anxiety'],
            ['title' => 'Mindfulness Meditation for Beginners', 'excerpt' => 'Start your meditation practice today'],
        ];

        $this->createPosts($category, $posts, $author, ['mental-health', 'wellness', 'stress']);
    }

    private function createWeightLossCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Weight Loss & Management',
            'slug' => 'weight-loss-management',
            'description' => 'Sustainable weight loss strategies and healthy weight management',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Create subcategories
        $subcategories = [
            [
                'name' => 'Diet Plans',
                'slug' => 'diet-plans',
                'description' => 'Various diet plans for weight loss',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Calorie Counting',
                'slug' => 'calorie-counting',
                'description' => 'Track and manage your calorie intake',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Weight Loss Tips',
                'slug' => 'weight-loss-tips',
                'description' => 'Practical tips for losing weight',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($subcategories as $subcat) {
            BlogCategory::create($subcat);
        }

        $posts = [
            ['title' => 'Healthy Weight Loss: Science-Based Strategies', 'excerpt' => 'Lose weight sustainably with evidence-based methods'],
            ['title' => 'Intermittent Fasting: Complete Beginner\'s Guide', 'excerpt' => 'Everything about intermittent fasting for weight loss'],
            ['title' => 'Metabolism Boosting Foods and Habits', 'excerpt' => 'Natural ways to increase your metabolism'],
        ];

        $this->createPosts($category, $posts, $author, ['weight-loss', 'diet', 'metabolism']);
    }

    private function createYogaCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Yoga & Flexibility',
            'slug' => 'yoga-flexibility',
            'description' => 'Yoga practices, poses, and flexibility training',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // Create subcategories
        $subcategories = [
            [
                'name' => 'Beginner Yoga',
                'slug' => 'beginner-yoga',
                'description' => 'Yoga basics for beginners',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Advanced Poses',
                'slug' => 'advanced-poses',
                'description' => 'Challenging yoga poses for experienced practitioners',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Yoga Styles',
                'slug' => 'yoga-styles',
                'description' => 'Different types of yoga practices',
                'parent_id' => $category->id,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($subcategories as $subcat) {
            BlogCategory::create($subcat);
        }

        $posts = [
            ['title' => 'Yoga for Beginners: Essential Poses', 'excerpt' => 'Start your yoga journey with these basic poses'],
            ['title' => 'Morning Yoga Routine for Energy', 'excerpt' => 'Energize your day with this morning yoga flow'],
            ['title' => 'Yoga for Stress Relief and Relaxation', 'excerpt' => 'Calm your mind with these relaxing yoga poses'],
        ];

        $this->createPosts($category, $posts, $author, ['yoga', 'flexibility', 'mindfulness']);
    }

    private function createSleepCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Sleep & Recovery',
            'slug' => 'sleep-recovery',
            'description' => 'Better sleep tips, recovery strategies, and rest optimization',
            'is_active' => true,
            'sort_order' => 6,
        ]);

        $posts = [
            ['title' => 'The Science of Sleep: Get Better Rest', 'excerpt' => 'Understanding sleep cycles and improving sleep quality'],
            ['title' => '10 Natural Sleep Aids That Actually Work', 'excerpt' => 'Evidence-based natural remedies for better sleep'],
            ['title' => 'Creating the Perfect Sleep Environment', 'excerpt' => 'Optimize your bedroom for quality sleep'],
        ];

        $this->createPosts($category, $posts, $author, ['sleep', 'recovery', 'rest']);
    }

    private function createImmuneHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Immune System Health',
            'slug' => 'immune-system-health',
            'description' => 'Boost immunity naturally with diet, lifestyle, and supplements',
            'is_active' => true,
            'sort_order' => 7,
        ]);

        $posts = [
            ['title' => '15 Ways to Boost Your Immune System Naturally', 'excerpt' => 'Strengthen your immunity with these proven strategies'],
            ['title' => 'Best Vitamins for Immune Support', 'excerpt' => 'Essential vitamins to support immune function'],
            ['title' => 'Foods That Strengthen Your Immune System', 'excerpt' => 'Immune-boosting foods to add to your diet'],
        ];

        $this->createPosts($category, $posts, $author, ['immune-health', 'immunity', 'wellness']);
    }

    private function createHeartHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Heart Health',
            'slug' => 'heart-health',
            'description' => 'Cardiovascular health, heart disease prevention, and heart-healthy living',
            'is_active' => true,
            'sort_order' => 8,
        ]);

        $posts = [
            ['title' => 'Heart-Healthy Diet: Foods to Eat and Avoid', 'excerpt' => 'Protect your heart with the right nutrition'],
            ['title' => 'Exercise for Heart Health: Best Workouts', 'excerpt' => 'Cardio exercises that strengthen your heart'],
            ['title' => 'Understanding Cholesterol and Blood Pressure', 'excerpt' => 'Manage key heart health markers naturally'],
        ];

        $this->createPosts($category, $posts, $author, ['heart-health', 'cardiovascular', 'wellness']);
    }

    private function createDigestiveHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Digestive Health',
            'slug' => 'digestive-health',
            'description' => 'Gut health, digestion, probiotics, and digestive wellness',
            'is_active' => true,
            'sort_order' => 9,
        ]);

        $posts = [
            ['title' => 'Gut Health 101: The Key to Overall Wellness', 'excerpt' => 'Why gut health matters and how to improve it'],
            ['title' => 'Probiotics vs Prebiotics: What\'s the Difference?', 'excerpt' => 'Understanding gut health supplements'],
            ['title' => 'Foods for Better Digestion', 'excerpt' => 'Improve digestive health with these foods'],
        ];

        $this->createPosts($category, $posts, $author, ['digestive-health', 'gut-health', 'probiotics']);
    }

    private function createSupplementsCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Supplements & Vitamins',
            'slug' => 'supplements-vitamins',
            'description' => 'Vitamin guides, supplement reviews, and nutritional support',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $posts = [
            ['title' => 'Essential Vitamins Everyone Should Take', 'excerpt' => 'Core vitamins for optimal health'],
            ['title' => 'Supplement Guide: What, When, and How Much', 'excerpt' => 'Complete guide to taking supplements correctly'],
            ['title' => 'Natural vs Synthetic Vitamins: Which is Better?', 'excerpt' => 'Understanding vitamin quality and absorption'],
        ];

        $this->createPosts($category, $posts, $author, ['supplements', 'vitamins', 'nutrition']);
    }

    private function createWomensHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Women\'s Health',
            'slug' => 'womens-health',
            'description' => 'Health topics specific to women\'s wellness and hormonal health',
            'is_active' => true,
            'sort_order' => 11,
        ]);

        $posts = [
            ['title' => 'Women\'s Nutrition: Essential Nutrients', 'excerpt' => 'Key nutrients every woman needs'],
            ['title' => 'Hormonal Balance: Natural Approaches', 'excerpt' => 'Support hormonal health naturally'],
            ['title' => 'Fitness for Women: Strength Training Benefits', 'excerpt' => 'Why women should lift weights'],
        ];

        $this->createPosts($category, $posts, $author, ['womens-health', 'hormones', 'wellness']);
    }

    private function createMensHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Men\'s Health',
            'slug' => 'mens-health',
            'description' => 'Health and wellness topics for men',
            'is_active' => true,
            'sort_order' => 12,
        ]);

        $posts = [
            ['title' => 'Men\'s Health Essentials: Top Priorities', 'excerpt' => 'Key health areas men should focus on'],
            ['title' => 'Testosterone Naturally: Foods and Lifestyle', 'excerpt' => 'Natural ways to support healthy testosterone'],
            ['title' => 'Building Muscle After 40: Complete Guide', 'excerpt' => 'Fitness strategies for men over 40'],
        ];

        $this->createPosts($category, $posts, $author, ['mens-health', 'fitness', 'testosterone']);
    }

    private function createSeniorHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Senior Health & Aging',
            'slug' => 'senior-health-aging',
            'description' => 'Healthy aging, senior wellness, and age-related health',
            'is_active' => true,
            'sort_order' => 13,
        ]);

        $posts = [
            ['title' => 'Healthy Aging: Nutrition for Seniors', 'excerpt' => 'Nutritional needs change with age'],
            ['title' => 'Exercise for Seniors: Safe and Effective', 'excerpt' => 'Stay active and healthy at any age'],
            ['title' => 'Brain Health: Preventing Cognitive Decline', 'excerpt' => 'Protect your brain as you age'],
        ];

        $this->createPosts($category, $posts, $author, ['senior-health', 'aging', 'wellness']);
    }

    private function createChildHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Children\'s Health',
            'slug' => 'childrens-health',
            'description' => 'Child nutrition, development, and pediatric wellness',
            'is_active' => true,
            'sort_order' => 14,
        ]);

        $posts = [
            ['title' => 'Children\'s Nutrition: Building Healthy Habits', 'excerpt' => 'Teach kids to eat healthy from an early age'],
            ['title' => 'Vitamins for Kids: What They Really Need', 'excerpt' => 'Essential nutrients for growing children'],
            ['title' => 'Physical Activity for Children: Guidelines', 'excerpt' => 'Keep kids active and healthy'],
        ];

        $this->createPosts($category, $posts, $author, ['childrens-health', 'pediatric', 'nutrition']);
    }

    private function createSkinCareCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Skin Care & Beauty',
            'slug' => 'skin-care-beauty',
            'description' => 'Natural skincare, beauty tips, and skin health',
            'is_active' => true,
            'sort_order' => 15,
        ]);

        $posts = [
            ['title' => 'Natural Skincare Routine for Glowing Skin', 'excerpt' => 'Achieve healthy skin naturally'],
            ['title' => 'Anti-Aging Skincare: What Actually Works', 'excerpt' => 'Science-backed anti-aging strategies'],
            ['title' => 'Foods for Healthy Skin', 'excerpt' => 'Eat your way to better skin'],
        ];

        $this->createPosts($category, $posts, $author, ['skincare', 'beauty', 'anti-aging']);
    }

    private function createDentalHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Dental Health',
            'slug' => 'dental-health',
            'description' => 'Oral hygiene, dental care, and teeth health',
            'is_active' => true,
            'sort_order' => 16,
        ]);

        $posts = [
            ['title' => 'Complete Guide to Oral Hygiene', 'excerpt' => 'Maintain healthy teeth and gums'],
            ['title' => 'Natural Remedies for Dental Health', 'excerpt' => 'Support oral health naturally'],
            ['title' => 'Foods That Strengthen Your Teeth', 'excerpt' => 'Nutrition for dental health'],
        ];

        $this->createPosts($category, $posts, $author, ['dental-health', 'oral-hygiene', 'teeth']);
    }

    private function createEyeHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Eye Health & Vision',
            'slug' => 'eye-health-vision',
            'description' => 'Vision care, eye health, and protecting your eyesight',
            'is_active' => true,
            'sort_order' => 17,
        ]);

        $posts = [
            ['title' => 'Protecting Your Vision: Eye Health Tips', 'excerpt' => 'Keep your eyes healthy for life'],
            ['title' => 'Nutrients for Eye Health', 'excerpt' => 'Vitamins and minerals for better vision'],
            ['title' => 'Digital Eye Strain: Prevention and Relief', 'excerpt' => 'Protect your eyes from screen time'],
        ];

        $this->createPosts($category, $posts, $author, ['eye-health', 'vision', 'wellness']);
    }

    private function createBoneHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Bone & Joint Health',
            'slug' => 'bone-joint-health',
            'description' => 'Strong bones, joint health, and mobility',
            'is_active' => true,
            'sort_order' => 18,
        ]);

        $posts = [
            ['title' => 'Building Strong Bones: Nutrition and Exercise', 'excerpt' => 'Prevent osteoporosis and maintain bone density'],
            ['title' => 'Joint Health: Natural Support Strategies', 'excerpt' => 'Keep joints healthy and pain-free'],
            ['title' => 'Calcium and Vitamin D: The Bone Health Duo', 'excerpt' => 'Essential nutrients for strong bones'],
        ];

        $this->createPosts($category, $posts, $author, ['bone-health', 'joint-health', 'osteoporosis']);
    }

    private function createDetoxCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Detox & Cleansing',
            'slug' => 'detox-cleansing',
            'description' => 'Natural detox methods, cleansing, and body purification',
            'is_active' => true,
            'sort_order' => 19,
        ]);

        $posts = [
            ['title' => 'Natural Detox: Myths vs Facts', 'excerpt' => 'The truth about detoxing your body'],
            ['title' => 'Liver Health: Natural Cleansing Methods', 'excerpt' => 'Support your liver naturally'],
            ['title' => 'Detox Foods and Drinks', 'excerpt' => 'Foods that support natural detoxification'],
        ];

        $this->createPosts($category, $posts, $author, ['detox', 'cleansing', 'liver-health']);
    }

    private function createHolisticHealthCategory($author)
    {
        $category = BlogCategory::create([
            'name' => 'Holistic Health & Natural Medicine',
            'slug' => 'holistic-health-natural-medicine',
            'description' => 'Holistic approaches, natural remedies, and alternative medicine',
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $posts = [
            ['title' => 'Introduction to Holistic Health', 'excerpt' => 'Whole-body approach to wellness'],
            ['title' => 'Herbal Remedies for Common Ailments', 'excerpt' => 'Natural herbs for health'],
            ['title' => 'Mind-Body Connection: Healing Naturally', 'excerpt' => 'The power of mind-body medicine'],
        ];

        $this->createPosts($category, $posts, $author, ['holistic-health', 'natural-medicine', 'herbs']);
    }

    private function createPosts($category, $posts, $author, $tagNames)
    {
        // Create or get tags
        $tags = collect($tagNames)->map(function ($tagName) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => ucwords(str_replace('-', ' ', $tagName))]
            );
        });

        foreach ($posts as $postData) {
            $content = $this->generateContent($postData['title'], $postData['excerpt']);
            
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $content,
                'author_id' => $author->id,
                'blog_category_id' => $category->id,
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 60)),
                'is_featured' => rand(0, 4) == 0, // 20% featured
                'allow_comments' => true,
                'views_count' => rand(50, 1000),
            ]);

            $post->tags()->attach($tags->pluck('id'));
            $post->categories()->attach($category->id);
        }

        $this->command->info("✓ Created category: {$category->name} with " . count($posts) . " posts");
    }

    private function generateContent($title, $excerpt)
    {
        return "<h2>Introduction</h2>
<p>{$excerpt}. In this comprehensive guide, we'll explore everything you need to know about this important health topic.</p>

<h2>Why This Matters</h2>
<p>Understanding this aspect of health is crucial for your overall wellbeing. Research shows that taking proactive steps in this area can lead to significant improvements in your quality of life.</p>

<h2>Key Points to Remember</h2>
<ul>
<li>Consistency is key to seeing results</li>
<li>Start with small, manageable changes</li>
<li>Listen to your body and adjust as needed</li>
<li>Consult healthcare professionals when necessary</li>
</ul>

<h2>Practical Tips</h2>
<p>Here are some actionable strategies you can implement today:</p>
<ol>
<li>Begin with one small change and build from there</li>
<li>Track your progress to stay motivated</li>
<li>Find support from friends, family, or online communities</li>
<li>Be patient with yourself - lasting change takes time</li>
</ol>

<h2>Common Mistakes to Avoid</h2>
<p>Many people make these common errors when starting out. Avoid these pitfalls for better success.</p>

<h2>Conclusion</h2>
<p>Taking care of your health is one of the best investments you can make. Start implementing these strategies today and you'll be on your way to better health and wellness.</p>";
    }
}
