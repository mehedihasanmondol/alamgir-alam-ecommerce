{{--
/**
 * Author Profile Feedback Section
 * 60/40 Layout: Featured Feedback (60%) | Appointment Coming Soon (40%)
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-11-25
 */
--}}

@php
    // Check if feedback is enabled
    $feedbackEnabled = \App\Models\SiteSetting::get('feedback_enabled', '1') === '1';
    
    // If feedback is disabled, don't show anything
    if (!$feedbackEnabled) {
        return;
    }
    
    $feedbackPerPage = (int) \App\Models\SiteSetting::get('feedback_per_author_page', '5');
    $ratingEnabled = \App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1';
    $showImages = \App\Models\SiteSetting::get('feedback_show_images', '1') === '1';
    $feedbackTitle = \App\Models\SiteSetting::get('feedback_title', 'Customer Feedback');
    $feedbackTimeEnabled = \App\Models\SiteSetting::get('feedback_time_enabled', '1') === '1';
    
    // Check if appointments are enabled
    $appointmentEnabled = \App\Models\SiteSetting::get('appointment_enabled', '1') === '1';
    
    // Get width settings
    $appointmentWidth = \App\Models\SiteSetting::get('author_page_appointment_width', 'full');
    $feedbackWidth = \App\Models\SiteSetting::get('author_page_feedback_width', 'full');
    
    // Map width to Tailwind classes
    $widthMap = [
        'full'          => 'lg:col-span-12',
        'half'          => 'lg:col-span-6',
        'one-third'     => 'lg:col-span-5',
        'two-third'     => 'lg:col-span-7',
        'quarter'       => 'lg:col-span-3',
        'three-quarter' => 'lg:col-span-9',
    ];
    
    $appointmentClass = $widthMap[$appointmentWidth] ?? 'lg:col-span-12';
    $feedbackClass = $widthMap[$feedbackWidth] ?? 'lg:col-span-12';
    
    // Check if both are full width for border divider
    $bothFullWidth = ($appointmentWidth === 'full' && $feedbackWidth === 'full' && $appointmentEnabled);
    
    $featuredFeedback = \App\Models\Feedback::approved()
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->limit($feedbackPerPage)
        ->get();
@endphp

<div class="bg-white border-t border-gray-200 p-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Featured Feedback Section --}}
        <div class="{{ $feedbackClass }} {{ $bothFullWidth ? 'border-b lg:border-b-0 lg:border-r border-gray-200 pb-6 lg:pb-0 lg:pr-6' : '' }}">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">{{ $feedbackTitle }}</h2>
                
            </div>

            @if($featuredFeedback->count() > 0)
                <div class="space-y-4 mb-2">
                    @foreach($featuredFeedback as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-sm font-semibold text-white">
                                        {{ strtoupper(substr($item->customer_display_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $item->customer_display_name }}</div>
                                        @if($ratingEnabled)
                                        <div class="flex items-center mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @if($feedbackTimeEnabled)
                                <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                @endif
                            </div>

                            {{-- Title --}}
                            @if($item->title)
                                <h4 class="font-semibold text-gray-900 mb-2 text-sm">{{ $item->title }}</h4>
                            @endif

                            {{-- Feedback Content --}}
                            <p class="text-gray-700 text-sm line-clamp-2">{{ $item->feedback }}</p>

                            {{-- Images Preview --}}
                            @if($showImages && $item->images && count($item->images) > 0)
                                <div class="flex gap-2 mt-3">
                                    @foreach(array_slice($item->images, 0, 3) as $image)
                                        @php
                                            $thumbPath = is_array($image) ? ($image['thumbnail'] ?? $image['original']) : $image;
                                        @endphp
                                        <img src="{{ asset('storage/' . $thumbPath) }}" 
                                             alt="Feedback image" 
                                             class="w-12 h-12 object-cover rounded border border-gray-200">
                                    @endforeach
                                    @if(count($item->images) > 3)
                                        <div class="w-12 h-12 bg-gray-100 rounded border border-gray-200 flex items-center justify-center text-xs text-gray-600">
                                            +{{ count($item->images) - 3 }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('feedback.index') }}" class="text-sm  text-blue-600 hover:text-blue-700 font-medium">
                    View More →
                </a>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <p class="text-gray-500">No feedback yet</p>
                    <a href="{{ route('feedback.index') }}" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">
                        Be the first to share your experience
                    </a>
                </div>
            @endif
        </div>

        {{-- Appointment Form Section (Sticky) - Only show if enabled --}}
        @if($appointmentEnabled)
        <div class="{{ $appointmentClass }}">
            <div class="lg:sticky lg:top-24">
                @livewire('appointment.appointment-form')
            </div>
        </div>
        @endif
    </div>
</div>
