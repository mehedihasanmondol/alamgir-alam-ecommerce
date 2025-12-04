import { initCKEditor } from './ckeditor-init.js';
import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Underline,
    Paragraph,
    Link,
    List,
    RemoveFormat
} from 'ckeditor5';

// Global editor instances
let descriptionEditor;
let shortDescriptionEditor;

// Initialize CKEditors when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Full CKEditor for Product Description
    const descriptionElement = document.getElementById('product-description-editor');
    if (descriptionElement) {
        initCKEditor('#product-description-editor', {
            placeholder: 'Write detailed product description here...',
            wordCountContainer: '#description-word-count'
        }).then(editor => {
            descriptionEditor = editor;
            console.log('CKEditor initialized for product description');
            
            // Sync with Livewire wire:model
            editor.model.document.on('change:data', () => {
                const content = editor.getData();
                const hiddenInput = document.getElementById('product-description-hidden');
                if (hiddenInput) {
                    hiddenInput.value = content;
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }).catch(error => {
            console.error('Error initializing description CKEditor:', error);
        });
    }

    // Initialize Minimal CKEditor for Short Description
    const shortDescriptionElement = document.getElementById('product-short-description-editor');
    if (shortDescriptionElement) {
        initMinimalCKEditor('#product-short-description-editor', {
            placeholder: 'Brief product summary (1-2 sentences)...'
        }).then(editor => {
            shortDescriptionEditor = editor;
            console.log('CKEditor initialized for product short description');
            
            // Sync with Livewire wire:model
            editor.model.document.on('change:data', () => {
                const content = editor.getData();
                const hiddenInput = document.getElementById('product-short-description-hidden');
                if (hiddenInput) {
                    hiddenInput.value = content;
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }).catch(error => {
            console.error('Error initializing short description CKEditor:', error);
        });
    }

    // Listen for CKEditor uploader event and trigger Livewire modal
    window.addEventListener('open-ckeditor-uploader', (event) => {
        const { field, multiple } = event.detail;
        
        // Dispatch Livewire event to open the media library
        Livewire.dispatch('openMediaLibrary', { 
            field: field, 
            multiple: multiple 
        });
    });
});

// Initialize Minimal CKEditor for short descriptions
function initMinimalCKEditor(selector, options = {}) {
    const element = document.querySelector(selector);
    if (!element) {
        console.error(`CKEditor: Element ${selector} not found`);
        return Promise.reject(new Error(`Element ${selector} not found`));
    }

    const minimalConfig = {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Bold,
            Italic,
            Underline,
            Paragraph,
            Link,
            List,
            RemoveFormat
        ],
        toolbar: {
            items: [
                'undo', 'redo',
                '|',
                'bold', 'italic', 'underline',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'link',
                '|',
                'removeFormat'
            ],
            shouldNotGroupWhenFull: true
        },
        link: {
            decorators: {
                openInNewTab: {
                    mode: 'manual',
                    label: 'Open in a new tab',
                    attributes: {
                        target: '_blank',
                        rel: 'noopener noreferrer'
                    }
                }
            },
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://'
        },
        placeholder: options.placeholder || 'Write your content here...',
        ...options
    };

    return ClassicEditor
        .create(element, minimalConfig)
        .then(editor => {
            console.log('Minimal CKEditor initialized successfully');
            return editor;
        })
        .catch(error => {
            console.error('Error initializing minimal CKEditor:', error);
            throw error;
        });
}

// Reinitialize editors after Livewire updates (for product steps)
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated - reinitializing CKEditors...');
    setTimeout(() => {
        if (document.getElementById('product-description-editor') && !descriptionEditor) {
            location.reload(); // Force reload to properly initialize editors
        }
    }, 100);
});

// Export for global use
window.descriptionEditor = () => descriptionEditor;
window.shortDescriptionEditor = () => shortDescriptionEditor;
