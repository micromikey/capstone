<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Support Ticket</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
        <h1 style="color: #2563eb; margin: 0 0 10px 0;">🎫 New Support Ticket</h1>
        <p style="margin: 0; color: #6b7280;">Ticket #{{ $ticket->ticket_number }}</p>
    </div>

    <div style="background-color: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h2 style="color: #111827; margin-top: 0;">{{ $ticket->subject }}</h2>
        
        <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
            <span style="background-color: #dbeafe; color: #1e40af; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                {{ ucfirst($ticket->category) }}
            </span>
            <span style="background-color: #fee2e2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                {{ ucfirst($ticket->priority) }} Priority
            </span>
            <span style="background-color: #dcfce7; color: #166534; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
            </span>
        </div>

        <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <h3 style="margin-top: 0; color: #374151; font-size: 14px;">Description:</h3>
            <p style="margin: 0; white-space: pre-wrap;">{{ $ticket->description }}</p>
        </div>

        <div style="border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 15px;">
            <p style="margin: 5px 0; font-size: 14px;"><strong>User:</strong> {{ $ticket->user->name }}</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>Email:</strong> {{ $ticket->user->email }}</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>User Type:</strong> {{ ucfirst($ticket->user->user_type) }}</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>Created:</strong> {{ $ticket->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>

        @if($ticket->attachments && count($ticket->attachments) > 0)
        <div style="border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 15px;">
            <h3 style="margin-top: 0; color: #374151; font-size: 14px;">📎 Attachments:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($ticket->attachments as $attachment)
                <li style="margin: 5px 0;">
                    <a href="{{ url(Storage::url($attachment['path'])) }}" style="color: #2563eb; text-decoration: none;">
                        {{ $attachment['name'] }} ({{ number_format($attachment['size'] / 1024, 2) }} KB)
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <p style="color: #6b7280; font-size: 14px; margin: 0;">
            Please respond to this ticket by replying to this email or logging into the HikeThere support system.
        </p>
        <p style="color: #6b7280; font-size: 12px; margin-top: 10px;">
            © {{ date('Y') }} HikeThere. All rights reserved.
        </p>
    </div>
</body>
</html>
