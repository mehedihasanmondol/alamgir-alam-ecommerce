@php
    $banners = \App\Models\PromotionalBanner::active()->ordered()->get();
    $dismissedBanners = session()->get('dismissed_banners', []);
    $activeBanners = $banners->filter(fn($banner) => !in_array($banner->id, $dismissedBanners));
@endphp

@if($activeBanners->count() > 0)
    <div x-data="promoBanner()" x-init="init()" class="relative overflow-hidden">
        @foreach($activeBanners as $index => $banner)
            <div 
                x-show="currentBanner === {{ $index }}"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="background-color: {{ $banner->background_color }}; color: {{ $banner->text_color }};"
                class="py-3 px-4 text-sm"
                @if($index !== 0) style="display: none;" @endif>
                
                <div class="container mx-auto">
                    <div class="flex items-center justify-between gap-4">
                        <!-- Navigation Arrow (Left) - Only if multiple banners -->
                        @if($activeBanners->count() > 1)
                            <button 
                                @click="prevBanner()"
                                class="flex-shrink-0 hover:opacity-75 transition p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                        @endif

                        <!-- Banner Content -->
                        <div class="flex-1 flex flex-col md:flex-row items-center justify-center gap-2 md:gap-4 text-center">
                            <div class="flex items-center gap-2">
                                <span class="font-bold">{{ $banner->title }}</span>
                                @if($banner->subtitle)
                                    <span class="hidden md:inline">{{ $banner->subtitle }}</span>
                                @endif
                            </div>

                            @if($banner->isCountdownActive())
                                <div 
                                    x-data="countdown({{ json_encode($banner->getTimeRemaining()) }}, {{ $banner->id }})"
                                    x-init="startCountdown()"
                                    class="flex items-center gap-1 font-mono font-bold">
                                    <span>Ends in:</span>
                                    <span x-text="formatTime()"></span>
                                </div>
                            @endif

                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" 
                                   class="underline hover:no-underline font-medium whitespace-nowrap">
                                    {{ $banner->link_text ?? 'Shop Now' }} →
                                </a>
                            @endif
                        </div>

                        <!-- Right Side Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Navigation Arrow (Right) - Only if multiple banners -->
                            @if($activeBanners->count() > 1)
                                <button 
                                    @click="nextBanner()"
                                    class="hover:opacity-75 transition p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endif

                            <!-- Dismiss Button -->
                            @if($banner->is_dismissible)
                                <button 
                                    @click="dismissBanner({{ $banner->id }})"
                                    class="hover:opacity-75 transition p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function promoBanner() {
            return {
                currentBanner: 0,
                totalBanners: {{ $activeBanners->count() }},
                autoPlayInterval: null,

                init() {
                    if (this.totalBanners > 1) {
                        this.startAutoPlay();
                    }
                },

                nextBanner() {
                    this.currentBanner = (this.currentBanner + 1) % this.totalBanners;
                    this.resetAutoPlay();
                },

                prevBanner() {
                    this.currentBanner = (this.currentBanner - 1 + this.totalBanners) % this.totalBanners;
                    this.resetAutoPlay();
                },

                startAutoPlay() {
                    this.autoPlayInterval = setInterval(() => {
                        this.nextBanner();
                    }, 5000);
                },

                resetAutoPlay() {
                    if (this.autoPlayInterval) {
                        clearInterval(this.autoPlayInterval);
                        this.startAutoPlay();
                    }
                },

                dismissBanner(bannerId) {
                    fetch('{{ route("promo-banners.dismiss") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ banner_id: bannerId })
                    }).then(() => {
                        location.reload();
                    });
                }
            }
        }

        function countdown(initialTime, bannerId) {
            return {
                timeRemaining: initialTime ? initialTime.total_seconds : 0,
                days: initialTime ? initialTime.days : 0,
                hours: initialTime ? initialTime.hours : 0,
                minutes: initialTime ? initialTime.minutes : 0,
                seconds: initialTime ? initialTime.seconds : 0,

                startCountdown() {
                    if (this.timeRemaining <= 0) return;

                    setInterval(() => {
                        if (this.timeRemaining > 0) {
                            this.timeRemaining--;
                            this.updateTime();
                        }
                    }, 1000);
                },

                updateTime() {
                    this.days = Math.floor(this.timeRemaining / 86400);
                    this.hours = Math.floor((this.timeRemaining % 86400) / 3600);
                    this.minutes = Math.floor((this.timeRemaining % 3600) / 60);
                    this.seconds = this.timeRemaining % 60;
                },

                formatTime() {
                    const pad = (num) => String(num).padStart(2, '0');
                    
                    if (this.days > 0) {
                        return `${this.days}D ${pad(this.hours)}H ${pad(this.minutes)}M ${pad(this.seconds)}S`;
                    }
                    
                    return `${pad(this.hours)}H ${pad(this.minutes)}M ${pad(this.seconds)}S`;
                }
            }
        }
    </script>
@endif
