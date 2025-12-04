# Coupon System - Quick Reference Guide

## 🚀 Quick Access URLs

| Page | URL | Access Level |
|------|-----|--------------|
| Admin Coupons List | `/admin/coupons` | Admin Only |
| Create Coupon | `/admin/coupons/create` | Admin Only |
| Edit Coupon | `/admin/coupons/{id}/edit` | Admin Only |
| Coupon Statistics | `/admin/coupons/{id}/statistics` | Admin Only |
| Public Coupons | `/coupons` | Everyone |

---

## 📝 Common Tasks

### Create a New Coupon

```
1. Navigate to /admin/coupons
2. Click "Create Coupon"
3. Enter code (or click "Generate")
4. Set discount type and value
5. Configure options
6. Click "Create Coupon"
```

### Apply Coupon (Customer)

```
1. Add items to cart
2. Go to cart page
3. Find "Discount Code" section
4. Enter coupon code
5. Click "Apply"
```

### View Coupon Statistics

```
1. Go to /admin/coupons
2. Find the coupon
3. Click purple statistics icon
4. View metrics and usage history
```

---

## 🎯 Coupon Configuration Options

### Discount Types
- **Percentage**: `10%`, `25%`, `50%`
- **Fixed Amount**: `৳100`, `৳500`, `৳1000`

### Restrictions
- **Min Purchase**: Minimum cart value required
- **Max Discount**: Cap on percentage discounts
- **Usage Limit**: Total times coupon can be used
- **Per User Limit**: Times each user can use it
- **Start Date**: When coupon becomes active
- **End Date**: When coupon expires
- **First Order Only**: New customers only
- **Free Shipping**: Waive shipping costs
- **Categories**: Limit to specific categories
- **Products**: Limit to specific products

---

## 🔍 Search & Filter

### Admin Panel Filters
- **Search**: By code, name, or description
- **Status**: Active, Inactive, Expired, Upcoming
- **Type**: Percentage or Fixed
- **Sort**: By any column (click header)

---

## 📊 Statistics Metrics

| Metric | Description |
|--------|-------------|
| Total Uses | How many times coupon was used |
| Unique Users | Number of different customers |
| Total Discount | Sum of all discounts given |
| Usage Rate | Percentage of limit used |
| Remaining Uses | How many uses left |

---

## ⚠️ Common Validation Errors

| Error | Cause | Solution |
|-------|-------|----------|
| "Invalid coupon code" | Code doesn't exist | Check spelling |
| "Coupon no longer valid" | Expired or inactive | Use active coupon |
| "Minimum purchase required" | Cart total too low | Add more items |
| "Usage limit reached" | Coupon fully used | Try different coupon |
| "First order only" | Not first order | Use different coupon |
| "Not applicable to cart" | Product restrictions | Check restrictions |

---

## 🎨 Coupon Code Examples

### Good Codes
- `SUMMER2024` - Seasonal
- `WELCOME10` - Welcome offer
- `SAVE20` - Simple savings
- `FREESHIP` - Free shipping
- `FLASH50` - Flash sale

### Auto-Generated
- `AB12CD34` - 8 characters
- `XY56ZW78` - Random

---

## 💡 Best Practices

### Creating Coupons
1. Use clear, memorable codes
2. Set reasonable expiry dates
3. Configure usage limits
4. Test before publishing
5. Monitor performance

### Managing Coupons
1. Deactivate expired coupons
2. Review statistics regularly
3. Remove unused coupons
4. Update descriptions
5. Track ROI

### Customer Experience
1. Make coupons easy to find
2. Clear terms and conditions
3. Visible in cart
4. Show savings amount
5. Confirm application

---

## 🔧 Troubleshooting

### Coupon Not Applying
1. Check if coupon is active
2. Verify expiry date
3. Check minimum purchase
4. Verify product/category restrictions
5. Check usage limits

### Statistics Not Showing
1. Ensure coupon has been used
2. Check database connection
3. Clear cache
4. Refresh page

### Frontend Display Issues
1. Clear browser cache
2. Check route configuration
3. Verify CouponService
4. Check view compilation

---

## 📱 Mobile Considerations

- Responsive design implemented
- Touch-friendly buttons
- Easy code copying
- Readable card layouts
- Optimized for small screens

---

## 🔐 Security Notes

- Codes are case-insensitive
- Server-side validation
- CSRF protection enabled
- Admin-only management
- Usage tracking active
- Audit trail maintained

---

## 📈 Performance Tips

1. **Cache Active Coupons**:
   ```php
   Cache::remember('active_coupons', 3600, function() {
       return $couponService->getActiveCoupons();
   });
   ```

2. **Limit Database Queries**:
   - Use eager loading
   - Cache frequently accessed data
   - Index coupon codes

3. **Optimize Frontend**:
   - Lazy load coupon images
   - Minimize API calls
   - Use Alpine.js efficiently

---

## 🎓 Training Resources

### For Admins
- Creating effective coupons
- Monitoring performance
- Managing restrictions
- Analyzing statistics

### For Support Staff
- Helping customers apply coupons
- Troubleshooting errors
- Explaining restrictions
- Checking coupon validity

---

## 📞 Quick Support

### Common Questions

**Q: How do I create a percentage discount?**  
A: Select "Percentage (%)" as type, enter value like 10 for 10%

**Q: Can I limit coupon to specific products?**  
A: Yes, use "Applicable Products" or "Excluded Products" fields

**Q: How do I make a coupon for new customers only?**  
A: Enable "First Order Only" toggle

**Q: Can I see who used a coupon?**  
A: Yes, go to Statistics page and check "Recent Usage History"

**Q: How do I deactivate a coupon?**  
A: Toggle the status switch in coupon list or edit page

---

## 🔄 Integration Points

### Cart System
- `CouponApplier` Livewire component
- Session-based storage
- Real-time validation
- Discount calculation

### Checkout System
- Discount display
- Free shipping handling
- Order total calculation
- Coupon code storage

### Order System
- Usage recording
- Pivot table tracking
- User relationship
- Discount amount storage

---

## 📋 Checklist for Launch

- [ ] Create initial coupons
- [ ] Test all coupon types
- [ ] Verify restrictions work
- [ ] Check mobile display
- [ ] Train support staff
- [ ] Update help documentation
- [ ] Monitor first week usage
- [ ] Gather customer feedback

---

**Version**: 2.0.0  
**Last Updated**: November 11, 2024  
**Status**: Production Ready ✅
