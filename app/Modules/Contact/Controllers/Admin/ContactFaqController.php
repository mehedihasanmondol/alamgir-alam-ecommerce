<?php

namespace App\Modules\Contact\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactFaq;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactFaqController extends Controller
{
    /**
     * Display a listing of FAQs
     */
    public function index(): View
    {
        $faqs = ContactFaq::orderBy('order')->orderBy('id')->paginate(20);
        
        return view('admin.contact.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new FAQ
     */
    public function create(): View
    {
        return view('admin.contact.faqs.create');
    }

    /**
     * Store a newly created FAQ
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        ContactFaq::create($validated);

        return redirect()
            ->route('admin.contact.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    /**
     * Show the form for editing the FAQ
     */
    public function edit(ContactFaq $faq): View
    {
        return view('admin.contact.faqs.edit', compact('faq'));
    }

    /**
     * Update the FAQ
     */
    public function update(Request $request, ContactFaq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $faq->update($validated);

        return redirect()
            ->route('admin.contact.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    /**
     * Remove the FAQ
     */
    public function destroy(ContactFaq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.contact.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }
}
