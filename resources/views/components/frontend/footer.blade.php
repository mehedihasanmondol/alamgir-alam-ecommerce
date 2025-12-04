{{-- 
/**
 * ModuleName: Frontend Newsletter Section
 * Purpose: Newsletter section for public-facing pages
 * 
 * Features:
 * - Newsletter subscription
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-01-06
 */
--}}

<!-- Wellness Hub / Blog Section -->
@if(\App\Models\FooterSetting::get('wellness_hub_section_enabled', '1') === '1')
<section class="bg-white py-8 border-t border-gray-200">
    <div class="container mx-auto px-4">
        <!-- Blog Articles Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
            @php
                $featuredPosts = \App\Modules\Blog\Models\Post::where('is_featured', true)
                    ->where('status', 'published')
                    ->with('media')
                    ->latest('published_at')
                    ->take(6)
                    ->get();
            @endphp
            
            @foreach($featuredPosts->take(3) as $post)
            <a href="{{ route('products.show', $post->slug) }}" class="group">
                <div class="bg-gray-100 rounded-lg overflow-hidden mb-2">
                    @if($post->media || $post->featured_image)
                        @if($post->media)
                            <img src="{{ $post->media->small_url }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                        @elseif($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                        @endif
                    @else
                        <div class="w-full h-32 bg-gradient-to-br from-blue-100 to-green-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <h4 class="text-xs font-medium text-gray-900 group-hover:text-green-600 line-clamp-2">
                    {{ $post->title }}
                </h4>
            </a>
            @endforeach
            
            <!-- Wellness Hub Badge -->
            <a href="{{ route('blog.index') }}" class="block bg-yellow-400 rounded-lg p-6 text-center hover:bg-yellow-500 transition">
                @php
                    $blogTitle = \App\Models\SiteSetting::get('blog_title', 'WELLBEING HUB');
                    $titleParts = explode(' ', strtoupper($blogTitle));
                    $firstPart = array_slice($titleParts, 0, -1);
                    $lastPart = end($titleParts);
                @endphp
                @if(count($titleParts) > 1)
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ implode(' ', $firstPart) }}</h3>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $lastPart }}</h2>
                @else
                    <h2 class="text-2xl font-bold text-gray-900">{{ $blogTitle }}</h2>
                @endif
            </a>
            
            @foreach($featuredPosts->slice(3, 3) as $post)
            <a href="{{ route('products.show', $post->slug) }}" class="group">
                <div class="bg-gray-100 rounded-lg overflow-hidden mb-2">
                    @if($post->media || $post->featured_image)
                        @if($post->media)
                            <img src="{{ $post->media->small_url }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                        @elseif($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                        @endif
                    @else
                        <div class="w-full h-32 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <h4 class="text-xs font-medium text-gray-900 group-hover:text-green-600 line-clamp-2">
                    {{ $post->title }}
                </h4>
            </a>
            @endforeach
        </div>

        <!-- Rewards Banner -->
        @php
            $rewardsSectionEnabled = \App\Models\FooterSetting::get('rewards_section_enabled', '1');
            $rewardsBrandName = \App\Models\FooterSetting::get('rewards_brand_name', 'iHerb');
            $rewardsSectionTitle = \App\Models\FooterSetting::get('rewards_section_title', 'REWARDS');
            $rewardsDescription = \App\Models\FooterSetting::get('rewards_description', 'Enjoy free products, insider access and exclusive offers');
            $rewardsLinkEnabled = \App\Models\FooterSetting::get('rewards_link_enabled', '1');
            $rewardsLinkUrl = \App\Models\FooterSetting::get('rewards_link_url', '#');
        @endphp
        
        
        @if($rewardsSectionEnabled == '1')
            @if($rewardsLinkEnabled == '1' && $rewardsLinkUrl !== '#')
                <a href="{{ $rewardsLinkUrl }}" class="block bg-green-100 rounded-lg py-4 px-6 text-center hover:bg-green-200 transition-colors">
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-2xl font-bold text-green-700">{{ $rewardsBrandName }}</span>
                        <span class="text-xl text-gray-600">|</span>
                        <span class="text-lg font-bold text-gray-900">{{ $rewardsSectionTitle }}</span>
                        <span class="text-gray-700 ml-4">{{ $rewardsDescription }}</span>
                    </div>
                </a>
            @else
                <div class="bg-green-100 rounded-lg py-4 px-6 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-2xl font-bold text-green-700">{{ $rewardsBrandName }}</span>
                        <span class="text-xl text-gray-600">|</span>
                        <span class="text-lg font-bold text-gray-900">{{ $rewardsSectionTitle }}</span>
                        <span class="text-gray-700 ml-4">{{ $rewardsDescription }}</span>
                    </div>
                </div>
            @endif
        @endif
    </div>
</section>
@endif

<!-- Value Guarantee Banner -->
@if(\App\Models\FooterSetting::get('value_guarantee_section_enabled', '1') === '1')
<section class="bg-yellow-50 py-4 border-y border-yellow-200">
    <div class="container mx-auto px-4">
        @php
            $valueGuarantee = \App\Models\FooterSetting::get('value_guarantee', 'World\'s best value - guaranteed!');
        @endphp
        <p class="text-center text-gray-800 font-semibold">{{ $valueGuarantee }}</p>
    </div>
</section>
@endif

{{-- 
/**
 * ModuleName: Frontend Footer Component
 * Purpose: Main footer for public-facing pages
 * 
 * Features:
 * - Newsletter subscription
 * - Quick links
 * - Social media links
 * - Copyright information
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-01-06
 */
--}}

<footer class="bg-white">
    <!-- Main Footer Links -->
    @if(\App\Models\FooterSetting::get('footer_links_section_enabled', '1') === '1')
    <div class="container mx-auto px-4 py-12">
        @php
            $footerLinks = \App\Models\FooterLink::where('is_active', true)
                ->orderBy('section')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('section');
        @endphp
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
            <!-- About -->
            @if($footerLinks->has('about'))
            <div>
                <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">ABOUT</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    @foreach($footerLinks['about'] as $link)
                        <li>
                            <a href="{{ $link->url }}" class="hover:text-green-600 transition">
                                @if($link->title === 'Store Reviews')
                                    <span class="text-red-600">New!</span> 
                                @endif
                                {{ $link->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Company -->
            @if($footerLinks->has('company'))
            <div>
                <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">COMPANY</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    @foreach($footerLinks['company'] as $link)
                        <li><a href="{{ $link->url }}" class="hover:text-green-600 transition">{{ $link->title }}</a></li>
                    @endforeach
                    {{-- Customer Feedback Link --}}
                    <li><a href="{{ route('feedback.index') }}" class="hover:text-green-600 transition">Customer Feedback</a></li>
                </ul>
            </div>
            @endif

            <!-- Resources -->
            @if($footerLinks->has('resources'))
            <div>
                <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">RESOURCES</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    @foreach($footerLinks['resources'] as $link)
                        <li><a href="{{ $link->url }}" class="hover:text-green-600 transition">{{ $link->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Customer Support -->
            @if($footerLinks->has('customer_support'))
            <div>
                <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">CUSTOMER SUPPORT</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    @foreach($footerLinks['customer_support'] as $link)
                        <li><a href="{{ $link->url }}" class="hover:text-green-600 transition">{{ $link->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Mobile Apps -->
            @php
                $mobileAppsEnabled = \App\Models\FooterSetting::get('mobile_apps_enabled', '1');
                $mobileAppsTitle = \App\Models\FooterSetting::get('mobile_apps_title', 'MOBILE APPS');
                $qrCodeEnabled = \App\Models\FooterSetting::get('qr_code_enabled', '1');
                $qrCodeImage = \App\Models\FooterSetting::get('qr_code_image', '');
                $googlePlayEnabled = \App\Models\FooterSetting::get('google_play_enabled', '1');
                $googlePlayUrl = \App\Models\FooterSetting::get('google_play_url', '#');
                $appStoreEnabled = \App\Models\FooterSetting::get('app_store_enabled', '1');
                $appStoreUrl = \App\Models\FooterSetting::get('app_store_url', '#');
            @endphp
            
            @if($mobileAppsEnabled == '1')
            <div>
                <h4 class="font-bold text-gray-900 mb-4 uppercase text-sm">{{ $mobileAppsTitle }}</h4>
                <div class="space-y-3">
                    @if($qrCodeEnabled == '1')
                        <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                            @if($qrCodeImage)
                                <img src="{{ asset('storage/' . $qrCodeImage) }}" alt="QR Code" class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="text-xs text-gray-500">QR Code</span>
                            @endif
                        </div>
                    @endif
                    
                    @if($googlePlayEnabled == '1' && $googlePlayUrl !== '#')
                        <a href="{{ $googlePlayUrl }}" class="block" target="_blank" rel="noopener">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Get it on Google Play" class="h-10">
                        </a>
                    @endif
                    
                    @if($appStoreEnabled == '1' && $appStoreUrl !== '#')
                        <a href="{{ $appStoreUrl }}" class="block" target="_blank" rel="noopener">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="Download on the App Store" class="h-10">
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Social Media -->
    <div class="border-t border-gray-200 py-6">
        <div class="container mx-auto px-4">
            @php
                $facebookUrl = \App\Models\FooterSetting::get('facebook_url', '#');
                $twitterUrl = \App\Models\FooterSetting::get('twitter_url', '#');
                $youtubeUrl = \App\Models\FooterSetting::get('youtube_url', '#');
                $pinterestUrl = \App\Models\FooterSetting::get('pinterest_url', '#');
                $instagramUrl = \App\Models\FooterSetting::get('instagram_url', '#');
                $siteName = \App\Models\SiteSetting::get('site_name', 'iHerb');
            @endphp
            <div class="flex justify-center space-x-6">
                <a href="{{ $facebookUrl }}" class="text-gray-600 hover:text-green-600 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="{{ $twitterUrl }}" class="text-gray-600 hover:text-green-600 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
                <a href="{{ $youtubeUrl }}" class="text-gray-600 hover:text-green-600 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a href="{{ $pinterestUrl }}" class="text-gray-600 hover:text-green-600 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>
                </a>
                <a href="{{ $instagramUrl }}" class="text-gray-600 hover:text-green-600 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                </a>
            </div>
            
        </div>
    </div>

    <!-- Newsletter Signup Section -->
    @if(\App\Models\FooterSetting::get('newsletter_section_enabled', '1') === '1')
    <div class="border-t border-gray-200 py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto text-center">
                @php
                    $newsletterTitle = \App\Models\FooterSetting::get('newsletter_title', 'BE THE FIRST TO GET PROMO OFFERS AND REWARD PERKS STRAIGHT TO YOUR INBOX');
                    $newsletterDescription = \App\Models\FooterSetting::get('newsletter_description', 'Your email address will be used to send you Health Newsletters and emails about our products, services, sales, and special offers.');
                    $siteName = \App\Models\SiteSetting::get('site_name', 'iHerb');
                @endphp
                <h3 class="text-xl font-bold text-gray-900 mb-6 uppercase">
                    {{ $newsletterTitle }}
                </h3>
                <form class="flex gap-2 mb-6">
                    <input 
                        type="email" 
                        placeholder="Enter Email Address" 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition"
                    >
                        Sign up
                    </button>
                </form>
                <p class="text-xs text-gray-600 mb-3">
                    {!! $newsletterDescription !!} 
                    
                </p>
                
            </div>
        </div>
    </div>
    @endif

    <!-- Copyright -->
    <div class="border-t border-gray-200 py-6">
        <div class="container mx-auto px-4">
            @php
                $copyrightText = \App\Models\FooterSetting::get('copyright_text', 'iHerb.com  Copyright 1997-2025 iHerb, Ltd. All rights reserved.');
                $siteName = \App\Models\SiteSetting::get('site_name', 'iHerb');
            @endphp
            <p class="text-xs text-gray-600 text-center mb-4 leading-relaxed">
                {!! $copyrightText !!} 
                
                
            </p>
            
        </div>
    </div>
</footer>
