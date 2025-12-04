# Quick Start: Secondary Menu Management

## 🚀 Installation (Required)

```bash
# Install new dependencies
npm install

# Build assets
npm run build

# Or for development with hot reload
npm run dev
```

## 📍 Access

Navigate to: **`/admin/secondary-menu`**

## ✨ Features

### Add Menu Item
1. Click **"Add Menu Item"** button
2. Fill in the form:
   - **Label**: Display text (e.g., "Sale Offers")
   - **URL**: Link destination (e.g., "/sale")
   - **Type**: Link or Dropdown
   - **Color**: Tailwind CSS class (e.g., text-red-600)
   - **Sort Order**: Display position
   - **Active**: Toggle visibility
   - **Open in new tab**: External links
3. Click **"Create Menu Item"**

### Edit Menu Item
1. Click the **edit icon** (pencil) on any row
2. Modify fields in the modal
3. Click **"Update Menu Item"**

### Delete Menu Item
1. Click the **delete icon** (trash) on any row
2. Confirm deletion in the modal
3. Item is soft-deleted (can be restored)

### Reorder Items
1. **Drag and drop** rows using the grip handle
2. Order saves automatically
3. Success notification appears

## 🎨 Modal Design

All modals feature:
- Glassmorphism effect (blur + transparency)
- Smooth animations
- Backdrop blur
- Consistent styling with product modals
- SVG icons (no Font Awesome)

## 📦 What Changed

### ✅ Removed
- CDN usage for SortableJS
- Traditional form submissions
- Page reloads
- 416 lines of mixed code

### ✅ Added
- Livewire component
- Modal-based UI
- Local SortableJS package
- Real-time validation
- Toast notifications
- 26-line clean index view

## 🔧 Troubleshooting

### Modal doesn't open
```bash
php artisan view:clear
php artisan livewire:discover
```

### Drag-and-drop not working
```bash
npm install
npm run build
```

### Styles not loading
```bash
php artisan config:clear
npm run build
```

## 📝 Color Options

Available Tailwind color classes:
- `text-gray-700` (Default)
- `text-red-600` (Sale/Urgent)
- `text-blue-600` (Info)
- `text-green-600` (Success)
- `text-purple-600` (Premium)
- `text-orange-600` (Warning)

## 🎯 Best Practices

1. **Keep labels short** (2-3 words max)
2. **Use descriptive URLs** (/sale, /best-sellers)
3. **Set logical sort order** (1, 2, 3...)
4. **Use colors consistently** (red for sales, blue for info)
5. **Test on mobile** (responsive design)

## 📚 Related Documentation

- `SECONDARY_MENU_MODAL_IMPLEMENTATION.md` - Full implementation details
- `SECONDARY-MENU-ADMIN-GUIDE.md` - Original guide
- `.windsurfrules` - Project guidelines

---

**Status**: ✅ Ready to use
**Last Updated**: November 6, 2025
