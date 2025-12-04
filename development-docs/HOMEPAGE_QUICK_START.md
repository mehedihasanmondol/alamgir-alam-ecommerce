# Homepage Quick Start Guide

## 🚀 Quick Test (5 Minutes)

### Step 1: Start Server
```bash
php artisan serve
```

### Step 2: Visit Homepage
Open browser and go to:
```
http://localhost:8000
```

### Step 3: Expected Result
You should see:
- ✅ Green gradient top bar with promotional messages
- ✅ iHerb-style logo and search bar
- ✅ Navigation menu with categories
- ✅ Hero banner section
- ✅ Footer with newsletter subscription

---

## 📊 If Products Don't Show

The homepage will show empty sections if there's no data. To add sample data:

### Option 1: Manual Entry (via Admin Panel)
1. Login to admin panel: `http://localhost:8000/admin/dashboard`
2. Create categories: `http://localhost:8000/admin/categories/create`
3. Create brands: `http://localhost:8000/admin/brands/create`
4. Create products: `http://localhost:8000/admin/products/create`

### Option 2: Database Seeder (Recommended)
Create a seeder file:
```bash
php artisan make:seeder HomepageDataSeeder
```

Add sample data and run:
```bash
php artisan db:seed --class=HomepageDataSeeder
```

---

## 🎨 Customization

### Change Logo
Edit: `resources/views/components/frontend/header.blade.php`
```blade
<div class="bg-green-600 text-white font-bold text-2xl px-3 py-2 rounded">
    iHerb <!-- Change this to your brand name -->
</div>
```

### Change Top Bar Messages
Edit: `resources/views/components/frontend/header.blade.php`
```blade
<span class="font-medium">Up to 70% off iHerb brands</span>
<!-- Change promotional messages here -->
```

### Change Hero Banner
Edit: `resources/views/frontend/home/index.blade.php`
```blade
<h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
    Welcome to Your Health & Wellness Store
    <!-- Change heading here -->
</h1>
```

---

## 🔧 Common Issues

### Issue: "Class 'App\Http\Controllers\HomeController' not found"
**Solution**: Clear cache
```bash
php artisan cache:clear
composer dump-autoload
```

### Issue: "View [frontend.home.index] not found"
**Solution**: Clear view cache
```bash
php artisan view:clear
```

### Issue: Header/Footer not showing
**Solution**: Check component paths
```bash
# Components should be in:
resources/views/components/frontend/header.blade.php
resources/views/components/frontend/footer.blade.php
```

### Issue: Styles not loading
**Solution**: Build assets
```bash
npm install
npm run dev
```

---

## 📱 Mobile Testing

### Test Responsive Design:
1. Open browser DevTools (F12)
2. Click mobile device icon
3. Test different screen sizes:
   - Mobile: 375px
   - Tablet: 768px
   - Desktop: 1024px

### Expected Behavior:
- ✅ Mobile menu appears on small screens
- ✅ Grid layouts adjust (1→2→4 columns)
- ✅ Navigation becomes scrollable
- ✅ Hero banner stacks vertically

---

## 🎯 Next Features to Implement

### Priority 1: Search
- [ ] Create Livewire search component
- [ ] Implement real-time product search
- [ ] Add search suggestions

### Priority 2: Cart
- [ ] Create cart Livewire component
- [ ] Implement add to cart functionality
- [ ] Create cart sidebar

### Priority 3: Product Details
- [ ] Create product detail page
- [ ] Implement variant selection
- [ ] Add product reviews

---

## 📚 File Locations

### Controllers
- `app/Http/Controllers/HomeController.php`

### Views
- `resources/views/layouts/app.blade.php`
- `resources/views/frontend/home/index.blade.php`
- `resources/views/components/frontend/header.blade.php`
- `resources/views/components/frontend/footer.blade.php`
- `resources/views/components/frontend/product-card.blade.php`

### Routes
- `routes/web.php`

### Documentation
- `HOMEPAGE_README.md` - Full documentation
- `HOMEPAGE_IMPLEMENTATION_SUMMARY.md` - Implementation summary
- `HOMEPAGE_QUICK_START.md` - This file

---

## ✅ Checklist

Before going live:
- [ ] Test on multiple browsers (Chrome, Firefox, Safari, Edge)
- [ ] Test on mobile devices
- [ ] Add real product data
- [ ] Optimize images (WebP format)
- [ ] Set up SSL certificate
- [ ] Configure SEO meta tags
- [ ] Test page load speed
- [ ] Enable caching
- [ ] Set up error monitoring

---

## 🆘 Need Help?

### Check Documentation:
1. **HOMEPAGE_README.md** - Detailed features and design system
2. **HOMEPAGE_IMPLEMENTATION_SUMMARY.md** - Complete file list
3. **.windsurfrules** - Project guidelines

### Common Commands:
```bash
# Clear all cache
php artisan optimize:clear

# Rebuild assets
npm run build

# Check routes
php artisan route:list

# Check for errors
tail -f storage/logs/laravel.log
```

---

**🎉 You're all set! The homepage is ready to use.**

**Created**: 2025-01-06  
**Version**: 1.0
