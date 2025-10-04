# HikeThere Support System - Complete Documentation

## Overview

The HikeThere Support System is a comprehensive email-based ticketing system that connects users (hikers and organizations) with the admin support team. The system tracks support requests, allows conversation threads, supports file attachments, and sends automatic email notifications.

## Features

### Core Features
- ✅ **Ticket Creation**: Users can create support tickets with subject, category, priority, and detailed descriptions
- ✅ **File Attachments**: Support for uploading screenshots, documents (JPG, PNG, PDF, DOC, DOCX) up to 10MB each
- ✅ **Ticket Management**: View all tickets, filter by status, and track ticket progress
- ✅ **Reply System**: Two-way communication between users and admin via email
- ✅ **Email Notifications**: Automatic emails sent to admin on ticket creation and user replies
- ✅ **Status Tracking**: Track ticket status (Open, In Progress, Waiting Response, Resolved, Closed)
- ✅ **Priority Levels**: Low, Medium, High, and Urgent priority classification
- ✅ **Categories**: General, Booking, Payment, Technical, Account, Trail, Event, Other

### User Experience
- **Navigation**: Support link added to main navigation menu (desktop & mobile)
- **Ticket List**: Clean interface showing all user's tickets with status badges
- **Ticket Details**: Full conversation thread view with attachments
- **Reply Interface**: Simple form to respond to tickets with optional attachments
- **Status Management**: Users can close their own tickets when resolved

## System Architecture

### Database Structure

#### `support_tickets` Table
```sql
- id (bigint, primary key)
- user_id (foreign key to users)
- ticket_number (unique identifier, e.g., TKT-ABC123XYZ)
- subject (string)
- category (enum)
- priority (enum)
- status (enum)
- description (text)
- attachments (json)
- admin_notes (text, nullable)
- assigned_to (foreign key to users, nullable)
- resolved_at (timestamp, nullable)
- created_at, updated_at (timestamps)
```

#### `support_ticket_replies` Table
```sql
- id (bigint, primary key)
- support_ticket_id (foreign key)
- user_id (foreign key)
- message (text)
- attachments (json)
- is_admin (boolean)
- is_internal_note (boolean)
- created_at, updated_at (timestamps)
```

### Models

#### SupportTicket Model (`app/Models/SupportTicket.php`)
- **Relationships**: 
  - `user()` - Belongs to User
  - `assignedTo()` - Belongs to User (admin)
  - `replies()` - Has many SupportTicketReply
- **Attributes**:
  - `status_color` - Returns Tailwind color based on status
  - `priority_color` - Returns Tailwind color based on priority
- **Scopes**:
  - `open()` - Filter open tickets
  - `closed()` - Filter closed/resolved tickets
- **Methods**:
  - `markAsResolved()` - Mark ticket as resolved
  - `markAsClosed()` - Mark ticket as closed

#### SupportTicketReply Model (`app/Models/SupportTicketReply.php`)
- **Relationships**:
  - `ticket()` - Belongs to SupportTicket
  - `user()` - Belongs to User

### Controllers

#### SupportController (`app/Http/Controllers/SupportController.php`)

**Methods:**

1. **index()** - Display list of user's tickets
   - Paginated list (10 per page)
   - Ordered by created_at DESC

2. **create()** - Show ticket creation form

3. **store(Request $request)** - Create new ticket
   - Validates input
   - Handles file uploads
   - Generates unique ticket number
   - Sends email to admin
   - Returns: Redirect to ticket details

4. **show(SupportTicket $ticket)** - Display ticket details
   - Authorization: Only ticket owner can view
   - Loads ticket with replies and user relationships

5. **reply(Request $request, SupportTicket $ticket)** - Add reply to ticket
   - Authorization: Only ticket owner
   - Handles file uploads
   - Updates ticket status to 'waiting_response'
   - Sends email to admin

6. **updateStatus(Request $request, SupportTicket $ticket)** - Update ticket status
   - Authorization: Only ticket owner
   - Allowed statuses: open, closed
   - Auto-resolves when status set to closed

7. **destroy(SupportTicket $ticket)** - Delete ticket
   - Authorization: Only ticket owner
   - Deletes all attachments from storage
   - Cascades to replies

### Mail Classes

#### SupportTicketCreated (`app/Mail/SupportTicketCreated.php`)
- Sent to: Admin email (configured in `mail.from.address`)
- Triggers: When new ticket is created
- Template: `resources/views/emails/support/ticket-created.blade.php`
- Contains:
  - Ticket number and subject
  - Category, priority, status badges
  - Full description
  - User information
  - Attachments list

#### SupportTicketReplyMail (`app/Mail/SupportTicketReplyMail.php`)
- Sent to: Admin email
- Triggers: When user replies to ticket
- Template: `resources/views/emails/support/ticket-reply.blade.php`
- Contains:
  - Ticket context
  - New reply message
  - Reply attachments
  - Link to view full conversation
  - Current ticket status

## Routes

All routes are protected by `auth` middleware and prefixed with `/support`:

```php
Route::prefix('support')->name('support.')->group(function () {
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/create', [SupportController::class, 'create'])->name('create');
    Route::post('/', [SupportController::class, 'store'])->name('store');
    Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
    Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('reply');
    Route::patch('/{ticket}/status', [SupportController::class, 'updateStatus'])->name('updateStatus');
    Route::delete('/{ticket}', [SupportController::class, 'destroy'])->name('destroy');
});
```

## Views

### Ticket List (`resources/views/support/index.blade.php`)
- Displays all user's tickets in a table
- Shows: Ticket number, subject, category, priority, status, created date
- Actions: View, Delete
- Empty state with call-to-action button

### Create Ticket (`resources/views/support/create.blade.php`)
- Form fields:
  - Subject (required)
  - Category dropdown (required)
  - Priority dropdown (required)
  - Description textarea (required)
  - File attachments (optional, multiple)
- Help tips section with best practices

### Ticket Details (`resources/views/support/show.blade.php`)
- Ticket information header with status badges
- Original description and attachments
- Conversation thread showing all replies
- Reply form (if ticket not closed)
- Actions: Close ticket, Delete ticket

## Configuration

### Email Settings

Configure in `.env` file:

```env
MAIL_FROM_ADDRESS=support@hikethere.com
MAIL_FROM_NAME="${APP_NAME}"
```

### File Storage

Attachments are stored in `storage/app/public/support-attachments/`

Make sure storage is linked:
```bash
php artisan storage:link
```

### Validation Rules

**Ticket Creation:**
- Subject: Required, max 255 characters
- Category: Required, must be one of predefined values
- Priority: Required, must be one of predefined values
- Description: Required
- Attachments: Optional, max 10MB each, allowed types: jpg, jpeg, png, pdf, doc, docx

**Reply:**
- Message: Required
- Attachments: Optional, max 10MB each, allowed types: jpg, jpeg, png, pdf, doc, docx

## Workflow

### User Creates Ticket
1. User navigates to Support from menu
2. Clicks "New Ticket"
3. Fills out form with issue details
4. Optionally uploads attachments
5. Submits ticket
6. **System generates unique ticket number** (e.g., TKT-67094CBE4FCA2)
7. **Email sent to admin** with full ticket details
8. User redirected to ticket details page

### User Adds Reply
1. User opens existing ticket
2. Scrolls to reply form
3. Types message and optionally adds attachments
4. Submits reply
5. **Ticket status updates to "Waiting Response"**
6. **Email sent to admin** with reply details
7. User sees confirmation message

### Admin Response (Via Email)
1. Admin receives email notification
2. Admin can reply directly to email or access system
3. User receives admin response via email
4. Conversation tracked in ticket thread

### Ticket Closure
1. User determines issue is resolved
2. Clicks "Close Ticket" button
3. Ticket status changes to "Closed"
4. Reply form hidden on closed tickets
5. User can create new ticket if needed

## Security Features

- ✅ **Authorization**: Users can only view their own tickets
- ✅ **CSRF Protection**: All forms protected with CSRF tokens
- ✅ **File Validation**: Strict file type and size validation
- ✅ **XSS Prevention**: All user input sanitized in blade templates
- ✅ **Database Security**: Eloquent ORM with parameter binding

## Best Practices for Users

### Creating Effective Tickets

**DO:**
- ✅ Use clear, descriptive subjects
- ✅ Provide step-by-step reproduction steps for bugs
- ✅ Include relevant booking/transaction numbers
- ✅ Attach screenshots or error messages
- ✅ Choose appropriate category and priority
- ✅ Be specific about the expected vs actual behavior

**DON'T:**
- ❌ Create duplicate tickets for the same issue
- ❌ Use vague subjects like "Help" or "Problem"
- ❌ Share sensitive passwords or payment details
- ❌ Use ticket system for urgent emergencies (call instead)

## Admin Guidelines (Email-Based)

### Responding to Tickets

1. **Read Carefully**: Review full ticket description and attachments
2. **Reply Promptly**: Respond within 24-48 hours
3. **Be Clear**: Provide step-by-step solutions
4. **Request Info**: Ask for additional details if needed
5. **Follow Up**: Check if issue is resolved

### Email Management

- Set up filters for support ticket emails
- Use email templates for common issues
- Include ticket number in all responses
- Reply to the user's registered email

## Troubleshooting

### Emails Not Sending

Check:
1. `.env` mail configuration is correct
2. Mail server credentials are valid
3. `config:clear` has been run after changes
4. Check Laravel logs: `storage/logs/laravel.log`

### Attachments Not Uploading

Check:
1. Storage is linked: `php artisan storage:link`
2. `storage/app/public` directory has write permissions
3. PHP `upload_max_filesize` and `post_max_size` are set correctly
4. File meets validation requirements

### Ticket Number Not Generating

- Ticket numbers are auto-generated using `uniqid()` in the model's `boot` method
- Format: `TKT-` + unique alphanumeric string
- If missing, check model boot method is being called

## Future Enhancements

Potential features for future development:

1. **Admin Dashboard**: Dedicated interface for admins to manage all tickets
2. **Ticket Assignment**: Assign tickets to specific support staff
3. **Canned Responses**: Pre-written responses for common issues
4. **Knowledge Base**: FAQ system to reduce ticket volume
5. **Live Chat**: Real-time support option
6. **Satisfaction Rating**: Allow users to rate support responses
7. **Ticket Search**: Advanced search and filtering
8. **SLA Tracking**: Monitor response and resolution times
9. **Internal Notes**: Private notes for admin team only
10. **Email Piping**: Allow users to create tickets by emailing

## API Endpoints (Future)

For mobile app or API integration:

```
GET    /api/support/tickets           - List tickets
POST   /api/support/tickets           - Create ticket
GET    /api/support/tickets/{id}      - View ticket
POST   /api/support/tickets/{id}/reply - Add reply
PATCH  /api/support/tickets/{id}      - Update ticket
DELETE /api/support/tickets/{id}      - Delete ticket
```

## Support Statistics

Useful queries for analytics:

```php
// Total tickets
SupportTicket::count()

// Open tickets
SupportTicket::open()->count()

// Tickets by priority
SupportTicket::where('priority', 'urgent')->count()

// Average response time
SupportTicket::whereNotNull('resolved_at')
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
    ->first()

// Tickets by category
SupportTicket::select('category', DB::raw('count(*) as total'))
    ->groupBy('category')
    ->get()
```

## Maintenance

### Regular Tasks

1. **Archive Old Tickets**: Archive closed tickets older than 1 year
2. **Clean Attachments**: Remove orphaned attachment files
3. **Monitor Storage**: Check disk space usage
4. **Review Logs**: Check for failed emails or errors
5. **Update Categories**: Add new categories based on ticket patterns

### Cleanup Commands

```php
// Delete tickets older than 2 years (closed)
SupportTicket::where('status', 'closed')
    ->where('created_at', '<', now()->subYears(2))
    ->delete();
```

## Testing

### Manual Testing Checklist

- [ ] Create ticket with all fields
- [ ] Create ticket with attachments
- [ ] View ticket details
- [ ] Add reply to ticket
- [ ] Add reply with attachments
- [ ] Close ticket
- [ ] Delete ticket
- [ ] Verify email notifications sent
- [ ] Check email template rendering
- [ ] Test file upload validation
- [ ] Test authorization (access other user's ticket)
- [ ] Test mobile responsiveness

## Conclusion

The HikeThere Support System provides a robust, user-friendly way for users to get help while maintaining simplicity through email-based admin communication. The system is scalable, secure, and ready for future enhancements like a full admin dashboard or live chat integration.

For questions or issues with the support system itself, please contact the development team.

---

**Last Updated**: October 5, 2025
**Version**: 1.0.0
**Author**: HikeThere Development Team
