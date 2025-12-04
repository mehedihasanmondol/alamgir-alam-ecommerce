<?php

namespace App\Modules\Blog\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Repositories\BlogCategoryRepository;
use App\Modules\Blog\Repositories\TagRepository;
use App\Modules\Blog\Services\PostService;
use App\Modules\Blog\Services\CommentService;
use Illuminate\Http\Request;

/**
 * ModuleName: Blog
 * Purpose: Frontend controller for public blog pages
 * 
 * @category Blog
 * @package  App\Modules\Blog\Controllers\Frontend
 * @author   AI Assistant
 * @created  2025-11-07
 */
class BlogController extends Controller
{
    protected PostService $postService;
    protected BlogCategoryRepository $categoryRepository;
    protected TagRepository $tagRepository;
    protected CommentService $commentService;

    public function __construct(
        PostService $postService,
        BlogCategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        CommentService $commentService
    ) {
        $this->postService = $postService;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->commentService = $commentService;
    }

    /**
     * Blog listing page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'latest');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('q');
        
        // Build query
        $query = \App\Modules\Blog\Models\Post::where('status', 'published')
            ->where('published_at', '<=', now());
        
        // Apply filters
        switch ($filter) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
                
            case 'articles':
                // Posts without YouTube video
                $query->where(function($q) {
                    $q->whereNull('youtube_url')
                      ->orWhere('youtube_url', '');
                });
                break;
                
            case 'videos':
                // Posts with YouTube video
                $query->whereNotNull('youtube_url')
                      ->where('youtube_url', '!=', '');
                break;
        }
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting (if not already sorted by filter)
        if ($filter !== 'popular') {
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'title':
                    $query->orderBy('title', 'asc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('published_at', 'desc');
                    break;
            }
        }
        
        // Paginate
        $posts = $query->with(['author', 'categories', 'tags', 'tickMarks', 'media'])->paginate($perPage)->appends($request->query());
        
        $featuredPosts = $this->postService->getFeaturedPosts(3);
        $popularPosts = $this->postService->getPopularPosts(5);
        $categories = $this->categoryRepository->getRoots();
        $popularTags = $this->tagRepository->getPopular(10);
        
        // Prepare SEO data for blog index page
        $blogTitle = \App\Models\SiteSetting::get('blog_title', 'Blog');
        $blogTagline = \App\Models\SiteSetting::get('blog_tagline', '');
        $blogImage = \App\Models\SiteSetting::get('blog_image');
        
        $seoData = [
            'title' => $blogTagline ? $blogTitle . ' | ' . $blogTagline : $blogTitle,
            'description' => \App\Models\SiteSetting::get('blog_description', 'Discover the latest articles and tips'),
            'keywords' => \App\Models\SiteSetting::get('blog_keywords', 'blog, articles, tips'),
            'og_image' => $blogImage ? asset('storage/' . $blogImage) : asset('images/og-default.jpg'),
            'og_type' => 'website',
            'canonical' => route('blog.index'),
        ];

        return view('frontend.blog.index', compact(
            'posts',
            'featuredPosts',
            'popularPosts',
            'categories',
            'popularTags',
            'filter',
            'seoData'
        ));
    }

    /**
     * Single post page
     */
    public function show($slug)
    {
        $post = $this->postService->getPostBySlug($slug);
        $post->load('author.authorProfile'); // Eager load author profile
        $post->load('products.variants', 'products.images', 'products.brand'); // Eager load products for Shop This Article
        $post->load('media'); // Eager load featured image media
        $relatedPosts = $post->relatedPosts(3);
        $popularPosts = $this->postService->getPopularPosts(5);
        $categories = $this->categoryRepository->getRoots();
        
        // Prepare SEO data - use post's SEO settings if exist, otherwise use defaults
        $blogTitle = \App\Models\SiteSetting::get('blog_title', 'Blog');
        
        $seoData = [
            'title' => !empty($post->meta_title) 
                ? $post->meta_title 
                : $post->title . ' | ' . $blogTitle,
            
            'description' => !empty($post->meta_description) 
                ? $post->meta_description 
                : (!empty($post->excerpt) 
                    ? \Illuminate\Support\Str::limit(strip_tags($post->excerpt), 160)
                    : \Illuminate\Support\Str::limit(strip_tags($post->content), 160)),
            
            'keywords' => !empty($post->meta_keywords) 
                ? $post->meta_keywords 
                : ($post->category ? $post->category->name . ', ' : '') . 'blog, article, ' . \App\Models\SiteSetting::get('blog_keywords', 'health, wellness'),
            
            'og_image' => ($post->media && $post->media->large_url)
                ? $post->media->large_url
                : ($post->featured_image 
                    ? asset('storage/' . $post->featured_image) 
                    : (\App\Models\SiteSetting::get('blog_image') 
                        ? asset('storage/' . \App\Models\SiteSetting::get('blog_image'))
                        : asset('images/og-default.jpg'))),
            
            'og_type' => 'article',
            'canonical' => url($post->slug),
            'author_name' => $post->author ? $post->author->name : null,
            'published_at' => $post->published_at,
            'updated_at' => $post->updated_at,
        ];

        return view('frontend.blog.show', compact(
            'post',
            'relatedPosts',
            'popularPosts',
            'categories',
            'seoData'
        ));
    }

    /**
     * Category archive page
     */
    public function category(Request $request, $slug)
    {
        $category = $this->categoryRepository->findBySlug($slug);
        $category->load('media'); // Eager load media library image
        
        // Get sidebar categories: if current category has children, show them; otherwise show root categories
        if ($category->children()->where('is_active', true)->count() > 0) {
            // Show subcategories of current category
            $categories = $category->children()
                ->where('is_active', true)
                ->withCount(['posts' => function($query) {
                    $query->where('status', 'published');
                }])
                ->orderBy('sort_order')
                ->get();
        } else {
            // Show root categories
            $categories = $this->categoryRepository->getRoots();
        }
        
        // Get filter parameters
        $search = $request->input('search');
        $sort = $request->input('sort', 'latest');
        $perPage = $request->input('per_page', 10);
        
        // Build query
        $query = $category->posts()->where('status', 'published');
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('published_at', 'desc');
                break;
        }
        
        // Paginate
        $posts = $query->with(['author', 'tags', 'tickMarks', 'media'])->paginate($perPage)->appends($request->query());
        
        // Prepare SEO data for blog category page - use category's SEO settings if exist, otherwise use defaults
        $blogTitle = SiteSetting::get('blog_title', 'Blog');
        
        $seoData = [
            'title' => !empty($category->meta_title) 
                ? $category->meta_title 
                : $category->name . ' | ' . $blogTitle,
            
            'description' => !empty($category->meta_description) 
                ? $category->meta_description 
                : (!empty($category->description) 
                    ? \Illuminate\Support\Str::limit(strip_tags($category->description), 160)
                    : 'Browse ' . $category->name . ' articles and posts. Discover the latest content in ' . $category->name),
            
            'keywords' => !empty($category->meta_keywords) 
                ? $category->meta_keywords 
                : $category->name . ', ' . $category->name . ' blog, ' . $category->name . ' articles, ' . SiteSetting::get('blog_keywords', 'blog, articles'),
            
            'og_image' => ($category->media && $category->media->large_url)
                ? $category->media->large_url
                : ($category->image_path
                    ? asset('storage/' . $category->image_path)
                    : (SiteSetting::get('blog_image')
                        ? asset('storage/' . SiteSetting::get('blog_image'))
                        : (\App\Models\SiteSetting::get('site_logo')
                            ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
                            : asset('images/og-default.jpg')))),
            
            'og_type' => 'website',
            'canonical' => route('blog.category', $category->slug),
        ];

        return view('frontend.blog.category', compact('category', 'posts', 'categories', 'seoData'));
    }

    /**
     * Tag archive page
     */
    public function tag($slug)
    {
        $tag = $this->tagRepository->findBySlug($slug);
        $posts = $this->postService->getPostsByTag($tag->id, config('app.paginate', 10));
        $categories = $this->categoryRepository->getRoots();

        return view('frontend.blog.tag', compact('tag', 'posts', 'categories'));
    }

    /**
     * Search results page
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $sort = $request->input('sort', 'latest');
        $perPage = $request->input('per_page', 10);
        
        // Build query
        $postsQuery = \App\Modules\Blog\Models\Post::where('status', 'published')
            ->where('published_at', '<=', now());
        
        // Apply search
        if ($query) {
            $postsQuery->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }
        
        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $postsQuery->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $postsQuery->orderBy('views_count', 'desc');
                break;
            case 'title':
                $postsQuery->orderBy('title', 'asc');
                break;
            case 'latest':
            default:
                $postsQuery->orderBy('published_at', 'desc');
                break;
        }
        
        // Paginate
        $posts = $postsQuery->with(['author', 'category', 'tags', 'tickMarks', 'media'])
            ->paginate($perPage)
            ->appends($request->query());
        
        $categories = $this->categoryRepository->getRoots();

        return view('frontend.blog.search', compact('query', 'posts', 'categories'));
    }

    /**
     * Store comment
     */
    public function storeComment(Request $request, $postId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:blog_comments,id',
            'guest_name' => 'required_without:user_id|string|max:255',
            'guest_email' => 'required_without:user_id|email|max:255',
            'guest_website' => 'nullable|url|max:255',
        ]);

        $validated['blog_post_id'] = $postId;
        $comment = $this->commentService->createComment($validated);

        return back()->with('success', 'আপনার মন্তব্য সফলভাবে জমা হয়েছে। অনুমোদনের অপেক্ষায় রয়েছে।');
    }

    /**
     * Author profile page
     */
    public function author(Request $request, $slug)
    {
        $authorProfile = \App\Models\AuthorProfile::where('slug', $slug)->firstOrFail();
        $author = User::where('id', $authorProfile->user_id)->firstOrFail();
        $author->load('authorProfile');
        $authorProfile->load('media'); // Eager load media library image
        
        // Get sorting parameter
        $sort = $request->get('sort', 'newest');
        
        // Get published posts by this author with sorting
        $postsQuery = $author->publishedPosts()
            ->with(['category', 'tags', 'media']);
        
        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $postsQuery->oldest('published_at');
                break;
            case 'most_viewed':
                $postsQuery->orderBy('views_count', 'desc');
                break;
            case 'most_popular':
                // Most popular = combination of views and comments
                $postsQuery->withCount('comments')
                    ->orderByRaw('(views_count + comments_count * 10) DESC');
                break;
            case 'newest':
            default:
                $postsQuery->latest('published_at');
                break;
        }
        
        $posts = $postsQuery->paginate(config('app.paginate', 12))->appends(['sort' => $sort]);
        
        // Get author stats
        $totalPosts = $author->publishedPosts()->count();
        $totalViews = $author->publishedPosts()->sum('views_count');
        $totalComments = \App\Modules\Blog\Models\Comment::whereHas('post', function($query) use ($author) {
            $query->where('author_id', $author->id);
        })->where('status', 'approved')->count();
        
        $categories = $this->categoryRepository->getRoots();
        
        // Prepare SEO data for author profile page
        $jobTitle = $authorProfile->job_title ?? 'Author Profile';
        
        $seoData = [
            'title' => $author->name . ' | ' . $jobTitle,
            'description' => $authorProfile->bio ? \Illuminate\Support\Str::limit(strip_tags($authorProfile->bio), 160) : 'View profile and articles by ' . $author->name,
            'keywords' => $author->name . ', author, blog, articles, writer' . ($authorProfile->job_title ? ', ' . $authorProfile->job_title : ''),
            'og_image' => ($authorProfile->media && $authorProfile->media->large_url)
                ? $authorProfile->media->large_url
                : ($authorProfile->avatar 
                    ? asset('storage/' . $authorProfile->avatar) 
                    : asset('images/default-avatar.jpg')),
            'og_type' => 'profile',
            'canonical' => route('blog.author', $authorProfile->slug),
            'author_name' => $author->name,
        ];
        
        return view('frontend.blog.author', compact(
            'author',
            'posts',
            'totalPosts',
            'totalViews',
            'totalComments',
            'categories',
            'sort',
            'seoData'
        ));
    }
}
