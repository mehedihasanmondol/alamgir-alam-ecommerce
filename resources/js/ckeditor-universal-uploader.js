/**
 * Custom Upload Adapter for CKEditor with Universal Image Uploader
 * This adapter integrates CKEditor's image upload with the Livewire Universal Image Uploader
 */

import { ButtonView } from './ckeditor-init.js';

class UniversalImageUploadAdapter {
    constructor(loader) {
        this.loader = loader;
        this.uploadPromise = null;
    }

    upload() {
        return new Promise((resolve, reject) => {
            // Store resolve/reject for later use
            this.uploadPromise = { resolve, reject };

            // Open the Universal Image Uploader modal with multiple support
            window.dispatchEvent(new CustomEvent('open-ckeditor-uploader', {
                detail: {
                    field: 'ckeditor_upload',
                    multiple: true // Enable multiple image selection
                }
            }));

            // Listen for image upload completion
            const handleImageUploaded = (event) => {
                const { media, field } = event.detail;
                
                if (field === 'ckeditor_upload' && media && media.length > 0) {
                    // For paste/drop operations, only use the first image
                    const uploadedImage = media[0];
                    
                    // Resolve with the uploaded image URL
                    resolve({
                        default: uploadedImage.large_url
                    });
                    
                    // Clean up event listener
                    window.removeEventListener('imageUploaded', handleImageUploaded);
                    window.removeEventListener('imageSelected', handleImageSelected);
                    window.removeEventListener('ckeditor-upload-cancelled', handleUploadCancelled);
                }
            };

            const handleImageSelected = (event) => {
                const { media, field } = event.detail;
                
                if (field === 'ckeditor_upload' && media && media.length > 0) {
                    // For paste/drop operations, only use the first image
                    const selectedImage = media[0];
                    
                    // Resolve with the selected image URL
                    resolve({
                        default: selectedImage.large_url
                    });
                    
                    // Clean up event listeners
                    window.removeEventListener('imageUploaded', handleImageUploaded);
                    window.removeEventListener('imageSelected', handleImageSelected);
                    window.removeEventListener('ckeditor-upload-cancelled', handleUploadCancelled);
                }
            };

            const handleUploadCancelled = () => {
                reject('Upload cancelled');
                
                // Clean up event listeners
                window.removeEventListener('imageUploaded', handleImageUploaded);
                window.removeEventListener('imageSelected', handleImageSelected);
                window.removeEventListener('ckeditor-upload-cancelled', handleUploadCancelled);
            };

            // Add event listeners
            window.addEventListener('imageUploaded', handleImageUploaded);
            window.addEventListener('imageSelected', handleImageSelected);
            window.addEventListener('ckeditor-upload-cancelled', handleUploadCancelled);
        });
    }

    abort() {
        // Dispatch event to close modal
        window.dispatchEvent(new CustomEvent('ckeditor-upload-cancelled'));
    }
}

/**
 * Plugin function to register the custom upload adapter and override default button
 */
export function UniversalImageUploadPlugin(editor) {
    // Register custom upload adapter (for paste/drop operations)
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new UniversalImageUploadAdapter(loader);
    };

    // Create custom button for media library
    editor.ui.componentFactory.add('mediaLibrary', locale => {
        const button = new ButtonView(locale);
        
        button.set({
            label: 'Insert image from media library',
            icon: '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6.91 10.54c.26-.23.64-.21.88.03l3.36 3.14 2.23-2.06a.64.64 0 0 1 .87 0l2.52 2.97V4.5H3.2v10.12l3.71-4.08zm10.27-7.51c.6 0 1.09.47 1.09 1.05v11.84c0 .59-.49 1.06-1.09 1.06H2.79c-.6 0-1.09-.47-1.09-1.06V4.08c0-.58.49-1.05 1.1-1.05h14.38zm-5.22 5.56a1.96 1.96 0 1 1 3.4-1.96 1.96 1.96 0 0 1-3.4 1.96z"/></svg>',
            tooltip: true,
            withText: false
        });
        
        // Handle button click - open media library with multiple image support
        button.on('execute', () => {
            // Open media library modal directly with multiple selection enabled
            window.dispatchEvent(new CustomEvent('open-ckeditor-uploader', {
                detail: {
                    field: 'ckeditor_upload',
                    multiple: true // Enable multiple image selection
                }
            }));
            
            // Listen for selected/uploaded images
            const handleImageReady = (eventData) => {
                const { media, field } = eventData.detail;
                
                if (field === 'ckeditor_upload' && media && media.length > 0) {
                    // Insert ALL selected/uploaded images into editor
                    editor.model.change(writer => {
                        // Track the last inserted element to insert subsequent images after it
                        let lastInsertedElement = null;
                        
                        // Insert each image sequentially
                        media.forEach((image, index) => {
                            const imageElement = writer.createElement('imageBlock', {
                                src: image.large_url
                            });
                            
                            // Insert image at appropriate position
                            if (index === 0) {
                                // First image at current selection
                                editor.model.insertContent(imageElement, editor.model.document.selection);
                                lastInsertedElement = imageElement;
                            } else {
                                // Subsequent images after the last inserted one
                                const insertPosition = writer.createPositionAfter(lastInsertedElement);
                                editor.model.insertContent(imageElement, insertPosition);
                                lastInsertedElement = imageElement;
                            }
                        });
                    });
                    
                    // Clean up event listeners
                    window.removeEventListener('imageUploaded', handleImageReady);
                    window.removeEventListener('imageSelected', handleImageReady);
                }
            };
            
            window.addEventListener('imageUploaded', handleImageReady);
            window.addEventListener('imageSelected', handleImageReady);
        });
        
        return button;
    });
}

// Make it available globally for easier integration
window.UniversalImageUploadPlugin = UniversalImageUploadPlugin;
