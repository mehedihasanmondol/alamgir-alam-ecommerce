# NPM Setup Guide - Remove CDN Dependencies

## ⚠️ Current Status

The admin panel is currently using CDN links for:
- ✅ **Livewire** - Installed via Composer
- ⚠️ **Alpine.js** - Using CDN (needs local install)
- ⚠️ **Font Awesome** - Using CDN (needs local install)

## 🔧 PowerShell Execution Policy Issue

If you get the error: "running scripts is disabled on this system"

### Solution 1: Enable Scripts for Current Session
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process
```

### Solution 2: Enable Scripts Permanently (Admin Required)
```powershell
# Run PowerShell as Administrator
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Solution 3: Use Command Prompt Instead
Open **Command Prompt** (cmd) instead of PowerShell and run npm commands there.

---

## 📦 Install Required Packages

Once npm is working, run these commands:

### 1. Install Alpine.js
```bash
npm install alpinejs
```

### 2. Install Font Awesome
```bash
npm install @fortawesome/fontawesome-free
```

### 3. Install All Dependencies
```bash
npm install
```

---

## 🔄 Update Files After Installation

### Step 1: Update `resources/js/app.js`

Uncomment the Alpine.js code:

```javascript
import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

### Step 2: Update `resources/css/app.css`

Add Font Awesome import at the top:

```css
@import '@fortawesome/fontawesome-free/css/all.css';

@tailwind base;
@tailwind components;
@tailwind utilities;
```

### Step 3: Update `resources/views/layouts/admin.blade.php`

Remove the CDN links:

**Remove these lines:**
```html
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<!-- Alpine.js CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

Font Awesome will be loaded via `app.css` and Alpine.js via `app.js`.

---

## 🏗️ Build Assets

After making changes, build the assets:

### Development Mode (with hot reload)
```bash
npm run dev
```

### Production Build
```bash
npm run build
```

---

## ✅ Verification

After setup, verify everything works:

1. **Check Alpine.js**:
   - Open browser console
   - Type: `Alpine`
   - Should show Alpine object (not undefined)

2. **Check Font Awesome**:
   - Icons should display properly
   - No 404 errors in console

3. **Check Livewire**:
   - Interactive components should work
   - Status toggle should work
   - Search should work

---

## 📋 Complete Setup Checklist

- [ ] Enable PowerShell scripts OR use Command Prompt
- [ ] Run `npm install`
- [ ] Run `npm install alpinejs`
- [ ] Run `npm install @fortawesome/fontawesome-free`
- [ ] Update `resources/js/app.js` (uncomment Alpine)
- [ ] Update `resources/css/app.css` (add Font Awesome)
- [ ] Update `resources/views/layouts/admin.blade.php` (remove CDNs)
- [ ] Run `npm run build`
- [ ] Test admin panel
- [ ] Verify no CDN requests in browser network tab

---

## 🚨 Temporary Workaround

**Current Setup**: The system is using CDN links temporarily to ensure functionality.

**Why**: PowerShell execution policy is blocking npm commands.

**Impact**: Everything works, but violates the "no CDN" rule.

**Priority**: Medium - System is functional, but should be fixed for production.

---

## 🔍 Troubleshooting

### Issue: npm command not found
**Solution**: Install Node.js from https://nodejs.org/

### Issue: PowerShell blocks scripts
**Solution**: Use Command Prompt or change execution policy (see above)

### Issue: Alpine not working after local install
**Solution**: 
1. Clear browser cache
2. Run `npm run build`
3. Hard refresh (Ctrl+Shift+R)

### Issue: Font Awesome icons not showing
**Solution**:
1. Verify import in `app.css`
2. Run `npm run build`
3. Clear browser cache

### Issue: Vite not running
**Solution**:
```bash
npm install
npm run dev
```

---

## 📚 Additional Resources

- **Alpine.js Docs**: https://alpinejs.dev/
- **Font Awesome Docs**: https://fontawesome.com/
- **Livewire Docs**: https://livewire.laravel.com/
- **Vite Docs**: https://vitejs.dev/

---

## 🎯 Next Steps After NPM Setup

1. Remove CDN links from admin layout
2. Build production assets: `npm run build`
3. Test all Livewire components
4. Verify Alpine.js dropdowns work
5. Check Font Awesome icons display
6. Deploy to production

---

**Status**: ⚠️ Pending NPM Setup  
**Priority**: Medium  
**Blocker**: PowerShell execution policy

**Quick Fix**: Use Command Prompt instead of PowerShell for npm commands.
