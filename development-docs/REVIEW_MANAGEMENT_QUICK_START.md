# Product Review Management - Quick Start Guide

## 🚀 Access the System

1. **Login to Admin Panel**
   - URL: `http://your-domain.com/admin/dashboard`
   - Requires admin role

2. **Navigate to Reviews**
   - Click **"Product Reviews"** in the left sidebar
   - Or visit: `http://your-domain.com/admin/reviews`

---

## 📊 Dashboard Overview

### Statistics Cards (Top of Page)
- **Total Reviews**: All reviews in the system
- **Pending**: Reviews waiting for approval (yellow)
- **Approved**: Published reviews (green)
- **Rejected**: Rejected reviews (red)
- **Avg Rating**: Overall average rating across all approved reviews

---

## 🔍 Filtering & Search

### Search Bar
- Search by:
  - Review content
  - Review title
  - Reviewer name
  - Product name
- Real-time search with 300ms debounce

### Advanced Filters (Click "Filters" button)
1. **Status Filter**
   - All Status
   - Pending
   - Approved
   - Rejected

2. **Rating Filter**
   - All Ratings
   - 5 Stars ⭐⭐⭐⭐⭐
   - 4 Stars ⭐⭐⭐⭐
   - 3 Stars ⭐⭐⭐
   - 2 Stars ⭐⭐
   - 1 Star ⭐

3. **Verified Purchase Filter**
   - All Reviews
   - Verified Purchase Only
   - Non-Verified Only

### Clear Filters
- Click **"Clear all filters"** to reset

---

## 📋 Review Table

### Columns
1. **ID**: Review ID number
2. **Product**: Product name (clickable link)
3. **Rating**: Visual star display (1-5)
4. **Review**: Title + truncated content + image badge
5. **Reviewer**: 
   - Name
   - Registered/Guest badge
   - Verified Purchase badge (if applicable)
6. **Status**: Pending/Approved/Rejected badge
7. **Helpful**: Thumbs up/down counts
8. **Date**: Creation date
9. **Actions**: View and Delete buttons

### Sorting
Click on column headers to sort:
- **ID**: Ascending/Descending
- **Rating**: Highest/Lowest first
- **Date**: Newest/Oldest first

### Pagination
- Choose items per page: 10, 15, 25, 50, 100
- Navigate with pagination controls at bottom

---

## ⚡ Quick Actions

### From Table Row (Pending Reviews Only)
1. **Approve Button** (Green)
   - Click to instantly approve
   - Review becomes visible on product page
   - Product rating updated automatically

2. **Reject Button** (Red)
   - Click to reject review
   - Review hidden from customers
   - Can be re-approved later

### View Review Details
1. Click the **eye icon** (View button)
2. Modal opens with full details:
   - Complete review text
   - All review images (if any)
   - Reviewer information
   - Product details
   - Helpful votes
   - Status timeline
3. **Quick Actions in Modal**:
   - Approve (if pending)
   - Reject (if pending)
   - Close modal

### Delete Review
1. Click the **trash icon** (Delete button)
2. Confirmation modal appears
3. Click **"Delete"** to confirm
4. Review is soft-deleted (can be restored from database if needed)

---

## 🎯 Common Tasks

### Approve Pending Reviews
1. Filter by Status: **Pending**
2. Review each item
3. Click **"Approve"** for valid reviews
4. Or click **View** → **Approve** in modal

### Reject Spam/Invalid Reviews
1. Identify suspicious reviews
2. Click **"Reject"** button
3. Review is hidden from customers

### Find Reviews for Specific Product
1. Use search bar
2. Type product name
3. All reviews for that product appear

### Check Verified Purchases
1. Click **"Filters"**
2. Select **"Verified Purchase Only"**
3. See only reviews from actual buyers

### View High/Low Ratings
1. Click **"Filters"**
2. Select rating (e.g., "1 Star" for complaints)
3. Address customer concerns

---

## 💡 Tips & Best Practices

### Moderation Guidelines
✅ **Approve reviews that:**
- Are genuine customer feedback
- Contain constructive criticism
- Include product details
- Are from verified purchases

❌ **Reject reviews that:**
- Contain spam or promotional content
- Use offensive language
- Are completely off-topic
- Include competitor mentions
- Have excessive links

### Response Time
- Try to moderate pending reviews within 24-48 hours
- Check the **Pending** count in statistics daily
- Prioritize verified purchase reviews

### Quality Control
- Read full review in modal before approving
- Check review images for appropriateness
- Verify reviewer information
- Look for patterns in spam reviews

### Customer Satisfaction
- Approved reviews build trust
- Mix of ratings (not just 5-stars) looks authentic
- Respond to negative reviews (future feature)

---

## 🔧 Troubleshooting

### Reviews Not Showing
- Check if status is "Approved"
- Verify product still exists
- Clear browser cache
- Run: `php artisan optimize:clear`

### Livewire Not Working
- Clear cache: `php artisan optimize:clear`
- Check browser console for errors
- Ensure JavaScript is enabled

### Images Not Displaying
- Check storage link: `php artisan storage:link`
- Verify images exist in `storage/app/public/reviews`
- Check file permissions

---

## 📱 Keyboard Shortcuts

- **Esc**: Close modal
- **Enter**: Submit form (when focused)
- **Tab**: Navigate between fields

---

## 🔗 Related Routes

- **All Reviews**: `/admin/reviews`
- **Pending Reviews**: `/admin/reviews/pending`
- **View Review**: `/admin/reviews/{id}`
- **Product Q&A**: `/admin/product-questions`

---

## 📞 Support

For technical issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify database connection
4. Ensure all migrations are run: `php artisan migrate`

---

**Last Updated**: November 8, 2025  
**Version**: 1.0
