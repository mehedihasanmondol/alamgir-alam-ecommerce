import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Underline,
    Paragraph,
    Link,
    List,
    RemoveFormat,
    Alignment
} from 'ckeditor5';

// Store all editor instances
const editorInstances = new Map();

// Initialize CKEditors when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCKEditors();
});

// Listen for Livewire navigation (if using Livewire SPA mode)
document.addEventListener('livewire:navigated', function() {
    initializeCKEditors();
});

// Initialize all CKEditor instances
function initializeCKEditors() {
    const editorElements = document.querySelectorAll('.ckeditor-content-minimal');
    
    editorElements.forEach(element => {
        // Skip if already initialized
        if (editorInstances.has(element.id)) {
            return;
        }
        
        const settingKey = element.getAttribute('data-setting-key');
        
        initMinimalCKEditor(element, {
            placeholder: 'Enter content...'
        }).then(editor => {
            editorInstances.set(element.id, editor);
            console.log(`CKEditor initialized for ${settingKey}`);
            
            // Sync with Livewire on content change
            editor.model.document.on('change:data', () => {
                const content = editor.getData();
                // Update the textarea value
                element.value = content;
                
                // Dispatch Livewire update
                if (window.Livewire) {
                    const component = window.Livewire.find(
                        element.closest('[wire\\:id]')?.getAttribute('wire:id')
                    );
                    if (component) {
                        component.set(`settings.${settingKey}`, content);
                    }
                }
            });
        }).catch(error => {
            console.error(`Error initializing CKEditor for ${settingKey}:`, error);
        });
    });
}

// Initialize Minimal CKEditor for site settings
function initMinimalCKEditor(element, options = {}) {
    if (!element) {
        console.error('CKEditor: Element not found');
        return Promise.reject(new Error('Element not found'));
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
            Alignment,
            RemoveFormat
        ],
        toolbar: {
            items: [
                'undo', 'redo',
                '|',
                'bold', 'italic', 'underline',
                '|',
                'alignment',
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
        alignment: {
            options: [
                { name: 'left', className: 'text-left' },
                { name: 'center', className: 'text-center' },
                { name: 'right', className: 'text-right' },
                { name: 'justify', className: 'text-justify' }
            ]
        },
        placeholder: options.placeholder || 'Write your content here...',
        ...options
    };

    return ClassicEditor
        .create(element, minimalConfig)
        .then(editor => {
            console.log('Minimal CKEditor initialized successfully');
            
            // Ensure content is saved on form submit
            const form = element.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    element.value = editor.getData();
                });
            }
            
            return editor;
        })
        .catch(error => {
            console.error('Error initializing minimal CKEditor:', error);
            throw error;
        });
}

// Cleanup function for when component is destroyed
window.addEventListener('beforeunload', () => {
    editorInstances.forEach((editor, id) => {
        if (editor) {
            editor.destroy().catch(error => {
                console.error(`Error destroying editor ${id}:`, error);
            });
        }
    });
    editorInstances.clear();
});

// Export for global use
window.siteSettingsEditors = editorInstances;
