# 🚀 Stock Management - Quick Start Guide

## 📋 Prerequisites Checklist

Before starting, ensure you have:
- [x] Laravel application running
- [x] Database configured and connected
- [x] At least one product in the database
- [x] Admin user account

---

## ⚡ 5-Minute Setup

### Step 1: Run Migrations (if not done)
```bash
php artisan migrate
```

### Step 2: Seed Demo Data (Optional but Recommended)
```bash
php artisan db:seed --class=StockManagementSeeder
```

This creates:
- ✅ 3 Warehouses (Main, Secondary, Outlet)
- ✅ 4 Suppliers (with complete details)

### Step 3: Clear Caches
```bash
php artisan optimize:clear
```

### Step 4: Access the System
Open your browser and visit:
```
http://localhost:8000/admin/stock
```

---

## 🎯 First-Time Walkthrough

### 1. View Dashboard (5 seconds)
```
URL: /admin/stock
```
- See overview statistics
- Check recent movements
- View pending alerts

### 2. Create a Warehouse (30 seconds)
```
URL: /admin/warehouses/create
```
**Required Fields:**
- Name: "Main Warehouse"
- Code: "WH-001"

**Optional Fields:**
- Address, City, Phone
- Manager Name
- Capacity
- Mark as Active

**Click:** "Create Warehouse"

### 3. Add a Supplier (30 seconds)
```
URL: /admin/suppliers/create
```
**Required Fields:**
- Name: "Test Supplier"
- Code: "SUP-001"
- Status: Active

**Optional Fields:**
- Contact information
- Payment terms
- Credit limit

**Click:** "Create Supplier"

### 4. Add Stock (1 minute)
```
URL: /admin/stock/add
```
**Steps:**
1. Select Product (dropdown)
2. Select Warehouse
3. Enter Quantity (e.g., 100)
4. Enter Unit Cost (e.g., 500.00)
5. Select Supplier (optional)
6. Add Reason: "Initial stock"
7. **Click:** "Add Stock"

**Success!** You've added your first stock! ✅

---

## 🔥 Common Operations

### Adding Stock from Supplier
```
1. Go to: /admin/stock/add
2. Select product
3. Select warehouse
4. Enter quantity & cost
5. Select supplier
6. Save
```

### Adjusting Stock (Corrections)
```
1. Go to: /admin/stock/adjust
2. Select product & warehouse
3. View current stock
4. Enter NEW quantity
5. Provide reason
6. Save
```

### Transferring Between Warehouses
```
1. Go to: /admin/stock/transfer
2. Select product
3. Select FROM warehouse
4. Select TO warehouse
5. Enter quantity
6. Save
```

### Viewing Stock History
```
1. Go to: /admin/stock/movements
2. Use filters:
   - Type (in/out/adjustment)
   - Warehouse
   - Date range
3. View complete log
```

### Managing Low Stock Alerts
```
1. Go to: /admin/stock/alerts
2. View products below minimum
3. Click "Resolve" when restocked
```

---

## 📊 Understanding Stock Types

### Stock IN (+)
- **Purchase from supplier** - Receiving new stock
- **Customer returns** - Items returned by customers
- **Found items** - Previously missing items found

### Stock OUT (-)
- **Sales** - Items sold to customers
- **Damaged** - Items damaged beyond use
- **Lost** - Missing or stolen items

### Adjustment (±)
- **Physical count** - Corrections after inventory audit
- **System errors** - Fixing data entry mistakes

### Transfer (→)
- **Between warehouses** - Moving stock to another location
- Creates two movements (out from source, in to destination)

---

## 🎨 Navigation Options

### Option 1: Add to Sidebar (Simple)
```blade
<li class="nav-item">
    <a href="{{ route('admin.stock.index') }}">
        <i class="fa fa-boxes"></i> Stock Management
    </a>
</li>
```

### Option 2: With Alert Badge
```blade
<li class="nav-item">
    <a href="{{ route('admin.stock.index') }}">
        <i class="fa fa-boxes"></i> Stock
        @php
            $alerts = App\Modules\Stock\Models\StockAlert::pending()->count();
        @endphp
        @if($alerts > 0)
            <span class="badge badge-danger">{{ $alerts }}</span>
        @endif
    </a>
</li>
```

See `ADMIN_NAVIGATION_STOCK.md` for more options.

---

## ✅ Verification Checklist

After setup, verify these work:

### Basic Operations
- [ ] Can view dashboard
- [ ] Can create warehouse
- [ ] Can create supplier
- [ ] Can add stock
- [ ] Can view stock movements
- [ ] Can see stock alerts

### Advanced Operations
- [ ] Can adjust stock
- [ ] Can transfer stock
- [ ] Can remove stock (damaged/lost)
- [ ] Can resolve alerts
- [ ] Can edit warehouse
- [ ] Can edit supplier

### Data Integrity
- [ ] Stock quantities update correctly
- [ ] Movement history shows before/after
- [ ] Alerts auto-generate for low stock
- [ ] Reference numbers are unique
- [ ] User tracking works

---

## 🐛 Common Issues & Solutions

### Issue: "Product not found"
**Solution:** Make sure you have products in your database first.
```bash
# Check if you have products
php artisan tinker
>>> App\Modules\Ecommerce\Product\Models\Product::count()
```

### Issue: "Warehouse required"
**Solution:** Create at least one warehouse first.
```
Visit: /admin/warehouses/create
```

### Issue: "Insufficient stock"
**Solution:** Check current stock level before removing/transferring.
```
Visit: /admin/stock/movements (to see current levels)
```

### Issue: Navigation not showing
**Solution:** Add the menu item to your admin sidebar.
```
See: ADMIN_NAVIGATION_STOCK.md
```

### Issue: Alerts not generating
**Solution:** Set low_stock_alert value in product variants.
```php
// In product variant
'low_stock_alert' => 5  // Alert when stock < 5
```

---

## 📈 Best Practices

### Daily Operations
1. **Morning**: Check stock alerts
2. **Throughout day**: Process stock movements as they happen
3. **Evening**: Review movement history

### Weekly Tasks
1. Check warehouse stock levels
2. Review pending alerts
3. Verify supplier payment terms

### Monthly Tasks
1. Physical inventory count
2. Adjust discrepancies
3. Review movement reports
4. Update supplier information

---

## 🎯 Key Performance Indicators (KPIs)

### Track These Metrics
- **Stock Turnover Rate** - How fast inventory moves
- **Stock Accuracy** - Physical vs system stock
- **Low Stock Incidents** - How often items run out
- **Average Stock Value** - Total inventory worth
- **Warehouse Utilization** - Capacity used %

---

## 💡 Pro Tips

### Tip 1: Use Meaningful Reference Numbers
The system auto-generates reference numbers like:
- `IN-20251112-0001` - Stock received
- `OUT-20251112-0001` - Stock sold
- `ADJ-20251112-0001` - Stock adjustment

### Tip 2: Always Add Notes
Provide context for future reference:
- "Received from supplier shipment #1234"
- "Damaged during transport"
- "Customer return - defective item"

### Tip 3: Set Appropriate Alert Thresholds
```
Fast-moving items: Higher threshold (e.g., 50)
Slow-moving items: Lower threshold (e.g., 5)
Critical items: Always keep buffer stock
```

### Tip 4: Regular Stock Audits
```
Weekly: Spot check high-value items
Monthly: Full warehouse audit
Quarterly: Cross-warehouse reconciliation
```

### Tip 5: Use Filters Effectively
```
View today's movements: Filter by today's date
Check supplier deliveries: Filter by type "IN" + supplier
Find adjustments: Filter by type "Adjustment"
```

---

## 🔐 Permissions (Optional)

If you want to restrict access, add these permissions:

```php
// Stock permissions
'view-stock'
'add-stock'
'adjust-stock'
'transfer-stock'
'view-movements'
'manage-warehouses'
'manage-suppliers'
'resolve-alerts'
```

---

## 📞 Need Help?

### Documentation Files
1. `STOCK_MANAGEMENT_IMPLEMENTATION.md` - Architecture
2. `STOCK_MANAGEMENT_100_COMPLETE.md` - Complete status
3. `STOCK_VIEWS_IMPLEMENTATION_GUIDE.md` - View templates
4. `ADMIN_NAVIGATION_STOCK.md` - Navigation options
5. `STOCK_QUICK_START.md` - This file

### Quick Commands
```bash
# Reset stock data (careful!)
php artisan migrate:fresh --seed

# Seed only stock data
php artisan db:seed --class=StockManagementSeeder

# Clear caches
php artisan optimize:clear

# Check routes
php artisan route:list --name=stock
```

---

## 🎊 You're All Set!

Your stock management system is now ready to use!

**Start here:** http://localhost:8000/admin/stock

Happy stock managing! 🚀
