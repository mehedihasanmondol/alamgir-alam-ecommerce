<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Ecommerce\Product\Models\ProductAttribute;
use App\Modules\Ecommerce\Product\Models\ProductAttributeValue;
use Livewire\Component;
use Illuminate\Support\Str;

class AttributeManager extends Component
{
    public $productAttributes = [];
    public $showAddAttribute = false;
    public $editingAttributeId = null;
    
    // New Attribute Form
    public $newAttribute = [
        'name' => '',
        'type' => 'select',
        'values' => ['']
    ];

    protected $rules = [
        'newAttribute.name' => 'required|string|max:255',
        'newAttribute.type' => 'required|in:select,color,button,image',
        'newAttribute.values' => 'required|array|min:1',
        'newAttribute.values.*' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadAttributes();
    }

    public function loadAttributes()
    {
        $this->productAttributes = ProductAttribute::with('values')
            ->orderBy('position')
            ->get()
            ->toArray();
    }

    public function addValueField()
    {
        $this->newAttribute['values'][] = '';
    }

    public function removeValueField($index)
    {
        unset($this->newAttribute['values'][$index]);
        $this->newAttribute['values'] = array_values($this->newAttribute['values']);
    }

    public function saveAttribute()
    {
        $this->validate();

        $attribute = ProductAttribute::create([
            'name' => $this->newAttribute['name'],
            'slug' => Str::slug($this->newAttribute['name']),
            'type' => $this->newAttribute['type'],
            'is_visible' => true,
            'is_variation' => true,
            'position' => ProductAttribute::max('position') + 1,
        ]);

        // Create attribute values
        foreach (array_filter($this->newAttribute['values']) as $index => $value) {
            ProductAttributeValue::create([
                'product_attribute_id' => $attribute->id,
                'value' => $value,
                'slug' => Str::slug($value),
                'position' => $index,
            ]);
        }

        $this->reset('newAttribute', 'showAddAttribute');
        $this->newAttribute['values'] = [''];
        $this->loadAttributes();
        
        session()->flash('success', 'Attribute created successfully!');
    }

    public function deleteAttribute($attributeId)
    {
        $attribute = ProductAttribute::find($attributeId);
        if ($attribute) {
            $attribute->values()->delete();
            $attribute->delete();
            $this->loadAttributes();
            session()->flash('success', 'Attribute deleted successfully!');
        }
    }

    public function toggleAddAttribute()
    {
        $this->showAddAttribute = !$this->showAddAttribute;
        if (!$this->showAddAttribute) {
            $this->reset('newAttribute');
            $this->newAttribute['values'] = [''];
        }
    }

    public function render()
    {
        return view('livewire.admin.product.attribute-manager');
    }
}
