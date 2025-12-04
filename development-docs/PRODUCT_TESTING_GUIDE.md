# Product Management System - Testing Guide

## 🎯 Overview
This guide will help you test all features of the product management system systematically.

---

## ✅ Pre-Testing Checklist

### 1. **Verify System Requirements**
```bash
# Check PHP version (8.2+)
php -v

# Check Laravel version (11.x)
php artisan --version

# Check database connection
php artisan db:show
```

### 2. **Ensure Storage is Linked**
```bash
php artisan storage:link
```

### 3. **Clear All Caches**
```bash
php artisan optimize:clear
```

### 4. **Start Development Server**
```bash
php artisan serve
```

---

## 📋 Testing Scenarios

### **Scenario 1: Product Attributes Management**

#### Test 1.1: Create Color Attribute
1. Navigate to `/admin/attributes`
2. Click "Add Attribute"
3. Fill in:
   - Name: `Color`
   - Type: `Color`
   - Check "Visible on product page"
   - Check "Used for variations"
4. Add values:
   - Red (#FF0000)
   - Blue (#0000FF)
   - Green (#00FF00)
   - Black (#000000)
5. Click "Create Attribute"

**Expected Result**: ✅ Attribute created successfully with color swatches displayed

#### Test 1.2: Create Size Attribute
1. Click "Add Attribute"
2. Fill in:
   - Name: `Size`
   - Type: `Button`
   - Check both visibility options
3. Add values:
   - Small
   - Medium
   - Large
   - XL
4. Click "Create Attribute"

**Expected Result**: ✅ Attribute created successfully

#### Test 1.3: Edit Attribute
1. Click edit icon on any attribute
2. Modify name or add/remove values
3. Click "Update Attribute"

**Expected Result**: ✅ Changes saved successfully

#### Test 1.4: Delete Attribute
1. Click delete icon
2. Confirm deletion

**Expected Result**: ✅ Attribute deleted successfully

---

### **Scenario 2: Simple Product Creation**

#### Test 2.1: Create Simple Product
1. Navigate to `/admin/products`
2. Click "Add Product"
3. **Step 1 - Basic Info**:
   - Name: `Classic White T-Shirt`
   - Product Type: `Simple`
   - Category: Select any
   - Brand: Select any
   - Description: Add some text
4. **Step 2 - Pricing & Stock**:
   - Price: `29.99`
   - Sale Price: `24.99`
   - SKU: `TSH-001`
   - Stock Quantity: `100`
5. **Step 3 - SEO & Media**:
   - Meta Title: `Classic White T-Shirt - Comfortable Cotton`
   - Meta Description: Add description
6. Click "Create Product"

**Expected Result**: ✅ Product created successfully

#### Test 2.2: Upload Product Images
1. From product list, click images icon (purple)
2. Click "Choose Images"
3. Select 3-5 product images
4. Click "Upload Images"
5. Wait for upload to complete
6. Click star icon on one image to set as primary

**Expected Result**: ✅ Images uploaded and primary image set

---

### **Scenario 3: Variable Product Creation**

#### Test 3.1: Create Variable Product
1. Click "Add Product"
2. **Step 1**:
   - Name: `Premium Cotton T-Shirt`
   - Product Type: `Variable`
   - Category & Brand: Select
   - Description: Add text
3. **Step 2**: Skip (variants will handle pricing)
4. **Step 3**: Add SEO info
5. Click "Create Product"

**Expected Result**: ✅ Product created

#### Test 3.2: Generate Variants
1. Click "Edit" on the variable product
2. Navigate to "Variant Manager" tab
3. Select attributes:
   - Color: Red, Blue, Black
   - Size: Small, Medium, Large
4. Click "Generate Combinations"

**Expected Result**: ✅ 9 variants generated (3 colors × 3 sizes)

#### Test 3.3: Set Variant Prices
1. For each generated variant:
   - Set Price: `34.99`
   - Set Sale Price: `29.99`
   - Set SKU: `TSH-VAR-001-RED-S` (example)
   - Set Stock: `50`
2. Click "Save All Variants"

**Expected Result**: ✅ All variant data saved

---

### **Scenario 4: Grouped Product Creation**

#### Test 4.1: Create Grouped Product
1. Click "Add Product"
2. **Step 1**:
   - Name: `T-Shirt Bundle Pack`
   - Product Type: `Grouped`
   - Description: `Pack of 3 t-shirts`
3. Complete other steps
4. Click "Create Product"

**Expected Result**: ✅ Grouped product created

#### Test 4.2: Add Child Products
1. Edit the grouped product
2. In "Grouped Products" section
3. Select 3 simple products to include
4. Save changes

**Expected Result**: ✅ Child products linked

---

### **Scenario 5: Affiliate Product Creation**

#### Test 5.1: Create Affiliate Product
1. Click "Add Product"
2. **Step 1**:
   - Name: `External Brand T-Shirt`
   - Product Type: `Affiliate`
   - External URL: `https://example.com/product`
   - Button Text: `Buy on Amazon`
3. Complete other steps
4. Click "Create Product"

**Expected Result**: ✅ Affiliate product created with external link

---

### **Scenario 6: Product List Features**

#### Test 6.1: Search Products
1. Go to `/admin/products`
2. Type product name in search box
3. Wait for live search results

**Expected Result**: ✅ Products filtered in real-time

#### Test 6.2: Filter by Category
1. Click "Filters" button
2. Select a category
3. View filtered results

**Expected Result**: ✅ Only products from selected category shown

#### Test 6.3: Filter by Brand
1. Select a brand from filter
2. View results

**Expected Result**: ✅ Only products from selected brand shown

#### Test 6.4: Filter by Product Type
1. Select "Variable" from type filter
2. View results

**Expected Result**: ✅ Only variable products shown

#### Test 6.5: Toggle Product Status
1. Click the toggle switch on any product
2. Observe status change

**Expected Result**: ✅ Product status toggled (active/inactive)

#### Test 6.6: Toggle Featured Status
1. Click the star icon on any product
2. Observe featured badge

**Expected Result**: ✅ Product featured status toggled

#### Test 6.7: Delete Product
1. Click delete icon (trash)
2. Confirm deletion in modal

**Expected Result**: ✅ Product deleted successfully

---

### **Scenario 7: Stock Management**

#### Test 7.1: Low Stock Alert
1. Edit a product
2. Set stock quantity to `3`
3. Set low stock threshold to `5`
4. Save product

**Expected Result**: ✅ Low stock indicator shown

#### Test 7.2: Out of Stock
1. Set stock quantity to `0`
2. Save product

**Expected Result**: ✅ "Out of Stock" badge displayed

---

### **Scenario 8: Image Management**

#### Test 8.1: Upload Multiple Images
1. Go to product images page
2. Select 5 images at once
3. Upload all

**Expected Result**: ✅ All images uploaded successfully

#### Test 8.2: Change Primary Image
1. Click star icon on different image
2. Observe primary badge move

**Expected Result**: ✅ Primary image changed

#### Test 8.3: Delete Image
1. Hover over image
2. Click delete icon
3. Confirm deletion

**Expected Result**: ✅ Image deleted from gallery and storage

---

## 🐛 Common Issues & Solutions

### Issue 1: Images Not Uploading
**Solution**:
```bash
# Check storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ensure storage link exists
php artisan storage:link
```

### Issue 2: Variants Not Generating
**Solution**:
- Ensure attributes are created first
- Check that attributes have "Used for variations" enabled
- Clear cache: `php artisan optimize:clear`

### Issue 3: Products Page Empty
**Solution**:
- Check browser console for JavaScript errors
- Disable browser extensions
- Clear browser cache (Ctrl+F5)
- Check Laravel logs: `storage/logs/laravel.log`

### Issue 4: CSP Errors
**Solution**:
- Disable browser security extensions
- Try in Incognito mode
- Check for antivirus interference

---

## 📊 Performance Testing

### Test Load Time
1. Open browser DevTools (F12)
2. Go to Network tab
3. Navigate to products page
4. Check page load time

**Expected**: < 2 seconds

### Test Image Upload Speed
1. Upload 10 images
2. Measure time taken

**Expected**: < 30 seconds for 10 images (2MB each)

---

## ✅ Final Checklist

- [ ] All 4 product types can be created
- [ ] Attributes system works (create, edit, delete)
- [ ] Variant generation works correctly
- [ ] Image upload and management works
- [ ] Search and filters work in real-time
- [ ] Stock management displays correctly
- [ ] Product status toggles work
- [ ] Featured products can be marked
- [ ] Products can be deleted
- [ ] Navigation links all work
- [ ] No console errors
- [ ] No PHP errors in logs

---

## 📝 Test Report Template

```
Test Date: ___________
Tester Name: ___________

| Feature | Status | Notes |
|---------|--------|-------|
| Simple Product Creation | ✅/❌ | |
| Variable Product Creation | ✅/❌ | |
| Grouped Product Creation | ✅/❌ | |
| Affiliate Product Creation | ✅/❌ | |
| Attribute Management | ✅/❌ | |
| Variant Generation | ✅/❌ | |
| Image Upload | ✅/❌ | |
| Search & Filters | ✅/❌ | |
| Stock Management | ✅/❌ | |

Overall Status: ✅ PASS / ❌ FAIL

Issues Found:
1. 
2. 
3. 
```

---

## 🎉 Success Criteria

The system is considered **production-ready** when:
- ✅ All test scenarios pass
- ✅ No critical bugs found
- ✅ Performance metrics met
- ✅ All features documented
- ✅ User feedback incorporated

---

**Happy Testing!** 🚀
