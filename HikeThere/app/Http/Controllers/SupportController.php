<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SupportTicketCreated;
use App\Mail\SupportTicketReplyMail;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->with('replies.user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:general,booking,payment,technical,account,trail,event,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        // Determine which disk to use with safety check
        $disk = config('filesystems.default', 'public');
        if ($disk === 'gcs') {
            try {
                if (!config('filesystems.disks.gcs.bucket')) {
                    $disk = 'public';
                    \Log::warning('GCS configured but bucket not set, using public disk');
                }
            } catch (\Exception $e) {
                $disk = 'public';
                \Log::error('GCS configuration error: ' . $e->getMessage());
            }
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', $disk);
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'attachments' => $attachments,
        ]);

        // Send email notification to admin
        try {
            $adminEmail = config('mail.from.address', 'support@hikethere.com');
            Mail::to($adminEmail)->send(new SupportTicketCreated($ticket));
        } catch (\Exception $e) {
            Log::error('Failed to send support ticket email: ' . $e->getMessage());
        }

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Support ticket created successfully. Ticket #' . $ticket->ticket_number);
    }

    public function show(SupportTicket $ticket)
    {
        // Only allow the ticket owner to view it
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $ticket->load(['replies.user', 'user']);

        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        // Only allow the ticket owner to reply
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        // Determine which disk to use with safety check
        $disk = config('filesystems.default', 'public');
        if ($disk === 'gcs') {
            try {
                if (!config('filesystems.disks.gcs.bucket')) {
                    $disk = 'public';
                    \Log::warning('GCS configured but bucket not set, using public disk');
                }
            } catch (\Exception $e) {
                $disk = 'public';
                \Log::error('GCS configuration error: ' . $e->getMessage());
            }
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', $disk);
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $reply = SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'attachments' => $attachments,
            'is_admin' => false,
        ]);

        // Update ticket status to waiting_response (waiting for admin)
        if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        } else {
            $ticket->update(['status' => 'waiting_response']);
        }

        // Send email notification to admin
        try {
            $adminEmail = config('mail.from.address', 'support@hikethere.com');
            Mail::to($adminEmail)->send(new SupportTicketReplyMail($ticket, $reply));
        } catch (\Exception $e) {
            Log::error('Failed to send support reply email: ' . $e->getMessage());
        }

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Reply sent successfully. The admin team will respond to your message via email.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        // Only allow the ticket owner to update status
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:open,closed',
        ]);

        $ticket->update(['status' => $validated['status']]);

        if ($validated['status'] === 'closed') {
            $ticket->markAsClosed();
        }

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function destroy(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Determine which disk to use with safety check
        $disk = config('filesystems.default', 'public');
        if ($disk === 'gcs') {
            try {
                if (!config('filesystems.disks.gcs.bucket')) {
                    $disk = 'public';
                }
            } catch (\Exception $e) {
                $disk = 'public';
            }
        }

        // Delete attachments
        if ($ticket->attachments) {
            foreach ($ticket->attachments as $attachment) {
                Storage::disk($disk)->delete($attachment['path']);
            }
        }

        // Delete reply attachments
        foreach ($ticket->replies as $reply) {
            if ($reply->attachments) {
                foreach ($reply->attachments as $attachment) {
                    Storage::disk($disk)->delete($attachment['path']);
                }
            }
        }

        $ticket->delete();

        return redirect()->route('support.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
