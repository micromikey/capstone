<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Support Ticket') }}
            </h2>
            <a href="{{ route('support.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Tickets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">How can we help you?</h3>
                        <p class="mt-1 text-sm text-gray-600">Fill out the form below and our support team will get back to you via email as soon as possible.</p>
                    </div>

                    <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject *</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                            <select name="category" id="category" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                                <option value="">Select a category</option>
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                                <option value="booking" {{ old('category') == 'booking' ? 'selected' : '' }}>Booking Issue</option>
                                <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Payment Problem</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical Issue</option>
                                <option value="account" {{ old('category') == 'account' ? 'selected' : '' }}>Account Settings</option>
                                <option value="trail" {{ old('category') == 'trail' ? 'selected' : '' }}>Trail Information</option>
                                <option value="event" {{ old('category') == 'event' ? 'selected' : '' }}>Event Question</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                            <select name="priority" id="priority" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-500 @enderror">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - General question</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium - Needs attention</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Urgent issue</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Critical problem</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                            <textarea name="description" id="description" rows="6" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                placeholder="Please provide as much detail as possible about your issue...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Include relevant details like error messages, booking numbers, or screenshots.</p>
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" id="attachments" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">You can upload up to 5 files (max 10MB each). Supported formats: JPG, PNG, PDF, DOC, DOCX</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('support.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Tips -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">💡 Tips for faster resolution:</h4>
                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                    <li>Be as specific as possible about your issue</li>
                    <li>Include screenshots or documents when relevant</li>
                    <li>Provide booking or transaction numbers if applicable</li>
                    <li>Check your email regularly for responses from our support team</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
