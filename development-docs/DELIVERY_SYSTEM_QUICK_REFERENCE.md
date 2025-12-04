# 🚀 Delivery System - Quick Reference Guide

## 📋 Quick Access

### **URLs**
- **Zones**: `http://localhost:8000/admin/delivery/zones`
- **Methods**: `http://localhost:8000/admin/delivery/methods`
- **Rates**: `http://localhost:8000/admin/delivery/rates`

### **Navigation**
Admin Panel → Shipping & Delivery → [Select Page]

---

## ⚡ Key Features

### **Real-Time Search**
- Type in search box
- Results update instantly (300ms debounce)
- No page reload required

### **Status Toggle**
- Click toggle switch
- Status updates immediately
- Toast notification confirms change

### **Filtering**
- **Methods**: Filter by type
- **Rates**: Filter by zone and/or method
- Filters work with search

### **Sorting**
- Click column headers
- Toggle ascending/descending
- Visual indicators show sort direction

### **Pagination**
- Select items per page (10, 15, 25, 50)
- Navigate pages
- Maintains search/filter state

---

## 🎨 UI Components

### **Statistics Cards**
- Top of each page
- Real-time data
- Color-coded icons

### **Search Bar**
- Instant search
- Loading indicator
- Clear button (when typing)

### **Toggle Switch**
- Green = Active
- Gray = Inactive
- Shows loading spinner

### **Toast Notifications**
- Green = Success
- Red = Error
- Auto-dismiss (3 seconds)

---

## 🔧 Livewire Components

### **Zone Management**
```blade
@livewire('admin.delivery.zone-table')
@livewire('admin.delivery.zone-status-toggle', ['zoneId' => $id, 'isActive' => $status])
```

### **Method Management**
```blade
@livewire('admin.delivery.method-table')
@livewire('admin.delivery.method-status-toggle', ['methodId' => $id, 'isActive' => $status])
```

### **Rate Management**
```blade
@livewire('admin.delivery.rate-table')
@livewire('admin.delivery.rate-status-toggle', ['rateId' => $id, 'isActive' => $status])
```

---

## 📊 Statistics Explained

### **Zones Page**
- **Total Zones**: All zones in system
- **Active**: Currently enabled zones
- **Inactive**: Disabled zones
- **Total Rates**: Sum of all rates across zones

### **Methods Page**
- **Total Methods**: All shipping methods
- **Active**: Currently enabled methods
- **Inactive**: Disabled methods
- **Free Shipping**: Methods with type "free"

### **Rates Page**
- **Total Rates**: All configured rates
- **Active**: Currently enabled rates
- **Inactive**: Disabled rates
- **Avg. Base Rate**: Average base rate in BDT

---

## 🎯 Common Actions

### **Search**
1. Type in search box
2. Wait 300ms
3. Results update automatically

### **Toggle Status**
1. Click toggle switch
2. Wait for loading spinner
3. See toast notification
4. Status updated

### **Delete Item**
1. Click trash icon
2. Confirm in popup
3. Item deleted
4. See success message

### **Change Per Page**
1. Select from dropdown
2. Page reloads with new count
3. Maintains current filters

---

## 🐛 Troubleshooting

### **Search Not Working**
- Check internet connection
- Clear browser cache
- Refresh page

### **Toggle Not Responding**
- Wait for current action to complete
- Check for error toast
- Refresh if stuck

### **Filters Not Applying**
- Clear search first
- Try one filter at a time
- Refresh page if needed

---

## 💡 Tips & Tricks

1. **Fast Search**: Type quickly, results appear after 300ms pause
2. **Multiple Filters**: Combine search + filters for precise results
3. **Keyboard Navigation**: Use Tab to navigate, Enter to search
4. **URL Sharing**: Copy URL to share filtered view with team
5. **Bulk Operations**: Select per-page = 50 for bulk viewing

---

## 📱 Mobile Usage

- All features work on mobile
- Swipe tables horizontally
- Tap to toggle status
- Pinch to zoom if needed

---

## 🔐 Permissions Required

- Admin access
- Delivery management permission
- Active user account

---

## 📞 Need Help?

- Check `DELIVERY_SYSTEM_LIVEWIRE_UPDATE.md` for detailed docs
- Review `DELIVERY_SYSTEM_README.md` for API reference
- Contact system administrator

---

**Version**: 4.0.0  
**Last Updated**: November 10, 2025
