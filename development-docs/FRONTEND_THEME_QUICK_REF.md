# 🎨 Frontend Theme - Quick Reference

## Most Used Frontend Theme Variables

### **Product Cards**
```blade
<!-- Product Card -->
<div class="{{ theme('product_card_bg') }} {{ theme('product_card_border') }} border {{ theme('product_card_hover_shadow') }}">
    <h3 class="{{ theme('product_title_text') }}">Product Name</h3>
    <span class="{{ theme('product_price_text') }}">$99.99</span>
    <span class="{{ theme('product_old_price_text') }} line-through">$149.99</span>
</div>
```

### **Buttons**
```blade
<!-- Add to Cart -->
<button class="{{ theme('cart_button_bg') }} {{ theme('cart_button_hover_bg') }} {{ theme('cart_button_text') }}">
    Add to Cart
</button>

<!-- View/Shop Button -->
<button class="{{ theme('shop_button_bg') }} {{ theme('shop_button_hover_bg') }} {{ theme('shop_button_text') }}">
    View Details
</button>
```

### **Navigation**
```blade
<nav class="{{ theme('nav_bg') }}">
    <a href="/" class="{{ theme('nav_text') }} {{ theme('nav_hover_text') }}">Home</a>
    <a href="/shop" class="{{ theme('nav_active_text') }}">Shop</a>
</nav>
```

### **Price Display**
```blade
<span class="{{ theme('price_color') }} text-2xl font-bold">$99.99</span>
<span class="{{ theme('sale_price_color') }} text-lg">Sale: $79.99</span>
```

### **Stock Status**
```blade
@if($inStock)
    <span class="{{ theme('stock_available_text') }}">In Stock</span>
@else
    <span class="{{ theme('stock_unavailable_text') }}">Out of Stock</span>
@endif
```

### **Category Badges**
```blade
<span class="{{ theme('category_badge_bg') }} {{ theme('category_badge_text') }} {{ theme('category_badge_hover_bg') }} px-3 py-1 rounded">
    Electronics
</span>
```

### **Discount Badges**
```blade
<span class="{{ theme('product_discount_badge_bg') }} {{ theme('product_discount_badge_text') }} px-2 py-1 rounded">
    -25%
</span>
```

### **Search Bar**
```blade
<input type="text" 
       class="{{ theme('search_input_bg') }} {{ theme('search_input_text') }} {{ theme('search_input_border') }} {{ theme('search_input_focus_border') }} border rounded px-4 py-2">
```

### **Rating Stars**
```blade
<i class="fas fa-star {{ theme('rating_star_color') }}"></i>
<i class="fas fa-star {{ theme('rating_star_empty_color') }}"></i>
```

### **Footer**
```blade
<footer class="{{ theme('footer_bg') }} {{ theme('footer_text') }}">
    <h3 class="{{ theme('footer_heading_text') }}">Quick Links</h3>
    <a href="#" class="{{ theme('footer_link_text') }} {{ theme('footer_link_hover_text') }}">About</a>
</footer>
```

### **Hero Section**
```blade
<section class="relative">
    <div class="{{ theme('hero_overlay_bg') }} bg-opacity-75"></div>
    <h1 class="{{ theme('hero_title_text') }}">Welcome</h1>
    <p class="{{ theme('hero_subtitle_text') }}">Shop Now</p>
    <button class="{{ theme('hero_button_bg') }} {{ theme('hero_button_hover_bg') }} {{ theme('hero_button_text') }}">
        Get Started
    </button>
</section>
```

### **Newsletter**
```blade
<section class="{{ theme('newsletter_bg') }} {{ theme('newsletter_text') }}">
    <h2>Subscribe</h2>
    <button class="{{ theme('newsletter_button_bg') }} {{ theme('newsletter_button_text') }}">
        Subscribe
    </button>
</section>
```

---

## Complete Variable List

| Variable | Use Case | Example Value |
|----------|----------|---------------|
| `nav_bg` | Navigation background | `bg-white` |
| `nav_text` | Nav link text | `text-gray-700` |
| `nav_hover_text` | Nav link hover | `hover:text-blue-600` |
| `nav_active_text` | Active nav link | `text-blue-600` |
| `product_card_bg` | Product card background | `bg-white` |
| `product_card_border` | Card border | `border-gray-200` |
| `product_card_hover_shadow` | Card hover effect | `hover:shadow-lg` |
| `product_title_text` | Product name color | `text-gray-900` |
| `product_price_text` | Price color | `text-blue-600` |
| `product_old_price_text` | Strike price | `text-gray-400` |
| `product_discount_badge_bg` | Discount bg | `bg-red-500` |
| `product_discount_badge_text` | Discount text | `text-white` |
| `shop_button_bg` | View button | `bg-blue-600` |
| `shop_button_hover_bg` | View hover | `hover:bg-blue-700` |
| `shop_button_text` | View text | `text-white` |
| `cart_button_bg` | Cart button | `bg-green-600` |
| `cart_button_hover_bg` | Cart hover | `hover:bg-green-700` |
| `cart_button_text` | Cart text | `text-white` |
| `footer_bg` | Footer background | `bg-gray-900` |
| `footer_text` | Footer text | `text-gray-300` |
| `footer_heading_text` | Footer headings | `text-white` |
| `footer_link_text` | Footer links | `text-gray-400` |
| `footer_link_hover_text` | Link hover | `hover:text-white` |
| `hero_overlay_bg` | Hero overlay | `bg-blue-900` |
| `hero_title_text` | Hero title | `text-white` |
| `hero_subtitle_text` | Hero subtitle | `text-blue-100` |
| `hero_button_bg` | Hero CTA | `bg-blue-600` |
| `hero_button_hover_bg` | CTA hover | `hover:bg-blue-700` |
| `hero_button_text` | CTA text | `text-white` |
| `category_badge_bg` | Category badge | `bg-blue-100` |
| `category_badge_text` | Badge text | `text-blue-800` |
| `category_badge_hover_bg` | Badge hover | `hover:bg-blue-200` |
| `price_color` | Regular price | `text-blue-600` |
| `sale_price_color` | Sale price | `text-red-500` |
| `stock_available_text` | In stock | `text-green-600` |
| `stock_unavailable_text` | Out of stock | `text-red-600` |
| `search_input_bg` | Search bg | `bg-gray-50` |
| `search_input_text` | Search text | `text-gray-900` |
| `search_input_border` | Search border | `border-gray-300` |
| `search_input_focus_border` | Focus border | `focus:border-blue-500` |
| `rating_star_color` | Filled star | `text-yellow-400` |
| `rating_star_empty_color` | Empty star | `text-gray-300` |
| `newsletter_bg` | Newsletter bg | `bg-blue-600` |
| `newsletter_text` | Newsletter text | `text-white` |
| `newsletter_button_bg` | Button bg | `bg-white` |
| `newsletter_button_text` | Button text | `text-blue-600` |

---

## Tips

### **Combine Multiple Variables**
```blade
{{ theme_classes(['cart_button_bg', 'cart_button_hover_bg', 'cart_button_text']) }}
```

### **Add Fallbacks**
```blade
{{ theme('price_color', 'text-blue-600') }}
```

### **Mix with Utility Classes**
```blade
<div class="{{ theme('product_card_bg') }} p-4 rounded-lg shadow">
    <!-- Theme colors + Tailwind utilities -->
</div>
```

---

## Admin Customization

Admins can change all these colors from:
**Admin Panel → Theme Settings → Edit Theme → Frontend Colors Section**

Each theme (Blue, Green, Red, Purple, Dark, Indigo) has different frontend colors!
