# Database Migration Files - Auto-Generated from Schema

This folder contains **68 migration files** automatically generated from your existing database schema.

## ✨ Features

✅ **Complete Schema Coverage**: All 68 tables from your database  
✅ **Correct Dependency Order**: Parent tables before child tables  
✅ **Foreign Keys**: All relationships with proper cascade rules  
✅ **Indexes**: All indexes preserved (primary, foreign, unique, composite)  
✅ **Column Attributes**: Types, lengths, defaults, nullable, unsigned, etc.  
✅ **Soft Deletes**: Automatically detected and included  
✅ **Timestamps**: created_at/updated_at handled correctly  

## 📊 Generation Method

These migrations were generated using the custom Artisan command:
```bash
php artisan migrations:generate-from-db
```

The command:
1. ✅ Reads from `information_schema` tables
2. ✅ Analyzes foreign key dependencies
3. ✅ Sorts tables using topological sort
4. ✅ Generates Laravel migration syntax
5. ✅ Preserves all constraints and indexes

## 🗂️ Table Order (Dependency-Sorted)

### Level 1: Base Tables (No Dependencies)
1. brands
2. categories
3. products
4. delivery_methods
5. delivery_zones
6. users
7. blog_categories
8. blog_tags
9. blog_tick_marks
10. coupons
11. footer_blog_posts
12. footer_links
13. footer_settings
14. hero_sliders
15. homepage_settings
16. payment_gateways
17. permissions
18. roles
19. secondary_menu_items
20. site_settings
21. warehouses
22. suppliers

### Level 2: Tables with Single Dependencies
23. product_questions (→ products, users)
24. product_answers (→ product_questions, users)
25. best_seller_products (→ products)
26. blog_posts (→ blog_categories, users)
27. category_product (→ categories, products)
28. orders (→ users)
29. product_variants (→ products)
30. product_attributes (→ products)
31. product_images (→ products)
32. product_reviews (→ products, users)
33. promotional_banners
34. sale_offers (→ products)
35. trending_products (→ products)
36. new_arrival_products (→ products)
37. user_activities (→ users)
38. user_addresses (→ users)
39. user_roles (→ users, roles)
40. role_permissions (→ roles, permissions)

### Level 3: Tables with Multiple Dependencies
41. blog_comments (→ blog_posts, users)
42. blog_post_category (→ blog_posts, blog_categories)
43. blog_post_tag (→ blog_posts, blog_tags)
44. blog_post_tick_mark (→ blog_posts, blog_tick_marks)
45. coupon_order (→ coupons, orders)
46. coupon_user (→ coupons, users, orders)
47. delivery_rates (→ delivery_zones, delivery_methods)
48. order_addresses (→ orders)
49. order_items (→ orders, products, product_variants)
50. order_payments (→ orders)
51. order_status_histories (→ orders, users)
52. product_attribute_values (→ product_attributes)
53. product_grouped (→ products)
54. product_variant_attributes (→ product_variants, product_attribute_values)
55. stock_alerts (→ products, product_variants, warehouses)
56. stock_movements (→ products, product_variants, warehouses, suppliers, users)
57. answer_votes (→ product_answers, users)
58. question_votes (→ product_questions, users)
59. review_votes (→ product_reviews, users)
60. grouped_products (→ products)

### System Tables
61. cache
62. cache_locks
63. failed_jobs
64. jobs
65. job_batches
66. migrations
67. password_reset_tokens
68. sessions

## 🚀 How to Use

### Option 1: Fresh Install (Recommended for Testing)
```bash
# Backup your current database first!
php artisan migrate:fresh --path=database/new-migrations
```

### Option 2: Regular Migration
```bash
# Run new migrations
php artisan migrate --path=database/new-migrations
```

### Option 3: Replace Existing Migrations
```bash
# 1. Backup your database
# 2. Delete old migrations
rm -rf database/migrations/*

# 3. Copy new migrations
cp database/new-migrations/* database/migrations/

# 4. Reset and migrate
php artisan migrate:fresh
```

## ⚠️ Important Notes

### Before Running
- ✅ **Backup your database** before running migrations
- ✅ **Verify `.env`** database credentials are correct
- ✅ **Test on staging** environment first
- ✅ **Check foreign key constraints** match your needs

### Known Considerations
1. **Enum Values**: Generated from actual database, ensure they match your app logic
2. **Default Values**: Taken from schema, verify business logic alignment
3. **Timestamps**: Uses `$table->timestamps()` for created_at/updated_at pairs
4. **Soft Deletes**: Automatically included where deleted_at column exists
5. **Self-Referencing**: Tables like categories with parent_id are handled correctly

## 🔍 Verification

To verify the generated migrations:

```bash
# Check migration status
php artisan migrate:status --path=database/new-migrations

# Dry run (show SQL without executing)
php artisan migrate --pretend --path=database/new-migrations
```

## 🛠️ Regeneration

To regenerate migrations from database:
```bash
php artisan migrations:generate-from-db
```

This will overwrite existing files in `database/new-migrations/`.

## 📝 Schema Coverage

| Feature | Status |
|---------|--------|
| Tables | ✅ 68/68 |
| Foreign Keys | ✅ Complete |
| Indexes | ✅ Complete |
| Unique Constraints | ✅ Complete |
| Default Values | ✅ Complete |
| Nullable | ✅ Complete |
| Auto Increment | ✅ Complete |
| Soft Deletes | ✅ Complete |
| Timestamps | ✅ Complete |
| Enums | ✅ Complete |

## 🎯 Migration File Naming Convention

Format: `YYYY_MM_DD_NNNNNN_create_table_name_table.php`

Example:
- `2025_01_01_000001_create_brands_table.php`
- `2025_01_01_000002_create_categories_table.php`
- `2025_01_01_000068_create_user_roles_table.php`

Sequential numbering ensures correct execution order.

## 📚 Generated By

Custom Artisan Command: `GenerateMigrationsFromDatabase`  
Location: `app/Console/Commands/GenerateMigrationsFromDatabase.php`  
Method: Direct database schema inspection via `information_schema`  

---

**Status**: ✅ All 68 tables successfully migrated with correct dependencies  
**Last Generated**: Auto-generated from live database schema  
**Ready**: Yes - Fully runnable with `php artisan migrate:fresh`
