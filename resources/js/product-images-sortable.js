/**
 * Product Images Sortable - Drag and Drop Reordering
 * Uses Sortable.js to enable image reordering in product form
 */

document.addEventListener('DOMContentLoaded', function() {
    initProductImagesSortable();
});

// Reinitialize after Livewire updates
document.addEventListener('livewire:navigated', function() {
    initProductImagesSortable();
});

// Also listen for Livewire updates
document.addEventListener('livewire:update', function() {
    setTimeout(() => {
        initProductImagesSortable();
    }, 100);
});

function initProductImagesSortable() {
    const sortableContainer = document.getElementById('product-images-sortable');
    
    if (!sortableContainer) {
        return; // Element not on this page
    }
    
    // Destroy existing sortable instance if exists
    if (sortableContainer.sortableInstance) {
        sortableContainer.sortableInstance.destroy();
    }
    
    // Create new sortable instance
    sortableContainer.sortableInstance = Sortable.create(sortableContainer, {
        animation: 200,
        handle: '.drag-handle',
        draggable: '.cursor-move',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        
        onEnd: function(evt) {
            const oldIndex = evt.oldIndex;
            const newIndex = evt.newIndex;
            
            // Only dispatch if position actually changed
            if (oldIndex !== newIndex) {
                // Dispatch Livewire event
                if (window.Livewire) {
                    window.Livewire.dispatch('reorderImages', {
                        oldIndex: oldIndex,
                        newIndex: newIndex
                    });
                }
            }
        }
    });
}

// Add CSS for sortable states
const style = document.createElement('style');
style.textContent = `
    .sortable-ghost {
        opacity: 0.4;
        background: #f3f4f6;
    }
    
    .sortable-chosen {
        cursor: grabbing !important;
    }
    
    .sortable-drag {
        opacity: 0.8;
        transform: rotate(2deg);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
`;
document.head.appendChild(style);
