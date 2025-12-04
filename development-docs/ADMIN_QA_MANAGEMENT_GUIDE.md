# Admin Product Q&A Management Guide

## Overview
Complete guide for managing product questions and answers from the admin panel.

---

## 🎯 Access Admin Panel

### URL
```
http://localhost:8000/admin/product-questions
```

### Requirements
- Must be logged in as admin
- Role: `admin`

---

## 📊 Admin Q&A Management Features

### 1. Questions List Page
**URL:** `/admin/product-questions`

**Features:**
- ✅ View all questions (pending by default)
- ✅ Search questions by text
- ✅ Filter by status (Pending, Approved, Rejected)
- ✅ See question details (product, author, date)
- ✅ View answer count
- ✅ See helpful/not helpful votes
- ✅ Quick actions (Approve, Reject, View, Delete)
- ✅ Pagination (15 per page)

**Filters:**
- **Search:** Search questions by text
- **Status:** Filter by Pending/Approved/Rejected
- **Default:** Shows pending questions first

---

### 2. Question Details Page
**URL:** `/admin/product-questions/{id}`

**Features:**
- ✅ View complete question details
- ✅ See all answers (approved, pending, rejected)
- ✅ Approve/Reject questions
- ✅ Approve/Reject answers
- ✅ Mark best answer
- ✅ View question stats
- ✅ Delete question
- ✅ Link to view on frontend

**Question Information:**
- Question text
- Asked by (name, user type)
- Date posted
- Product link
- Helpful/Not helpful votes
- Status badge

**Answer Information:**
- Answer text
- Answered by
- Verified purchase badge
- Best answer badge
- Status (Pending/Approved/Rejected)
- Helpful votes
- Quick actions

---

## 🔧 Admin Actions

### Question Actions

#### 1. Approve Question
- **Button:** Green checkmark
- **Action:** Changes status to "approved"
- **Effect:** Question becomes visible on frontend
- **Location:** List page or details page

#### 2. Reject Question
- **Button:** Yellow X
- **Action:** Changes status to "rejected"
- **Effect:** Question hidden from frontend
- **Location:** List page or details page

#### 3. Delete Question
- **Button:** Red trash icon
- **Action:** Soft deletes question and all answers
- **Confirmation:** Yes/No prompt
- **Effect:** Permanently removes from display
- **Location:** List page or details page

#### 4. View Details
- **Button:** Blue eye icon
- **Action:** Opens question details page
- **Location:** List page

---

### Answer Actions

#### 1. Approve Answer
- **Button:** Green "Approve" button
- **Action:** Changes answer status to "approved"
- **Effect:** Answer becomes visible on frontend
- **Location:** Question details page

#### 2. Reject Answer
- **Button:** Yellow "Reject" button
- **Action:** Changes answer status to "rejected"
- **Effect:** Answer hidden from frontend
- **Location:** Question details page

#### 3. Mark as Best Answer
- **Button:** Blue "Mark as Best" button
- **Action:** Sets answer as best answer
- **Effect:** Shows "Best Answer" badge on frontend
- **Note:** Only one best answer per question
- **Location:** Question details page

---

## 📋 Workflow Examples

### Moderate New Questions

1. **Go to:** `/admin/product-questions`
2. **Default view:** Shows pending questions
3. **Review question:**
   - Read question text
   - Check product relevance
   - Verify not spam
4. **Take action:**
   - Click ✅ to approve
   - Click ❌ to reject
   - Click 🗑️ to delete
5. **Result:** Question status updated

### Moderate Answers

1. **Go to:** Question details page
2. **Scroll to:** Answers section
3. **Review each answer:**
   - Read answer text
   - Check if helpful
   - Verify not spam
4. **Take action:**
   - Click "Approve" for good answers
   - Click "Reject" for bad answers
   - Click "Mark as Best" for best answer
5. **Result:** Answer status updated

### Search & Filter

1. **Search by text:**
   - Enter keywords in search box
   - Click "Filter"
   - View matching questions

2. **Filter by status:**
   - Select status from dropdown
   - Click "Filter"
   - View filtered results

3. **Reset filters:**
   - Click "Reset" button
   - Returns to default view

---

## 🎨 UI Features

### Status Badges
- **Approved:** Green badge
- **Pending:** Yellow badge
- **Rejected:** Red badge

### Special Badges
- **Verified Purchase:** Green checkmark (answers only)
- **Best Answer:** Blue star (answers only)

### Icons
- ✅ Approve (green)
- ❌ Reject (yellow)
- 👁️ View (blue)
- 🗑️ Delete (red)
- 👍 Helpful votes
- 👎 Not helpful votes

---

## 📊 Quick Stats (Details Page)

### Question Stats
- Total Answers
- Approved Answers
- Pending Answers

### Helpful Votes
- Thumbs up count
- Thumbs down count

---

## 🔗 Routes Reference

### Admin Routes
```php
// List all questions
GET /admin/product-questions

// View question details
GET /admin/product-questions/{id}

// Approve question
POST /admin/questions/{id}/approve

// Reject question
POST /admin/questions/{id}/reject

// Delete question
DELETE /admin/product-questions/{id}

// Approve answer
POST /admin/answers/{id}/approve

// Reject answer
POST /admin/answers/{id}/reject

// Mark best answer
POST /admin/answers/{id}/best
```

---

## 💡 Best Practices

### Question Moderation
1. ✅ Approve relevant product questions
2. ✅ Reject spam or inappropriate content
3. ✅ Delete duplicate questions
4. ✅ Check question quality before approving

### Answer Moderation
1. ✅ Approve helpful, accurate answers
2. ✅ Reject promotional content
3. ✅ Mark best answer for each question
4. ✅ Prioritize verified purchase answers

### Response Time
- ⏱️ Review pending questions daily
- ⏱️ Approve good content within 24 hours
- ⏱️ Respond to spam immediately

---

## 🚨 Spam Detection

### Auto-Detection (Backend)
The system automatically detects:
- Spam keywords (viagra, casino, etc.)
- Excessive links (>2 links)
- Rate limiting (5 questions/day per user)

### Manual Review
Look for:
- Irrelevant questions
- Promotional content
- Duplicate questions
- Offensive language
- Fake reviews

**Action:** Reject or delete spam content

---

## 📱 Responsive Design

The admin panel is fully responsive:
- ✅ Desktop (full features)
- ✅ Tablet (optimized layout)
- ✅ Mobile (touch-friendly)

---

## 🔐 Security

### Access Control
- Only admin users can access
- Middleware: `auth`, `role:admin`
- Protected routes

### Data Protection
- CSRF protection on all forms
- XSS protection (sanitized input)
- SQL injection protection (Eloquent)

---

## 📈 Performance

### Optimization
- ✅ Eager loading relationships
- ✅ Pagination (15 per page)
- ✅ Database indexes
- ✅ Efficient queries

---

## 🎯 Quick Access

### From Admin Dashboard
Add a menu item:
```html
<a href="{{ route('admin.product-questions.index') }}">
    <i class="fas fa-question-circle"></i> Product Q&A
</a>
```

### Pending Count Badge
Show pending count:
```php
$pendingCount = \App\Modules\Ecommerce\Product\Models\ProductQuestion::where('status', 'pending')->count();
```

---

## 📝 Summary

### What You Can Do
1. ✅ View all product questions
2. ✅ Search and filter questions
3. ✅ Approve/Reject questions
4. ✅ View question details
5. ✅ Moderate answers
6. ✅ Mark best answers
7. ✅ Delete spam content
8. ✅ View helpful votes
9. ✅ Track answer counts
10. ✅ Link to frontend view

### Access Points
- **Main List:** `/admin/product-questions`
- **Question Details:** `/admin/product-questions/{id}`
- **Quick Actions:** Available on both pages

---

**Last Updated:** November 8, 2025  
**Status:** ✅ Fully Functional  
**Version:** 1.0
