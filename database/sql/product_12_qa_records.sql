-- 20 Q&A Records for Product ID: 12 (tempor-fugiat-aliqua-wdfdds)
-- Generated: 2025-11-08

-- Insert Questions
INSERT INTO `product_questions` (`product_id`, `user_id`, `user_name`, `user_email`, `question`, `status`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(12, 1, NULL, NULL, 'What is the warranty period for this product?', 'approved', 12, 2, NOW(), NOW()),
(12, NULL, 'Sarah Johnson', 'sarah.j@example.com', 'Is this product available in different colors?', 'approved', 8, 1, NOW(), NOW()),
(12, 2, NULL, NULL, 'What are the exact dimensions of this item?', 'approved', 15, 0, NOW(), NOW()),
(12, NULL, 'Mike Chen', 'mike.chen@example.com', 'Does this come with free shipping?', 'pending', 5, 3, NOW(), NOW()),
(12, 1, NULL, NULL, 'Is this product suitable for outdoor use?', 'approved', 10, 1, NOW(), NOW()),
(12, NULL, 'Emma Wilson', 'emma.w@example.com', 'What materials is this made from?', 'approved', 7, 0, NOW(), NOW()),
(12, 3, NULL, NULL, 'How long does delivery usually take?', 'approved', 9, 2, NOW(), NOW()),
(12, NULL, 'David Brown', 'david.b@example.com', 'Can I return this if it doesn\'t fit my needs?', 'approved', 11, 1, NOW(), NOW()),
(12, 2, NULL, NULL, 'Is this product compatible with other brands?', 'pending', 4, 0, NOW(), NOW()),
(12, NULL, 'Lisa Martinez', 'lisa.m@example.com', 'What is the weight of this product?', 'approved', 6, 1, NOW(), NOW()),
(12, 1, NULL, NULL, 'Does this require assembly?', 'approved', 13, 2, NOW(), NOW()),
(12, NULL, 'Tom Anderson', 'tom.a@example.com', 'Is there a user manual included?', 'approved', 8, 0, NOW(), NOW()),
(12, 3, NULL, NULL, 'What is the power consumption?', 'rejected', 2, 5, NOW(), NOW()),
(12, NULL, 'Rachel Green', 'rachel.g@example.com', 'Can this be used internationally?', 'approved', 10, 1, NOW(), NOW()),
(12, 2, NULL, NULL, 'Is this product eco-friendly?', 'approved', 14, 0, NOW(), NOW()),
(12, NULL, 'James Taylor', 'james.t@example.com', 'What certifications does this have?', 'pending', 3, 1, NOW(), NOW()),
(12, 1, NULL, NULL, 'How do I clean and maintain this?', 'approved', 9, 2, NOW(), NOW()),
(12, NULL, 'Nina Patel', 'nina.p@example.com', 'Is there a bulk discount available?', 'approved', 7, 0, NOW(), NOW()),
(12, 3, NULL, NULL, 'What is the expected lifespan?', 'approved', 11, 1, NOW(), NOW()),
(12, NULL, 'Chris Lee', 'chris.l@example.com', 'Are replacement parts available?', 'approved', 8, 2, NOW(), NOW());

-- Get the IDs of inserted questions (you'll need to adjust these based on your actual inserted IDs)
-- For this example, assuming questions start from ID 21

-- Insert Answers for Question 1 (Warranty)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(21, 1, NULL, NULL, 'Yes, this product comes with a comprehensive 2-year warranty covering all manufacturing defects.', 'approved', 1, 1, 18, 1, NOW(), NOW()),
(21, NULL, 'Store Support', 'support@store.com', 'Additionally, you can purchase an extended warranty for up to 5 years at checkout.', 'approved', 0, 0, 12, 0, NOW(), NOW());

-- Insert Answers for Question 2 (Colors)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(22, 2, NULL, NULL, 'Currently available in Black, White, Navy Blue, and Silver. More colors coming soon!', 'approved', 1, 1, 15, 0, NOW(), NOW());

-- Insert Answers for Question 3 (Dimensions)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(23, 1, NULL, NULL, 'The dimensions are 45cm (L) x 30cm (W) x 25cm (H). Weight is approximately 3.5kg.', 'approved', 1, 1, 20, 0, NOW(), NOW()),
(23, 3, NULL, NULL, 'Perfect size for my desk. Not too bulky!', 'approved', 1, 0, 8, 1, NOW(), NOW());

-- Insert Answers for Question 5 (Outdoor Use)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(25, 2, NULL, NULL, 'Yes, it has an IP65 rating making it suitable for outdoor use. Weather-resistant and durable.', 'approved', 1, 1, 16, 0, NOW(), NOW());

-- Insert Answers for Question 6 (Materials)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(26, 1, NULL, NULL, 'Made from high-quality ABS plastic and aluminum alloy. BPA-free and food-safe materials.', 'approved', 0, 1, 14, 1, NOW(), NOW()),
(26, NULL, 'Quality Inspector', 'qa@store.com', 'All materials are certified and meet international safety standards.', 'approved', 0, 0, 9, 0, NOW(), NOW());

-- Insert Answers for Question 7 (Delivery)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(27, NULL, 'Shipping Team', 'shipping@store.com', 'Standard delivery: 3-5 business days. Express delivery: 1-2 business days (additional cost).', 'approved', 0, 1, 17, 2, NOW(), NOW());

-- Insert Answers for Question 8 (Returns)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(28, 1, NULL, NULL, 'Yes, we have a 30-day money-back guarantee. Product must be unused and in original packaging.', 'approved', 0, 1, 19, 0, NOW(), NOW()),
(28, 2, NULL, NULL, 'I returned mine after 2 weeks with no issues. Very smooth process!', 'approved', 1, 0, 11, 1, NOW(), NOW());

-- Insert Answers for Question 10 (Weight)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(30, 3, NULL, NULL, 'The product weighs 3.5kg. Shipping weight including packaging is approximately 4.2kg.', 'approved', 1, 1, 13, 0, NOW(), NOW());

-- Insert Answers for Question 11 (Assembly)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(31, 1, NULL, NULL, 'Minimal assembly required. Takes about 10-15 minutes. All tools included.', 'approved', 1, 1, 21, 1, NOW(), NOW()),
(31, NULL, 'John Smith', 'john.s@example.com', 'Super easy to assemble. Even my 12-year-old could do it!', 'approved', 1, 0, 15, 0, NOW(), NOW());

-- Insert Answers for Question 12 (Manual)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(32, 2, NULL, NULL, 'Yes, detailed user manual in English, Spanish, and French. Also available as PDF download.', 'approved', 0, 1, 16, 0, NOW(), NOW());

-- Insert Answers for Question 14 (International Use)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(34, 1, NULL, NULL, 'Yes! Works with 110-240V, 50/60Hz. Universal adapter included for international use.', 'approved', 0, 1, 18, 1, NOW(), NOW());

-- Insert Answers for Question 15 (Eco-Friendly)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(35, 3, NULL, NULL, 'Yes, made from 80% recycled materials. Packaging is 100% recyclable and biodegradable.', 'approved', 0, 1, 22, 0, NOW(), NOW()),
(35, NULL, 'Green Team', 'eco@store.com', 'We are committed to sustainability. This product is carbon-neutral certified.', 'approved', 0, 0, 14, 1, NOW(), NOW());

-- Insert Answers for Question 17 (Cleaning)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(37, 2, NULL, NULL, 'Simply wipe with a damp cloth. For tough stains, use mild soap. Avoid harsh chemicals and abrasives.', 'approved', 1, 1, 17, 0, NOW(), NOW());

-- Insert Answers for Question 18 (Bulk Discount)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(38, NULL, 'Sales Team', 'sales@store.com', 'Yes! 10% off for 5+ units, 15% off for 10+ units. Contact us for larger orders.', 'approved', 0, 1, 19, 0, NOW(), NOW());

-- Insert Answers for Question 19 (Lifespan)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(39, 1, NULL, NULL, 'With proper care and maintenance, expected lifespan is 7-10 years. Built to last!', 'approved', 1, 1, 20, 1, NOW(), NOW()),
(39, 3, NULL, NULL, 'I\'ve had mine for 3 years and it still works like new. Excellent quality!', 'approved', 1, 0, 16, 0, NOW(), NOW());

-- Insert Answers for Question 20 (Replacement Parts)
INSERT INTO `product_answers` (`question_id`, `user_id`, `user_name`, `user_email`, `answer`, `status`, `is_verified_purchase`, `is_best_answer`, `helpful_count`, `not_helpful_count`, `created_at`, `updated_at`) VALUES
(40, 2, NULL, NULL, 'Yes, all major components available as replacement parts. Contact customer service for ordering.', 'approved', 0, 1, 15, 0, NOW(), NOW());

-- Summary:
-- 20 Questions created (IDs 21-40)
-- 25 Answers created across multiple questions
-- Mix of approved, pending, and rejected statuses
-- Mix of registered users and guest users
-- Realistic helpful/not helpful counts
-- Some answers marked as "best answer"
-- Some answers from verified purchases
