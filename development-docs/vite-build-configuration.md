# Vite Build Configuration

## Overview
Complete Vite configuration for Laravel ecommerce project with all required JavaScript and CSS files.

---

## Configuration File: `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '0.0.0.0',        // allow network access
        port: 5173,             // or any port you like
        hmr: {
            host: 'localhost', // Change to your local IP for network access
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js', 
                'resources/js/admin.js',
                'resources/js/ckeditor-init.js',  // CKEditor initialization
                'resources/js/blog-post-editor.js',  // Blog post editor
                'resources/js/product-editor.js',  // Product editor
                'resources/js/footer-settings-editor.js',  // Footer settings editor
                'resources/js/site-settings-editor.js'  // Site settings editor
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

---

## JavaScript Files Included

### 1. **app.js** - Frontend Application
- **Path**: `resources/js/app.js`
- **Purpose**: Main frontend JavaScript
- **Used In**: All frontend pages
- **Includes**: Alpine.js, Axios, Bootstrap

### 2. **admin.js** - Admin Panel
- **Path**: `resources/js/admin.js`
- **Purpose**: Admin panel specific JavaScript
- **Used In**: All admin pages
- **Includes**: Admin utilities, helpers

### 3. **ckeditor-init.js** - CKEditor Initialization
- **Path**: `resources/js/ckeditor-init.js`
- **Purpose**: Initialize CKEditor with custom configuration
- **Used In**: All pages with CKEditor (via layout)
- **Size**: ~1.13 MB (287 KB gzipped)
- **Includes**: Full CKEditor 5 with all plugins

### 4. **blog-post-editor.js** - Blog Post Editor
- **Path**: `resources/js/blog-post-editor.js`
- **Purpose**: Blog post creation/editing functionality
- **Used In**: 
  - `admin/blog/posts/create.blade.php`
  - `admin/blog/posts/edit.blade.php`
- **Features**: CKEditor integration, image upload, category management

### 5. **product-editor.js** - Product Editor
- **Path**: `resources/js/product-editor.js`
- **Purpose**: Product creation/editing functionality
- **Used In**:
  - `admin/product/create-livewire.blade.php`
  - `admin/product/edit-livewire.blade.php`
- **Features**: CKEditor integration, variant management, image handling

### 6. **footer-settings-editor.js** - Footer Settings Editor
- **Path**: `resources/js/footer-settings-editor.js`
- **Purpose**: Footer settings management
- **Used In**: `admin/footer-management/index.blade.php`
- **Features**: CKEditor for footer content, link management

### 7. **site-settings-editor.js** - Site Settings Editor
- **Path**: `resources/js/site-settings-editor.js`
- **Purpose**: Site settings management
- **Used In**: `admin/site-settings/index.blade.php`
- **Features**: Settings form handling, validation

---

## CSS Files Included

### 1. **app.css** - Main Stylesheet
- **Path**: `resources/css/app.css`
- **Purpose**: Main application styles
- **Size**: ~155 KB (23 KB gzipped)
- **Includes**: Tailwind CSS, custom styles

### 2. **ckeditor-init.css** - CKEditor Styles
- **Path**: Bundled with `ckeditor-init.js`
- **Purpose**: CKEditor UI styles
- **Size**: ~275 KB (37 KB gzipped)

---

## Build Commands

### Development Mode
```bash
# Start Vite dev server with HMR
npm run dev
```

**Features:**
- Hot Module Replacement (HMR)
- Fast refresh
- Source maps
- Network access on port 5173

### Production Build
```bash
# Build optimized assets for production
npm run build
```

**Output:**
- Minified JavaScript and CSS
- Hashed filenames for cache busting
- Gzipped assets
- Manifest file for Laravel

**Build Output:**
```
public/build/
├── manifest.json (2.22 KB)
├── assets/
│   ├── app-[hash].css (154.77 KB / 23.23 KB gzipped)
│   ├── ckeditor-init-[hash].css (274.74 KB / 36.99 KB gzipped)
│   ├── app-[hash].js (44.97 KB / 14.30 KB gzipped)
│   ├── admin-[hash].js (0.41 KB / 0.31 KB gzipped)
│   ├── ckeditor-init-[hash].js (290.45 KB / 69.88 KB gzipped)
│   ├── blog-post-editor-[hash].js (4.55 KB / 1.66 KB gzipped)
│   ├── product-editor-[hash].js (2.46 KB / 1.05 KB gzipped)
│   ├── footer-settings-editor-[hash].js (2.00 KB / 0.92 KB gzipped)
│   ├── site-settings-editor-[hash].js (1.99 KB / 1.03 KB gzipped)
│   ├── sortable.esm-[hash].js (73.25 KB / 27.08 KB gzipped)
│   └── index-[hash].js (845.03 KB / 220.08 KB gzipped)
```

---

## Network Access Configuration

### For Local Development
```javascript
hmr: {
    host: 'localhost',
}
```

### For Network Access (Mobile Testing)
```javascript
hmr: {
    host: '192.168.0.118', // Your computer's local IP
}
```

**To find your local IP:**
- **Windows**: `ipconfig` (look for IPv4 Address)
- **Mac/Linux**: `ifconfig` or `ip addr`

**Access from mobile:**
- Dev server: `http://192.168.0.118:5173`
- Laravel app: `http://192.168.0.118:8000`

---

## Common Issues & Solutions

### Issue 1: "Unable to locate file in Vite manifest"
**Cause**: JavaScript file not included in Vite config
**Solution**: Add the file to `vite.config.js` input array and run `npm run build`

### Issue 2: "Can't resolve 'cropperjs/dist/cropper.css'"
**Cause**: Missing npm dependencies
**Solution**: Run `npm install`

### Issue 3: Build warnings about chunk size
**Cause**: CKEditor is a large library (>500 KB)
**Solution**: This is expected and normal. CKEditor is properly code-split.

### Issue 4: HMR not working on network
**Cause**: HMR host set to localhost
**Solution**: Change `hmr.host` to your local IP address

### Issue 5: Assets not loading in production
**Cause**: Assets not built or manifest missing
**Solution**: Run `npm run build` before deploying

---

## Deployment Checklist

Before deploying to production:

- [ ] Run `npm install` to ensure all dependencies are installed
- [ ] Run `npm run build` to compile production assets
- [ ] Verify `public/build/manifest.json` exists
- [ ] Verify all asset files exist in `public/build/assets/`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Clear Laravel cache: `php artisan cache:clear`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Clear view cache: `php artisan view:clear`

---

## File Size Optimization

### Current Sizes (Gzipped)
- **Total CSS**: ~60 KB
- **Total JS**: ~335 KB
- **CKEditor**: ~287 KB (largest component)

### Optimization Tips
1. **CKEditor**: Consider using CKEditor CDN for smaller bundle
2. **Code Splitting**: Already implemented via Vite
3. **Tree Shaking**: Automatically done by Vite
4. **Compression**: Enable Gzip/Brotli on server

---

## Adding New JavaScript Files

To add a new JavaScript file to the build:

1. **Create the file** in `resources/js/`
2. **Add to Vite config**:
   ```javascript
   input: [
       // ... existing files
       'resources/js/your-new-file.js',  // Your new file
   ],
   ```
3. **Rebuild assets**: `npm run build`
4. **Use in Blade**:
   ```blade
   @push('scripts')
   @vite('resources/js/your-new-file.js')
   @endpush
   ```

---

## Performance Metrics

### Development Mode (npm run dev)
- **Build Time**: ~2-5 seconds
- **HMR Update**: <100ms
- **Page Load**: Fast with HMR

### Production Build (npm run build)
- **Build Time**: ~25-30 seconds
- **Total Size**: ~2.5 MB (uncompressed)
- **Total Size**: ~400 KB (gzipped)
- **Page Load**: Optimized with caching

---

## Browser Support

### Supported Browsers
- Chrome/Edge: Last 2 versions
- Firefox: Last 2 versions
- Safari: Last 2 versions
- iOS Safari: Last 2 versions
- Android Chrome: Last 2 versions

### Polyfills
- Not required for modern browsers
- Consider adding for IE11 support if needed

---

## Summary

The Vite configuration is now complete with all required JavaScript and CSS files. All admin pages (blog posts, products, site settings, footer settings) will work correctly in production mode.

**Key Points:**
- ✅ All JavaScript files included in build
- ✅ CKEditor properly configured
- ✅ Network access supported
- ✅ Production build optimized
- ✅ HMR working in development
- ✅ Assets properly versioned with hashes
