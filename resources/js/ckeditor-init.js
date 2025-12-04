import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Paragraph,
    Heading,
    Link,
    List,
    ListProperties,
    BlockQuote,
    Table,
    TableToolbar,
    TableProperties,
    TableCellProperties,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    // ImageUpload - Not used, using custom plugin instead
    MediaEmbed,
    Indent,
    IndentBlock,
    Alignment,
    Font,
    RemoveFormat,
    SourceEditing,
    HorizontalLine,
    CodeBlock,
    Code,
    Subscript,
    Superscript,
    FindAndReplace,
    SpecialCharacters,
    SpecialCharactersEssentials,
    WordCount,
    Fullscreen,
    FileRepository,
    ButtonView
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';
import { UniversalImageUploadPlugin } from './ckeditor-universal-uploader.js';

// Export ButtonView for use in other modules
export { ButtonView };

// Initialize CKEditor on a textarea
export function initCKEditor(selector, options = {}) {
    const element = document.querySelector(selector);
    if (!element) {
        console.error(`CKEditor: Element ${selector} not found`);
        return null;
    }

    const defaultConfig = {
        licenseKey: 'GPL', // Free GPL license for open-source projects
        plugins: [
            Essentials,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Paragraph,
            Heading,
            Link,
            List,
            ListProperties,
            BlockQuote,
            Table,
            TableToolbar,
            TableProperties,
            TableCellProperties,
            Image,
            ImageToolbar,
            ImageCaption,
            ImageStyle,
            ImageResize,
            // ImageUpload removed - using custom plugin instead
            FileRepository,
            MediaEmbed,
            Indent,
            IndentBlock,
            Alignment,
            Font,
            RemoveFormat,
            SourceEditing,
            HorizontalLine,
            CodeBlock,
            Code,
            Subscript,
            Superscript,
            FindAndReplace,
            SpecialCharacters,
            SpecialCharactersEssentials,
            WordCount,
            Fullscreen,
            UniversalImageUploadPlugin
        ],
        toolbar: {
            items: [
                'undo', 'redo',
                '|',
                'heading',
                '|',
                'bold', 'italic', 'underline',
                '|',
                 'mediaLibrary', 'blockQuote',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'fullscreen',
                '|',
                'alignment',
                '|',
                'link',
                '|', // Additional features dropdown
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
                '|',
                'insertTable', 'mediaEmbed', 'codeBlock',
                '|',
                'outdent', 'indent',
                '|',
                'strikethrough', 'subscript', 'superscript', 'code',
                '|',
                'specialCharacters', 'horizontalLine',
                '|',
                'findAndReplace', 'removeFormat',
                '|',
                'sourceEditing'
            ],
            shouldNotGroupWhenFull: false // Enable toolbar wrapping/grouping
        },
        heading: {
            options: [
                { 
                    model: 'paragraph', 
                    title: 'Paragraph', 
                    class: 'ck-heading_paragraph'
                },
                { 
                    model: 'heading1', 
                    view: { name: 'h1', classes: 'text-4xl font-extrabold text-gray-900 leading-tight mt-0 mb-4' }, 
                    title: 'Heading 1', 
                    class: 'text-4xl font-extrabold ck-heading_heading1' 
                },
                { 
                    model: 'heading2', 
                    view: { name: 'h2', classes: 'text-3xl font-bold text-gray-800 leading-snug mt-6 mb-3' }, 
                    title: 'Heading 2', 
                    class: 'text-3xl font-bold ck-heading_heading2' 
                },
                { 
                    model: 'heading3', 
                    view: { name: 'h3', classes: 'text-2xl font-semibold text-gray-700 leading-normal mt-5 mb-2' }, 
                    title: 'Heading 3', 
                    class: 'text-2xl font-semibold ck-heading_heading3' 
                },
                { 
                    model: 'heading4', 
                    view: { name: 'h4', classes: 'text-xl font-semibold text-gray-600 leading-relaxed mt-4 mb-2' }, 
                    title: 'Heading 4', 
                    class: 'text-xl font-semibold ck-heading_heading4' 
                }
            ]
        },
        fontSize: {
            options: [
                { title: 'Tiny', model: '0.75rem', view: { name: 'span', classes: 'text-xs', styles: { 'font-size': '0.75rem' } } },
                { title: 'Small', model: '0.875rem', view: { name: 'span', classes: 'text-sm', styles: { 'font-size': '0.875rem' } } },
                // { title: 'Default', model: 'default', view: { name: 'span', classes: 'text-base', styles: { 'font-size': '0.875rem' }  } },
                { title: 'Large', model: '1.125rem', view: { name: 'span', classes: 'text-lg', styles: { 'font-size': '1.125rem' } } },
                { title: 'Extra Large', model: '1.25rem', view: { name: 'span', classes: 'text-xl', styles: { 'font-size': '1.25rem' } } },
                { title: 'Huge', model: '1.5rem', view: { name: 'span', classes: 'text-2xl', styles: { 'font-size': '1.5rem' } } }
            ],
            supportAllValues: true
        },
        fontFamily: {
            options: [
                { title: 'Default', model: undefined, view: { name: 'span', classes: 'font-sans' } },
                { title: 'Sans Serif', model: 'ui-sans-serif, system-ui, sans-serif', view: { name: 'span', classes: 'font-sans' } },
                { title: 'Serif', model: 'ui-serif, Georgia, serif', view: { name: 'span', classes: 'font-serif' } },
                { title: 'Monospace', model: 'ui-monospace, monospace', view: { name: 'span', classes: 'font-mono' } }
            ],
            supportAllValues: true
        },
        image: {
            toolbar: [
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side',
                '|',
                'toggleImageCaption',
                'imageTextAlternative',
                '|',
                'linkImage'
            ],
            styles: {
                options: [
                    {
                        name: 'inline',
                        title: 'Inline',
                        icon: 'left',
                        modelElements: ['imageInline'],
                        className: 'inline-block max-w-full h-auto'
                    },
                    {
                        name: 'block',
                        title: 'Block',
                        icon: 'center',
                        modelElements: ['imageBlock'],
                        className: 'block max-w-full h-auto rounded-lg shadow-md my-6 mx-auto'
                    },
                    {
                        name: 'side',
                        title: 'Side',
                        icon: 'right',
                        modelElements: ['imageBlock'],
                        className: 'float-right ml-4 mb-4 max-w-sm rounded-lg shadow-md'
                    }
                ]
            }
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells',
                'tableProperties',
                'tableCellProperties'
            ],
            tableProperties: {
                borderColors: [
                    { color: '#e5e7eb', label: 'Gray' },
                    { color: '#3b82f6', label: 'Blue' },
                    { color: '#10b981', label: 'Green' },
                    { color: '#ef4444', label: 'Red' },
                    { color: '#000000', label: 'Black' }
                ],
                backgroundColors: [
                    { color: '#ffffff', label: 'White' },
                    { color: '#f9fafb', label: 'Gray 50' },
                    { color: '#f3f4f6', label: 'Gray 100' },
                    { color: '#e5e7eb', label: 'Gray 200' },
                    { color: '#dbeafe', label: 'Blue 100' },
                    { color: '#dcfce7', label: 'Green 100' },
                    { color: '#fef3c7', label: 'Yellow 100' }
                ],
                defaultProperties: {
                    borderStyle: 'none',
                    borderWidth: '0px',
                    borderColor: '#e5e7eb',
                    alignment: 'center',
                    width: 'auto',
                    height: 'auto'
                }
            },
            tableCellProperties: {
                borderColors: [
                    { color: '#e5e7eb', label: 'Gray' },
                    { color: '#3b82f6', label: 'Blue' },
                    { color: '#10b981', label: 'Green' }
                ],
                backgroundColors: [
                    { color: '#ffffff', label: 'White' },
                    { color: '#f9fafb', label: 'Gray 50' },
                    { color: '#f3f4f6', label: 'Gray 100' },
                    { color: '#dbeafe', label: 'Blue 100' },
                    { color: '#dcfce7', label: 'Green 100' },
                    { color: '#fef3c7', label: 'Yellow 100' }
                ],
                defaultProperties: {
                    padding: '0.75rem',
                    borderStyle: 'solid',
                    borderWidth: '1px',
                    borderColor: '#e5e7eb'
                }
            }
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
                },
                defaultLink: {
                    mode: 'automatic',
                    callback: url => url,
                    attributes: {
                        class: 'text-blue-600 hover:text-blue-800 underline transition-colors'
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
        codeBlock: {
            languages: [
                { language: 'plaintext', label: 'Plain text', class: 'language-plaintext' },
                { language: 'javascript', label: 'JavaScript', class: 'language-javascript' },
                { language: 'php', label: 'PHP', class: 'language-php' },
                { language: 'html', label: 'HTML', class: 'language-html' },
                { language: 'css', label: 'CSS', class: 'language-css' },
                { language: 'python', label: 'Python', class: 'language-python' },
                { language: 'java', label: 'Java', class: 'language-java' },
                { language: 'sql', label: 'SQL', class: 'language-sql' },
                { language: 'json', label: 'JSON', class: 'language-json' }
            ]
        },
        list: {
            properties: {
                styles: {
                    useAttribute: false, // Use CSS list-style-type for better Tailwind integration
                    listStyleTypes: {
                        numbered: ['decimal', 'lower-alpha', 'upper-alpha', 'lower-roman', 'upper-roman'],
                        bulleted: ['disc', 'circle', 'square']
                    }
                },
                startIndex: true,
                reversed: true
            }
        },
        mediaEmbed: {
            previewsInData: true,
            providers: [
                {
                    name: 'youtube',
                    url: [
                        /^(?:m\.)?youtube\.com\/watch\?v=([\w-]+)/,
                        /^(?:m\.)?youtube\.com\/v\/([\w-]+)/,
                        /^youtube\.com\/embed\/([\w-]+)/,
                        /^youtu\.be\/([\w-]+)/
                    ],
                    html: match => {
                        const id = match[1];
                        return (
                            '<div class="aspect-w-16 aspect-h-9 my-6">' +
                                `<iframe src="https://www.youtube.com/embed/${id}" ` +
                                'frameborder="0" allow="autoplay; encrypted-media" allowfullscreen ' +
                                'class="w-full h-full rounded-lg shadow-lg">' +
                                '</iframe>' +
                            '</div>'
                        );
                    }
                },
                {
                    name: 'vimeo',
                    url: /^vimeo\.com\/(\d+)/,
                    html: match => {
                        const id = match[1];
                        return (
                            '<div class="aspect-w-16 aspect-h-9 my-6">' +
                                `<iframe src="https://player.vimeo.com/video/${id}" ` +
                                'frameborder="0" allow="autoplay; fullscreen" allowfullscreen ' +
                                'class="w-full h-full rounded-lg shadow-lg">' +
                                '</iframe>' +
                            '</div>'
                        );
                    }
                }
            ]
        },
        htmlEmbed: {
            showPreviews: true,
            sanitizeHtml: (inputHtml) => {
                return { html: inputHtml, hasChanged: false };
            }
        },
        placeholder: 'Write your content here...',
        ...options
    };

    return ClassicEditor
        .create(element, defaultConfig)
        .then(editor => {
            window.editor = editor; // Make editor globally accessible for debugging
            
            // Word count tracking
            const wordCountPlugin = editor.plugins.get('WordCount');
            const wordCountContainer = options.wordCountContainer || '#word-count';
            const wordCountElement = document.querySelector(wordCountContainer);
            
            if (wordCountElement) {
                wordCountElement.appendChild(wordCountPlugin.wordCountContainer);
            }

            console.log('CKEditor initialized successfully');
            return editor;
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
            throw error;
        });
}

// Export for global use
window.initCKEditor = initCKEditor;
