# Organization Booking and Payment Management System

## Overview
This document outlines the complete implementation of the organization-side booking and payment management system, where organizations can view bookings from hikers, manage payment credentials, and receive payments through PayMongo and Xendit.

## Features Implemented

### 1. **Organization Bookings Management**
Organizations can now view and manage all bookings made by hikers for their trails.

#### Enhanced Booking List (`/org/bookings`)
- **Statistics Dashboard**: 
  - Total Bookings count
  - Total Revenue (from paid bookings)
  - Paid Bookings count
  - Pending Bookings count
  
- **Booking Table Columns**:
  - Booking ID
  - Hiker Information (name and email)
  - Trail name
  - Booking date
  - Party size
  - Amount (in pesos)
  - Payment Status (Paid, Pending, Failed, No Payment)
  - Booking Status (Pending, Confirmed, Cancelled, Completed)
  - Actions (View details)

#### Booking Details Page (`/org/bookings/{id}`)
- Full booking information including:
  - Hiker details (name, email)
  - Trail name
  - Booking date and party size
  - Notes from hiker
  
- **Payment Information Section**:
  - Total amount
  - Payment status with color-coded badges
  - Payment timestamp (when paid)
  - Payment ID from payment gateway
  
- **Status Management**:
  - Update booking status (Pending, Confirmed, Cancelled, Completed)

### 2. **Payment Setup and Credential Management**
Organizations can configure their payment gateway credentials to receive payments.

#### Payment Setup Page (`/org/payment`)
- **Overview Dashboard**:
  - PayMongo configuration status
  - Xendit configuration status
  - Active payment gateway indicator

- **PayMongo Configuration**:
  - Secret Key field (encrypted storage)
  - Public Key field (encrypted storage)
  - Test connection button
  - Clear credentials button
  - Real-time configuration status

- **Xendit Configuration**:
  - API Key field (for future use)
  - Currently using hardcoded credentials (as requested)
  - Note indicating hardcoded implementation

- **Active Gateway Selection**:
  - Choose between PayMongo and Xendit
  - Saves preference for payment processing

- **Help Section**:
  - Step-by-step guide to obtain PayMongo API keys
  - Information about Xendit integration

#### Security Features
- All API keys and secrets are **encrypted** using Laravel's Crypt facade
- Credentials stored securely in `organization_payment_credentials` table
- Only organization users can access payment setup
- Proper middleware protection (auth, approval, organization type)

## Technical Implementation

### Database Schema

#### `organization_payment_credentials` Table
```php
- id (primary key)
- user_id (foreign key to users table)
- paymongo_secret_key (encrypted text)
- paymongo_public_key (encrypted text)
- xendit_api_key (encrypted text)
- active_gateway (enum: 'paymongo', 'xendit')
- is_active (boolean)
- timestamps
```

### Models

#### `OrganizationPaymentCredential` Model
- Automatic encryption/decryption of API keys
- Helper methods:
  - `hasPaymongoConfigured()` - Check if PayMongo is set up
  - `hasXenditConfigured()` - Check if Xendit is set up
  - `hasAnyGatewayConfigured()` - Check if any gateway is configured
- Relationship to User model

### Controllers

#### `OrganizationBookingController` (Enhanced)
- `index()` - List all bookings with payment data and statistics
- `show()` - Show booking details with payment information
- `updateStatus()` - Update booking status

#### `OrganizationPaymentController` (New)
- `index()` - Display payment setup page
- `update()` - Save/update payment credentials
- `test()` - Test payment gateway connection
- `clear()` - Remove payment credentials for specific gateway

### Routes

All routes are protected with middleware:
- `auth:sanctum` - Requires authentication
- `check.approval` - Organization must be approved
- `user.type:organization` - Only for organization users

#### Payment Routes
```php
GET    /org/payment           - Payment setup page
PUT    /org/payment           - Update credentials
POST   /org/payment/test      - Test gateway connection
DELETE /org/payment/clear     - Clear credentials
```

#### Booking Routes (Existing, Enhanced)
```php
GET    /org/bookings                 - List all bookings
GET    /org/bookings/{id}            - View booking details
PATCH  /org/bookings/{id}/status     - Update booking status
```

### Views

#### `resources/views/org/bookings/index.blade.php` (Enhanced)
- Added revenue statistics
- Added payment status column
- Added amount column
- Improved visual indicators

#### `resources/views/org/bookings/show.blade.php` (Enhanced)
- Added payment information section
- Shows payment status, amount, payment ID
- Shows payment timestamp when paid

#### `resources/views/org/payment/index.blade.php` (New)
- Complete payment setup interface
- Status overview cards
- PayMongo configuration form
- Xendit configuration form (hardcoded note)
- Test connection functionality
- Clear credentials functionality
- Help section with instructions

### Navigation Menu

#### Desktop Menu (Dropdown)
Added "Payment Setup" link under "Account Management" section:
- Profile
- Edit Profile
- Account Settings
- **Payment Setup** (organizations only)

#### Mobile Menu (Responsive)
Same structure as desktop, added in responsive navigation section

## User Flow

### For Organizations:

1. **View Bookings**:
   - Navigate to "Bookings" from main menu
   - See dashboard with total bookings, revenue, paid bookings
   - View list of all bookings from hikers
   - Click "View" to see detailed booking information including payment status

2. **Manage Booking**:
   - Open booking details
   - Review hiker information and booking details
   - Check payment status and amount
   - Update booking status (confirm, cancel, complete)

3. **Setup Payment Gateway**:
   - Click profile dropdown → "Payment Setup"
   - View current configuration status
   - Choose active gateway (PayMongo or Xendit)
   - Enter PayMongo Secret and Public keys
   - Save configuration (credentials are encrypted)
   - Test connection to verify credentials
   - Clear credentials if needed

4. **Receive Payments**:
   - Once configured, the system will use organization's credentials
   - Payments from hikers go directly to organization's account
   - Organization can track all payments in bookings list

## Security Considerations

1. **Encryption**: All API keys are encrypted using Laravel's encryption
2. **Access Control**: Only organization users can access payment setup
3. **Approval Check**: Organizations must be approved to access these features
4. **Middleware Protection**: Multiple middleware layers for security
5. **Password Fields**: Sensitive fields use password input type
6. **Clear Functionality**: Allows safe removal of credentials

## Payment Gateway Integration

### PayMongo
- Full configuration support
- Organizations provide their own API keys
- Test and Live mode supported
- Connection testing available

### Xendit
- Currently hardcoded (as requested)
- Configuration UI prepared for future enhancement
- Can be activated by entering API key in the future

## Testing Checklist

- [x] Migration runs successfully
- [x] Organization can access bookings list
- [x] Bookings show payment information
- [x] Revenue statistics calculate correctly
- [x] Organization can view booking details with payment info
- [x] Organization can update booking status
- [x] Payment Setup link visible only for organizations
- [x] Payment setup page loads correctly
- [x] Can save PayMongo credentials
- [x] Credentials are encrypted in database
- [x] Can test PayMongo connection
- [x] Can clear PayMongo credentials
- [x] Active gateway selection works
- [x] Xendit shows hardcoded notice
- [x] Navigation menu shows only for organizations

## Files Created/Modified

### Created Files:
1. `database/migrations/2025_10_03_000000_create_organization_payment_credentials_table.php`
2. `app/Models/OrganizationPaymentCredential.php`
3. `app/Http/Controllers/OrganizationPaymentController.php`
4. `resources/views/org/payment/index.blade.php`

### Modified Files:
1. `app/Http/Controllers/OrganizationBookingController.php`
2. `resources/views/org/bookings/index.blade.php`
3. `resources/views/org/bookings/show.blade.php`
4. `resources/views/navigation-menu.blade.php`
5. `routes/web.php`
6. `app/Models/User.php`

## Future Enhancements

1. **Xendit Full Integration**: Replace hardcoded credentials with dynamic configuration
2. **Payment Analytics**: Add revenue charts and analytics dashboard
3. **Payout Management**: Track and manage payouts to organizations
4. **Multi-Gateway Support**: Allow multiple payment gateways simultaneously
5. **Webhook Management**: Configure webhooks for payment notifications
6. **Refund Management**: Handle refunds from organization interface
7. **Export Functionality**: Export booking and payment reports
8. **Email Notifications**: Notify organizations of new bookings/payments

## Notes

- The system is designed to be scalable and secure
- All sensitive data is encrypted at rest
- The payment credentials are per-organization (each org has their own)
- The current implementation focuses on PayMongo with Xendit hardcoded
- Organizations receive payments directly to their configured accounts
- The booking status and payment status are tracked independently

## Support

For questions or issues:
1. Check the help section in Payment Setup page
2. Review PayMongo documentation: https://developers.paymongo.com/
3. Ensure your organization account is approved
4. Verify all credentials are entered correctly
5. Test connection after configuring credentials
