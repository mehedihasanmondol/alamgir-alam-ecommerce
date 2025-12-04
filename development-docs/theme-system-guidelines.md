# Theme System Guidelines - MUST FOLLOW

## ⚠️ CRITICAL: Add This Section to .windsurfrules

Copy and paste the following into your `.windsurfrules` file after section 9 (Image Management):

---

### 10. Theme System (Tailwind Color-Based)
**CRITICAL**: All color styling MUST use the theme system. Never hardcode colors!

#### Theme Philosophy
- **NOT folder-based** - No separate theme directories
- **NOT layout-based** - Same layouts for all themes
- **NOT asset-based** - No separate CSS/JS builds
- **ONLY Tailwind utility classes** - Simple color swaps via database

#### Using Themes in Code

**✅ CORRECT - Always Use Theme Functions:**
```blade
<!-- Button Example -->
<button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_bg_hover') }} {{ theme('button_primary_text') }} px-4 py-2 rounded-lg">
    Click Me
</button>

<!-- Card Example -->
<div class="{{ theme('card_bg') }} {{ theme('card_border') }} border rounded-lg p-4">
    <h3 class="{{ theme('card_text') }}">Card Title</h3>
</div>

<!-- Multiple Classes -->
<div class="{{ theme_classes(['primary_bg', 'primary_text', 'rounded-lg', 'p-4']) }}">
    Content
</div>

<!-- Link Example -->
<a href="#" class="{{ theme('link_text') }} {{ theme('link_hover_text') }}">
    Read More
</a>

<!-- Badge Example -->
<span class="{{ theme('badge_success_bg') }} {{ theme('badge_success_text') }} px-2 py-1 rounded">
    Active
</span>
```

**❌ WRONG - NEVER Hardcode Colors:**
```blade
<!-- DON'T DO THIS -->
<button class="bg-blue-600 hover:bg-blue-700 text-white">Click Me</button>

<!-- DON'T DO THIS -->
<div class="bg-white text-gray-900 border-gray-200">Content</div>

<!-- DON'T DO THIS -->
<a href="#" class="text-blue-600 hover:text-blue-800">Link</a>
```

#### Available Theme Keys

**Primary Colors:**
- `primary_bg` - Primary background
- `primary_bg_hover` - Primary background hover
- `primary_text` - Primary text color
- `primary_border` - Primary border color

**Buttons:**
- `button_primary_bg` - Primary button background
- `button_primary_bg_hover` - Primary button hover (includes hover: prefix)
- `button_primary_text` - Primary button text
- `button_secondary_bg` - Secondary button background
- `button_secondary_bg_hover` - Secondary button hover
- `button_secondary_text` - Secondary button text

**Cards & Containers:**
- `card_bg` - Card background
- `card_text` - Card text color
- `card_border` - Card border color
- `card_shadow` - Card shadow class

**Status Colors:**
- `success_bg` / `success_text` - Success states
- `danger_bg` / `danger_text` - Error/danger states
- `warning_bg` / `warning_text` - Warning states
- `info_bg` / `info_text` - Info states

**Badges:**
- `badge_primary_bg` / `badge_primary_text`
- `badge_success_bg` / `badge_success_text`
- `badge_danger_bg` / `badge_danger_text`
- `badge_warning_bg` / `badge_warning_text`

**Forms & Inputs:**
- `input_bg` - Input background
- `input_text` - Input text color
- `input_border` - Input border
- `input_focus_border` - Input focus border (includes focus: prefix)
- `input_focus_ring` - Input focus ring (includes focus: prefix)

**Admin Sidebar:**
- `sidebar_bg` - Sidebar background
- `sidebar_text` - Sidebar text
- `sidebar_active_bg` - Active menu item background
- `sidebar_active_text` - Active menu item text
- `sidebar_hover_bg` - Hover state (includes hover: prefix)

**Tables:**
- `table_header_bg` - Table header background
- `table_header_text` - Table header text
- `table_row_hover` - Table row hover (includes hover: prefix)
- `table_border` - Table border color

**Links:**
- `link_text` - Link text color
- `link_hover_text` - Link hover text (includes hover: prefix)

**Header:**
- `header_bg` - Header background
- `header_text` - Header text
- `header_border` - Header border

#### Helper Functions

```php
// Get single theme class
theme('primary_bg')                    // Returns: 'bg-blue-600'
theme('button_primary_bg', 'bg-gray-500') // With fallback

// Get multiple classes at once
theme_classes(['primary_bg', 'primary_text', 'rounded-lg'])
// Returns: 'bg-blue-600 text-blue-600 rounded-lg'

// Get active theme model
$theme = theme_active();
echo $theme->label; // "Default (Blue)"
```

#### When Creating New Features

1. **ALWAYS check theme system first** before hardcoding any color
2. **Use existing theme keys** when possible
3. **If you need a new color variant:**
   - Check if similar key exists (e.g., use `primary_bg` instead of creating `header_primary_bg`)
   - Only create new key if truly unique use case
   - Document the new key in `ThemeService::getThemeCategories()`
   - Add migration to add column to `theme_settings` table

4. **For neutral colors** (gray, white, black):
   - Use theme keys for consistency
   - Examples: `card_bg` (white), `table_header_bg` (gray-50)

5. **For hover/focus states:**
   - Store complete class with prefix: `hover:bg-blue-700`
   - This allows different hover colors per theme

#### Migration Example (Adding New Theme Key)

If you need a new theme key:

```php
// In a new migration file
public function up(): void
{
    Schema::table('theme_settings', function (Blueprint $table) {
        $table->string('footer_bg')->default('bg-gray-900')->after('header_border');
        $table->string('footer_text')->default('text-gray-300')->after('footer_bg');
    });
}
```

Then update:
1. `ThemeSetting` model `$fillable` array
2. `ThemeSettingSeeder` predefined themes
3. `ThemeService::getThemeCategories()` for admin UI
4. `ThemeController` validation rules

#### Testing Your Theme Implementation

```blade
<!-- Test all theme keys on a single page -->
<div class="space-y-4 p-4">
    <button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_text') }} px-4 py-2">
        Primary Button
    </button>
    <div class="{{ theme('card_bg') }} {{ theme('card_border') }} border p-4">
        <p class="{{ theme('card_text') }}">Card Content</p>
    </div>
    <span class="{{ theme('badge_success_bg') }} {{ theme('badge_success_text') }} px-2 py-1">
        Badge
    </span>
</div>
```

Then switch themes in admin panel and verify all elements change color correctly.

#### Common Mistakes to Avoid

❌ **Mixing hardcoded colors with theme:**
```blade
<div class="bg-blue-600 {{ theme('primary_text') }}">Mixed (Wrong!)</div>
```

❌ **Forgetting hover/focus prefixes:**
```blade
<button class="{{ theme('primary_bg') }} hover:bg-blue-700">
    <!-- Wrong: Should use theme('primary_bg_hover') -->
</button>
```

❌ **Not checking for existing keys:**
```blade
<div class="bg-blue-600">
    <!-- Wrong: Use theme('primary_bg') instead -->
</div>
```

✅ **Correct approach:**
```blade
<button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_bg_hover') }} {{ theme('button_primary_text') }}">
    Fully Themed
</button>
```

#### Performance Note

Theme classes are cached in Laravel cache. After changing themes in admin panel:
- Cache is automatically cleared
- New theme takes effect immediately
- No need to rebuild assets or clear browser cache

#### Admin Access

- **Theme Settings**: `/admin/theme-settings`
- **Features**:
  - View all themes with live previews
  - Activate any theme with one click
  - Customize colors for any theme
  - Duplicate themes to create custom variants
  - Reset predefined themes to defaults

---

## Summary

**GOLDEN RULE**: If it has a color, use `theme()` function. No exceptions!

This ensures:
- ✅ Consistent theming across entire application
- ✅ Easy theme switching by admins
- ✅ No need to touch code when changing colors
- ✅ Simple Tailwind utility classes (no custom CSS needed)

**Remember**: Themes are database-driven Tailwind classes. When admin changes theme, entire site updates instantly!
