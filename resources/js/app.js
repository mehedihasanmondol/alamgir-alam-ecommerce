import '../css/app.css';
import './bootstrap';

// Import CropperJS (CSS will be linked in HTML)
import Cropper from 'cropperjs';
window.Cropper = Cropper;

// Import Sortable.js for drag-and-drop
import Sortable from 'sortablejs';
window.Sortable = Sortable;

// Import image cropper module
import './image-cropper.js';

// Import product images sortable module
import './product-images-sortable.js';

// Note: Alpine.js is included with Livewire 3, no need to import/start it separately
