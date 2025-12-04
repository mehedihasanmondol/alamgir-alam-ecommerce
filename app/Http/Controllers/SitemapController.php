<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\User;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Tag;
use Illuminate\Http\Response;

/**
 * Sitemap Controller
 * Purpose: Generate dynamic XML sitemap for search engines
 * 
 * @author Windsurf AI
 * @date 2025-11-20
 */
class SitemapController extends Controller
{
    /**
     * Generate and return sitemap.xml
     */
    public function index(): Response
    {
        // Check if sitemap is enabled
        if (SiteSetting::get('sitemap_enabled', '1') !== '1') {
            abort(404);
        }

        $xml = $this->generateSitemap();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap XML content
     */
    private function generateSitemap(): string
    {
        $urls = [];
        
        // Homepage
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];
        
        // Shop page
        $urls[] = [
            'loc' => route('shop'),
            'lastmod' => now()->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];
        
        // Products
        $products = Product::active()
            ->with('images')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'image' => $product->getPrimaryThumbnailUrl(), // Use media library
            ];
        }
        
        // Categories
        $categories = Category::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('categories.show', $category->slug),
                'lastmod' => $category->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }
        
        // Brands
        $brands = Brand::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($brands as $brand) {
            $urls[] = [
                'loc' => route('brands.show', $brand->slug),
                'lastmod' => $brand->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }
        
        // Blog posts
        $posts = Post::published()
            ->orderBy('published_at', 'desc')
            ->get();
            
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => url($post->slug),
                'lastmod' => $post->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }
        
        // Blog categories
        $blogCategories = BlogCategory::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($blogCategories as $category) {
            $urls[] = [
                'loc' => route('blog.category', $category->slug),
                'lastmod' => $category->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }
        
        // Blog tags
        $blogTags = Tag::orderBy('updated_at', 'desc')->get();
        foreach ($blogTags as $tag) {
            $urls[] = [
                'loc' => route('blog.tag', $tag->slug),
                'lastmod' => $tag->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ];
        }
        
        // Author pages (only authors with published posts)
        $authors = User::whereHas('authorProfile')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->with('authorProfile')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($authors as $author) {
            if ($author->authorProfile && $author->authorProfile->slug) {
                $urls[] = [
                    'loc' => route('blog.author', $author->authorProfile->slug),
                    'lastmod' => $author->updated_at->toIso8601String(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        }
        
        // Static pages
        $staticPages = [
            ['url' => route('blog.index'), 'priority' => '0.8'],
            ['url' => route('categories.index'), 'priority' => '0.7'],
            ['url' => route('brands.index'), 'priority' => '0.6'],
            ['url' => route('coupons.index'), 'priority' => '0.5'],
            ['url' => route('contact.index'), 'priority' => '0.5'],
            ['url' => route('feedback.index'), 'priority' => '0.5'],
        ];
        
        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $page['url'],
                'lastmod' => now()->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => $page['priority'],
            ];
        }
        
        return $this->buildXml($urls);
    }

    /**
     * Build XML from URLs array
     */
    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            
            if (isset($url['lastmod'])) {
                $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            }
            
            if (isset($url['changefreq'])) {
                $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            }
            
            if (isset($url['priority'])) {
                $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            }
            
            // Add image tag if present
            if (isset($url['image']) && !empty($url['image'])) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($url['image']) . "</image:loc>\n";
                $xml .= "    </image:image>\n";
            }
            
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}
