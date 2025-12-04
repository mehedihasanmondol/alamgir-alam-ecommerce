@extends('layouts.admin')

@section('title', 'Add Product')

@push('styles')
<style>
/* CKEditor Custom Styling */

/* Force list markers to display (override Tailwind reset) */
.ck-content ul,
.ck-content ol {
    margin-left: 20px;
}

</style>
@endpush

@section('content')
    @livewire('admin.product.product-form')
@endsection

@push('scripts')
@vite('resources/js/product-editor.js')
<script>
// Product form specific scripts
let editorRetryCount = 0;
const maxRetries = 50; // Try for 5 seconds max

function initEditors() {
    // Check if CKEditor elements exist
    const descElement = document.getElementById('product-description-editor');
    const shortDescElement = document.getElementById('product-short-description-editor');
    
    if (!descElement && !shortDescElement) {
        editorRetryCount++;
        if (editorRetryCount < maxRetries) {
            setTimeout(initEditors, 100);
        } else {
            console.log('CKEditor: Elements not found after max retries. They may be on a different step.');
        }
        return;
    }
    
    // Reset retry count
    editorRetryCount = 0;
    
    console.log('CKEditor elements found - initialization handled by product-editor.js');
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded for product form');
    initEditors();
});

// Livewire event listeners
if (typeof Livewire !== 'undefined') {
    Livewire.hook('commit', ({ component, commit, respond }) => {
        // After any Livewire action completes
        setTimeout(() => {
            if (document.getElementById('product-description-editor')) {
                console.log('CKEditor elements found after commit');
                editorRetryCount = 0;
                initEditors();
            }
        }, 100);
    });
}
</script>
@endpush
