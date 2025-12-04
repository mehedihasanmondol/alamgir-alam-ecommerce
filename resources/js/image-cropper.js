import Cropper from 'cropperjs';

/**
 * Image Cropper Component for Universal Image Uploader
 */
window.initImageCropper = function(imageElement, options = {}) {
    const defaultOptions = {
        aspectRatio: NaN, // Free crop by default
        viewMode: 1, // Restrict crop box to not exceed canvas
        dragMode: 'move',
        autoCropArea: 1, // Make crop box fill 100% of container
        restore: false,
        guides: true,
        center: true,
        highlight: true,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        responsive: true,
        checkOrientation: true,
        modal: true,
        background: true,
        zoomable: true,
        zoomOnWheel: true,
        zoomOnTouch: true,
        rotatable: true,
        scalable: true,
        minContainerWidth: 200,
        minContainerHeight: 200,
        ready() {
            console.log('Cropper ready');
        },
        ...options
    };

    return new Cropper(imageElement, defaultOptions);
};

/**
 * Get cropped canvas as blob
 */
window.getCroppedBlob = function(cropperInstance, quality = 0.7) {
    return new Promise((resolve, reject) => {
        const canvas = cropperInstance.getCroppedCanvas();
        
        if (!canvas) {
            reject(new Error('Failed to get cropped canvas'));
            return;
        }

        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
            } else {
                reject(new Error('Failed to create blob'));
            }
        }, 'image/webp', quality);
    });
};

/**
 * Get cropped canvas as base64 data URL
 */
window.getCroppedDataURL = function(cropperInstance, quality = 0.7) {
    const canvas = cropperInstance.getCroppedCanvas();
    
    if (!canvas) {
        throw new Error('Failed to get cropped canvas');
    }

    return canvas.toDataURL('image/webp', quality);
};

/**
 * Set aspect ratio presets
 */
window.setAspectRatio = function(cropperInstance, ratio) {
    if (!cropperInstance || typeof cropperInstance.setAspectRatio !== 'function') {
        console.error('Invalid cropper instance or setAspectRatio method not available');
        return;
    }
    
    try {
        if (ratio === 'free' || ratio === null) {
            cropperInstance.setAspectRatio(NaN);
        } else {
            cropperInstance.setAspectRatio(parseFloat(ratio));
        }
        console.log('Aspect ratio changed to:', ratio);
    } catch (error) {
        console.error('Failed to set aspect ratio:', error);
    }
};

/**
 * Estimate compressed file size
 */
window.estimateCompressedSize = function(cropperInstance, quality = 0.7) {
    try {
        const dataURL = window.getCroppedDataURL(cropperInstance, quality);
        // Remove data URL prefix
        const base64 = dataURL.split(',')[1];
        // Calculate size in bytes (base64 is ~33% larger than binary)
        const sizeInBytes = (base64.length * 3) / 4;
        
        return {
            bytes: sizeInBytes,
            kb: (sizeInBytes / 1024).toFixed(2),
            mb: (sizeInBytes / (1024 * 1024)).toFixed(2)
        };
    } catch (error) {
        console.error('Failed to estimate size:', error);
        return { bytes: 0, kb: 0, mb: 0 };
    }
};

/**
 * Alpine.js component for cropper modal
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('cropperModal', () => ({
        showModal: false,
        cropperInstance: null,
        currentImageIndex: null,
        currentImageSrc: null,
        selectedAspectRatio: 'free',
        compressionQuality: 70,
        estimatedSize: { kb: 0, mb: 0 },
        scaleX: 1,
        scaleY: 1,
        
        aspectRatioPresets: [
            { label: 'Free', value: 'free' },
            { label: 'Square (1:1)', value: '1' },
            { label: 'Landscape (16:9)', value: '1.7778' },
            { label: 'Portrait (9:16)', value: '0.5625' },
            { label: 'Wide (21:9)', value: '2.3333' },
            { label: '4:3', value: '1.3333' },
            { label: '3:2', value: '1.5' },
        ],

        init() {
            // Listen for cropper open event
            window.addEventListener('open-cropper', (event) => {
                this.openCropper(event.detail.index, event.detail.src);
            });
        },

        openCropperWithImage(detail) {
            this.openCropper(detail.index, detail.imageUrl);
        },

        openCropper(index, imageSrc) {
            console.log('Opening cropper for index:', index);
            
            // ALWAYS destroy and reset first
            if (this.cropperInstance) {
                console.log('Destroying existing cropper instance');
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }
            
            // Reset state
            this.selectedAspectRatio = 'free';
            this.currentImageIndex = index;
            this.currentImageSrc = imageSrc;
            this.scaleX = 1;
            this.scaleY = 1;
            this.showModal = true;
            
            // Wait for modal to render, then init cropper
            this.$nextTick(() => {
                setTimeout(() => {
                    const imageElement = this.$refs.cropperImage;
                    
                    if (!imageElement) {
                        console.error('Image element not found');
                        return;
                    }
                    
                    // Function to initialize cropper
                    const initCropper = () => {
                        console.log('Initializing cropper');
                        
                        // Destroy existing instance if any
                        if (this.cropperInstance) {
                            try {
                                this.cropperInstance.destroy();
                            } catch (e) {
                                console.warn('Error destroying previous instance:', e);
                            }
                            this.cropperInstance = null;
                        }
                        
                        // Initialize cropper
                        this.cropperInstance = window.initImageCropper(imageElement, {
                            aspectRatio: NaN,
                            viewMode: 1,
                            autoCropArea: 1
                        });
                        
                        console.log('Cropper initialized:', this.cropperInstance);
                    };
                    
                    // Check if image is already loaded
                    if (imageElement.complete && imageElement.naturalHeight !== 0) {
                        initCropper();
                    } else {
                        // Wait for image to load
                        imageElement.onload = initCropper;
                        imageElement.onerror = () => {
                            console.error('Failed to load image');
                        };
                    }
                }, 300);
            });
        },

        closeCropper() {
            if (this.cropperInstance) {
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }
            this.showModal = false;
            this.currentImageIndex = null;
            this.currentImageSrc = null;
        },

        changeAspectRatio() {
            if (!this.cropperInstance) {
                console.error('Cropper instance not available');
                return;
            }
            
            console.log('Changing aspect ratio to:', this.selectedAspectRatio);
            window.setAspectRatio(this.cropperInstance, this.selectedAspectRatio);
        },

        updateEstimatedSize() {
            if (this.cropperInstance) {
                const quality = this.compressionQuality / 100;
                this.estimatedSize = window.estimateCompressedSize(this.cropperInstance, quality);
            }
        },

        async saveCropped() {
            if (!this.cropperInstance) {
                console.error('Cropper instance not available');
                alert('Cropper not initialized. Please try again.');
                return;
            }

            try {
                const quality = this.compressionQuality / 100;
                const dataURL = window.getCroppedDataURL(this.cropperInstance, quality);
                
                // Emit event with cropped image data
                window.dispatchEvent(new CustomEvent('cropped-image-saved', {
                    detail: {
                        index: this.currentImageIndex,
                        dataURL: dataURL
                    }
                }));

                this.closeCropper();
            } catch (error) {
                console.error('Failed to save cropped image:', error);
                alert('Failed to save cropped image: ' + error.message);
            }
        },

        rotate(degrees) {
            if (!this.cropperInstance) {
                console.error('Cropper instance not available');
                return;
            }
            
            if (typeof this.cropperInstance.rotate !== 'function') {
                console.error('Rotate method not available on cropper instance');
                return;
            }
            
            try {
                this.cropperInstance.rotate(degrees);
                console.log('Rotated by:', degrees);
            } catch (error) {
                console.error('Rotate failed:', error);
            }
        },

        flip(direction) {
            if (!this.cropperInstance) {
                console.error('Cropper instance not available');
                return;
            }
            
            if (typeof this.cropperInstance.scaleX !== 'function' || typeof this.cropperInstance.scaleY !== 'function') {
                console.error('Scale methods not available on cropper instance');
                return;
            }
            
            try {
                if (direction === 'horizontal') {
                    this.scaleX = -this.scaleX;
                    this.cropperInstance.scaleX(this.scaleX);
                    console.log('Flipped horizontally, scaleX:', this.scaleX);
                } else {
                    this.scaleY = -this.scaleY;
                    this.cropperInstance.scaleY(this.scaleY);
                    console.log('Flipped vertically, scaleY:', this.scaleY);
                }
            } catch (error) {
                console.error('Flip failed:', error);
            }
        },

        reset() {
            if (!this.cropperInstance) {
                console.error('Cropper instance not available');
                return;
            }
            
            if (typeof this.cropperInstance.reset !== 'function') {
                console.error('Reset method not available on cropper instance');
                return;
            }
            
            try {
                this.cropperInstance.reset();
                this.scaleX = 1;
                this.scaleY = 1;
                console.log('Cropper reset');
            } catch (error) {
                console.error('Reset failed:', error);
            }
        },

        zoom(factor) {
            if (!this.cropperInstance) {
                console.error('Cropper instance not available');
                return;
            }
            
            if (typeof this.cropperInstance.zoom !== 'function') {
                console.error('Zoom method not available on cropper instance');
                return;
            }
            
            try {
                this.cropperInstance.zoom(factor);
                console.log('Zoomed by:', factor);
            } catch (error) {
                console.error('Zoom failed:', error);
            }
        }
    }));
});

// Functions are already available on window object, no export needed
