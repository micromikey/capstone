<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ticket #{{ $ticket->ticket_number }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $ticket->subject }}</p>
            </div>
            <a href="{{ route('support.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Tickets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Ticket Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $ticket->status_color }}-100 text-{{ $ticket->status_color }}-800">
                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $ticket->priority_color }}-100 text-{{ $ticket->priority_color }}-800">
                                    {{ ucfirst($ticket->priority) }} Priority
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($ticket->category) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">
                                Created {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            @if($ticket->status !== 'closed')
                                <form action="{{ route('support.updateStatus', $ticket) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="closed">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-gray-600 rounded hover:bg-gray-700" onclick="return confirm('Are you sure you want to close this ticket?')">
                                        Close Ticket
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('support.destroy', $ticket) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this ticket?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Description:</h3>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</div>

                        @if($ticket->attachments && count($ticket->attachments) > 0)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Attachments:</h4>
                                <div class="space-y-2">
                                    @foreach($ticket->attachments as $attachment)
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ $attachment['name'] }} ({{ number_format($attachment['size'] / 1024, 2) }} KB)
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Conversation Thread -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Conversation</h3>

                    <div class="space-y-4">
                        @forelse($ticket->replies as $reply)
                            <div class="flex {{ $reply->is_admin ? 'justify-start' : 'justify-end' }}">
                                <div class="max-w-3xl {{ $reply->is_admin ? 'bg-gray-50' : 'bg-blue-50' }} rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium {{ $reply->is_admin ? 'text-gray-900' : 'text-blue-900' }}">
                                                {{ $reply->is_admin ? 'Support Team' : $reply->user->name }}
                                            </span>
                                            @if($reply->is_admin)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">Admin</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reply->message }}</div>

                                    @if($reply->attachments && count($reply->attachments) > 0)
                                        <div class="mt-3 space-y-1">
                                            @foreach($reply->attachments as $attachment)
                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="flex items-center text-xs text-blue-600 hover:text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    {{ $attachment['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No replies yet. The support team will respond to your ticket via email.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Reply</h3>
                        <form action="{{ route('support.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Your Message *</label>
                                <textarea name="message" id="message" rows="4" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('message') border-red-500 @enderror"
                                    placeholder="Type your reply here...">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reply_attachments" class="block text-sm font-medium text-gray-700">Attachments (Optional)</label>
                                <input type="file" name="attachments[]" id="reply_attachments" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    Send Reply
                                </button>
                            </div>
                        </form>

                        <p class="mt-4 text-xs text-gray-500">
                            💡 Note: Your reply will be sent to the support team via email. They will respond directly to your registered email address.
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600">This ticket is closed. If you need further assistance, please create a new ticket.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
