<?php

namespace App\Livewire\Admin;

use App\Models\ContactFaq;
use Livewire\Component;

class ContactFaqManager extends Component
{
    public $faqs;
    public $editingId = null;
    public $question = '';
    public $answer = '';
    public $is_active = true;
    public $order = 0;
    public $showAddForm = false;

    protected $rules = [
        'question' => 'required|string|max:500',
        'answer' => 'required|string',
        'is_active' => 'boolean',
        'order' => 'nullable|integer',
    ];

    public function mount($faqs)
    {
        $this->faqs = $faqs;
    }

    public function showAdd()
    {
        $this->showAddForm = true;
        $this->resetForm();
    }

    public function cancelAdd()
    {
        $this->showAddForm = false;
        $this->resetForm();
    }

    public function saveFaq()
    {
        $this->validate();

        if ($this->editingId) {
            // Update existing
            $faq = ContactFaq::findOrFail($this->editingId);
            $faq->update([
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->is_active,
                'order' => $this->order ?: 0,
            ]);
            session()->flash('success', 'FAQ updated successfully.');
        } else {
            // Create new
            ContactFaq::create([
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->is_active,
                'order' => $this->order ?: 0,
            ]);
            session()->flash('success', 'FAQ added successfully.');
        }

        $this->resetForm();
        $this->showAddForm = false;
        $this->faqs = ContactFaq::ordered()->get();
    }

    public function editFaq($id)
    {
        $faq = ContactFaq::findOrFail($id);
        $this->editingId = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->is_active = $faq->is_active;
        $this->order = $faq->order;
        $this->showAddForm = true;
    }

    public function deleteFaq($id)
    {
        ContactFaq::findOrFail($id)->delete();
        $this->faqs = ContactFaq::ordered()->get();
        session()->flash('success', 'FAQ deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $faq = ContactFaq::findOrFail($id);
        $faq->update(['is_active' => !$faq->is_active]);
        $this->faqs = ContactFaq::ordered()->get();
        session()->flash('success', 'FAQ status updated.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->question = '';
        $this->answer = '';
        $this->is_active = true;
        $this->order = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.contact-faq-manager');
    }
}
