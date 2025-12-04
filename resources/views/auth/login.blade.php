@extends('layouts.app')

@section('title', 'Sign in or create an account')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-6xl">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-green-600">{{\App\Models\SiteSetting::get('site_name', 'Iherb')}}</h1>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2 gap-0">
                <!-- Left Side - Login Form -->
                <div class="p-8 md:p-12">
                    <!-- Cancel Link -->
                    <div class="mb-6">
                        <a href="/" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Cancel</a>
                    </div>

                    <!-- Title -->
                    <h2 class="text-2xl font-semibold text-gray-900 mb-3">Sign in or create an account</h2>
                    <p class="text-gray-600 text-sm mb-8">
                        Enter your email or mobile number to get started. If you already have an account, we'll find it for you.
                    </p>

                    <!-- Multi-Step Login Form (Livewire) -->
                    @livewire('auth.multi-step-login')

                    <!-- Divider -->
                    @if(\App\Models\SiteSetting::get('enable_google_login', '1') === '1' || \App\Models\SiteSetting::get('enable_facebook_login', '1') === '1' || \App\Models\SiteSetting::get('enable_apple_login', '0') === '1')
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">or</span>
                            </div>
                        </div>

                        <!-- Social Login Buttons -->
                        <div class="space-y-3">
                            <!-- Google -->
                            @if(\App\Models\SiteSetting::get('enable_google_login', '1') === '1')
                                <a 
                                    href="{{ route('social.login', 'google') }}"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200"
                                >
                                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">Sign in with Google</span>
                                </a>
                            @endif

                            <!-- Facebook -->
                            @if(\App\Models\SiteSetting::get('enable_facebook_login', '1') === '1')
                                <a 
                                    href="{{ route('social.login', 'facebook') }}"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200"
                                >
                                    <svg class="w-5 h-5 mr-3" fill="#1877F2" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">Sign in with Facebook</span>
                                </a>
                            @endif

                            <!-- Apple -->
                            @if(\App\Models\SiteSetting::get('enable_apple_login', '0') === '1')
                                <a 
                                    href="{{ route('social.login', 'apple') }}"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200"
                                >
                                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">Sign in with Apple</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Side - Dynamic Content -->
                <div class="bg-gray-50 p-8 md:p-12">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">{{ \App\Models\SiteSetting::get('login_page_title', 'Why iHerb?') }}</h3>
                    
                    {!! \App\Models\SiteSetting::get('login_page_content', '<p class="text-gray-600">Welcome to our store!</p>') !!}
                </div>
            </div>
        </div>

       
    </div>
</div>

@endsection
