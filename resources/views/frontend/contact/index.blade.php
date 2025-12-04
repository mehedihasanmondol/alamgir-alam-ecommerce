@extends('layouts.app')

@section('title', \App\Models\ContactSetting::get('seo_title', 'Contact Us - ' . config('app.name')))

@section('description', \App\Models\ContactSetting::get('seo_description', 'Get in touch with us. Contact us for any inquiries, support, or feedback.'))

@section('keywords', \App\Models\ContactSetting::get('seo_keywords', 'contact, support, customer service'))

@section('og_type', 'website')
@section('og_title', \App\Models\ContactSetting::get('seo_og_title', \App\Models\ContactSetting::get('seo_title', 'Contact Us')))
@section('og_description', \App\Models\ContactSetting::get('seo_og_description', \App\Models\ContactSetting::get('seo_description', 'Contact us for any inquiries.')))
@if(\App\Models\ContactSetting::get('seo_image'))
@section('og_image', asset('storage/' . \App\Models\ContactSetting::get('seo_image')))
@endif

@section('twitter_card', 'summary_large_image')
@section('twitter_title', \App\Models\ContactSetting::get('seo_twitter_title', \App\Models\ContactSetting::get('seo_title', 'Contact Us')))
@section('twitter_description', \App\Models\ContactSetting::get('seo_twitter_description', \App\Models\ContactSetting::get('seo_description', 'Contact us for any inquiries.')))
@if(\App\Models\ContactSetting::get('seo_image'))
@section('twitter_image', asset('storage/' . \App\Models\ContactSetting::get('seo_image')))
@endif

@section('content')


<!-- Main Content Area -->
<div class="container mx-auto px-4 py-12">
    <!-- Row 1: Get in Touch (50%) + Contact Form (50%) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Left 50%: Get in Touch (Contact Info + Chambers Merged) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-comments text-blue-600 mr-2"></i>Get in Touch</h2>
            
            <!-- Contact Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                @if($settings['email'])
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 mb-0.5">Email</p>
                        <a href="mailto:{{ $settings['email'] }}" class="text-sm text-gray-800 hover:text-blue-600 font-medium">{{ $settings['email'] }}</a>
                    </div>
                </div>
                @endif

                @if($settings['phone'])
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-phone text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 mb-0.5">Phone</p>
                        <a href="tel:{{ $settings['phone'] }}" class="text-sm text-gray-800 hover:text-green-600 font-medium">{{ $settings['phone'] }}</a>
                    </div>
                </div>
                @endif

                @if($settings['whatsapp'])
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-whatsapp text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 mb-0.5">WhatsApp</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}" target="_blank" class="text-sm text-gray-800 hover:text-green-600 font-medium">{{ $settings['whatsapp'] }}</a>
                    </div>
                </div>
                @endif

                @if($settings['address'])
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 mb-0.5">Address</p>
                        <p class="text-sm text-gray-800">
                            {{ $settings['address'] }}
                            @if(!empty($settings['city']) || !empty($settings['state']) || !empty($settings['zip']))
                                <br>
                                @if(!empty($settings['city'])){{ $settings['city'] }}@endif
                                @if(!empty($settings['state'])){{ !empty($settings['city']) ? ', ' : '' }}{{ $settings['state'] }}@endif
                                @if(!empty($settings['zip'])) {{ $settings['zip'] }}@endif
                            @endif
                            @if(!empty($settings['country']))
                                <br>{{ $settings['country'] }}
                            @endif
                        </p>
                    </div>
                </div>
                @endif

                @if($settings['business_hours'])
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 mb-0.5">Business Hours</p>
                        <p class="text-sm text-gray-800">{{ $settings['business_hours'] }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Our Chambers -->
            @if($chambers && $chambers->count() > 0)
            <div class="border-t border-gray-200 pt-5 mt-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-building text-blue-600 mr-2"></i>Our Chambers</h3>
                <div class="space-y-3">
                    @foreach($chambers as $chamber)
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                        <h4 class="font-semibold text-base text-gray-800 mb-2">{{ $chamber->name }}</h4>
                        @if($chamber->address)
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>{{ $chamber->address }}
                        </p>
                        @endif
                        <div class="flex flex-wrap gap-3 text-sm mb-2">
                            @if($chamber->phone)
                            <a href="tel:{{ $chamber->phone }}" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-phone mr-1"></i>{{ $chamber->phone }}
                            </a>
                            @endif
                            @if($chamber->email)
                            <a href="mailto:{{ $chamber->email }}" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-envelope mr-1"></i>{{ $chamber->email }}
                            </a>
                            @endif
                        </div>
                        @if(!empty($chamber->operating_hours))
                        <div class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-clock text-orange-600 mr-2"></i>
                            @php
                                $hours = $chamber->operating_hours;
                                if (is_string($hours)) {
                                    $decoded = json_decode($hours, true);
                                    $hours = $decoded ?? $hours;
                                }
                                if (is_array($hours)) {
                                    foreach ($hours as $day => $time) {
                                        if (is_array($time)) {
                                            // Filter out empty, null, -1 values (both string and int)
                                            $filtered = array_filter($time, function($val) {
                                                if ($val === null || $val === '' || $val === '-1' || $val === -1) {
                                                    return false;
                                                }
                                                return true;
                                            });
                                            // Re-index array after filtering
                                            $filtered = array_values($filtered);
                                            
                                            if (empty($filtered) || count($filtered) === 0) {
                                                echo '<span class="text-gray-500">' . ucfirst($day) . ': Closed</span><br>';
                                            } else {
                                                // Only take first 2 elements (start and end time)
                                                $displayTime = array_slice($filtered, 0, 2);
                                                echo ucfirst($day) . ': ' . implode(' - ', $displayTime) . '<br>';
                                            }
                                        } else {
                                            if (empty($time) || $time === '-1' || $time === -1 || $time === null) {
                                                echo '<span class="text-gray-500">' . ucfirst($day) . ': Closed</span><br>';
                                            } else {
                                                echo ucfirst($day) . ': ' . $time . '<br>';
                                            }
                                        }
                                    }
                                } else {
                                    echo $hours;
                                }
                            @endphp
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Social Media -->
            @if($settings['facebook'] || $settings['twitter'] || $settings['instagram'] || $settings['linkedin'] || $settings['youtube'])
            <div class="border-t border-gray-200 pt-5 mt-5">
                <p class="text-sm text-gray-600 font-medium mb-3">Follow Us</p>
                <div class="flex space-x-2">
                    @if($settings['facebook'])<a href="{{ $settings['facebook'] }}" target="_blank" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition"><i class="fab fa-facebook-f"></i></a>@endif
                    @if($settings['twitter'])<a href="{{ $settings['twitter'] }}" target="_blank" class="w-10 h-10 bg-sky-500 rounded-full flex items-center justify-center text-white hover:bg-sky-600 transition"><i class="fab fa-twitter"></i></a>@endif
                    @if($settings['instagram'])<a href="{{ $settings['instagram'] }}" target="_blank" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center text-white hover:bg-pink-700 transition"><i class="fab fa-instagram"></i></a>@endif
                    @if($settings['linkedin'])<a href="{{ $settings['linkedin'] }}" target="_blank" class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center text-white hover:bg-blue-800 transition"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if($settings['youtube'])<a href="{{ $settings['youtube'] }}" target="_blank" class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700 transition"><i class="fab fa-youtube"></i></a>@endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right 50%: Contact Form -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-paper-plane text-blue-600 mr-2"></i>Send us a Message</h2>
            <div class="mb-6">
                @livewire('contact.contact-form')

            </div>

            <div class=" overflow-hidden">
                <div class="p-4">
                    <h3 class="text-xl font-bold"><i class="fas fa-map-marked-alt mr-2"></i>Find Us on Map</h3>
                </div>
                @if(!empty($settings['map_embed_code']))
                <div class="w-full" style="height: 400px;">
                    {!! $settings['map_embed_code'] !!}
                </div>
                @else
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-map text-4xl mb-2"></i>
                    <p>Map will be displayed here once configured</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Row 2: FAQs (50%) + Google Maps (50%) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left 50%: FAQs -->
        @if($faqs->count() > 0)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-purple-600 to-purple-700">
                <h3 class="text-xl font-bold text-white"><i class="fas fa-question-circle mr-2"></i>Frequently Asked Questions</h3>
            </div>
            <div class="p-6 space-y-3" x-data="{ activeAccordion: null }">
                @foreach($faqs as $index => $faq)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="activeAccordion = activeAccordion === {{ $index }} ? null : {{ $index }}" class="w-full px-4 py-3 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-semibold text-sm text-gray-800">{{ $faq->question }}</span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': activeAccordion === {{ $index }} }"></i>
                    </button>
                    <div x-show="activeAccordion === {{ $index }}" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;" class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        <p class="text-sm text-gray-600">{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

