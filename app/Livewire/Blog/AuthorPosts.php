<?php

namespace App\Livewire\Blog;

use App\Modules\Blog\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AuthorPosts extends Component
{
    use WithPagination;

    public $authorId;
    public $sort = 'newest';
    
    protected $queryString = [
        'sort' => ['except' => 'newest'],
    ];

    public function mount($authorId)
    {
        $this->authorId = $authorId;
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Post::where('author_id', $this->authorId)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['category', 'tags', 'tickMarks']); // Eager load relationships

        // Apply sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'most_viewed':
                $query->orderBy('views_count', 'desc');
                break;
            case 'most_popular':
                $query->withCount('comments')
                      ->orderBy('comments_count', 'desc')
                      ->orderBy('views_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('published_at', 'desc');
                break;
        }

        $posts = $query->paginate(12);
        $totalPosts = Post::where('author_id', $this->authorId)
            ->where('status', 'published')
            ->count();

        return view('livewire.blog.author-posts', [
            'posts' => $posts,
            'totalPosts' => $totalPosts,
        ]);
    }
}
