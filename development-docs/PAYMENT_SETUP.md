# Payment Gateway Setup Guide

## Overview
This system supports multiple payment gateways for online payments. Initially configured for:
- **bKash** (Fully Implemented)
- **Nagad** (Ready for implementation)
- **SSL Commerz** (Ready for implementation)

## Installation Steps

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Payment Gateways
```bash
php artisan db:seed --class=PaymentGatewaySeeder
```

This will create three payment gateway records in the database (all initially inactive).

## Admin Configuration

### Access Payment Gateways
1. Login to admin panel
2. Navigate to **Payments > Payment Gateways** in the sidebar
3. You'll see all available payment gateways

### Configure bKash

#### Get Credentials
1. Register as bKash merchant at: https://merchant.bka.sh
2. Get your credentials:
   - App Key
   - App Secret
   - Username
   - Password

#### Configure in Admin
1. Click "Configure" on bKash gateway
2. Fill in the credentials
3. Set Base URL:
   - **Test Mode**: `https://tokenized.sandbox.bka.sh/v1.2.0-beta`
   - **Live Mode**: `https://tokenized.pay.bka.sh/v1.2.0-beta`
4. Check "Test Mode" for testing
5. Check "Active" to enable for customers
6. Click "Save Configuration"

### Configure Nagad (When Ready)
1. Get merchant credentials from Nagad
2. Click "Configure" on Nagad gateway
3. Fill in:
   - Merchant ID
   - Merchant Number
   - Public Key
   - Private Key
   - Base URL
4. Save configuration

### Configure SSL Commerz (When Ready)
1. Register at: https://sslcommerz.com
2. Get Store ID and Store Password
3. Configure in admin panel
4. Save configuration

## Testing Payment Flow

### Test Mode
1. Ensure gateway is in "Test Mode"
2. Place a test order
3. On order details page, click payment gateway
4. Use test credentials provided by payment gateway
5. Complete test payment
6. Verify order status updates to "Paid"

### bKash Test Credentials
Check bKash merchant portal for sandbox test numbers and OTP codes.

## Going Live

### Checklist
- ✅ All payments tested successfully in test mode
- ✅ Update Base URL to production URL
- ✅ Uncheck "Test Mode"
- ✅ Verify credentials are for production
- ✅ Test one real transaction
- ✅ Monitor payment logs

### Switch to Live Mode
1. Go to Payment Gateway configuration
2. Update Base URL to production
3. Uncheck "Test Mode"
4. Save configuration

## Customer Experience

### Payment Flow
1. Customer places order (COD or Online)
2. If payment not completed:
   - View order details
   - See "Pay Now" section
   - Choose payment gateway (bKash, Nagad, etc.)
3. Redirected to payment gateway
4. Complete payment
5. Redirected back to site
6. Order status updated automatically

### Order Details Page
- Shows payment status (Pending/Paid)
- Payment method used
- Transaction ID (if paid)
- "Pay Now" button (if unpaid)
- Available payment gateways

## Technical Details

### Database Tables
- `payment_gateways`: Stores gateway configurations
- `orders`: Added columns:
  - `payment_gateway_id`: Links to payment gateway
  - `transaction_id`: Stores transaction reference

### Routes
- `/payment/initiate/{order}`: Initiate payment
- `/payment/bkash/callback`: bKash payment callback
- `/payment/nagad/callback`: Nagad payment callback
- `/payment/sslcommerz/success`: SSL Commerz success
- `/payment/sslcommerz/fail`: SSL Commerz failure
- `/payment/sslcommerz/cancel`: SSL Commerz cancel

### Admin Routes
- `/admin/payment-gateways`: List gateways
- `/admin/payment-gateways/{id}/edit`: Configure gateway
- `/admin/payment-gateways/{id}/toggle`: Toggle active status

## Troubleshooting

### Payment Not Working
1. Check gateway is "Active"
2. Verify credentials are correct
3. Check Base URL matches mode (test/live)
4. Review Laravel logs: `storage/logs/laravel.log`
5. Verify callback URLs are accessible

### Order Not Updating
1. Check payment gateway callback is reaching server
2. Verify transaction ID in database
3. Check logs for errors
4. Ensure order exists before payment

### Test Mode Not Working
1. Verify using test credentials
2. Check test Base URL is correct
3. Use test phone numbers provided by gateway

## Security Notes

- ✅ Credentials stored encrypted in database (JSON)
- ✅ Never expose credentials in frontend
- ✅ Use HTTPS in production
- ✅ Validate all callback data
- ✅ Log all payment transactions
- ✅ Regular security audits recommended

## Support

### bKash Support
- Merchant Portal: https://merchant.bka.sh
- Documentation: Check merchant portal
- Support: Contact through merchant portal

### For Code Issues
Check Laravel logs and payment service logs for detailed error messages.

## Future Enhancements

### Planned Features
- [ ] Complete Nagad integration
- [ ] Complete SSL Commerz integration
- [ ] Payment refund system
- [ ] Payment analytics dashboard
- [ ] Email notifications for payments
- [ ] SMS notifications for payments
- [ ] Webhook support
- [ ] Payment retry mechanism

---

**Version**: 1.0  
**Last Updated**: November 2025  
**Status**: Production Ready (bKash)
