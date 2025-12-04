<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Payment Gateway Admin Controller
 * 
 * Manages payment gateway configurations
 */
class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        return view('admin.payment-gateways.index', compact('gateways'));
    }

    public function edit(PaymentGateway $gateway)
    {
        return view('admin.payment-gateways.edit', compact('gateway'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_test_mode' => 'boolean',
            'sort_order' => 'integer',
            'credentials' => 'required|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Handle logo upload with WebP compression
        if ($request->hasFile('logo')) {
            $imageService = app(ImageCompressionService::class);
            
            // Delete old logo if exists
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            
            // Compress and store as WebP
            $logoPath = $imageService->compressAndStore(
                $request->file('logo'),
                'payment-gateways',
                'public'
            );
            $validated['logo'] = $logoPath;
        }

        $gateway->update($validated);

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Payment gateway updated successfully');
    }

    public function toggleStatus(PaymentGateway $gateway)
    {
        $gateway->update(['is_active' => !$gateway->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $gateway->is_active,
        ]);
    }
}
