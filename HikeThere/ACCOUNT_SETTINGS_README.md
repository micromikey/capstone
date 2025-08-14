# Account Settings Feature

## Overview

The Account Settings feature provides a comprehensive interface for both hikers and organizations to manage their account security, preferences, and settings. This feature consolidates all account management functionality into a single, user-friendly interface.

## Features

### Security Settings
- **Password Management**: Update account passwords with secure validation
- **Two-Factor Authentication**: Enable/disable 2FA with QR code setup and recovery codes
- **Browser Sessions**: Manage and log out other browser sessions across devices

### Additional Security
- **Login History**: View recent login attempts and account activity
- **Security Questions**: Set up additional account recovery options

### Account Management
- **Email Verification**: Verify email addresses and resend verification emails
- **API Tokens**: Manage API tokens for programmatic access
- **Notification Preferences**: Configure email and push notification settings
- **Privacy Settings**: Control profile visibility and data sharing preferences

### Organization-Specific Features
- **Approval Status**: View organization approval status (approved, pending, rejected)
- **Organization Dashboard**: Quick access to organization management

### Danger Zone
- **Account Deletion**: Permanently delete accounts with confirmation

## Navigation

The Account Settings feature is accessible through:
- **Desktop Navigation**: Profile dropdown menu → Account Settings
- **Mobile Navigation**: Hamburger menu → Account Settings

## User Types

### Hikers
- Access to all standard account management features
- Email verification required
- Profile and preference management

### Organizations
- All standard features plus organization-specific settings
- Approval status tracking
- No email verification required (approval-based access)

## Technical Implementation

### Routes
- `GET /account/settings` - Main account settings page

### Controllers
- `AccountSettingsController` - Handles routing to appropriate views based on user type

### Views
- `resources/views/account/hiker-settings.blade.php` - Hiker account settings
- `resources/views/account/organization-settings.blade.php` - Organization account settings

### Integration
- Integrates with existing Laravel Fortify features (2FA, password management)
- Integrates with existing Laravel Jetstream features (API tokens, account deletion)
- Uses existing profile management components

## Security Features

### Two-Factor Authentication
- QR code generation for authenticator apps
- Recovery codes for account recovery
- Secure setup and confirmation process

### Session Management
- View active sessions across devices
- Log out other sessions remotely
- Session security monitoring

### Password Security
- Current password verification
- Strong password requirements
- Secure password update process

## User Experience

### Design Principles
- **Clear Organization**: Features grouped by category (Security, Account Management, etc.)
- **Visual Hierarchy**: Color-coded sections and intuitive icons
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Accessibility**: Proper contrast, readable fonts, and keyboard navigation

### Navigation Flow
1. User accesses Account Settings from profile menu
2. System determines user type (hiker/organization)
3. Appropriate settings view is displayed
4. Users can navigate between different sections
5. Quick action buttons provide easy access to related features

## Future Enhancements

### Planned Features
- **Advanced Notification Settings**: Granular control over notification types
- **Privacy Controls**: Detailed profile visibility settings
- **Security Logs**: Comprehensive security event tracking
- **Backup Codes**: Additional account recovery options

### Integration Opportunities
- **Audit Logging**: Track all account changes and security events
- **Advanced Analytics**: User behavior and security insights
- **Third-party Integrations**: OAuth providers, SSO options

## Maintenance

### Regular Tasks
- Monitor security feature usage
- Update security recommendations
- Review and update privacy settings
- Test 2FA functionality

### Security Considerations
- Regular security audits
- Monitor for suspicious activity
- Update security features as needed
- User education on security best practices

## Support

For technical support or feature requests related to Account Settings, please contact the development team or create an issue in the project repository.

---

**Note**: This feature is designed to provide enterprise-level account management capabilities while maintaining simplicity and ease of use for all user types.
