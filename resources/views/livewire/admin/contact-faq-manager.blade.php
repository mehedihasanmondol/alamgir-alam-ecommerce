<div>
    <!-- Add/Edit Form -->
    @if($showAddForm)
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $editingId ? 'Edit FAQ' : 'Add New FAQ' }}
            </h3>
            <button wire:click="cancelAdd" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4">
            <!-- Question -->
            <div>
                <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                    Question <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    wire:model="question"
                    id="question"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('question') ? 'border-red-500' : '' }}"
                    placeholder="Enter FAQ question"
                >
                @error('question')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Answer -->
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                    Answer <span class="text-red-500">*</span>
                </label>
                <textarea 
                    wire:model="answer"
                    id="answer"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('answer') ? 'border-red-500' : '' }}"
                    placeholder="Enter FAQ answer"
                ></textarea>
                @error('answer')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select 
                        wire:model="is_active"
                        id="is_active"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Display Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                        Display Order
                    </label>
                    <input 
                        type="number" 
                        wire:model="order"
                        id="order"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="0"
                    >
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <button 
                    wire:click="cancelAdd"
                    type="button" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button 
                    wire:click="saveFaq"
                    type="button" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ $editingId ? 'Update' : 'Add' }} FAQ
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Add Button (when form is hidden) -->
    @if(!$showAddForm)
    <div class="mb-6">
        <button 
            wire:click="showAdd"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Add New FAQ
        </button>
    </div>
    @endif

    <!-- FAQs List -->
    @if($faqs->count() > 0)
    <div class="space-y-4">
        @foreach($faqs as $faq)
        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $faq->question }}</h4>
                        @if($faq->is_active)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                <i class="fas fa-eye-slash mr-1"></i> Inactive
                            </span>
                        @endif
                        <span class="text-xs text-gray-500">Order: {{ $faq->order }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mt-2">{{ $faq->answer }}</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 ml-4">
                    <button 
                        wire:click="toggleStatus({{ $faq->id }})"
                        class="p-2 text-gray-600 hover:text-blue-600 transition-colors"
                        title="{{ $faq->is_active ? 'Deactivate' : 'Activate' }}">
                        <i class="fas fa-{{ $faq->is_active ? 'eye-slash' : 'eye' }}"></i>
                    </button>
                    <button 
                        wire:click="editFaq({{ $faq->id }})"
                        class="p-2 text-blue-600 hover:text-blue-800 transition-colors"
                        title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button 
                        wire:click="deleteFaq({{ $faq->id }})"
                        wire:confirm="Are you sure you want to delete this FAQ?"
                        class="p-2 text-red-600 hover:text-red-800 transition-colors"
                        title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <i class="fas fa-question-circle text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-500 text-lg font-medium">No FAQs yet</p>
        <p class="text-gray-400 text-sm mt-2">Click "Add New FAQ" to create your first frequently asked question</p>
    </div>
    @endif
</div>
