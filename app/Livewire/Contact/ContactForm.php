<?php

namespace App\Livewire\Contact;

use Livewire\Component;
use App\Modules\Contact\Services\ContactService;
use Illuminate\Support\Facades\RateLimiter;

class ContactForm extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = '';
    public $message = '';
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:10|max:5000',
    ];

    protected $messages = [
        'name.required' => 'Please enter your name',
        'email.required' => 'Please enter your email address',
        'email.email' => 'Please enter a valid email address',
        'subject.required' => 'Please enter a subject',
        'message.required' => 'Please enter your message',
        'message.min' => 'Message must be at least 10 characters',
        'message.max' => 'Message must not exceed 5000 characters',
    ];

    public function submit(ContactService $contactService)
    {
        // Rate limiting
        $key = 'contact-form:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->errorMessage = 'Too many attempts. Please try again later.';
            return;
        }

        $this->validate();

        try {
            RateLimiter::hit($key, 300); // 5 minutes

            $contactService->storeMessage([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);

            $this->successMessage = 'Thank you for contacting us! We will get back to you soon.';
            $this->errorMessage = '';
            $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while sending your message. Please try again.';
            $this->successMessage = '';
        }
    }

    public function render()
    {
        return view('livewire.contact.contact-form');
    }
}
