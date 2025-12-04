# Tick Mark System - Usage Examples

## Example 1: Display Tick Marks in Post List

```blade
<!-- resources/views/admin/blog/posts/index.blade.php -->
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Tick Marks</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $post)
        <tr>
            <td>{{ $post->title }}</td>
            <td>
                <div class="flex items-center gap-1">
                    @forelse($post->tickMarks as $tickMark)
                        <span class="inline-flex items-center p-1 rounded {{ $tickMark->bg_color }} {{ $tickMark->text_color }}" 
                              title="{{ $tickMark->label }}">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                {!! $tickMark->getIconHtml() !!}
                            </svg>
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">No marks</span>
                    @endforelse
                </div>
            </td>
            <td>
                <a href="{{ route('admin.blog.posts.edit', $post) }}">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

## Example 2: Display on Frontend Blog Post

```blade
<!-- resources/views/frontend/blog/show.blade.php -->
<article>
    <header>
        <h1>{{ $post->title }}</h1>
        
        <!-- Tick Marks -->
        @if($post->tickMarks->count() > 0)
        <div class="flex items-center gap-2 mt-2">
            @foreach($post->tickMarks as $tickMark)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $tickMark->bg_color }} {{ $tickMark->text_color }}">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        {!! $tickMark->getIconHtml() !!}
                    </svg>
                    {{ $tickMark->label }}
                </span>
            @endforeach
        </div>
        @endif
    </header>
    
    <div class="content">
        {!! $post->content !!}
    </div>
</article>
```

## Example 3: Filter Posts by Tick Mark

```php
// In your controller
public function filterByTickMark($tickMarkSlug)
{
    $tickMark = TickMark::where('slug', $tickMarkSlug)->firstOrFail();
    
    $posts = Post::whereHas('tickMarks', function($query) use ($tickMark) {
        $query->where('blog_tick_marks.id', $tickMark->id);
    })
    ->published()
    ->latest()
    ->paginate(10);
    
    return view('blog.filtered', compact('posts', 'tickMark'));
}
```

## Example 4: Programmatically Assign Tick Marks

```php
// In a controller or service
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\TickMark;

// Method 1: Using the model directly
$post = Post::find(1);
$verifiedTickMark = TickMark::where('slug', 'verified')->first();
$post->attachTickMark($verifiedTickMark->id, 'Verified by admin');

// Method 2: Using the service
use App\Modules\Blog\Services\TickMarkService;

$tickMarkService = app(TickMarkService::class);
$tickMarkService->attachTickMark($postId, $tickMarkId, 'Optional notes');

// Method 3: Sync multiple at once
$post->syncTickMarks([1, 2, 3]); // Replace all with these IDs
```

## Example 5: Create Tick Mark Programmatically

```php
use App\Modules\Blog\Models\TickMark;

$tickMark = TickMark::create([
    'name' => 'Breaking News',
    'label' => 'Breaking News',
    'description' => 'Latest breaking news stories',
    'icon' => 'lightning-bolt',
    'color' => 'red',
    'bg_color' => 'bg-red-500',
    'text_color' => 'text-white',
    'is_active' => true,
    'is_system' => false,
    'sort_order' => 10,
]);
```

## Example 6: Bulk Operations

```php
// Attach a tick mark to multiple posts
use App\Modules\Blog\Services\TickMarkService;

$tickMarkService = app(TickMarkService::class);
$postIds = [1, 2, 3, 4, 5];
$tickMarkId = 1; // Verified

$affected = $tickMarkService->bulkAttachTickMark($postIds, $tickMarkId);
// Returns number of posts affected

// Detach from multiple posts
$affected = $tickMarkService->bulkDetachTickMark($postIds, $tickMarkId);
```

## Example 7: Get Posts by Tick Mark

```php
// Using the service
use App\Modules\Blog\Services\TickMarkService;

$tickMarkService = app(TickMarkService::class);
$verifiedPosts = $tickMarkService->getPostsByTickMark('verified', 10);

// Or using Eloquent directly
$tickMark = TickMark::where('slug', 'verified')->first();
$posts = $tickMark->posts()
    ->published()
    ->latest()
    ->paginate(10);
```

## Example 8: Display Tick Mark Statistics

```blade
<!-- Admin Dashboard Widget -->
<div class="grid grid-cols-4 gap-4">
    @foreach($tickMarks as $tickMark)
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded {{ $tickMark->bg_color }} {{ $tickMark->text_color }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        {!! $tickMark->getIconHtml() !!}
                    </svg>
                    {{ $tickMark->label }}
                </span>
            </div>
            <div class="text-2xl font-bold">
                {{ $tickMark->posts_count }}
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">posts</p>
    </div>
    @endforeach
</div>
```

## Example 9: Conditional Display

```blade
<!-- Show different content based on tick marks -->
@if($post->hasTickMark('premium'))
    <div class="premium-content">
        <p>This is premium content. Subscribe to read more!</p>
        <a href="/subscribe" class="btn">Subscribe Now</a>
    </div>
@else
    <div class="content">
        {!! $post->content !!}
    </div>
@endif

<!-- Show badge if verified -->
@if($post->hasTickMark('verified'))
    <div class="verified-badge">
        ✓ This content has been verified by our editorial team
    </div>
@endif
```

## Example 10: API Response

```php
// In your API controller
public function show($id)
{
    $post = Post::with('tickMarks')->findOrFail($id);
    
    return response()->json([
        'id' => $post->id,
        'title' => $post->title,
        'content' => $post->content,
        'tick_marks' => $post->tickMarks->map(function($tickMark) {
            return [
                'id' => $tickMark->id,
                'name' => $tickMark->name,
                'label' => $tickMark->label,
                'color' => $tickMark->color,
                'icon' => $tickMark->icon,
            ];
        }),
    ]);
}
```

## Example 11: Search Posts with Specific Tick Marks

```php
// Search for posts that have both "verified" AND "editor-choice"
$posts = Post::whereHas('tickMarks', function($query) {
    $query->where('slug', 'verified');
})
->whereHas('tickMarks', function($query) {
    $query->where('slug', 'editor-choice');
})
->get();

// Search for posts that have "verified" OR "trending"
$posts = Post::whereHas('tickMarks', function($query) {
    $query->whereIn('slug', ['verified', 'trending']);
})
->get();
```

## Example 12: Custom Blade Component

```php
// app/View/Components/TickMarkBadge.php
namespace App\View\Components;

use Illuminate\View\Component;

class TickMarkBadge extends Component
{
    public $tickMark;
    public $size;
    
    public function __construct($tickMark, $size = 'md')
    {
        $this->tickMark = $tickMark;
        $this->size = $size;
    }
    
    public function render()
    {
        return view('components.tick-mark-badge');
    }
}
```

```blade
<!-- resources/views/components/tick-mark-badge.blade.php -->
@php
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];
@endphp

<span class="inline-flex items-center rounded-full font-medium {{ $tickMark->bg_color }} {{ $tickMark->text_color }} {{ $sizeClasses[$size] }}">
    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
        {!! $tickMark->getIconHtml() !!}
    </svg>
    {{ $tickMark->label }}
</span>
```

```blade
<!-- Usage -->
@foreach($post->tickMarks as $tickMark)
    <x-tick-mark-badge :tick-mark="$tickMark" size="md" />
@endforeach
```

---

These examples cover most common use cases. Mix and match as needed for your specific requirements!
