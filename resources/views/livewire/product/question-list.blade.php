<div>
    <!-- Search and Filters -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
        <div class="md:col-span-6">
            <div class="relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search questions..." 
                       class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="md:col-span-6">
            <select wire:model.live="sortBy" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="recent">Most Recent</option>
                <option value="helpful">Most Helpful</option>
                <option value="most_answers">Most Answers</option>
            </select>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Questions List -->
    <div class="space-y-6">
        @forelse($questions as $question)
            <div class="border-b border-gray-200 pb-6">
                <!-- Question -->
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900 flex-1 pr-4">
                        {{ $question->question }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button wire:click="toggleAnswerForm({{ $question->id }})" 
                                class="px-4 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">
                            Answer
                        </button>
                    </div>
                </div>

                <!-- Question Meta -->
                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                    <span>{{ $question->author_name }}</span>
                    <span>{{ $question->created_at->diffForHumans() }}</span>
                    <span>{{ $question->answer_count }} {{ Str::plural('answer', $question->answer_count) }}</span>
                </div>

                <!-- Question Votes -->
                <div class="flex items-center space-x-4 mb-4">
                    <button wire:click="voteHelpful('question', {{ $question->id }}, true)" 
                            class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        <span>{{ $question->helpful_count }}</span>
                    </button>
                    <button wire:click="voteHelpful('question', {{ $question->id }}, false)" 
                            class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                        </svg>
                        <span>{{ $question->not_helpful_count }}</span>
                    </button>
                </div>

                <!-- Answer Form -->
                @if($showAnswerForm[$question->id] ?? false)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <textarea wire:model="answerText.{{ $question->id }}" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Write your answer..."></textarea>
                        @error("answerText.{$question->id}")
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex items-center space-x-2">
                            <button wire:click="submitAnswer({{ $question->id }})" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Submit Answer
                            </button>
                            <button wire:click="toggleAnswerForm({{ $question->id }})" 
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Answers -->
                @if($question->approvedAnswers->count() > 0)
                    <div class="ml-4 pl-4 border-l-2 border-gray-200 space-y-4">
                        @foreach($question->approvedAnswers as $answer)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600">
                                    {{ substr($answer->author_name, 0, 2) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $answer->author_name }}</span>
                                        <span class="text-xs text-gray-500">{{ $answer->created_at->diffForHumans() }}</span>
                                        @if($answer->is_verified_purchase)
                                            <span class="flex items-center text-xs text-green-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Verified Purchase
                                            </span>
                                        @endif
                                        @if($answer->is_best_answer)
                                            <span class="flex items-center text-xs text-blue-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                Best Answer
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700 text-sm mb-2">{{ $answer->answer }}</p>
                                    <div class="flex items-center space-x-4">
                                        <button wire:click="voteHelpful('answer', {{ $answer->id }}, true)" 
                                                class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                            <span>{{ $answer->helpful_count }}</span>
                                        </button>
                                        <button wire:click="voteHelpful('answer', {{ $answer->id }}, false)" 
                                                class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                                            </svg>
                                            <span>{{ $answer->not_helpful_count }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No questions yet</h3>
                <p class="mt-1 text-sm text-gray-500">Be the first to ask a question about this product.</p>
            </div>
        @endforelse
    </div>

    <!-- Load More Section -->
    @if($questions->count() > 0)
        <div class="mt-6">
            <!-- Showing count -->
            <div class="text-center text-sm text-gray-600 mb-4">
                Showing <span class="font-semibold text-gray-900">{{ $loadedCount }}</span> of 
                <span class="font-semibold text-gray-900">{{ $totalCount }}</span> 
                {{ Str::plural('question', $totalCount) }}
            </div>

            <!-- Load More Button -->
            @if($hasMore)
                <div class="text-center">
                    <button wire:click="loadMore" 
                            wire:loading.attr="disabled"
                            class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span wire:loading.remove wire:target="loadMore">Load More Questions</span>
                        <span wire:loading wire:target="loadMore" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
