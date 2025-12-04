<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessageTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 15;
    public $viewingMessage = null;
    public $deletingMessageId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($messageId)
    {
        $message = ContactMessage::findOrFail($messageId);
        $message->update(['status' => 'read']);
        
        session()->flash('success', 'Message marked as read.');
    }

    public function markAsReplied($messageId)
    {
        $message = ContactMessage::findOrFail($messageId);
        $message->update(['status' => 'replied']);
        
        session()->flash('success', 'Message marked as replied.');
    }

    public function archive($messageId)
    {
        $message = ContactMessage::findOrFail($messageId);
        $message->update(['status' => 'archived']);
        
        session()->flash('success', 'Message archived.');
    }

    public function viewMessage($messageId)
    {
        $this->viewingMessage = ContactMessage::findOrFail($messageId);
        
        // Mark as read when viewing
        if ($this->viewingMessage->status === 'unread') {
            $this->viewingMessage->update(['status' => 'read']);
        }
    }

    public function closeViewModal()
    {
        $this->viewingMessage = null;
    }

    public function confirmDelete($messageId)
    {
        $this->deletingMessageId = $messageId;
    }

    public function deleteMessage()
    {
        if ($this->deletingMessageId) {
            $message = ContactMessage::findOrFail($this->deletingMessageId);
            $message->delete();
            
            $this->deletingMessageId = null;
            session()->flash('success', 'Message deleted successfully.');
        }
    }

    public function cancelDelete()
    {
        $this->deletingMessageId = null;
    }

    public function render()
    {
        $query = ContactMessage::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $messages = $query->latest()->paginate($this->perPage);
        $unreadCount = ContactMessage::where('status', 'unread')->count();

        return view('livewire.admin.contact-message-table', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }
}
