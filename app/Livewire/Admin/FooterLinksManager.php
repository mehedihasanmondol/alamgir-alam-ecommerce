<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\FooterLink;

class FooterLinksManager extends Component
{
    public $sections = [
        'about' => 'About',
        'company' => 'Company', 
        'resources' => 'Resources',
        'customer_support' => 'Customer Support'
    ];
    
    // Modal states
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    // Form data
    public $selectedSection = '';
    public $linkTitle = '';
    public $linkUrl = '';
    public $editingLinkId = null;
    public $deletingLinkId = null;
    
    // Refresh trigger
    public $refreshLinks = 0;
    
    protected $rules = [
        'linkTitle' => 'required|string|max:255',
        'linkUrl' => 'required|string|max:255',
        'selectedSection' => 'required|string'
    ];

    protected $messages = [
        'linkTitle.required' => 'Link title is required.',
        'linkUrl.required' => 'Link URL is required.',
        'selectedSection.required' => 'Please select a section.'
    ];

    public function getLinksProperty()
    {
        // The refreshLinks property forces re-computation when incremented
        $this->refreshLinks;
        
        return FooterLink::orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');
    }

    public function openAddModal($section)
    {
        $this->resetForm();
        $this->selectedSection = $section;
        $this->showAddModal = true;
    }

    public function openEditModal($linkId)
    {
        $link = FooterLink::find($linkId);
        if ($link) {
            $this->editingLinkId = $linkId;
            $this->selectedSection = $link->section;
            $this->linkTitle = $link->title;
            $this->linkUrl = $link->url;
            $this->showEditModal = true;
        }
    }

    public function openDeleteModal($linkId)
    {
        $this->deletingLinkId = $linkId;
        $this->showDeleteModal = true;
    }

    public function addLink()
    {
        $this->validate();

        $maxSortOrder = FooterLink::where('section', $this->selectedSection)->max('sort_order') ?? 0;

        FooterLink::create([
            'section' => $this->selectedSection,
            'title' => $this->linkTitle,
            'url' => $this->linkUrl,
            'sort_order' => $maxSortOrder + 1,
            'is_active' => true
        ]);

        $this->refreshLinks++;
        $this->closeAddModal();
        $this->dispatch('link-added', 'Link added successfully!');
    }

    public function updateLink()
    {
        $this->validate();

        $link = FooterLink::find($this->editingLinkId);
        if ($link) {
            $link->update([
                'title' => $this->linkTitle,
                'url' => $this->linkUrl,
                'section' => $this->selectedSection
            ]);

            $this->refreshLinks++;
            $this->closeEditModal();
            $this->dispatch('link-updated', 'Link updated successfully!');
        }
    }

    public function deleteLink()
    {
        $link = FooterLink::find($this->deletingLinkId);
        if ($link) {
            $link->delete();
            $this->refreshLinks++;
            $this->closeDeleteModal();
            $this->dispatch('link-deleted', 'Link deleted successfully!');
        }
    }

    public function toggleActive($linkId)
    {
        $link = FooterLink::find($linkId);
        if ($link) {
            $link->update(['is_active' => !$link->is_active]);
            $this->refreshLinks++;
            $this->dispatch('link-toggled', 'Link status updated!');
        }
    }

    public function moveUp($linkId)
    {
        $link = FooterLink::find($linkId);
        if ($link && $link->sort_order > 1) {
            $previousLink = FooterLink::where('section', $link->section)
                ->where('sort_order', $link->sort_order - 1)
                ->first();
            
            if ($previousLink) {
                $tempOrder = $link->sort_order;
                $link->update(['sort_order' => $previousLink->sort_order]);
                $previousLink->update(['sort_order' => $tempOrder]);
                $this->refreshLinks++;
            }
        }
    }

    public function moveDown($linkId)
    {
        $link = FooterLink::find($linkId);
        if ($link) {
            $nextLink = FooterLink::where('section', $link->section)
                ->where('sort_order', $link->sort_order + 1)
                ->first();
            
            if ($nextLink) {
                $tempOrder = $link->sort_order;
                $link->update(['sort_order' => $nextLink->sort_order]);
                $nextLink->update(['sort_order' => $tempOrder]);
                $this->refreshLinks++;
            }
        }
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetForm();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingLinkId = null;
    }

    private function resetForm()
    {
        $this->linkTitle = '';
        $this->linkUrl = '';
        $this->selectedSection = '';
        $this->editingLinkId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.footer-links-manager');
    }
}
