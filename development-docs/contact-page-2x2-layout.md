# Contact Page - 2x2 Grid Layout

## Date: November 26, 2025

## Overview
Complete redesign of contact page to a modern 2x2 grid layout (50/50 split) with compact UI/UX, merged chambers integration, and Google Maps HTML embed support.

---

## New Layout Structure

```
┌──────────────────────────────────────────────────────────────┐
│                      Page Header                              │
└──────────────────────────────────────────────────────────────┘

┌─────────────────────────────┬─────────────────────────────────┐
│ Row 1 - Left (50%)         │ Row 1 - Right (50%)             │
│                             │                                 │
│ GET IN TOUCH                │ CONTACT FORM                    │
│ • Contact Info (Compact)    │ • Name                          │
│ • Chambers (Compact/Merged) │ • Email                         │
│ • Social Media (Compact)    │ • Phone                         │
│                             │ • Subject                       │
│                             │ • Message                       │
│                             │ • Submit Button                 │
├─────────────────────────────┼─────────────────────────────────┤
│ Row 2 - Left (50%)         │ Row 2 - Right (50%)             │
│                             │                                 │
│ FAQs                        │ GOOGLE MAPS                     │
│ • Collapsible Questions     │ • HTML Embed Code               │
│ • Smooth Animations         │   (from Contact Settings)       │
│ • Purple Header             │ • OR API-based Map              │
│                             │ • Blue Header                   │
└─────────────────────────────┴─────────────────────────────────┘
```

---

## Features

### Row 1: Left Side - Get in Touch (50%)

**Compact Contact Information:**
- ✅ Email (clickable mailto:)
- ✅ Phone (clickable tel:)
- ✅ WhatsApp (clickable wa.me link)
- ✅ Address (formatted with city, state, zip, country)
- ✅ Business Hours

**Design:**
- Smaller icons (w-8 h-8 instead of w-12 h-12)
- Compact spacing (space-y-2 instead of space-y-6)
- Text size: text-sm and text-xs for better fit
- Colored icon backgrounds (blue, green, purple, orange)

**Our Chambers (Compact & Merged):**
- ✅ Integrated in the same card (not separate section)
- ✅ Border-top separator for visual distinction
- ✅ Gray background cards (bg-gray-50)
- ✅ Hover effect (hover:bg-gray-100)
- ✅ Shows: name, address, phone, email
- ✅ Inline phone/email links
- ✅ Smaller text (text-xs, text-sm)
- ✅ Dynamically from appointment Chamber model

**Social Media (Compact):**
- Border-top separator
- Smaller icons (w-8 h-8 instead of w-10 h-10)
- Facebook, Twitter, Instagram, LinkedIn, YouTube

---

### Row 1: Right Side - Contact Form (50%)

**Livewire Contact Form:**
- ✅ Name field
- ✅ Email field
- ✅ Phone field
- ✅ Subject field
- ✅ Message textarea
- ✅ Submit button with loading state
- ✅ Real-time validation
- ✅ Success/error messages

**Design:**
- Full height to match left column
- Paper plane icon in header
- Same shadow and border-radius as left card

---

### Row 2: Left Side - FAQs (50%)

**Collapsible FAQ Accordion:**
- ✅ Purple gradient header
- ✅ Alpine.js powered accordion
- ✅ Smooth transitions
- ✅ Chevron rotation animation
- ✅ Question/Answer pairs
- ✅ Hover effects
- ✅ One open at a time

**Design:**
- Purple header (from-purple-600 to-purple-700)
- White card with shadow
- Gray background for answers (bg-gray-50)
- Border around each FAQ item

---

### Row 2: Right Side - Google Maps (50%)

**Google Maps Integration:**

**Option 1: HTML Embed Code (Preferred)**
- Admin can paste Google Maps iframe embed code
- No API key required
- Easy to configure
- Stored in `map_embed_code` setting
- Height: 400px

**Option 2: API-based Map (Fallback)**
- Uses Google Maps API key
- Dynamic with markers
- Latitude/Longitude from settings
- Only used if embed code is empty

**Option 3: Placeholder**
- Shown if no embed code and no API key
- Simple icon and message
- "Map will be displayed here once configured"

**Design:**
- Blue gradient header (from-blue-600 to-blue-700)
- White card with shadow
- Full-height map display (400px)

---

## File Structure

### Frontend View:
**`resources/views/frontend/contact/index.blade.php`**

**Key Sections:**
1. **Header** (lines 8-14)
2. **Row 1** (lines 18-129)
   - Left: Get in Touch (lines 20-122)
   - Right: Contact Form (lines 124-128)
3. **Row 2** (lines 132-173)
   - Left: FAQs (lines 134-153)
   - Right: Google Maps (lines 156-172)
4. **Scripts** (lines 177-194)

---

## Settings

### Google Maps Embed Code

**New Setting Added:**
- **Key:** `map_embed_code`
- **Group:** `map`
- **Type:** `textarea`
- **Description:** "Google Maps HTML embed code (iframe). If provided, this will be used instead of API-based map."
- **Order:** 4

**How to Get Embed Code:**
1. Go to [Google Maps](https://www.google.com/maps)
2. Search for your location
3. Click "Share" button
4. Click "Embed a map" tab
5. Copy the HTML code (iframe)
6. Paste in Contact Settings → Map Settings → Map Embed Code

**Example Embed Code:**
```html
<iframe src="https://www.google.com/maps/embed?pb=..." 
        width="100%" 
        height="400" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
</iframe>
```

---

## Admin Configuration

### Contact Settings Page
**Access:** Admin → Communication → Contact Messages → [Contact Settings Button]

**Map Settings Section:**
1. **Map Latitude** - Coordinate for API map
2. **Map Longitude** - Coordinate for API map
3. **Map Zoom** - Zoom level (1-20)
4. **Map Embed Code** ← NEW!
   - Paste Google Maps iframe code here
   - If filled, this overrides API-based map
   - Easier to use, no API key required

---

## Responsive Behavior

### Desktop (lg and up):
```
┌──────────────┬──────────────┐
│ Get in Touch │ Contact Form │
├──────────────┼──────────────┤
│ FAQs         │ Google Maps  │
└──────────────┴──────────────┘
```

### Mobile (below lg):
```
┌──────────────────┐
│ Get in Touch     │
├──────────────────┤
│ Contact Form     │
├──────────────────┤
│ FAQs             │
├──────────────────┤
│ Google Maps      │
└──────────────────┘
```

**Classes Used:**
- `grid grid-cols-1 lg:grid-cols-2` - 1 column mobile, 2 columns desktop
- `gap-6` - Spacing between cards
- `mb-6` - Margin between rows

---

## Design Improvements

### Compact UI/UX:
1. **Smaller Icons:**
   - Contact info: `w-8 h-8` (was `w-12 h-12`)
   - Social media: `w-8 h-8` (was `w-10 h-10`)

2. **Tighter Spacing:**
   - Contact items: `space-y-2` (was `space-y-6`)
   - Chambers: `space-y-3` (was `gap-6` grid)

3. **Smaller Text:**
   - Contact info: `text-sm` and `text-xs`
   - Chambers: `text-xs` for details
   - Better information density

4. **Merged Sections:**
   - Contact info + Chambers + Social in ONE card
   - Border-top separators for visual distinction
   - No separate cards cluttering the layout

5. **Equal Heights:**
   - All 4 sections have balanced heights
   - Professional grid appearance
   - Better use of space

---

## Chamber Integration

**Source:** `\App\Models\Chamber::active()->ordered()->get()`

**Display Format (Compact):**
```php
<div class="bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition">
    <h4 class="font-semibold text-sm">Chamber Name</h4>
    <p class="text-xs text-gray-600">
        <i class="fas fa-map-marker-alt"></i> Address
    </p>
    <div class="flex gap-2 text-xs">
        <a href="tel:..."><i class="fas fa-phone"></i> Phone</a>
        <a href="mailto:..."><i class="fas fa-envelope"></i> Email</a>
    </div>
</div>
```

**Benefits:**
- Shows all chambers without taking too much space
- Maintains appointment system integration
- Gray background distinguishes from main contact info
- Hover effect for interactivity

---

## Map Priority Logic

```php
@if(!empty($settings['map_embed_code']))
    {!! $settings['map_embed_code'] !!}
@elseif(config('services.google_maps.api_key') && $settings['map_latitude'])
    <div id="map"></div>
    <script>initMap();</script>
@else
    <p>Map placeholder</p>
@endif
```

**Priority:**
1. **HTML Embed Code** (if provided in settings)
2. **API Map** (if API key and coordinates exist)
3. **Placeholder** (if nothing configured)

---

## Benefits

### User Experience:
✅ **Balanced Layout** - Equal 50/50 split looks professional
✅ **Compact Info** - More information in less space
✅ **Easy Navigation** - All contact methods immediately visible
✅ **Chambers Visible** - All locations shown without separate section
✅ **Interactive FAQs** - Quick answers to common questions
✅ **Visual Map** - Easy location finding

### Admin Experience:
✅ **Easy Map Setup** - Just paste embed code (no API key needed)
✅ **One Place** - All contact info in Get in Touch section
✅ **Dynamic Chambers** - Managed from appointment system
✅ **Settings Control** - Everything configurable from admin

### Technical:
✅ **No API Costs** - Embed code is free, unlimited
✅ **Better Performance** - Static embed loads faster than API
✅ **Responsive** - Mobile-friendly grid system
✅ **Modular** - Easy to maintain and update

---

## Testing Checklist

### Frontend:
- [ ] Contact page loads without errors
- [ ] Desktop shows 2x2 grid layout
- [ ] Mobile shows stacked layout (4 rows)
- [ ] Get in Touch section shows all contact info
- [ ] Chambers display compactly with correct data
- [ ] Contact form submits successfully
- [ ] FAQs are collapsible
- [ ] Google Maps embed displays correctly
- [ ] Social media links work
- [ ] All icons render properly

### Admin:
- [ ] Contact Settings shows map_embed_code field
- [ ] Can paste Google Maps iframe code
- [ ] Code saves correctly
- [ ] Map displays on frontend after saving
- [ ] Fallback to API map works if no embed code
- [ ] Placeholder shows if nothing configured

### Responsive:
- [ ] Desktop (1024px+): 2 columns per row
- [ ] Tablet (768-1023px): 1 column stacked
- [ ] Mobile (< 768px): 1 column stacked
- [ ] Spacing looks good on all sizes
- [ ] Text is readable on all sizes

---

## Migration Notes

### From Old Layout to New:

**What Changed:**
- Layout: From 2/3 + 1/3 sidebar → 2x2 equal grid
- Contact Info: From large cards → compact single card
- Chambers: From separate large section → compact merged section
- Form: From main content → dedicated 50% right column
- FAQs: From sidebar → dedicated 50% bottom-left
- Map: From sidebar → dedicated 50% bottom-right with embed support

**What Stayed:**
- All contact information fields
- Chambers from appointment system
- Livewire contact form
- FAQ functionality
- Google Maps integration
- All data and settings

**New Features:**
- Google Maps HTML embed code support
- Compact UI for better space usage
- 2x2 professional grid layout
- Merged chambers in Get in Touch section

---

## Summary

### Layout:
🎯 **2x2 Grid** - Four equal sections (50% each)
📱 **Responsive** - Stacks on mobile
💼 **Professional** - Clean, modern design

### Sections:
1. **Get in Touch** - Contact info + chambers + social (compact)
2. **Contact Form** - Full Livewire form
3. **FAQs** - Collapsible accordion
4. **Google Maps** - HTML embed or API map

### Key Features:
✅ Compact UI/UX
✅ Merged chambers
✅ Google Maps embed code support
✅ Balanced 50/50 layout
✅ Mobile responsive
✅ Easy admin configuration

---

**Status:** ✅ **Complete - 2x2 Layout Implemented**

**Last Updated:** November 26, 2025  
**Version:** 4.0.0 (2x2 Grid Layout)
