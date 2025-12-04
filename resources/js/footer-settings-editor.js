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

// Global editor instances
let newsletterEditor;
let copyrightEditor;

// Initialize CKEditors when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Minimal CKEditor for Newsletter Description
    const newsletterElement = document.getElementById('newsletter-description-editor');
    if (newsletterElement) {
        initMinimalCKEditor('#newsletter-description-editor', {
            placeholder: 'Enter newsletter description...'
        }).then(editor => {
            newsletterEditor = editor;
            console.log('CKEditor initialized for newsletter description');
            
            // Sync with form
            editor.model.document.on('change:data', () => {
                newsletterElement.value = editor.getData();
            });
        }).catch(error => {
            console.error('Error initializing newsletter CKEditor:', error);
        });
    }

    // Initialize Minimal CKEditor for Copyright Text
    const copyrightElement = document.getElementById('copyright-text-editor');
    if (copyrightElement) {
        initMinimalCKEditor('#copyright-text-editor', {
            placeholder: 'Enter copyright text...'
        }).then(editor => {
            copyrightEditor = editor;
            console.log('CKEditor initialized for copyright text');
            
            // Sync with form
            editor.model.document.on('change:data', () => {
                copyrightElement.value = editor.getData();
            });
        }).catch(error => {
            console.error('Error initializing copyright CKEditor:', error);
        });
    }
});

// Initialize Minimal CKEditor for footer settings
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

// Export for global use
window.newsletterEditor = () => newsletterEditor;
window.copyrightEditor = () => copyrightEditor;
