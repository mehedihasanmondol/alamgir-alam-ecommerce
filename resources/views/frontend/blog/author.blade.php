@extends('layouts.app')

@section('title', $seoData['title'] ?? ($author->name . ' - Author Profile - ' . \App\Models\SiteSetting::get('blog_title', 'Blog')))

@section('description', $seoData['description'] ?? (!empty($author->authorProfile?->bio) ? \Illuminate\Support\Str::limit($author->authorProfile->bio, 155) : 'View all posts by ' . $author->name))

@section('keywords', $seoData['keywords'] ?? ($author->name . ', author, blog posts, articles, writer'))

@section('og_type', $seoData['og_type'] ?? 'profile')
@section('og_title', $seoData['title'] ?? ($author->name . ' - Author Profile'))
@section('og_description', $seoData['description'] ?? (!empty($author->authorProfile?->bio) ? \Illuminate\Support\Str::limit($author->authorProfile->bio, 155) : 'View all posts by ' . $author->name))
@section('og_image', $seoData['og_image'] ?? (!empty($author->authorProfile?->media) ? $author->authorProfile->media->large_url : (!empty($author->authorProfile?->avatar) ? asset('storage/' . $author->authorProfile->avatar) : (!empty($author->media) ? $author->media->large_url : (!empty($author->avatar) ? asset('storage/' . $author->avatar) : asset('images/default-avatar.jpg'))))))
@section('canonical', $seoData['canonical'] ?? route('blog.author', $author->authorProfile->slug))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $seoData['title'] ?? ($author->name . ' - Author Profile'))
@section('twitter_description', $seoData['description'] ?? (!empty($author->authorProfile?->bio) ? \Illuminate\Support\Str::limit($author->authorProfile->bio, 155) : 'View all posts by ' . $author->name))
@section('twitter_image', $seoData['og_image'] ?? (!empty($author->authorProfile?->media) ? $author->authorProfile->media->large_url : (!empty($author->authorProfile?->avatar) ? asset('storage/' . $author->authorProfile->avatar) : (!empty($author->media) ? $author->media->large_url : (!empty($author->avatar) ? asset('storage/' . $author->avatar) : asset('images/default-avatar.jpg'))))))

@if(isset($seoData['author_name']))
@section('author', $seoData['author_name'])
@endif

@section('content')
<div class="bg-gradient-to-b from-gray-50  min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Sidebar -->
            <x-blog.sidebar 
                title="{{ \App\Models\SiteSetting::get('blog_title', 'Wellness Hub') }}"
                subtitle="{{ \App\Models\SiteSetting::get('blog_tagline', 'Health & Lifestyle Blog') }}"
                :categories="$categories"
                :currentCategory="null"
                categoryType="blog"
            />

            <!-- Main Content -->
            <div class="lg:col-span-9">
                <!-- Author Profile Card -->
                <div class="  mb-8">
                    <div class="bg-white rounded-tl-2xl rounded-tr-2xl  overflow-hidden">
                        <!-- Cover Background -->
                        <div class="h-20 "></div>
                        
                        <!-- Author Info -->
                        <div class="px-8 pb-8">
                        <div class="flex flex-col md:flex-row items-start gap-6 -mt-16">
                            <!-- Avatar (Top Aligned) -->
                            <div class="flex-shrink-0 self-start">
                                @if($author->authorProfile?->media)
                                    <img src="{{ $author->authorProfile->media->medium_url }}" 
                                         alt="{{ $author->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover">
                                @elseif($author->authorProfile?->avatar)
                                    <img src="{{ asset('storage/' . $author->authorProfile->avatar) }}" 
                                         alt="{{ $author->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover">
                                @elseif($author->media)
                                    <img src="{{ $author->media->medium_url }}" 
                                         alt="{{ $author->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover">
                                @elseif($author->avatar)
                                    <img src="{{ asset('storage/' . $author->avatar) }}" 
                                         alt="{{ $author->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold">
                                        {{ substr($author->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Name, Title, and Bio -->
                            <div class="flex-1 ">
                                <!-- Name and Social Links Row -->
                                <div class="flex items-start  gap-4 mb-2">
                                    <h1 class="text-3xl md:text-4xl mr-4 font-bold text-gray-900">{{ $author->name }}</h1>
                                    
                                    <!-- Social Links (Icons Only) -->
                                    @if($author->authorProfile && $author->authorProfile->hasSocialLinks())
                                        <div class="flex  ">
                                            @if($author->authorProfile->website)
                                                <a href="{{ $author->authorProfile->website }}" target="_blank" rel="noopener noreferrer" 
                                                   title="Website"
                                                   class="p-2  hover:bg-gray-200 text-gray-700  transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->twitter)
                                                <a href="https://twitter.com/{{ $author->authorProfile->twitter }}" target="_blank" rel="noopener noreferrer"
                                                   title="Twitter"
                                                   class="p-2  hover:bg-sky-200 text-sky-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->facebook)
                                                <a href="https://facebook.com/{{ $author->authorProfile->facebook }}" target="_blank" rel="noopener noreferrer"
                                                   title="Facebook"
                                                   class="p-2 hover:bg-blue-200 text-blue-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 3.667h-3.533v7.98H9.101z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->linkedin)
                                                <a href="https://linkedin.com/in/{{ $author->authorProfile->linkedin }}" target="_blank" rel="noopener noreferrer"
                                                   title="LinkedIn"
                                                   class="p-2  hover:bg-blue-200 text-blue-80 transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->instagram)
                                                <a href="https://instagram.com/{{ $author->authorProfile->instagram }}" target="_blank" rel="noopener noreferrer"
                                                   title="Instagram"
                                                   class="p-2  hover:bg-pink-200 text-pink-700  transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->github)
                                                <a href="https://github.com/{{ $author->authorProfile->github }}" target="_blank" rel="noopener noreferrer"
                                                   title="GitHub"
                                                   class="p-2  hover:bg-gray-900 text-white  transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->youtube)
                                                <a href="{{'https://youtube.com/@'}}{{ $author->authorProfile->youtube }}" target="_blank" rel="noopener noreferrer"
                                                   title="YouTube"
                                                   class="p-2  hover:bg-red-200 text-red-700  transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($author->authorProfile->whatsapp)
                                                <a href="https://wa.me/{{ $author->authorProfile->whatsapp }}" target="_blank" rel="noopener noreferrer"
                                                   title="WhatsApp"
                                                   class="p-2  hover:bg-green-200 text-green-700  transition-colors">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Job Title -->
                                @if($author->authorProfile?->job_title)
                                    <p class="text-lg text-gray-600 font-medium mb-3">{{ $author->authorProfile->job_title }}</p>
                                @endif
                                
                                <!-- Bio (After Job Title) -->
                                @if($author->authorProfile?->bio)
                                    <p class="text-gray-700 border-t-1 border-gray-200 pt-4 leading-relaxed">{{ $author->authorProfile->bio }}</p>
                                @endif
                            </div>
                        </div>
                        

                        </div>
                    <!-- Feedback Section (60/40 Layout) -->
                    <x-feedback.author-profile-section />
                    </div>

                    

                    <!-- Posts Section with Livewire (Border Separation) -->
                <div class="">
                    <livewire:blog.author-posts :authorId="$author->id" />
                </div>
                </div>

                
            </div>
        </div>
    </div>
</div>

@endsection
