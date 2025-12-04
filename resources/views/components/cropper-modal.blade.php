<div x-data="cropperModal()" 
    x-show="showModal" 
    x-cloak
    @open-cropper.window="openCropperWithImage($event.detail)"
    class="fixed inset-0 z-50 overflow-y-auto" 
    @keydown.escape.window="closeCropper()"
    style="display: none;">
    
    {{-- Backdrop with blur --}}
    <div class="fixed inset-0 transition-all duration-300" 
         style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         @click="closeCropper()"></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative rounded-lg shadow-2xl w-full border border-gray-200"
             style="max-width: 95vw; max-height: 95vh; background-color: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Crop & Edit Image</h3>
                    <button type="button" @click="closeCropper()" 
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6" style="max-height: calc(95vh - 140px); overflow: hidden;">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-full">
                    {{-- Image Area --}}
                    <div class="lg:col-span-3 flex items-center justify-center">
                        <div class="bg-gray-900 rounded-lg overflow-hidden w-full" style="height: 70vh; max-height: 700px; min-height: 400px;">
                            <img x-ref="cropperImage" :src="currentImageSrc" 
                                alt="Image to crop" 
                                style="display: block; max-width: 100%; max-height: 100%; width: auto; height: auto; margin: 0 auto;">
                        </div>
                    </div>

                    {{-- Controls --}}
                    <div class="space-y-4 overflow-y-auto" style="max-height: 70vh;">

                        {{-- Aspect Ratio --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Aspect Ratio</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" 
                                    @click="selectedAspectRatio = 'free'; changeAspectRatio()"
                                    :class="{ 'bg-blue-600 text-white border-blue-600': selectedAspectRatio === 'free', 'bg-white text-gray-700 border-gray-300 hover:border-blue-400': selectedAspectRatio !== 'free' }"
                                    class="px-3 py-2.5 border rounded-lg text-sm font-medium transition-all">
                                    Free
                                </button>
                                <button type="button" 
                                    @click="selectedAspectRatio = '1'; changeAspectRatio()"
                                    :class="{ 'bg-blue-600 text-white border-blue-600': selectedAspectRatio === '1', 'bg-white text-gray-700 border-gray-300 hover:border-blue-400': selectedAspectRatio !== '1' }"
                                    class="px-3 py-2.5 border rounded-lg text-sm font-medium transition-all">
                                    1:1
                                </button>
                                <button type="button" 
                                    @click="selectedAspectRatio = '1.777'; changeAspectRatio()"
                                    :class="{ 'bg-blue-600 text-white border-blue-600': selectedAspectRatio === '1.777', 'bg-white text-gray-700 border-gray-300 hover:border-blue-400': selectedAspectRatio !== '1.777' }"
                                    class="px-3 py-2.5 border rounded-lg text-sm font-medium transition-all">
                                    16:9
                                </button>
                                <button type="button" 
                                    @click="selectedAspectRatio = '1.333'; changeAspectRatio()"
                                    :class="{ 'bg-blue-600 text-white border-blue-600': selectedAspectRatio === '1.333', 'bg-white text-gray-700 border-gray-300 hover:border-blue-400': selectedAspectRatio !== '1.333' }"
                                    class="px-3 py-2.5 border rounded-lg text-sm font-medium transition-all">
                                    4:3
                                </button>
                            </div>
                        </div>

                        {{-- Transform Controls --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Transform</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" @click="rotate(90)" 
                                    class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                    <span class="text-lg">↻</span> Rotate Right
                                </button>
                                <button type="button" @click="rotate(-90)" 
                                    class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                    <span class="text-lg">↺</span> Rotate Left
                                </button>
                                <button type="button" @click="flip('horizontal')" 
                                    class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                    <span class="text-lg">⇄</span> Flip H
                                </button>
                                <button type="button" @click="flip('vertical')" 
                                    class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                    <span class="text-lg">⇅</span> Flip V
                                </button>
                            </div>
                        </div>

                        {{-- Zoom Controls --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Zoom</label>
                            <div class="flex gap-2">
                                <button type="button" @click="zoom(0.1)" 
                                    class="flex-1 px-3 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                    <span class="text-lg">+</span> Zoom In
                                </button>
                                <button type="button" @click="zoom(-0.1)" 
                                    class="flex-1 px-3 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                                    <span class="text-lg">−</span> Zoom Out
                                </button>
                            </div>
                        </div>

                        {{-- Reset --}}
                        <button type="button" @click="reset()" 
                            class="w-full px-3 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                            🔄 Reset All
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" @click="closeCropper()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="saveCropped()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Apply Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
