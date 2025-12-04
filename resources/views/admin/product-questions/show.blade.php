@extends('layouts.admin')

@section('title', 'Question Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Question Details</h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.product-questions.index') }}" class="text-blue-600 hover:text-blue-800">Questions</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-500">Details</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.product-questions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Question Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Question</h3>
                    @if($question->status == 'approved')
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($question->status == 'pending')
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @endif
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $question->question }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1"><strong>Asked by:</strong> {{ $question->author_name }}</p>
                            @if($question->user)
                                <p class="text-xs text-gray-500">Registered User</p>
                            @else
                                <p class="text-xs text-gray-500">Guest ({{ $question->user_email }})</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1"><strong>Date:</strong> {{ $question->created_at->format('M d, Y h:i A') }}</p>
                            <p class="text-sm text-gray-600"><strong>Product:</strong> 
                                <a href="{{ route('products.show', $question->product->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    {{ $question->product->name }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-thumbs-up text-green-600"></i> {{ $question->helpful_count }} Helpful
                        </span>
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-thumbs-down text-red-600"></i> {{ $question->not_helpful_count }} Not Helpful
                        </span>
                    </div>

                    @if($question->status == 'pending')
                        <div class="flex gap-2">
                            <form action="{{ route('admin.questions.approve', $question->id) }}" method="POST" id="approve-question-{{ $question->id }}">
                                @csrf
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                            detail: { 
                                                title: 'Approve Question', 
                                                message: 'Are you sure you want to approve this question? It will be visible to all users.',
                                                onConfirm: () => document.getElementById('approve-question-{{ $question->id }}').submit()
                                            } 
                                        }))" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-check mr-1"></i> Approve Question
                                </button>
                            </form>
                            <form action="{{ route('admin.questions.reject', $question->id) }}" method="POST" id="reject-question-{{ $question->id }}">
                                @csrf
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                            detail: { 
                                                title: 'Reject Question', 
                                                message: 'Are you sure you want to reject this question? It will be hidden from users.',
                                                onConfirm: () => document.getElementById('reject-question-{{ $question->id }}').submit()
                                            } 
                                        }))" 
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-times mr-1"></i> Reject Question
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Answers Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Answers ({{ $question->answers->count() }})
                    </h3>
                </div>
                <div class="p-6">
                    @forelse($question->answers as $answer)
                        <div class="border rounded-lg p-4 mb-4 {{ $answer->is_best_answer ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex flex-wrap gap-2">
                                    <strong class="text-gray-900">{{ $answer->author_name }}</strong>
                                    @if($answer->is_verified_purchase)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle"></i> Verified Purchase
                                        </span>
                                    @endif
                                    @if($answer->is_best_answer)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-star"></i> Best Answer
                                        </span>
                                    @endif
                                    @if($answer->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($answer->status == 'rejected')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                    @endif
                                </div>
                                <small class="text-gray-500">{{ $answer->created_at->diffForHumans() }}</small>
                            </div>
                            
                            <p class="text-gray-700 mb-3">{{ $answer->answer }}</p>
                            
                            <div class="flex items-center gap-4 mb-3">
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-thumbs-up text-green-600"></i> {{ $answer->helpful_count }}
                                </span>
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-thumbs-down text-red-600"></i> {{ $answer->not_helpful_count }}
                                </span>
                            </div>

                            <div class="flex gap-2">
                                @if($answer->status == 'pending')
                                    <form action="{{ route('admin.answers.approve', $answer->id) }}" method="POST" id="approve-answer-{{ $answer->id }}">
                                        @csrf
                                        <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                                    detail: { 
                                                        title: 'Approve Answer', 
                                                        message: 'Are you sure you want to approve this answer?',
                                                        onConfirm: () => document.getElementById('approve-answer-{{ $answer->id }}').submit()
                                                    } 
                                                }))" 
                                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.answers.reject', $answer->id) }}" method="POST" id="reject-answer-{{ $answer->id }}">
                                        @csrf
                                        <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                                    detail: { 
                                                        title: 'Reject Answer', 
                                                        message: 'Are you sure you want to reject this answer?',
                                                        onConfirm: () => document.getElementById('reject-answer-{{ $answer->id }}').submit()
                                                    } 
                                                }))" 
                                                class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                @endif
                                @if(!$answer->is_best_answer && $answer->status == 'approved')
                                    <form action="{{ route('admin.answers.best', $answer->id) }}" method="POST" id="best-answer-{{ $answer->id }}">
                                        @csrf
                                        <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                                    detail: { 
                                                        title: 'Mark as Best Answer', 
                                                        message: 'Are you sure you want to mark this as the best answer? Any existing best answer will be unmarked.',
                                                        onConfirm: () => document.getElementById('best-answer-{{ $answer->id }}').submit()
                                                    } 
                                                }))" 
                                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                            <i class="fas fa-star"></i> Mark as Best
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-comments text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">No answers yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Answers</span>
                            <strong class="text-gray-900">{{ $question->answer_count }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Approved Answers</span>
                            <strong class="text-gray-900">{{ $question->answers->where('status', 'approved')->count() }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pending Answers</span>
                            <strong class="text-gray-900">{{ $question->answers->where('status', 'pending')->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('products.show', $question->product->slug) }}#qa" target="_blank" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-external-link-alt mr-1"></i> View on Frontend
                    </a>
                    <form action="{{ route('admin.product-questions.destroy', $question->id) }}" method="POST" id="delete-question-{{ $question->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                    detail: { 
                                        title: 'Delete Question', 
                                        message: 'Are you sure you want to delete this question and all its answers? This action cannot be undone.',
                                        onConfirm: () => document.getElementById('delete-question-{{ $question->id }}').submit()
                                    } 
                                }))" 
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-1"></i> Delete Question
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
