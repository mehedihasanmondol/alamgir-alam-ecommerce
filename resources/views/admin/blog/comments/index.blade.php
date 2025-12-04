@extends('layouts.admin')

@section('title', 'Comment Moderation')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Comment Moderation</h1>
        <p class="text-gray-600 mt-1">Manage and moderate blog comments</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $counts['all'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $counts['approved'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Spam</p>
            <p class="text-2xl font-bold text-red-600">{{ $counts['spam'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500 text-sm">Trash</p>
            <p class="text-2xl font-bold text-gray-600">{{ $counts['trash'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="spam" {{ request('status') == 'spam' ? 'selected' : '' }}>Spam</option>
                    <option value="trash" {{ request('status') == 'trash' ? 'selected' : '' }}>Trash</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                Filter
            </button>
            <a href="{{ route('admin.blog.comments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                Reset
            </a>
        </form>
    </div>

    <!-- Comments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($comments as $comment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $comment->commenter_name }}</div>
                        <div class="text-sm text-gray-500">{{ $comment->commenter_email }}</div>
                        @if($comment->isGuest())
                            <span class="text-xs text-gray-400">(Guest)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ Str::limit($comment->content, 100) }}</div>
                        <div class="text-xs text-gray-500 mt-1">IP: {{ $comment->ip_address }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('products.show', $comment->post->slug) }}" target="_blank"
                           class="text-sm text-blue-600 hover:text-blue-800">
                            {{ Str::limit($comment->post->title, 40) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($comment->status === 'approved')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        @elseif($comment->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($comment->status === 'spam')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Spam
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Trash
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $comment->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($comment->status === 'pending')
                            <button onclick="approveComment({{ $comment->id }})" 
                                    class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                        @endif
                        @if($comment->status !== 'spam')
                            <button onclick="markAsSpam({{ $comment->id }})" 
                                    class="text-orange-600 hover:text-orange-900 mr-3">Spam</button>
                        @endif
                        @if($comment->status !== 'trash')
                            <button onclick="moveToTrash({{ $comment->id }})" 
                                    class="text-gray-600 hover:text-gray-900 mr-3">Trash</button>
                        @endif
                        <button onclick="deleteComment({{ $comment->id }})" 
                                class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <p class="mt-2 text-sm">No comments found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($comments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $comments->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function approveComment(id) {
    if (confirm('Approve this comment?')) {
        fetch(`/admin/blog/comments/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function markAsSpam(id) {
    if (confirm('Mark this comment as spam?')) {
        fetch(`/admin/blog/comments/${id}/spam`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function moveToTrash(id) {
    if (confirm('Move this comment to trash?')) {
        fetch(`/admin/blog/comments/${id}/trash`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteComment(id) {
    if (confirm('Permanently delete this comment? This cannot be undone.')) {
        fetch(`/admin/blog/comments/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection
