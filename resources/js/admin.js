import './bootstrap';
import Sortable from 'sortablejs';

// Make Sortable available globally for Livewire components
window.Sortable = Sortable;

// Initialize Sortable for secondary menu (if element exists)
document.addEventListener('DOMContentLoaded', function() {
    const sortableMenu = document.getElementById('sortable-menu');
    
    if (sortableMenu) {
        new Sortable(sortableMenu, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function(evt) {
                const order = [];
                document.querySelectorAll('#sortable-menu tr').forEach(row => {
                    const id = row.getAttribute('data-id');
                    if (id) order.push(id);
                });
                
                // Dispatch Livewire event to update order
                window.Livewire.dispatch('reorder-menu', { order: order });
            }
        });
    }
});
