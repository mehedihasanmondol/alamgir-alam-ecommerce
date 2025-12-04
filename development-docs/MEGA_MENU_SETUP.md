# Hoverable Mega Menu Setup Guide

## Overview
A hoverable mega menu has been implemented in the header navigation, similar to iHerb's design. The menu displays a large dropdown with multiple columns showing subcategories and trending brands.

## Features Implemented

### 1. **Mega Menu Structure**
- Multi-column layout (5 columns)
- Hover-activated dropdown
- Smooth transitions with Alpine.js
- Organized subcategories with icons
- Trending brands section
- Responsive design

### 2. **Menu Categories**
Currently implemented for the "Bath" category with subcategories:
- Bath & Shower (Bar Soap, Bath Soaks, Body Scrubs, etc.)
- Essential Oils (Blends, Diffusers, Sets, Spray, Singles)
- Body Care (Massage Oils, Hand Cream, Lotion, Self-Tan)
- Haircare (Conditioner, Detangler, Hair Color, Shampoo, etc.)
- Trending Brands (Visual brand showcase)

### 3. **Technical Implementation**
- **Alpine.js** for hover state management
- **Tailwind CSS** for styling
- **Blade Components** for reusability
- **Smooth animations** with CSS transitions

## Installation Steps

### Step 1: No Additional Installation Required! ✅
Alpine.js is already included with Livewire 3, so you don't need to install it separately.

### Step 2: Compile Assets
Simply compile the assets:

```bash
npm run dev
```

Or for production:

```bash
npm run build
```

### Step 3: Clear Cache (Optional)
If you don't see changes immediately:

```bash
php artisan view:clear
php artisan cache:clear
```

### Important Note About Alpine.js
This project uses Livewire 3, which already includes Alpine.js. We use Livewire's Alpine instance to avoid conflicts. The mega menu will work automatically without any additional Alpine.js installation.

## File Changes

### Modified Files:
1. **resources/views/components/frontend/header.blade.php**
   - Added mega menu dropdown structure
   - Implemented Alpine.js hover functionality
   - Added multi-column layout

2. **resources/js/app.js**
   - Removed duplicate Alpine.js import (uses Livewire's Alpine)
   - Cleaned up to avoid conflicts

## How to Add More Mega Menus

To add mega menus to other categories (Supplements, Sports, Beauty, etc.), follow this pattern:

```html
<li class="relative" 
    @mouseenter="activeMenu = 'category-name'" 
    @mouseleave="activeMenu = null">
    <a href="#" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition whitespace-nowrap inline-flex items-center">
        Category Name
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </a>
    
    <!-- Mega Menu Dropdown -->
    <div x-show="activeMenu === 'category-name'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute left-0 top-full mt-2 w-screen max-w-5xl bg-white shadow-2xl rounded-lg border border-gray-200 z-50"
         style="display: none;">
        <div class="p-8">
            <div class="grid grid-cols-5 gap-8">
                <!-- Add your columns here -->
            </div>
        </div>
    </div>
</li>
```

## Dynamic Menu with Database

To make the menu dynamic using categories from the database:

### 1. Create a View Composer
Create `app/Http/View/Composers/MenuComposer.php`:

```php
<?php

namespace App\Http\View\Composers;

use App\Modules\Ecommerce\Category\Models\Category;
use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view)
    {
        $menuCategories = Category::with(['activeChildren' => function($query) {
            $query->limit(10); // Limit subcategories
        }])
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->ordered()
        ->get();

        $view->with('menuCategories', $menuCategories);
    }
}
```

### 2. Register the Composer
In `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\MenuComposer;

public function boot()
{
    View::composer('components.frontend.header', MenuComposer::class);
}
```

### 3. Update Header Component
Replace static menu items with dynamic data:

```blade
@foreach($menuCategories as $category)
    <li class="relative" 
        @mouseenter="activeMenu = '{{ $category->slug }}'" 
        @mouseleave="activeMenu = null">
        <a href="{{ $category->getUrl() }}" 
           class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition whitespace-nowrap inline-flex items-center">
            {{ $category->name }}
            @if($category->activeChildren->count() > 0)
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            @endif
        </a>
        
        @if($category->activeChildren->count() > 0)
            <!-- Mega Menu Dropdown -->
            <div x-show="activeMenu === '{{ $category->slug }}'" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute left-0 top-full mt-2 w-screen max-w-5xl bg-white shadow-2xl rounded-lg border border-gray-200 z-50"
                 style="display: none;">
                <div class="p-8">
                    <div class="grid grid-cols-4 gap-8">
                        @foreach($category->activeChildren as $subcategory)
                            <div>
                                <h3 class="text-sm font-bold text-blue-600 mb-3">
                                    <a href="{{ $subcategory->getUrl() }}">{{ $subcategory->name }}</a>
                                </h3>
                                @if($subcategory->activeChildren->count() > 0)
                                    <ul class="space-y-2">
                                        @foreach($subcategory->activeChildren as $child)
                                            <li>
                                                <a href="{{ $child->getUrl() }}" 
                                                   class="text-sm text-gray-700 hover:text-green-600 transition">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </li>
@endforeach
```

## Customization Options

### Change Dropdown Width
Modify the `max-w-5xl` class to adjust width:
- `max-w-4xl` - Smaller
- `max-w-6xl` - Larger
- `max-w-7xl` - Extra large

### Change Number of Columns
Modify the `grid-cols-5` class:
- `grid-cols-3` - 3 columns
- `grid-cols-4` - 4 columns
- `grid-cols-6` - 6 columns

### Change Hover Colors
Update the hover classes:
- `hover:text-green-600` - Change green to your brand color
- `hover:bg-green-50` - Change background color

### Animation Speed
Modify transition duration:
- `duration-200` - Current (200ms)
- `duration-300` - Slower
- `duration-150` - Faster

## Troubleshooting

### Menu Not Showing
1. Check if Alpine.js is loaded: Open browser console and type `Alpine`
2. Verify Vite is running: `npm run dev`
3. Clear browser cache: Ctrl+Shift+R

### Menu Appears Behind Other Elements
Add higher z-index to the dropdown:
```html
class="... z-[100]"
```

### Menu Closes Too Quickly
Add a delay to the mouseleave event:
```html
@mouseleave.debounce.200ms="activeMenu = null"
```

## Browser Compatibility
- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- IE11: ❌ Not supported (Alpine.js requires modern browsers)

## Performance Notes
- The mega menu is hidden by default (`display: none`)
- Only renders when hovered (Alpine.js handles visibility)
- Uses CSS transforms for smooth animations
- No JavaScript required for hover detection (uses Alpine.js directives)

## Next Steps
1. Install Alpine.js: `npm install alpinejs`
2. Run dev server: `npm run dev`
3. Test the menu by hovering over "Bath"
4. Add more categories following the pattern above
5. Consider making it dynamic with database categories

## Support
If you encounter any issues, check:
- Alpine.js documentation: https://alpinejs.dev
- Tailwind CSS documentation: https://tailwindcss.com
- Laravel Vite documentation: https://laravel.com/docs/vite
