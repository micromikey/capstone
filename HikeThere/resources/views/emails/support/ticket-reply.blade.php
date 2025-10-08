<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Reply to Support Ticket</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
        <h1 style="color: #2563eb; margin: 0 0 10px 0;">💬 New Reply to Support Ticket</h1>
        <p style="margin: 0; color: #6b7280;">Ticket #{{ $ticket->ticket_number }}</p>
    </div>

    <div style="background-color: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h2 style="color: #111827; margin-top: 0;">{{ $ticket->subject }}</h2>
        
        <div style="background-color: #eff6ff; border-left: 4px solid #2563eb; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div>
                    <strong style="color: #1e40af;">{{ $reply->user->name }}</strong>
                    @if($reply->is_admin)
                    <span style="background-color: #2563eb; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">ADMIN</span>
                    @endif
                </div>
                <span style="color: #6b7280; font-size: 12px;">{{ $reply->created_at->format('M j, Y \a\t g:i A') }}</span>
            </div>
            <p style="margin: 0; white-space: pre-wrap; color: #374151;">{{ $reply->message }}</p>

            @if($reply->attachments && count($reply->attachments) > 0)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #bfdbfe;">
                <p style="margin: 0 0 5px 0; font-size: 13px; font-weight: 600; color: #1e40af;">📎 Attachments:</p>
                @foreach($reply->attachments as $attachment)
                <a href="{{ url(\App\Helpers\StorageHelper::url($attachment['path'])) }}" style="display: block; color: #2563eb; text-decoration: none; font-size: 13px; margin: 5px 0;">
                    → {{ $attachment['name'] }}
                </a>
                @endforeach
            </div>
            @endif
        </div>

        <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px;">
            <h3 style="margin-top: 0; color: #374151; font-size: 14px;">Original Ticket Description:</h3>
            <p style="margin: 0; white-space: pre-wrap; font-size: 14px; color: #6b7280;">{{ Str::limit($ticket->description, 200) }}</p>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('support.show', $ticket) }}" style="display: inline-block; background-color: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                View Full Conversation
            </a>
        </div>
    </div>

    <div style="background-color: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0; font-size: 14px; color: #78350f;">
            <strong>⚠️ Important:</strong> To reply to this ticket, please log in to your HikeThere account and use the support system, or simply reply to this email.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <p style="color: #6b7280; font-size: 14px; margin: 0;">
            Ticket Status: <strong>{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</strong>
        </p>
        <p style="color: #6b7280; font-size: 12px; margin-top: 10px;">
            © {{ date('Y') }} HikeThere. All rights reserved.
        </p>
    </div>
</body>
</html>
