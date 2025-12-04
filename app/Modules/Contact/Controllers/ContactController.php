<?php

namespace App\Modules\Contact\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contact\Requests\ContactMessageRequest;
use App\Modules\Contact\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(
        protected ContactService $contactService
    ) {}

    /**
     * Display the contact page
     */
    public function index(): View
    {
        $settings = $this->contactService->getContactSettings();
        $faqs = $this->contactService->getActiveFaqs();
        
        // Get chambers dynamically from appointment management
        $chambers = \App\Models\Chamber::active()->ordered()->get();

        return view('frontend.contact.index', compact('settings', 'faqs', 'chambers'));
    }

    /**
     * Store a contact message
     */
    public function store(ContactMessageRequest $request): RedirectResponse
    {
        try {
            $this->contactService->storeMessage($request->validated());

            return redirect()
                ->route('contact.index')
                ->with('success', 'Thank you for contacting us! We will get back to you soon.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while sending your message. Please try again.');
        }
    }
}
