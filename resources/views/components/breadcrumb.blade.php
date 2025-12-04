@props(['items' => []])

{{-- 
/**
 * Breadcrumb Component (iHerb Style)
 * 
 * Features:
 * - Structured data for SEO (Schema.org)
 * - Accessible (ARIA labels)
 * - Responsive design
 * - Hover effects
 * - Icon support
 * - Truncation for long names
 * 
 * Usage:
 * <x-breadcrumb :items="[
 *     ['label' => 'Home', 'url' => route('home')],
 *     ['label' => 'Category', 'url' => route('shop', ['category' => 'slug'])],
 *     ['label' => 'Product Name', 'url' => null] // Current page (no link)
 * ]" />
 */
--}}

<nav aria-label="Breadcrumb" class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-3">
        <ol class="flex flex-wrap items-center space-x-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList">
            @foreach($items as $index => $item)
                <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    @if($loop->last)
                        {{-- Current page (no link) --}}
                        <span class="text-gray-900 font-medium" itemprop="name" aria-current="page">
                            {{ Str::limit($item['label'], 50) }}
                        </span>
                        <meta itemprop="position" content="{{ $index + 1 }}" />
                    @else
                        {{-- Clickable breadcrumb --}}
                        <a href="{{ $item['url'] }}" 
                           class="text-gray-600 hover:text-orange-600 transition-colors duration-200 flex items-center"
                           itemprop="item">
                            @if($loop->first && !isset($item['icon']))
                                {{-- Home icon for first item --}}
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                            @endif
                            <span itemprop="name">{{ $item['label'] }}</span>
                        </a>
                        <meta itemprop="position" content="{{ $index + 1 }}" />
                        
                        {{-- Separator --}}
                        <svg class="w-4 h-4 mx-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
