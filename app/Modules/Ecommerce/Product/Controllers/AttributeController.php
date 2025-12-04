<?php

namespace App\Modules\Ecommerce\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Product\Models\ProductAttribute;
use App\Modules\Ecommerce\Product\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();
        return view('admin.product.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.product.attributes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'slug' => 'nullable|string|max:255|unique:product_attributes,slug',
            'type' => 'required|in:select,color,button',
            'is_visible' => 'boolean',
            'is_variation' => 'boolean',
            'values' => 'required|array|min:1',
            'values.*.value' => 'required|string|max:255',
            'values.*.color_code' => 'nullable|string|max:7',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_visible'] = $request->has('is_visible');
        $validated['is_variation'] = $request->has('is_variation');

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'],
            'is_visible' => $validated['is_visible'],
            'is_variation' => $validated['is_variation'],
            'position' => ProductAttribute::max('position') + 1,
        ]);

        foreach ($validated['values'] as $index => $valueData) {
            $attribute->values()->create([
                'value' => $valueData['value'],
                'slug' => Str::slug($valueData['value']),
                'color_code' => $valueData['color_code'] ?? null,
                'position' => $index,
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully!');
    }

    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('values');
        return view('admin.product.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,' . $attribute->id,
            'slug' => 'nullable|string|max:255|unique:product_attributes,slug,' . $attribute->id,
            'type' => 'required|in:select,color,button',
            'is_visible' => 'boolean',
            'is_variation' => 'boolean',
            'values' => 'required|array|min:1',
            'values.*.id' => 'nullable|exists:product_attribute_values,id',
            'values.*.value' => 'required|string|max:255',
            'values.*.color_code' => 'nullable|string|max:7',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_visible'] = $request->has('is_visible');
        $validated['is_variation'] = $request->has('is_variation');

        $attribute->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'],
            'is_visible' => $validated['is_visible'],
            'is_variation' => $validated['is_variation'],
        ]);

        // Sync values
        $existingValueIds = [];
        foreach ($validated['values'] as $index => $valueData) {
            if (!empty($valueData['id'])) {
                // Update existing
                $value = ProductAttributeValue::find($valueData['id']);
                if ($value && $value->product_attribute_id === $attribute->id) {
                    $value->update([
                        'value' => $valueData['value'],
                        'slug' => Str::slug($valueData['value']),
                        'color_code' => $valueData['color_code'] ?? null,
                        'position' => $index,
                    ]);
                    $existingValueIds[] = $value->id;
                }
            } else {
                // Create new
                $value = $attribute->values()->create([
                    'value' => $valueData['value'],
                    'slug' => Str::slug($valueData['value']),
                    'color_code' => $valueData['color_code'] ?? null,
                    'position' => $index,
                ]);
                $existingValueIds[] = $value->id;
            }
        }

        // Delete removed values
        $attribute->values()->whereNotIn('id', $existingValueIds)->delete();

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully!');
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully!');
    }
}
