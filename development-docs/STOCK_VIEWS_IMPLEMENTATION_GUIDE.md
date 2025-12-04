# Stock Management Views - Complete Implementation Guide

## ✅ Completed Views

### 1. Dashboard (`admin/stock/index.blade.php`) ✅
- Overview statistics
- Recent movements widget
- Low stock alerts widget
- Quick action buttons

## 📋 Remaining Views to Create

### Stock Movements

#### `resources/views/admin/stock/movements/index.blade.php`
```blade
@extends('layouts.admin')

@section('title', 'Stock Movements')

@section('content')
<!-- Filters: Type, Warehouse, Date Range, Search -->
<!-- Table with: Reference#, Product, Type, Quantity, Warehouse, Date, User -->
<!-- Pagination -->
@endsection
```

### Stock Operations

#### `resources/views/admin/stock/add.blade.php`
**Form Fields:**
- Product selector (with search - use Livewire or Select2)
- Variant selector (if product has variants)
- Warehouse dropdown
- Quantity input
- Unit cost input
- Supplier dropdown
- Reason (optional)
- Notes (textarea)

#### `resources/views/admin/stock/remove.blade.php`
**Form Fields:**
- Product selector
- Variant selector
- Warehouse dropdown
- Type radio (out/damaged/lost)
- Quantity input
- Reason (required)
- Notes

#### `resources/views/admin/stock/adjust.blade.php`
**Form Fields:**
- Product selector
- Variant selector
- Warehouse dropdown
- Current stock display (readonly, fetched via AJAX)
- New quantity input
- Reason (required)
- Notes

#### `resources/views/admin/stock/transfer.blade.php`
**Form Fields:**
- Product selector
- Variant selector
- From warehouse dropdown
- To warehouse dropdown
- Quantity input
- Current stock in source warehouse (readonly)
- Notes

### Stock Alerts

#### `resources/views/admin/stock/alerts/index.blade.php`
**Elements:**
- Alert list table
- Product name
- Warehouse
- Current stock vs Min stock
- Status badges
- Resolve button
- Filters (status, warehouse)

### Warehouse Management

#### `resources/views/admin/stock/warehouses/index.blade.php`
```blade
- Warehouse list table
- Columns: Code, Name, City, Manager, Status, Actions
- Create new button
- Edit/Delete actions
- Set as default button
```

#### `resources/views/admin/stock/warehouses/create.blade.php`
```blade
- Form with all warehouse fields
- Name, Code, Address, City, State, Postal Code
- Phone, Email, Manager Name
- Capacity, Notes
- Active checkbox
```

#### `resources/views/admin/stock/warehouses/edit.blade.php`
```blade
- Same as create form but with existing data
- Option to set as default
```

### Supplier Management

#### `resources/views/admin/stock/suppliers/index.blade.php`
```blade
- Supplier list table
- Columns: Code, Name, Contact, Email, Phone, Status, Actions
- Create new button
- Edit/Delete actions
```

#### `resources/views/admin/stock/suppliers/create.blade.php`
```blade
- Form with all supplier fields
- Name, Code, Email, Phone, Mobile
- Address, City, State, Postal Code
- Contact Person details
- Credit Limit, Payment Terms
- Status dropdown, Notes
```

#### `resources/views/admin/stock/suppliers/edit.blade.php`
```blade
- Same as create form with existing data
```

## 🎨 Reusable Components

### Product Selector Component
Create: `resources/views/components/product-selector.blade.php`

```blade
@props(['name' => 'product_id', 'selected' => null, 'required' => false])

<div x-data="productSelector">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Product {{ $required ? '*' : '' }}
    </label>
    <!-- Search input -->
    <!-- Dropdown with results -->
    <!-- Hidden input with selected value -->
</div>
```

### Stock Level Display Component
Create: `resources/views/components/stock-level.blade.php`

```blade
@props(['productId', 'variantId' => null, 'warehouseId'])

<div wire:poll.5s>
    Current Stock: <span class="font-bold">{{ $currentStock }}</span> units
</div>
```

## 📱 Livewire Components (Recommended)

### 1. ProductStockSelector
```bash
php artisan make:livewire Admin/ProductStockSelector
```

**Features:**
- Real-time product search
- Variant selection
- Display current stock
- AJAX validation

### 2. StockMovementForm
```bash
php artisan make:livewire Admin/StockMovementForm
```

**Features:**
- Dynamic form based on movement type
- Real-time stock validation
- Auto-calculate totals

### 3. StockAlertBadge
```bash
php artisan make:livewire Admin/StockAlertBadge
```

**Features:**
- Real-time alert count
- Dropdown with recent alerts
- Mark as read functionality

## 🎯 Quick Start Templates

### Basic Form Template
```blade
@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-gray-600 mt-1">{{ $subtitle }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ $action }}">
                @csrf
                
                <!-- Form fields here -->
                
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ $cancelUrl }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

### Basic List Template
```blade
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header with Create Button -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
        <a href="{{ $createUrl }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Create New
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <!-- Table headers -->
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Table rows -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
```

## 🔧 AJAX Stock Lookup

Add to your main JavaScript file:

```javascript
// Get current stock via AJAX
function getCurrentStock(productId, variantId, warehouseId) {
    return fetch('/admin/stock/current-stock?' + new URLSearchParams({
        product_id: productId,
        variant_id: variantId || '',
        warehouse_id: warehouseId
    }))
    .then(response => response.json())
    .then(data => data.stock);
}

// Usage in form
document.getElementById('warehouse_id').addEventListener('change', async function() {
    const productId = document.getElementById('product_id').value;
    const warehouseId = this.value;
    
    if (productId && warehouseId) {
        const stock = await getCurrentStock(productId, null, warehouseId);
        document.getElementById('current-stock-display').textContent = stock;
    }
});
```

## 📊 Table Component Examples

### Movement Type Badge
```blade
@php
$colors = [
    'in' => 'bg-green-100 text-green-800',
    'out' => 'bg-red-100 text-red-800',
    'adjustment' => 'bg-yellow-100 text-yellow-800',
    'transfer' => 'bg-blue-100 text-blue-800',
    'damaged' => 'bg-orange-100 text-orange-800',
    'lost' => 'bg-gray-100 text-gray-800',
];
@endphp

<span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$type] ?? 'bg-gray-100' }}">
    {{ ucfirst($type) }}
</span>
```

### Status Badge
```blade
@if($isActive)
    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
@else
    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
@endif
```

## 🚀 Next Steps

1. **Copy template structures** from existing admin views in your project
2. **Use Blade components** from your `resources/views/components/` directory
3. **Follow your project's design system** (colors, spacing, typography)
4. **Add validation messages** using Laravel's validation
5. **Implement search/filter** using your existing patterns
6. **Add success/error toasts** using your toast system

## 📝 Priority Order

Create views in this order:

1. ✅ Dashboard (`index.blade.php`) - DONE
2. Movements list (`movements/index.blade.php`)
3. Add stock form (`add.blade.php`)
4. Adjust stock form (`adjust.blade.php`)
5. Warehouse CRUD (3 files)
6. Supplier CRUD (3 files)
7. Transfer form (`transfer.blade.php`)
8. Remove stock form (`remove.blade.php`)
9. Alerts list (`alerts/index.blade.php`)

## 💡 Tips

- **Reuse your existing components** - Don't recreate the wheel
- **Copy from similar views** - Orders, Products have similar patterns
- **Use Livewire for complex interactions** - Product selection, stock validation
- **Keep forms simple** - Progressive disclosure for advanced options
- **Add helpful hints** - Show current stock, minimum quantities
- **Validate client-side first** - Better UX with instant feedback
- **Use modals for quick actions** - Resolve alerts, set default warehouse

Your dashboard view is complete and follows your project's design patterns. Use it as a reference for creating the remaining views!
