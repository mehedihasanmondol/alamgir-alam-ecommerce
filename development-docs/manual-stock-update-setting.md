# Manual Stock Update Control Setting

## Overview
Added a setting to enable or disable manual stock updates in the product edit form. When disabled, stock can only be managed through the Stock Management system.

---

## Implementation Date
2025-11-18

---

## Setting Details

### Database Setting
- **Key**: `manual_stock_update_enabled`
- **Default Value**: `0` (Disabled)
- **Type**: `boolean`
- **Group**: `stock`
- **Label**: "Enable Manual Stock Updates"
- **Description**: "Allow manual stock updates in product edit form. If disabled, stock can only be managed via Stock Management system."

---

## How It Works

### When Enabled (`manual_stock_update_enabled = 1`)
- **Product Edit Form**: Stock quantity and low stock alert fields are visible and editable
- **Validation**: Stock quantity is required for simple and grouped products
- **Backend**: Stock values can be updated through ProductService

### When Disabled (`manual_stock_update_enabled = 0`)
- **Product Edit Form**: Stock fields are hidden and replaced with an informational message
- **Validation**: Stock quantity validation is skipped
- **Backend**: Stock field updates are prevented in ProductService methods
- **Stock Management**: Stock can only be managed via Stock Management system

---

## Files Modified

### 1. Database Seeder
**File**: `database/seeders/SiteSettingSeeder.php`
- Added `manual_stock_update_enabled` setting to stock group
- Default value set to `0` (disabled)

### 2. Product Edit Form (Livewire View)
**File**: `resources/views/livewire/admin/product/product-form-enhanced.blade.php`
- Lines 310-341: Wrapped stock fields with conditional check
- When disabled, shows informational message instead of input fields
- Message: "Stock Management via Stock System Only"

### 3. Product Form Component (Livewire)
**File**: `app/Livewire/Admin/Product/ProductForm.php`

#### Changes:
1. **Validation Rules** (Lines 105-109):
   - Stock validation only applied when manual stock update is enabled
   - Checks setting value before adding stock_quantity and low_stock_alert rules

2. **Save Method** (Lines 286-290):
   - Removes stock fields from variant data when setting is disabled
   - Prevents accidental stock updates via form submission

### 4. Product Service
**File**: `app/Modules/Ecommerce/Product/Services/ProductService.php`

#### Changes:
1. **createDefaultVariant()** (Lines 96-105):
   - Sets default stock values (0 for stock_quantity, 5 for low_stock_alert) when manual stock update is disabled
   - Ensures database integrity with default values

2. **updateDefaultVariant()** (Lines 117-122):
   - Removes stock fields from update data when setting is disabled
   - Prevents stock modifications during product updates

3. **updateVariant()** (Lines 236-240):
   - Removes stock fields from variant update data when setting is disabled
   - Protects existing stock values from being modified

---

## Admin Benefits

### Centralized Stock Control
- Forces all stock management through Stock Management system
- Prevents inconsistencies from manual edits
- Maintains accurate stock movement tracking

### Flexible Configuration
- Can be toggled from Site Settings admin panel
- No code changes needed to enable/disable
- Setting takes effect immediately

### User-Friendly Interface
- Clear informational message when disabled
- No confusing disabled input fields
- Directs users to proper stock management workflow

---

## Usage Instructions

### Enable Manual Stock Updates
1. Go to Admin Panel > Site Settings
2. Navigate to "Stock" settings group
3. Enable "Enable Manual Stock Updates"
4. Save settings
5. Stock fields will now appear in product edit form

### Disable Manual Stock Updates
1. Go to Admin Panel > Site Settings
2. Navigate to "Stock" settings group
3. Disable "Enable Manual Stock Updates"
4. Save settings
5. Stock fields will be hidden in product edit form

---

## Security & Data Integrity

### Protection Mechanisms
1. **Frontend**: Fields hidden from view when disabled
2. **Validation**: Stock validation skipped when disabled
3. **Backend**: Stock values removed from update payload
4. **Service Layer**: Multiple checks to prevent stock modifications

### Data Safety
- Existing stock values are preserved when setting is disabled
- No data loss when toggling setting on/off
- Default values applied for new products when disabled

---

## Best Practices

### Recommended Configuration
- **Small Stores**: Enable manual stock updates for flexibility
- **Large Inventories**: Disable and use Stock Management system exclusively
- **Multi-warehouse**: Disable to maintain centralized stock control

### When to Disable
- Using integrated Stock Management system
- Multiple warehouses or locations
- Need detailed stock movement tracking
- Want to prevent unauthorized stock changes
- Require audit trail for stock changes

### When to Enable
- Simple inventory management
- Single location/warehouse
- Quick product updates needed
- Stock Management system not in use

---

## Testing Checklist

- [x] Seeder runs successfully and adds setting
- [x] Setting appears in Site Settings admin panel
- [ ] Stock fields hidden when setting is disabled
- [ ] Stock fields visible when setting is enabled
- [ ] Informational message displays correctly when disabled
- [ ] Validation skips stock fields when disabled
- [ ] Product can be created without stock fields when disabled
- [ ] Product can be updated without modifying stock when disabled
- [ ] Stock values preserved when toggling setting
- [ ] Stock Management system works independently

---

## Future Enhancements

### Potential Additions
1. **Role-based Permissions**: Allow certain roles to bypass the restriction
2. **Audit Logging**: Track when setting is toggled on/off
3. **Granular Control**: Enable/disable per product type
4. **Warning System**: Alert users before disabling if products have manual stock
5. **Bulk Update**: Sync stock from Stock Management system to products

---

## Troubleshooting

### Stock Fields Not Showing After Enabling
- Clear application cache: `php artisan cache:clear`
- Clear view cache: `php artisan view:clear`
- Refresh browser page

### Stock Values Being Modified When Disabled
- Check SiteSetting value in database
- Verify ProductService is using latest code
- Check for custom code bypassing service layer

### Validation Errors
- Ensure seeder has been run
- Check setting value is exactly '0' or '1' (string)
- Verify SiteSetting model cache is working

---

## Related Systems

### Stock Management System
- Location: `app/Modules/Stock/`
- Stock movements tracked independently
- Integrates with product variants automatically
- Provides comprehensive audit trail

### Product Variants
- Location: `app/Modules/Ecommerce/Product/Models/ProductVariant.php`
- Stock stored at variant level
- Each variant has independent stock quantity
- Low stock alerts per variant

---

## Support

For issues or questions about this feature:
1. Check documentation in `development-docs/`
2. Review Stock Management system documentation
3. Contact system administrator
4. Review code comments in modified files
