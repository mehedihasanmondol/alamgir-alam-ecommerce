# 🎨 Theme System - Quick Start Guide

## What is This?

A **simple color-based theme system** where you can change your entire website's colors from the admin panel. No code changes needed!

## How It Works

- Stores Tailwind CSS color classes in database
- Admin switches themes with one click
- All pages update instantly
- 6 predefined themes included

## Setup (3 Commands)

```bash
# 1. Load helper functions
composer dump-autoload

# 2. Create theme database table
php artisan migrate --path=database/migrations/2025_11_20_100000_create_theme_settings_table.php

# 3. Load predefined themes
php artisan db:seed --class=ThemeSettingSeeder
```

## Access Admin Panel

Go to: **`/admin/theme-settings`**

## Predefined Themes

1. **Default (Blue)** - Current theme
2. **Green Nature** - Fresh, environmental
3. **Red Energy** - Bold, energetic
4. **Purple Royal** - Luxury, premium
5. **Dark Mode** - Dark backgrounds
6. **Indigo Professional** - Corporate look

## Using in Code

### ❌ Before (Hardcoded):
```blade
<button class="bg-blue-600 hover:bg-blue-700 text-white">Click</button>
```

### ✅ After (Theme System):
```blade
<button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_bg_hover') }} {{ theme('button_primary_text') }}">
    Click
</button>
```

## Available Theme Functions

```php
// Get single color class
theme('primary_bg')                          // Returns: 'bg-blue-600'
theme('button_primary_text', 'text-white')  // With fallback

// Get multiple classes at once
theme_classes(['primary_bg', 'primary_text', 'rounded-lg'])
// Returns: 'bg-blue-600 text-blue-600 rounded-lg'

// Get active theme info
$theme = theme_active();
echo $theme->label;  // "Default (Blue)"
```

## Common Theme Keys

| Purpose | Key | Example |
|---------|-----|---------|
| Primary Button | `button_primary_bg` | `bg-blue-600` |
| Primary Button Hover | `button_primary_bg_hover` | `hover:bg-blue-700` |
| Primary Button Text | `button_primary_text` | `text-white` |
| Card Background | `card_bg` | `bg-white` |
| Card Text | `card_text` | `text-gray-900` |
| Success Badge | `badge_success_bg` | `bg-green-100` |
| Success Badge Text | `badge_success_text` | `text-green-800` |
| Link Color | `link_text` | `text-blue-600` |
| Link Hover | `link_hover_text` | `hover:text-blue-800` |
| Input Border Focus | `input_focus_border` | `focus:border-blue-500` |
| Sidebar Active | `sidebar_active_bg` | `bg-blue-600` |

[See complete list in documentation]

## Admin Features

**From `/admin/theme-settings` you can:**
- ✅ Activate any theme instantly
- ✅ Customize colors for each theme
- ✅ Duplicate themes to create custom versions
- ✅ Reset predefined themes to defaults
- ✅ Preview themes before activating

## Examples

### Button Set
```blade
<!-- Primary -->
<button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_bg_hover') }} {{ theme('button_primary_text') }} px-4 py-2 rounded">
    Save
</button>

<!-- Secondary -->
<button class="{{ theme('button_secondary_bg') }} {{ theme('button_secondary_bg_hover') }} {{ theme('button_secondary_text') }} px-4 py-2 rounded border {{ theme('button_secondary_border') }}">
    Cancel
</button>
```

### Card
```blade
<div class="{{ theme('card_bg') }} {{ theme('card_border') }} border rounded-lg {{ theme('card_shadow') }} p-6">
    <h3 class="{{ theme('card_text') }} font-bold text-lg">Title</h3>
    <p class="{{ theme('card_text') }} text-sm">Content goes here</p>
</div>
```

### Badges
```blade
<span class="{{ theme('badge_success_bg') }} {{ theme('badge_success_text') }} px-2 py-1 rounded text-xs">
    Active
</span>

<span class="{{ theme('badge_danger_bg') }} {{ theme('badge_danger_text') }} px-2 py-1 rounded text-xs">
    Error
</span>
```

### Form Input
```blade
<input type="text" 
       class="{{ theme('input_bg') }} {{ theme('input_text') }} {{ theme('input_border') }} {{ theme('input_focus_border') }} {{ theme('input_focus_ring') }} border rounded px-3 py-2">
```

## Migration from Hardcoded Colors

**Your project has:**
- 1,821 hardcoded background colors
- 2,083 hardcoded text colors

**Priority Order:**
1. Admin panel (highest impact)
2. Frontend components
3. Email templates

**Search & Replace:**
- `bg-blue-600` → `{{ theme('primary_bg') }}`
- `text-blue-600` → `{{ theme('primary_text') }}`
- `bg-green-600` → `{{ theme('success_bg') }}`
- `bg-red-600` → `{{ theme('danger_bg') }}`

## Troubleshooting

**Theme not changing?**
```bash
php artisan cache:clear
php artisan view:clear
# Then hard refresh browser (Ctrl+F5)
```

**Function not found?**
```bash
composer dump-autoload
```

**Colors look wrong?**
```bash
npm run build
# Clear browser cache
```

## Documentation

- **Complete Guide**: `development-docs/theme-system-complete.md`
- **Development Rules**: `development-docs/theme-system-guidelines.md`
- **Add to .windsurfrules**: See guidelines document

## Rule for Future Development

**🚨 GOLDEN RULE: If it has a color, use `theme()` function!**

Never hardcode colors:
```blade
❌ class="bg-blue-600 text-white"
✅ class="{{ theme('primary_bg') }} {{ theme('primary_text') }}"
```

## Need Help?

Check full documentation in `development-docs/theme-system-complete.md`

---

**System Status:** ✅ Ready to use  
**Admin Access:** `/admin/theme-settings`  
**Predefined Themes:** 6  
**Theme Variables:** 70+  
**Performance:** Cached (fast!)
