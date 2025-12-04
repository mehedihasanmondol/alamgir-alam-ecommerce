<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Media;
use App\Models\AuthorProfile;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Tag;
use App\Modules\Blog\Models\Comment;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use App\Modules\Ecommerce\Product\Models\ProductImage;
use App\Modules\Ecommerce\Product\Models\ProductAttribute;
use App\Modules\Ecommerce\Product\Models\ProductAttributeValue;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;

class MigrateFromWordPress extends Command
{
    protected $signature = 'wordpress:migrate 
                            {--domain=https://prokriti.org : WordPress site domain}
                            {--wc-key= : WooCommerce Consumer Key}
                            {--wc-secret= : WooCommerce Consumer Secret}
                            {--only-posts : Migrate only blog posts}
                            {--only-products : Migrate only WooCommerce products}
                            {--skip-images : Skip image download}
                            {--batch-size=10 : Number of items to process per batch}
                            {--start-from=1 : Page number to start from}';

    protected $description = 'Migrate content from WordPress/WooCommerce to Laravel';

    protected $wordpressDomain;
    protected $wcKey;
    protected $wcSecret;
    protected $imageMapping = [];
    protected $userMapping = [];
    protected $categoryMapping = [];
    protected $tagMapping = [];
    protected $lastTimestamp = null;
    protected $timestampIncrement = 4; // seconds between records (production safety)
    protected $lastUnixTimestamp = 0; // Track unix timestamp for reliable comparison
    protected $recordCounter = 0; // Track total records migrated

    public function handle()
    {
        $this->wordpressDomain = $this->option('domain') ?? env('WORDPRESS_DOMAIN', 'https://example.org');
        $this->wcKey = $this->option('wc-key') ?? env('WOOCOMMERCE_KEY', 'wkey');
        $this->wcSecret = $this->option('wc-secret') ?? env('WOOCOMMERCE_SECRET', 'wsecret');
        
        // Initialize timestamp tracker to start from 30 days ago
        $this->lastTimestamp = now()->subDays(30);
        $this->lastUnixTimestamp = $this->lastTimestamp->timestamp;

        $this->info("🚀 Starting WordPress Migration from: {$this->wordpressDomain}");
        $this->info("   ⏱️  Guaranteed unique timestamps: {$this->timestampIncrement}s + 2s spacing (UTC)");
        $this->info("   📊 Starting unix timestamp: {$this->lastUnixTimestamp}");
        $this->newLine();

        try {
            DB::beginTransaction();

            // Step 1: Migrate Authors/Users
            $this->migrateAuthors();

            // Step 2: Migrate Categories & Tags
            $this->migrateTaxonomies();

            // Step 3: Migrate Blog Posts
            if (!$this->option('only-products')) {
                $this->migratePosts();
            }

            // Step 4: Migrate WooCommerce Products
            if (!$this->option('only-posts') && $this->wcKey && $this->wcSecret) {
                $this->migrateProducts();
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ Migration completed successfully!');
            $this->info("   📊 Total records with unique timestamps: {$this->recordCounter}");
            $this->info("   ⏱️  Final unix timestamp: {$this->lastUnixTimestamp}");
            $this->displayStatistics();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Migration failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    protected function migrateAuthors()
    {
        $this->info('👥 Migrating WordPress Authors...');
        
        $response = Http::get("{$this->wordpressDomain}/wp-json/wp/v2/users", [
            'per_page' => 100,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch users from WordPress");
        }

        $users = $response->json();
        $this->info("  Found " . count($users) . " authors");

        foreach ($users as $wpUser) {
            $email = $wpUser['slug'] . '@migrated.local'; // WordPress doesn't expose real emails via REST API
            
            $timestamps = $this->getNextTimestamp();
            
            $user = $this->updateOrCreateWithTimestamps(
                User::class,
                ['email' => $email],
                [
                    'name' => $wpUser['name'] ?? $wpUser['slug'],
                    'password' => bcrypt(Str::random(32)),
                    'email_verified_at' => $timestamps['created_at'],
                ],
                $timestamps
            );

            // Create AuthorProfile for each migrated user
            $authorSlug = generate_slug($wpUser['name'] ?? $wpUser['slug']);
            if (empty($authorSlug)) {
                $authorSlug = Str::slug($wpUser['name'] ?? $wpUser['slug']);
            }
            
            $profileTimestamps = $this->getNextTimestamp();
            
            $this->updateOrCreateWithTimestamps(
                AuthorProfile::class,
                ['user_id' => $user->id],
                [
                    'slug' => $authorSlug,
                    'bio' => $wpUser['description'] ?? null,
                    'website' => $wpUser['url'] ?? null,
                    'is_featured' => false,
                    'display_order' => 0,
                ],
                $profileTimestamps
            );

            $this->userMapping[$wpUser['id']] = $user->id;
            $this->info("    ✓ Migrated: {$wpUser['name']} → User ID: {$user->id}");
        }
        
        $this->info("  ✅ Migrated " . count($users) . " authors");
        $this->newLine();
    }

    protected function migrateTaxonomies()
    {
        $this->info('📂 Migrating Categories & Tags...');
        
        // Migrate Blog Categories
        $this->info("  📁 Migrating Blog Categories...");
        $categories = $this->fetchAll("{$this->wordpressDomain}/wp-json/wp/v2/categories");
        
        foreach ($categories as $wpCat) {
            $catSlug = generate_slug($wpCat['name']) ?: Str::slug($wpCat['name']);
            if (empty($catSlug)) {
                $catSlug = 'category-' . $wpCat['id'];
            }
            
            $catTimestamps = $this->getNextTimestamp();
            
            $category = $this->updateOrCreateWithTimestamps(
                BlogCategory::class,
                ['slug' => $catSlug],
                [
                    'name' => $wpCat['name'],
                    'description' => $wpCat['description'] ?? null,
                    'meta_title' => $wpCat['name'],
                    'meta_description' => $wpCat['description'] ?? null,
                ],
                $catTimestamps
            );

            $this->categoryMapping['blog'][$wpCat['id']] = $category->id;
            $this->info("    ✓ {$wpCat['name']} → ID: {$category->id}");
        }

        // Migrate Tags
        $this->info("  🏷️  Migrating Tags...");
        $tags = $this->fetchAll("{$this->wordpressDomain}/wp-json/wp/v2/tags");
        
        foreach ($tags as $wpTag) {
            $tagSlug = generate_slug($wpTag['name']) ?: Str::slug($wpTag['name']);
            if (empty($tagSlug)) {
                $tagSlug = 'tag-' . $wpTag['id'];
            }
            
            $tagTimestamps = $this->getNextTimestamp();
            
            $tag = $this->updateOrCreateWithTimestamps(
                Tag::class,
                ['slug' => $tagSlug],
                [
                    'name' => $wpTag['name'],
                    'description' => $wpTag['description'] ?? null,
                ],
                $tagTimestamps
            );

            $this->tagMapping[$wpTag['id']] = $tag->id;
            $this->info("    ✓ {$wpTag['name']} → ID: {$tag->id}");
        }

        // Migrate Product Categories (if WooCommerce)
        if ($this->wcKey && $this->wcSecret) {
            $this->info("  📦 Migrating Product Categories...");
            $prodCategories = $this->fetchAllWooCommerce('/wc/v3/products/categories');
            
            foreach ($prodCategories as $wpCat) {
                $prodCatSlug = generate_slug($wpCat['name']) ?: Str::slug($wpCat['name']);
                if (empty($prodCatSlug)) {
                    $prodCatSlug = 'product-category-' . $wpCat['id'];
                }
                
                $prodCatTimestamps = $this->getNextTimestamp();
                
                $category = $this->updateOrCreateWithTimestamps(
                    Category::class,
                    ['slug' => $prodCatSlug],
                    [
                        'name' => $wpCat['name'],
                        'description' => $wpCat['description'] ?? null,
                        'parent_id' => $wpCat['parent'] > 0 ? ($this->categoryMapping['product'][$wpCat['parent']] ?? null) : null,
                        'meta_title' => $wpCat['name'],
                        'meta_description' => $wpCat['description'] ?? null,
                    ],
                    $prodCatTimestamps
                );

                $this->categoryMapping['product'][$wpCat['id']] = $category->id;
                $this->info("    ✓ {$wpCat['name']} → ID: {$category->id}");
            }
        }
        
        $this->info("  ✅ Taxonomies migration completed");
        $this->newLine();
    }

    protected function migratePosts()
    {
        $this->info('📝 Migrating Blog Posts...');
        
        $page = $this->option('start-from');
        $batchSize = $this->option('batch-size');
        $totalMigrated = 0;

        do {
            $response = Http::get("{$this->wordpressDomain}/wp-json/wp/v2/posts", [
                'per_page' => $batchSize,
                'page' => $page,
                '_embed' => true, // Get featured media
                'status' => 'publish', // Only published posts
            ]);

            if (!$response->successful()) {
                break;
            }

            $posts = $response->json();
            
            if (empty($posts)) {
                break;
            }

            $progressBar = $this->output->createProgressBar(count($posts));
            $progressBar->start();

            foreach ($posts as $wpPost) {
                try {
                    $this->migratePost($wpPost);
                    $totalMigrated++;
                } catch (\Exception $e) {
                    $this->warn("\n  ⚠️  Failed to migrate post ID {$wpPost['id']}: " . $e->getMessage());
                }
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info("  ✓ Page {$page} completed ({$totalMigrated} posts so far)");

            $page++;

            // Continue until we get fewer items than batch size (last page)
            if (count($posts) < $batchSize) {
                break;
            }

        } while (true);

        $this->info("✅ Migrated {$totalMigrated} blog posts");
    }

    protected function migratePost($wpPost)
    {
        // Generate proper slug from title (handles Bangla to English conversion)
        $postSlug = generate_slug($wpPost['title']['rendered']);
        if (empty($postSlug)) {
            $postSlug = Str::slug($wpPost['title']['rendered']);
        }
        if (empty($postSlug)) {
            $postSlug = 'post-' . $wpPost['id'];
        }

        // Skip if post already exists
        if (Post::where('slug', $postSlug)->exists()) {
            $this->warn("  ⏭️  Skipping existing post: {$wpPost['title']['rendered']}");
            return null;
        }

        // Download featured image
        $mediaId = null;
        if (!$this->option('skip-images') && isset($wpPost['_embedded']['wp:featuredmedia'][0])) {
            $featuredMedia = $wpPost['_embedded']['wp:featuredmedia'][0];
            $mediaId = $this->downloadAndCreateMedia(
                $featuredMedia['source_url'],
                $featuredMedia['alt_text'] ?? $wpPost['title']['rendered']
            );
        }

        // Replace image URLs in content and remove flex classes
        $content = $wpPost['content']['rendered'];
        if (!$this->option('skip-images')) {
            $content = $this->replaceImagesInContent($content);
        }
        $content = $this->removeFlexClasses($content);

        // Get author ID
        $authorId = $this->userMapping[$wpPost['author']] ?? User::first()->id;
        
        // Get custom timestamps
        $postTimestamps = $this->getNextTimestamp(
            isset($wpPost['date']) ? $wpPost['date'] : null,
            isset($wpPost['modified']) ? $wpPost['modified'] : null
        );

        // Create post with custom timestamps
        $post = $this->createWithTimestamps(
            Post::class,
            [
                'slug' => $postSlug,
                'title' => html_entity_decode($wpPost['title']['rendered'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'excerpt' => $this->stripHtml($wpPost['excerpt']['rendered'] ?? ''),
                'content' => $content,
                'author_id' => $authorId,
                'media_id' => $mediaId,
                'status' => $wpPost['status'] === 'publish' ? 'published' : 'draft',
                'published_at' => $wpPost['status'] === 'publish' ? $wpPost['date'] : null,
                'meta_title' => isset($wpPost['yoast_head_json']['og_title']) ? html_entity_decode($wpPost['yoast_head_json']['og_title'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : html_entity_decode($wpPost['title']['rendered'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'meta_description' => isset($wpPost['yoast_head_json']['og_description']) ? html_entity_decode($wpPost['yoast_head_json']['og_description'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : null,
                'meta_keywords' => isset($wpPost['yoast_head_json']['keywords']) ? implode(', ', $wpPost['yoast_head_json']['keywords']) : null,
                'allow_comments' => $wpPost['comment_status'] === 'open',
            ],
            $postTimestamps
        );

        // Attach categories
        if (!empty($wpPost['categories'])) {
            $categoryIds = [];
            foreach ($wpPost['categories'] as $wpCatId) {
                if (isset($this->categoryMapping['blog'][$wpCatId])) {
                    $categoryIds[] = $this->categoryMapping['blog'][$wpCatId];
                }
            }
            if (!empty($categoryIds)) {
                $post->categories()->sync($categoryIds);
            }
        }

        // Attach tags
        if (!empty($wpPost['tags'])) {
            $tagIds = [];
            foreach ($wpPost['tags'] as $wpTagId) {
                if (isset($this->tagMapping[$wpTagId])) {
                    $tagIds[] = $this->tagMapping[$wpTagId];
                }
            }
            if (!empty($tagIds)) {
                $post->tags()->sync($tagIds);
            }
        }

        return $post;
    }

    protected function migrateProducts()
    {
        if (!$this->wcKey || !$this->wcSecret) {
            $this->warn('⚠️  WooCommerce credentials not provided. Skipping products migration.');
            return;
        }

        $this->info('🛒 Migrating WooCommerce Products...');
        
        $page = 1;
        $batchSize = $this->option('batch-size');
        $totalMigrated = 0;

        do {
            // Fetch products directly with WooCommerce authentication
            $response = Http::withBasicAuth($this->wcKey, $this->wcSecret)
                ->get("{$this->wordpressDomain}/wp-json/wc/v3/products", [
                    'per_page' => $batchSize,
                    'page' => $page,
                    'status' => 'publish', // Only published products
                    'orderby' => 'date', // Sort by date_created
                    'order' => 'asc', // Ascending order (oldest → newest)
                ]);

            if (!$response->successful()) {
                $this->warn("Failed to fetch products: " . $response->body());
                break;
            }

            $products = $response->json();

            if (empty($products)) {
                break;
            }

            $productsInPage = count($products);
            $progressBar = $this->output->createProgressBar($productsInPage);
            $progressBar->start();

            foreach ($products as $wcProduct) {
                try {
                    $this->migrateProduct($wcProduct);
                    $totalMigrated++;
                } catch (\Exception $e) {
                    $this->warn("\n  ⚠️  Failed to migrate product ID {$wcProduct['id']}: " . $e->getMessage());
                }
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info("  ✓ Page {$page} completed ({$totalMigrated} products so far)");

            $page++;

            // Continue while we're getting products (don't stop just because we got fewer than batch size)
        } while ($productsInPage > 0);

        $this->info("✅ Migrated {$totalMigrated} products");
    }

    protected function migrateProduct($wcProduct)
    {
        // Generate proper slug from product name (handles Bangla to English conversion)
        $productSlug = generate_slug($wcProduct['name']);
        if (empty($productSlug)) {
            $productSlug = Str::slug($wcProduct['name']);
        }
        if (empty($productSlug)) {
            $productSlug = 'product-' . $wcProduct['id'];
        }

        // Skip if product already exists
        if (Product::where('slug', $productSlug)->exists()) {
            $this->warn("  ⏭️  Skipping existing product: {$wcProduct['name']}");
            return null;
        }

        // Determine product type
        $productType = 'simple';
        if ($wcProduct['type'] === 'variable') {
            $productType = 'variable';
        } elseif ($wcProduct['type'] === 'grouped') {
            $productType = 'grouped';
        } elseif ($wcProduct['type'] === 'external') {
            $productType = 'affiliate';
        }

        // Get or create brand (if exists in metadata)
        $brandId = null;
        if (!empty($wcProduct['brands']) && is_array($wcProduct['brands'])) {
            $brandName = $wcProduct['brands'][0]['name'] ?? null;
            if ($brandName) {
                $brand = Brand::firstOrCreate(
                    ['slug' => Str::slug($brandName)],
                    ['name' => $brandName]
                );
                $brandId = $brand->id;
            }
        }

        // Replace images in description and remove flex classes
        $description = $wcProduct['description'];
        if (!$this->option('skip-images')) {
            $description = $this->replaceImagesInContent($description);
        }
        $description = $this->removeFlexClasses($description);

        // Process short description
        $shortDescription = $this->removeFlexClasses($wcProduct['short_description']);
        
        // Get custom timestamps
        $productTimestamps = $this->getNextTimestamp(
            isset($wcProduct['date_created']) ? $wcProduct['date_created'] : null,
            isset($wcProduct['date_modified']) ? $wcProduct['date_modified'] : null
        );

        // Create product with custom timestamps
        $product = $this->createWithTimestamps(
            Product::class,
            [
                'slug' => $productSlug,
                'name' => html_entity_decode($wcProduct['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'description' => $description,
                'short_description' => $shortDescription,
                'brand_id' => $brandId,
                'product_type' => $productType,
                'status' => $wcProduct['status'] === 'publish' ? 'published' : 'draft',
                'external_url' => $wcProduct['external_url'] ?? null,
                'button_text' => $wcProduct['button_text'] ?? null,
                'is_active' => $wcProduct['status'] === 'publish',
                'is_featured' => $wcProduct['featured'],
                'meta_title' => html_entity_decode($wcProduct['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'meta_description' => $this->stripHtml(html_entity_decode($wcProduct['short_description'], ENT_QUOTES | ENT_HTML5, 'UTF-8')),
            ],
            $productTimestamps
        );

        // Attach categories
        if (!empty($wcProduct['categories'])) {
            $categoryIds = [];
            foreach ($wcProduct['categories'] as $wcCat) {
                if (isset($this->categoryMapping['product'][$wcCat['id']])) {
                    $categoryIds[] = $this->categoryMapping['product'][$wcCat['id']];
                }
            }
            if (!empty($categoryIds)) {
                $product->categories()->sync($categoryIds);
            }
        }

        // Create product variant (default) - Ensure all required fields have defaults
        $variantData = [
            'name' => !empty($wcProduct['name']) ? $wcProduct['name'] : 'Default Variant',
            'sku' => !empty($wcProduct['sku']) ? $wcProduct['sku'] : 'SKU-' . $product->id ,
            'price' => !empty($wcProduct['regular_price']) ? (float)$wcProduct['regular_price'] : 0,
            'sale_price' => !empty($wcProduct['sale_price']) ? (float)$wcProduct['sale_price'] : null,
            'stock_quantity' => isset($wcProduct['stock_quantity']) ? (int)$wcProduct['stock_quantity'] : 0,
            'stock_status' => ($wcProduct['stock_status'] ?? 'instock') === 'instock' ? 'in_stock' : 'out_of_stock',
            'weight' => $this->emptyToNull($wcProduct['weight'] ?? null),
            'length' => $this->emptyToNull($wcProduct['dimensions']['length'] ?? null),
            'width' => $this->emptyToNull($wcProduct['dimensions']['width'] ?? null),
            'height' => $this->emptyToNull($wcProduct['dimensions']['height'] ?? null),
        ];
        
        $variantTimestamps = $this->getNextTimestamp(
            isset($wcProduct['date_created']) ? $wcProduct['date_created'] : null,
            isset($wcProduct['date_modified']) ? $wcProduct['date_modified'] : null
        );
        
        $variant = $this->updateOrCreateWithTimestamps(
            ProductVariant::class,
            [
                'product_id' => $product->id,
                'is_default' => true,
            ],
            $variantData,
            $variantTimestamps
        );

        // Download and attach product images
        if (!$this->option('skip-images')) {
            $this->migrateProductImages($product, $wcProduct['images'] ?? []);
        }

        // Migrate variations for variable products
        if ($productType === 'variable') {
            $this->migrateProductVariations($product, $wcProduct['id']);
        }

        return $product;
    }

    protected function migrateProductImages($product, $images)
    {
        $sortOrder = 0;
        foreach ($images as $image) {
            $mediaId = $this->downloadAndCreateMedia($image['src'], $image['alt'] ?? $product->name);
            
            if ($mediaId) {
                $imageTimestamps = $this->getNextTimestamp();
                
                $this->updateOrCreateWithTimestamps(
                    ProductImage::class,
                    [
                        'product_id' => $product->id,
                        'media_id' => $mediaId,
                    ],
                    [
                        'is_primary' => $sortOrder === 0,
                        'sort_order' => $sortOrder++,
                        'alt_text' => $image['alt'] ?? $product->name,
                    ],
                    $imageTimestamps
                );
            }
        }
    }

    protected function migrateProductVariations($product, $wcProductId)
    {
        $variations = $this->fetchWooCommerce("/wc/v3/products/{$wcProductId}/variations", [
            'per_page' => 100,
        ]);

        foreach ($variations as $variation) {
            $mediaId = null;
            if (!$this->option('skip-images') && !empty($variation['image']['src'])) {
                $mediaId = $this->downloadAndCreateMedia(
                    $variation['image']['src'],
                    $product->name . ' - Variation'
                );
            }

            // Build variation name from attributes
            $variantName = $product->name;
            if (!empty($variation['attributes'])) {
                $attributes = array_map(function($attr) {
                    return $attr['option'];
                }, $variation['attributes']);
                $variantName .= ' - ' . implode(', ', $attributes);
            }

            $variationTimestamps = $this->getNextTimestamp(
                isset($variation['date_created']) ? $variation['date_created'] : null,
                isset($variation['date_modified']) ? $variation['date_modified'] : null
            );
            
            $this->updateOrCreateWithTimestamps(
                ProductVariant::class,
                [
                    'product_id' => $product->id,
                    'sku' => $variation['sku'] ?: 'VAR-' . $variation['id'],
                ],
                [
                    'name' => $variantName,
                    'price' => (float)$variation['regular_price'] ?: 0,
                    'sale_price' => $variation['sale_price'] ? (float)$variation['sale_price'] : null,
                    'stock_quantity' => (int)$variation['stock_quantity'] ?: 0,
                    'stock_status' => $variation['stock_status'] === 'instock' ? 'in_stock' : 'out_of_stock',
                    'weight' => $this->emptyToNull($variation['weight'] ?? null),
                    'length' => $this->emptyToNull($variation['dimensions']['length'] ?? null),
                    'width' => $this->emptyToNull($variation['dimensions']['width'] ?? null),
                    'height' => $this->emptyToNull($variation['dimensions']['height'] ?? null),
                    'media_id' => $mediaId,
                    'is_default' => false,
                ],
                $variationTimestamps
            );
        }
    }

    protected function downloadAndCreateMedia($url, $altText = null)
    {
        try {
            // Check if already downloaded
            if (isset($this->imageMapping[$url])) {
                return $this->imageMapping[$url];
            }

            // Encode URL properly for Bangla characters
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            $pathParts = explode('/', $path);
            $encodedParts = array_map(function($part) {
                return rawurlencode(rawurldecode($part));
            }, $pathParts);
            $encodedPath = implode('/', $encodedParts);
            
            $encodedUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '') . $encodedPath;
            if (!empty($parsedUrl['query'])) {
                $encodedUrl .= '?' . $parsedUrl['query'];
            }

            // Download image with proper headers
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get($encodedUrl);
                
            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()}");
            }
            
            $imageContent = $response->body();
            
            // Generate safe filename
            $originalFilename = basename(parse_url($url, PHP_URL_PATH));
            $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            $baseFilename = pathinfo($originalFilename, PATHINFO_FILENAME);
            
            // Convert Bangla to English slug
            $safeFilename = generate_slug($baseFilename) ?: Str::slug($baseFilename);
            if (empty($safeFilename)) {
                $safeFilename = 'image-' . time() . '-' . rand(1000, 9999);
            }
            $filename = $safeFilename . '.' . $extension;
            
            // Store in wordpress directory
            $wordpressPath = 'wordpress/' . date('Y/m/') . $filename;
            Storage::disk('public')->put($wordpressPath, $imageContent);

            $fileSize = strlen($imageContent);
            $fullPath = Storage::disk('public')->path($wordpressPath);
            $mimeType = mime_content_type($fullPath);

            // Get image dimensions
            $width = null;
            $height = null;
            $aspectRatio = null;
            
            if (strpos($mimeType, 'image/') === 0) {
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo !== false) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    
                    // Calculate aspect ratio (width / height)
                    if ($height > 0) {
                        $aspectRatio = round($width / $height, 4);
                    }
                }
            }

            // Create Media record with custom timestamps
            $mediaTimestamps = $this->getNextTimestamp();
            
            $media = $this->createWithTimestamps(
                Media::class,
                [
                    'user_id' => User::first()->id ?? null,
                    'original_filename' => $originalFilename,
                    'filename' => $filename,
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'size' => $fileSize,
                    'width' => $width,
                    'height' => $height,
                    'aspect_ratio' => $aspectRatio,
                    'disk' => 'public',
                    'path' => $wordpressPath,
                    'alt_text' => $altText,
                    'scope' => 'global',
                ],
                $mediaTimestamps
            );

            $this->imageMapping[$url] = $media->id;

            return $media->id;

        } catch (\Exception $e) {
            $this->warn("  ⚠️  Failed to download image: {$url} - " . $e->getMessage());
            return null;
        }
    }

    protected function replaceImagesInContent($content)
    {
        // Find all image URLs in content
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $imageUrl) {
                try {
                    // Check if image is from WordPress domain
                    $parsedImageUrl = parse_url($imageUrl);
                    $parsedWpDomain = parse_url($this->wordpressDomain);
                    
                    // Skip if image is from external domain
                    if (isset($parsedImageUrl['host']) && isset($parsedWpDomain['host'])) {
                        if ($parsedImageUrl['host'] !== $parsedWpDomain['host']) {
                            $this->info("  ⏭️  Keeping external image: {$imageUrl}");
                            continue; // Keep external image as-is
                        }
                    }
                    
                    // Download and get new URL (only for WordPress domain images)
                    $mediaId = $this->downloadAndCreateMedia($imageUrl);
                    if ($mediaId) {
                        $media = Media::find($mediaId);
                        if ($media) {
                            // Use the url attribute which provides the full URL
                            $newUrl = $media->url;
                            
                            // Replace URL in content
                            $content = str_replace($imageUrl, $newUrl, $content);
                        }
                    }
                } catch (\Exception $e) {
                    // Silently skip failed image replacements
                    $this->warn("  ⚠️  Failed to replace image in content: {$imageUrl}");
                    continue;
                }
            }
        }

        return $content;
    }

    protected function removeFlexClasses($content)
    {
        if (empty($content)) {
            return $content;
        }

        // Remove all flex-related classes from HTML tags
        // Matches class attributes and removes any class containing 'flex'
        $content = preg_replace_callback(
            '/class=["\']([^"\']*)["\']/',
            function ($matches) {
                $classes = $matches[1];
                // Split classes and filter out flex-related ones
                $classArray = explode(' ', $classes);
                $filteredClasses = array_filter($classArray, function($class) {
                    return stripos($class, 'flex') === false;
                });
                
                // If no classes remain, remove the entire class attribute
                if (empty($filteredClasses)) {
                    return '';
                }
                
                // Return cleaned class attribute
                return 'class="' . implode(' ', $filteredClasses) . '"';
            },
            $content
        );

        // Clean up any double spaces that might result from removed attributes
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/\s+>/', '>', $content);

        return $content;
    }

    protected function fetchAll($url)
    {
        $allItems = [];
        $page = 1;
        
        do {
            $response = Http::get($url, [
                'per_page' => 100,
                'page' => $page,
            ]);

            if (!$response->successful()) {
                break;
            }

            $items = $response->json();
            if (empty($items)) {
                break;
            }

            $allItems = array_merge($allItems, $items);
            $page++;

        } while (count($items) === 100);

        return $allItems;
    }

    protected function fetchWooCommerce($endpoint, $params = [])
    {
        $response = Http::withBasicAuth($this->wcKey, $this->wcSecret)
            ->get($this->wordpressDomain . '/wp-json' . $endpoint, $params);

        if (!$response->successful()) {
            throw new \Exception("WooCommerce API request failed: " . $response->body());
        }

        return $response->json();
    }

    protected function fetchAllWooCommerce($endpoint)
    {
        $allItems = [];
        $page = 1;
        
        do {
            $items = $this->fetchWooCommerce($endpoint, [
                'per_page' => 100,
                'page' => $page,
            ]);

            if (empty($items)) {
                break;
            }

            $allItems = array_merge($allItems, $items);
            $page++;

        } while (count($items) === 100);

        return $allItems;
    }

    protected function stripHtml($text)
    {
        return strip_tags($text);
    }

    protected function emptyToNull($value)
    {
        // Convert empty strings or empty values to null for decimal columns
        if ($value === '' || $value === null || $value === false) {
            return null;
        }
        return $value;
    }

    protected function displayStatistics()
    {
        $this->info('📊 Migration Statistics:');
        $this->table(
            ['Item', 'Count'],
            [
                ['Users Migrated', count($this->userMapping)],
                ['Blog Categories', count($this->categoryMapping['blog'] ?? [])],
                ['Product Categories', count($this->categoryMapping['product'] ?? [])],
                ['Tags', count($this->tagMapping)],
                ['Images Downloaded', count($this->imageMapping)],
                ['Total Posts', Post::count()],
                ['Total Products', Product::count()],
            ]
        );
    }
    
    /**
     * Create a model instance with custom timestamps
     * Bypasses Eloquent's automatic timestamp generation
     * 
     * @param string $model Model class name
     * @param array $attributes Model attributes (without timestamps)
     * @param array $timestamps Custom timestamps ['created_at' => Carbon, 'updated_at' => Carbon]
     * @return mixed Model instance
     */
    protected function createWithTimestamps($model, array $attributes, array $timestamps)
    {
        // Create the model instance (Eloquent will auto-set timestamps)
        $instance = $model::create($attributes);
        
        // Immediately update timestamps using Query Builder (bypasses Eloquent)
        DB::table($instance->getTable())
            ->where($instance->getKeyName(), $instance->getKey())
            ->update([
                'created_at' => $timestamps['created_at'],
                'updated_at' => $timestamps['updated_at'],
            ]);
        
        // Refresh the model to get updated timestamps
        $instance->refresh();
        
        return $instance;
    }
    
    /**
     * UpdateOrCreate with custom timestamps
     * 
     * @param string $model Model class name
     * @param array $search Search criteria
     * @param array $attributes Model attributes (without timestamps)
     * @param array $timestamps Custom timestamps ['created_at' => Carbon, 'updated_at' => Carbon]
     * @return mixed Model instance
     */
    protected function updateOrCreateWithTimestamps($model, array $search, array $attributes, array $timestamps)
    {
        // Use updateOrCreate (Eloquent will auto-set timestamps)
        $instance = $model::updateOrCreate($search, $attributes);
        
        // Update timestamps using Query Builder (bypasses Eloquent)
        DB::table($instance->getTable())
            ->where($instance->getKeyName(), $instance->getKey())
            ->update([
                'created_at' => $timestamps['created_at'],
                'updated_at' => $timestamps['updated_at'],
            ]);
        
        // Refresh the model
        $instance->refresh();
        
        return $instance;
    }
    
    /**
     * Get next unique timestamp pair
     * 
     * CRITICAL: ALWAYS increments to guarantee uniqueness across Windows/Linux.
     * Never reuses timestamps, even from WordPress originals.
     * 
     * @param string|null $originalCreated WordPress original created timestamp (unused, kept for compatibility)
     * @param string|null $originalModified WordPress original modified timestamp (unused, kept for compatibility)
     * @return array ['created_at' => Carbon, 'updated_at' => Carbon]
     */
    protected function getNextTimestamp($originalCreated = null, $originalModified = null)
    {
        $this->recordCounter++;
        
        // ALWAYS increment - never trust WordPress timestamps for uniqueness
        // This prevents ALL duplicate timestamp issues across platforms
        $this->lastUnixTimestamp += $this->timestampIncrement;
        
        // Create Carbon instance in UTC to ensure consistency
        $createdAt = \Carbon\Carbon::createFromTimestamp($this->lastUnixTimestamp, 'UTC');
        
        // Add 2 more seconds for updated_at (created_at + 2)
        $this->lastUnixTimestamp += 2;
        $updatedAt = \Carbon\Carbon::createFromTimestamp($this->lastUnixTimestamp, 'UTC');
        
        // Update the Carbon tracker
        $this->lastTimestamp = $updatedAt->copy();
        
        // Debug log every 10 records on production
        if ($this->recordCounter % 10 === 0) {
            $this->info("  [{$this->recordCounter}] Timestamp: {$createdAt->format('Y-m-d H:i:s')} (unix: {$createdAt->timestamp})");
        }
        
        // Return Carbon instances for Eloquent
        return [
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
