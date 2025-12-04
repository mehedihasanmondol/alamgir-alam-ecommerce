<?php

namespace App\Livewire\Blog;

use App\Modules\Blog\Models\Comment;
use App\Modules\Blog\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CommentSection extends Component
{
    public Post $post;
    
    #[Validate('required|string|max:255')]
    public $guest_name = '';
    
    #[Validate('required|email|max:255')]
    public $guest_email = '';
    
    #[Validate('required|string|min:3|max:1000')]
    public $content = '';
    
    public $replyingTo = null;
    public $showSuccess = false;
    public $perPage = 10;
    public $loadedComments = 10;

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function loadMore()
    {
        $this->loadedComments += $this->perPage;
    }

    public function submitComment()
    {
        // Check if user has pending comments on this post
        $hasPendingComment = false;
        
        if (auth()->check()) {
            $hasPendingComment = Comment::where('blog_post_id', $this->post->id)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->exists();
        } else {
            // Check by guest email
            if ($this->guest_email) {
                $hasPendingComment = Comment::where('blog_post_id', $this->post->id)
                    ->where('guest_email', $this->guest_email)
                    ->where('status', 'pending')
                    ->exists();
            }
        }
        
        if ($hasPendingComment) {
            $this->addError('content', 'You already have a pending comment on this post. Please wait for it to be approved before submitting another comment.');
            return;
        }
        
        // Validate based on auth status
        if (auth()->guest()) {
            $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'content' => 'required|string|min:3|max:1000',
            ]);
        } else {
            $this->validate([
                'content' => 'required|string|min:3|max:1000',
            ]);
        }

        // Create comment
        $comment = new Comment();
        $comment->blog_post_id = $this->post->id;
        $comment->content = $this->content;
        $comment->status = 'pending'; // Needs approval
        
        if (auth()->check()) {
            $comment->user_id = auth()->id();
        } else {
            $comment->guest_name = $this->guest_name;
            $comment->guest_email = $this->guest_email;
        }
        
        if ($this->replyingTo) {
            $comment->parent_id = $this->replyingTo;
        }
        
        $comment->save();

        // Reset form
        $this->reset(['content', 'guest_name', 'guest_email', 'replyingTo']);
        
        // Show success message
        $this->showSuccess = true;
        
        // Refresh comments
        $this->post->refresh();
        
        // Hide success message after 5 seconds
        $this->dispatch('comment-posted');
    }

    public function replyTo($commentId)
    {
        $this->replyingTo = $commentId;
        $this->dispatch('scroll-to-form');
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
    }

    public function render()
    {
        $totalComments = $this->post->approvedComments()->count();
        $comments = $this->post->approvedComments()
            ->with('approvedReplies')
            ->latest()
            ->take($this->loadedComments)
            ->get();
        
        $remainingComments = max(0, $totalComments - $this->loadedComments);
        
        return view('livewire.blog.comment-section', [
            'comments' => $comments,
            'commentsCount' => $totalComments,
            'remainingComments' => $remainingComments,
        ]);
    }
}
