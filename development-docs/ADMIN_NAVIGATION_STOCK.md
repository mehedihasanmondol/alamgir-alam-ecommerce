# Admin Navigation - Stock Management

## Add to Your Admin Sidebar

Copy and paste this code into your admin layout sidebar navigation file:
`resources/views/layouts/admin.blade.php` or wherever your admin menu is defined.

### Option 1: Simple Navigation (Recommended)

```blade
<!-- Stock Management -->
<li class="nav-item {{ Request::is('admin/stock*') || Request::is('admin/warehouses*') || Request::is('admin/suppliers*') ? 'active' : '' }}">
    <a href="{{ route('admin.stock.index') }}" class="nav-link">
        <svg class="nav-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <span>Stock Management</span>
    </a>
</li>
```

### Option 2: With Submenu (Full Access)

```blade
<!-- Stock Management with Submenu -->
<li class="nav-item has-submenu {{ Request::is('admin/stock*') || Request::is('admin/warehouses*') || Request::is('admin/suppliers*') ? 'active' : '' }}">
    <a href="{{ route('admin.stock.index') }}" class="nav-link">
        <svg class="nav-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <span>Stock Management</span>
        <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </a>
    <ul class="nav-submenu">
        <li><a href="{{ route('admin.stock.index') }}">Dashboard</a></li>
        <li><a href="{{ route('admin.stock.movements') }}">Stock Movements</a></li>
        <li><a href="{{ route('admin.stock.add') }}">Add Stock</a></li>
        <li><a href="{{ route('admin.stock.adjust') }}">Adjust Stock</a></li>
        <li><a href="{{ route('admin.stock.transfer') }}">Transfer Stock</a></li>
        <li><a href="{{ route('admin.stock.alerts') }}">Stock Alerts</a></li>
        <li><a href="{{ route('admin.warehouses.index') }}">Warehouses</a></li>
        <li><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
    </ul>
</li>
```

### Option 3: Tailwind CSS Styled (Modern)

```blade
<!-- Stock Management (Tailwind) -->
<li>
    <a href="{{ route('admin.stock.index') }}" 
       class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ Request::is('admin/stock*') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <span class="font-medium">Stock Management</span>
        @if($pendingAlerts = App\Modules\Stock\Models\StockAlert::pending()->count())
            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingAlerts }}</span>
        @endif
    </a>
</li>
```

### Option 4: With Icon Library (FontAwesome)

```blade
<!-- Stock Management (FontAwesome) -->
<li class="nav-item {{ Request::is('admin/stock*') || Request::is('admin/warehouses*') || Request::is('admin/suppliers*') ? 'active' : '' }}">
    <a href="{{ route('admin.stock.index') }}" class="nav-link">
        <i class="fa fa-boxes nav-icon"></i>
        <span>Stock Management</span>
        @if($alerts = App\Modules\Stock\Models\StockAlert::pending()->count())
            <span class="badge badge-danger ml-auto">{{ $alerts }}</span>
        @endif
    </a>
</li>
```

## Quick Access Links (Add to Dashboard)

```blade
<!-- Stock Quick Links -->
<div class="row">
    <div class="col-md-3">
        <a href="{{ route('admin.stock.add') }}" class="card card-stat">
            <div class="card-body">
                <i class="fa fa-plus-circle text-green-600"></i>
                <h5>Add Stock</h5>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.stock.movements') }}" class="card card-stat">
            <div class="card-body">
                <i class="fa fa-history text-blue-600"></i>
                <h5>Movements</h5>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.stock.alerts') }}" class="card card-stat">
            <div class="card-body">
                <i class="fa fa-exclamation-triangle text-red-600"></i>
                <h5>Alerts</h5>
                <span class="badge">{{ App\Modules\Stock\Models\StockAlert::pending()->count() }}</span>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.warehouses.index') }}" class="card card-stat">
            <div class="card-body">
                <i class="fa fa-warehouse text-purple-600"></i>
                <h5>Warehouses</h5>
            </div>
        </a>
    </div>
</div>
```

## Mobile Navigation (Optional)

```blade
<!-- Mobile Stock Menu -->
<div class="mobile-nav-item">
    <a href="{{ route('admin.stock.index') }}">
        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <span>Stock</span>
    </a>
</div>
```

## Usage Tips

1. **Choose the option that matches your existing navigation style**
2. **Place it in the appropriate section** (usually after Products or Orders)
3. **Test the active states** by visiting different stock pages
4. **Adjust classes** to match your admin theme

## Available Routes

All these routes are already registered and working:

- `admin.stock.index` - Dashboard
- `admin.stock.movements` - Movement history
- `admin.stock.add` - Add stock form
- `admin.stock.remove` - Remove stock form
- `admin.stock.adjust` - Adjust stock form
- `admin.stock.transfer` - Transfer form
- `admin.stock.alerts` - Stock alerts
- `admin.warehouses.index` - Warehouses list
- `admin.suppliers.index` - Suppliers list

Choose the navigation style that best fits your admin theme!
