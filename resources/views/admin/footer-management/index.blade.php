@extends('layouts.admin')

@section('title', 'Footer Management')

@push('styles')
<style>
/* CKEditor Custom Styling */
.ck-editor__editable {
    min-height: 150px;
    max-height: 300px;
}

.ck.ck-editor__main>.ck-editor__editable {
    background: #ffffff;
    border-radius: 0 0 0.5rem 0.5rem;
}

/* Force list markers to display (override Tailwind reset) */
.ck-content ul,
.ck-content ol {
    margin-left: 20px;
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Footer Management</h1>
        <p class="text-gray-600 mt-1">Manage all footer content, links, and settings</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('settings')" id="tab-settings" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    <i class="fas fa-cog mr-2"></i>General Settings
                </button>
                <button onclick="showTab('links')" id="tab-links" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-link mr-2"></i>Footer Links
                </button>
                <button onclick="showTab('blog')" id="tab-blog" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-newspaper mr-2"></i>Blog Posts
                </button>
                <button onclick="showTab('social')" id="tab-social" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-share-alt mr-2"></i>Social Media
                </button>
                <button onclick="showTab('mobile')" id="tab-mobile" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-mobile-alt mr-2"></i>Mobile Apps
                </button>
                <button onclick="showTab('rewards')" id="tab-rewards" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-gift mr-2"></i>Rewards
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- General Settings Tab -->
            <div id="content-settings" class="tab-content">
                <!-- Newsletter Section Toggle -->
                <x-admin.footer-section-toggle
                    sectionKey="newsletter_section_enabled"
                    sectionName="Newsletter Section"
                    description="Email signup form with promotional offers"
                    :enabled="\App\Models\FooterSetting::get('newsletter_section_enabled', '1')"
                />

                <!-- Value Guarantee Toggle -->
                <x-admin.footer-section-toggle
                    sectionKey="value_guarantee_section_enabled"
                    sectionName="Value Guarantee Banner"
                    description="Yellow banner with guarantee message"
                    :enabled="\App\Models\FooterSetting::get('value_guarantee_section_enabled', '1')"
                />

                <!-- General Settings Form -->
                <form action="{{ route('admin.footer-management.update-settings') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Newsletter Title</label>
                            <input type="text" name="newsletter_title" value="{{ $settings['general']->firstWhere('key', 'newsletter_title')->value ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Newsletter Description</label>
                            <textarea name="newsletter_description" id="newsletter-description-editor" class="ckeditor-content-minimal">{{ $settings['general']->firstWhere('key', 'newsletter_description')->value ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Value Guarantee Text</label>
                            <input type="text" name="value_guarantee" value="{{ $settings['general']->firstWhere('key', 'value_guarantee')->value ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rewards Program Text</label>
                            <input type="text" name="rewards_text" value="{{ $settings['general']->firstWhere('key', 'rewards_text')->value ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Copyright Text</label>
                            <textarea name="copyright_text" id="copyright-text-editor" class="ckeditor-content-minimal">{{ $settings['legal']->firstWhere('key', 'copyright_text')->value ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Links Tab -->
            <div id="content-links" class="tab-content hidden">
                <!-- Footer Links Section Toggle -->
                <x-admin.footer-section-toggle
                    sectionKey="footer_links_section_enabled"
                    sectionName="Footer Links Section"
                    description="About, Company, Resources, Customer Support, Mobile Apps columns"
                    :enabled="\App\Models\FooterSetting::get('footer_links_section_enabled', '1')"
                />
                
                @livewire('admin.footer-links-manager')
            </div>

            <!-- Blog Posts Tab -->
            <div id="content-blog" class="tab-content hidden">
                <!-- Wellness Hub Section Toggle -->
                <x-admin.footer-section-toggle
                    sectionKey="wellness_hub_section_enabled"
                    sectionName="Wellness Hub / Blog Section"
                    description="Featured blog articles grid displayed at top of footer"
                    :enabled="\App\Models\FooterSetting::get('wellness_hub_section_enabled', '1')"
                />
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Blog Post</h3>
                    <form action="{{ route('admin.footer-management.store-blog') }}" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="text" name="title" placeholder="Blog Post Title" required class="px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="url" placeholder="URL" required class="px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="file" name="image" accept="image/*" class="px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <button type="submit" class="mt-4 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add Blog Post
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($blogPosts as $post)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-32 object-cover">
                        @else
                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        </div>
                        @endif
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $post->title }}</h4>
                            <p class="text-xs text-gray-500 mb-3">{{ $post->url }}</p>
                            <form action="{{ route('admin.footer-management.delete-blog', $post) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Delete this post?')">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Social Media Tab -->
            <div id="content-social" class="tab-content hidden">
                <!-- Social Media Section Toggle -->
                <x-admin.footer-section-toggle
                    sectionKey="social_media_section_enabled"
                    sectionName="Social Media Section"
                    description="Social media icons (Facebook, Twitter, YouTube, Pinterest, Instagram)"
                    :enabled="\App\Models\FooterSetting::get('social_media_section_enabled', '1')"
                />
                
                <form action="{{ route('admin.footer-management.update-settings') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['facebook' => 'Facebook', 'twitter' => 'Twitter', 'youtube' => 'YouTube', 'pinterest' => 'Pinterest', 'instagram' => 'Instagram'] as $platform => $name)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-{{ $platform }} mr-2"></i>{{ $name }} URL
                            </label>
                            <input type="text" name="{{ $platform }}_url" value="{{ $settings['social']->firstWhere('key', $platform . '_url')->value ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://{{ $platform }}.com/yourpage">
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Save Social Links
                    </button>
                </form>
            </div>

            <!-- Mobile Apps Tab -->
            <div id="content-mobile" class="tab-content hidden">
                
                <form action="{{ route('admin.footer-management.update-settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-6">
                        <!-- Mobile Apps Section Toggle -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Mobile Apps Section</h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="mobile_apps_enabled" value="0">
                                    <input type="checkbox" name="mobile_apps_enabled" value="1" 
                                           {{ ($settings['mobile_apps']->firstWhere('key', 'mobile_apps_enabled')->value ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Enable Mobile Apps Section</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                <input type="text" name="mobile_apps_title" 
                                       value="{{ $settings['mobile_apps']->firstWhere('key', 'mobile_apps_title')->value ?? 'MOBILE APPS' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="MOBILE APPS">
                            </div>
                        </div>

                        <!-- QR Code Settings -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">QR Code</h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="qr_code_enabled" value="0">
                                    <input type="checkbox" name="qr_code_enabled" value="1" 
                                           {{ ($settings['mobile_apps']->firstWhere('key', 'qr_code_enabled')->value ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Show QR Code</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code Image</label>
                                <input type="file" name="qr_code_image" id="qr_code_image" accept="image/*" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       onchange="previewQRCode(this)">
                                <p class="text-xs text-gray-500 mt-1">Upload a QR code image (PNG, JPG, SVG). Recommended size: 96x96px</p>
                                
                                <!-- QR Code Preview Container -->
                                <div class="mt-3 flex items-start space-x-4">
                                    @php
                                        $qrCodeImage = $settings['mobile_apps']->firstWhere('key', 'qr_code_image')->value ?? '';
                                    @endphp
                                    
                                    <!-- Current QR Code -->
                                    @if($qrCodeImage)
                                        <div id="current-qr-container">
                                            <img src="{{ asset('storage/' . $qrCodeImage) }}" alt="Current QR Code" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                                            <p class="text-xs text-gray-500 mt-1 text-center">Current</p>
                                        </div>
                                    @endif
                                    
                                    <!-- Preview QR Code (Hidden by default) -->
                                    <div id="preview-qr-container" class="hidden">
                                        <img id="qr-preview" src="" alt="QR Code Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-blue-500">
                                        <p class="text-xs text-blue-600 mt-1 text-center font-medium">New Preview</p>
                                        <button type="button" onclick="clearQRPreview()" class="text-xs text-red-600 hover:text-red-800 mt-1 block mx-auto">
                                            <i class="fas fa-times mr-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Play Store -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <i class="fab fa-google-play mr-2 text-green-600"></i>Google Play Store
                                </h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="google_play_enabled" value="0">
                                    <input type="checkbox" name="google_play_enabled" value="1" 
                                           {{ ($settings['mobile_apps']->firstWhere('key', 'google_play_enabled')->value ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Show Google Play Button</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Google Play Store URL</label>
                                <input type="text" name="google_play_url" 
                                       value="{{ $settings['mobile_apps']->firstWhere('key', 'google_play_url')->value ?? '#' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="https://play.google.com/store/apps/details?id=your.app.id">
                            </div>
                        </div>

                        <!-- Apple App Store -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <i class="fab fa-apple mr-2 text-gray-800"></i>Apple App Store
                                </h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="app_store_enabled" value="0">
                                    <input type="checkbox" name="app_store_enabled" value="1" 
                                           {{ ($settings['mobile_apps']->firstWhere('key', 'app_store_enabled')->value ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Show App Store Button</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Apple App Store URL</label>
                                <input type="text" name="app_store_url" 
                                       value="{{ $settings['mobile_apps']->firstWhere('key', 'app_store_url')->value ?? '#' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="https://apps.apple.com/app/your-app-name/id123456789">
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            <i class="fas fa-save mr-2"></i>Save Mobile Apps Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Rewards Tab -->
            <div id="content-rewards" class="tab-content hidden">
                @php
                    // Debug: Check what's in settings
                    $rewardsSettings = $settings['rewards'] ?? collect();
                    $mobileAppsSettings = $settings['mobile_apps'] ?? collect();
                @endphp
                
                
                <form action="{{ route('admin.footer-management.update-settings') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Rewards Section Toggle -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Rewards Section</h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="rewards_section_enabled" value="0">
                                    <input type="checkbox" name="rewards_section_enabled" value="1" 
                                           {{ (isset($settings['rewards']) && $settings['rewards']->firstWhere('key', 'rewards_section_enabled') ? $settings['rewards']->firstWhere('key', 'rewards_section_enabled')->value : '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Enable Rewards Section</span>
                                </label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Name</label>
                                    <input type="text" name="rewards_brand_name" 
                                           value="{{ isset($settings['rewards']) && $settings['rewards']->firstWhere('key', 'rewards_brand_name') ? $settings['rewards']->firstWhere('key', 'rewards_brand_name')->value : 'iHerb' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                           placeholder="iHerb">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                    <input type="text" name="rewards_section_title" 
                                           value="{{ $settings['rewards']->firstWhere('key', 'rewards_section_title')->value ?? 'REWARDS' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                           placeholder="REWARDS">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <input type="text" name="rewards_description" 
                                       value="{{ $settings['rewards']->firstWhere('key', 'rewards_description')->value ?? 'Enjoy free products, insider access and exclusive offers' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enjoy free products, insider access and exclusive offers">
                            </div>
                        </div>

                        <!-- Rewards Link Settings -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-link mr-2 text-blue-600"></i>Rewards Link
                                </h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="rewards_link_enabled" value="0">
                                    <input type="checkbox" name="rewards_link_enabled" value="1" 
                                           {{ ($settings['rewards']->firstWhere('key', 'rewards_link_enabled')->value ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Make Rewards Section Clickable</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rewards Page URL</label>
                                <input type="text" name="rewards_link_url" 
                                       value="{{ $settings['rewards']->firstWhere('key', 'rewards_link_url')->value ?? '#' }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="/rewards or https://example.com/rewards">
                                <p class="text-xs text-gray-500 mt-1">URL where users will be redirected when clicking the rewards section</p>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                <i class="fas fa-eye mr-2 text-green-600"></i>Preview
                            </h3>
                            <div class="bg-green-100 rounded-lg py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <span class="text-2xl font-bold text-green-700" id="preview-brand-name">iHerb</span>
                                    <span class="text-xl text-gray-600">|</span>
                                    <span class="text-lg font-bold text-gray-900" id="preview-section-title">REWARDS</span>
                                    <span class="text-gray-700 ml-4" id="preview-description">Enjoy free products, insider access and exclusive offers</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2 text-center">This is how the rewards section will appear in the footer</p>
                        </div>

                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            <i class="fas fa-save mr-2"></i>Save Rewards Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/footer-settings-editor.js')
<script>
// CKEditor initialization is handled by footer-settings-editor.js

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// CKEditor content is automatically synced on form submit

// QR Code Preview Functions
function previewQRCode(input) {
    const previewContainer = document.getElementById('preview-qr-container');
    const previewImage = document.getElementById('qr-preview');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file.');
            input.value = '';
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function clearQRPreview() {
    const input = document.getElementById('qr_code_image');
    const previewContainer = document.getElementById('preview-qr-container');
    const previewImage = document.getElementById('qr-preview');
    
    // Clear the file input
    input.value = '';
    
    // Hide preview container
    previewContainer.classList.add('hidden');
    
    // Clear preview image source
    previewImage.src = '';
}

// Rewards Preview Functions
document.addEventListener('DOMContentLoaded', function() {
    // Get input elements
    const brandNameInput = document.querySelector('input[name="rewards_brand_name"]');
    const sectionTitleInput = document.querySelector('input[name="rewards_section_title"]');
    const descriptionInput = document.querySelector('input[name="rewards_description"]');
    
    // Get preview elements
    const previewBrandName = document.getElementById('preview-brand-name');
    const previewSectionTitle = document.getElementById('preview-section-title');
    const previewDescription = document.getElementById('preview-description');
    
    // Update preview on input change
    if (brandNameInput && previewBrandName) {
        brandNameInput.addEventListener('input', function() {
            previewBrandName.textContent = this.value || 'iHerb';
        });
    }
    
    if (sectionTitleInput && previewSectionTitle) {
        sectionTitleInput.addEventListener('input', function() {
            previewSectionTitle.textContent = this.value || 'REWARDS';
        });
    }
    
    if (descriptionInput && previewDescription) {
        descriptionInput.addEventListener('input', function() {
            previewDescription.textContent = this.value || 'Enjoy free products, insider access and exclusive offers';
        });
    }
});
</script>
@endpush
@endsection
