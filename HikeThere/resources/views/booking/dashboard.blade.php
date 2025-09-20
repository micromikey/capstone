<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hiking Bookings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-pending { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .status-confirmed { background: linear-gradient(135deg, #10b981, #059669); }
        .status-cancelled { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .status-completed { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        
        .booking-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #10b981;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border: 1px solid #cbd5e1;
        }
    </style>
</head>
<body>
<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-50">
        
        <!-- Header -->
        <div class="bg-white shadow-lg border-b border-emerald-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">⛰️ Hiking Admin Dashboard</h1>
                        <p class="text-gray-600 mt-1">Manage bookings and track adventures</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Welcome back, Admin</p>
                            <p class="text-xs text-emerald-600">{{ now()->format('l, F j, Y') }}</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white font-bold">
                            A
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stat-card rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            📊
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalBookings ?? '42' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            ⏳
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pendingBookings ?? '8' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            ✅
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Confirmed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $confirmedBookings ?? '28' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                            💰
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue ?? 126500) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Filter by Status:</label>
                            <select id="statusFilter" class="ml-2 border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-700">Trail:</label>
                            <select id="trailFilter" class="ml-2 border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Trails</option>
                                <option value="mt_kulis">Mt. Kulis</option>
                                <option value="mt_mariglem">Mt. Mariglem</option>
                                <option value="mt_tagapo">Mt. Tagapo</option>
                                <option value="mt_batulao">Mt. Batulao</option>
                                <option value="mt_387">Mt. 387</option>
                                <option value="mt_pulag">Mt. Pulag</option>
                                <option value="mt_fato">Mt. Fato</option>
                                <option value="mt_malindig">Mt. Malindig</option>
                                <option value="mt_guiting">Mt. Guiting</option>
                                <option value="mt_apo">Mt. Apo</option>
                            </select>
                        </div>
                        
                        <div>
                            <input type="date" id="dateFilter" class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Filter by date">
                        </div>
                        
                        <button onclick="clearFilters()" class="bg-gray-500 text-white px-4 py-1 rounded-md text-sm hover:bg-gray-600 transition-colors">
                            Clear Filters
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button onclick="exportBookings()" class="bg-emerald-600 text-white px-4 py-2 rounded-md text-sm hover:bg-emerald-700 transition-colors">
                            📥 Export CSV
                        </button>
                        <span class="text-sm text-gray-500" id="bookingCount">Showing {{ $bookings->count() ?? '12' }} bookings</span>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="space-y-4" id="bookingsList">
                
                @forelse($bookings ?? collect([
                    (object)[
                        'id' => 1,
                        'booking_reference' => 'HK2024001',
                        'fullname' => 'Juan Dela Cruz',
                        'email' => 'juan@email.com',
                        'phone' => '+63 912 345 6789',
                        'trail' => 'mt_pulag',
                        'trail_name' => 'Mt. Pulag',
                        'hike_date' => '2024-12-15',
                        'participants' => 2,
                        'total_amount' => 9800,
                        'payment_option' => 'downpayment',
                        'payment_method' => 'gcash',
                        'status' => 'confirmed',
                        'emergency_contact' => 'Maria Dela Cruz - 0917 123 4567',
                        'created_at' => now()->subDays(2)
                    ],
                    (object)[
                        'id' => 2,
                        'booking_reference' => 'HK2024002',
                        'fullname' => 'Sarah Johnson',
                        'email' => 'sarah.j@email.com',
                        'phone' => '+63 905 678 9012',
                        'trail' => 'mt_batulao',
                        'trail_name' => 'Mt. Batulao',
                        'hike_date' => '2024-12-20',
                        'participants' => 4,
                        'total_amount' => 6000,
                        'payment_option' => 'full',
                        'payment_method' => 'bank',
                        'status' => 'pending',
                        'emergency_contact' => 'Mike Johnson - 0918 987 6543',
                        'created_at' => now()->subDays(1)
                    ],
                    (object)[
                        'id' => 3,
                        'booking_reference' => 'HK2024003',
                        'fullname' => 'Carlos Reyes',
                        'email' => 'carlos@email.com',
                        'phone' => '+63 920 111 2222',
                        'trail' => 'mt_apo',
                        'trail_name' => 'Mt. Apo',
                        'hike_date' => '2024-11-30',
                        'participants' => 1,
                        'total_amount' => 7500,
                        'payment_option' => 'downpayment',
                        'payment_method' => 'paypal',
                        'status' => 'completed',
                        'emergency_contact' => 'Ana Reyes - 0919 333 4444',
                        'created_at' => now()->subDays(5)
                    ]
                ]) as $booking)
                    <div class="booking-card bg-white rounded-lg shadow-md p-6" data-status="{{ $booking->status }}" data-trail="{{ $booking->trail }}" data-date="{{ $booking->hike_date }}">
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            
                            <!-- Booking Info -->
                            <div class="lg:col-span-2">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $booking->fullname }}</h3>
                                        <p class="text-sm text-gray-500">{{ $booking->booking_reference }}</p>
                                        <div class="flex items-center mt-2">
                                            <span class="status-badge {{ 'status-' . $booking->status }} text-white px-3 py-1 rounded-full text-xs font-bold">
                                                @switch($booking->status)
                                                    @case('pending') ⏳ Pending @break
                                                    @case('confirmed') ✅ Confirmed @break
                                                    @case('cancelled') ❌ Cancelled @break
                                                    @case('completed') 🎯 Completed @break
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <span class="text-gray-500 w-20">📍 Trail:</span>
                                        <span class="font-medium">{{ $booking->trail_name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-500 w-20">📅 Date:</span>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($booking->hike_date)->format('M j, Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-500 w-20">👥 Count:</span>
                                        <span class="font-medium">{{ $booking->participants }} person(s)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-500 w-20">📧 Email:</span>
                                        <span class="font-medium text-blue-600">{{ $booking->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-500 w-20">📱 Phone:</span>
                                        <span class="font-medium">{{ $booking->phone }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3">💰 Payment Details</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Total Amount:</span>
                                        <p class="font-bold text-emerald-600 text-lg">₱{{ number_format($booking->total_amount) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Payment Option:</span>
                                        <p class="font-medium">{{ ucfirst($booking->payment_option) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Method:</span>
                                        <p class="font-medium">{{ ucfirst($booking->payment_method) }}</p>
                                    </div>
                                    <div class="pt-2">
                                        <span class="text-gray-500">Emergency Contact:</span>
                                        <p class="text-xs font-medium text-red-600">{{ $booking->emergency_contact }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2">
                                <h4 class="font-bold text-gray-800 mb-2">🛠️ Actions</h4>
                                
                                @if($booking->status === 'pending')
                                    <button onclick="updateBookingStatus({{ $booking->id }}, 'confirmed')" class="bg-green-600 text-white px-3 py-2 rounded-md text-sm hover:bg-green-700 transition-colors">
                                        ✅ Confirm
                                    </button>
                                    <button onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')" class="bg-red-600 text-white px-3 py-2 rounded-md text-sm hover:bg-red-700 transition-colors">
                                        ❌ Cancel
                                    </button>
                                @endif
                                
                                @if($booking->status === 'confirmed')
                                    <button onclick="updateBookingStatus({{ $booking->id }}, 'completed')" class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                                        🎯 Mark Complete
                                    </button>
                                @endif
                                
                                <button onclick="viewBookingDetails({{ $booking->id }})" class="bg-gray-600 text-white px-3 py-2 rounded-md text-sm hover:bg-gray-700 transition-colors">
                                    👁️ View Details
                                </button>
                                
                                <button onclick="sendEmail({{ $booking->id }})" class="bg-emerald-600 text-white px-3 py-2 rounded-md text-sm hover:bg-emerald-700 transition-colors">
                                    📧 Send Email
                                </button>
                                
                                <div class="text-xs text-gray-500 mt-2">
                                    Booked: {{ $booking->created_at->format('M j, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="text-4xl mb-4">📭</div>
                        <h3 class="text-lg font-medium text-gray-900">No bookings found</h3>
                        <p class="text-gray-500">No bookings match your current filters.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white">
                <h3 class="text-xl font-bold">📋 Booking Details</h3>
                <p class="text-emerald-100 mt-1">Complete booking information</p>
            </div>
            <div id="modalContent" class="p-6 overflow-y-auto max-h-96">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Filter functionality
        function filterBookings() {
            const statusFilter = document.getElementById('statusFilter').value;
            const trailFilter = document.getElementById('trailFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const bookings = document.querySelectorAll('.booking-card');
            let visibleCount = 0;

            bookings.forEach(booking => {
                const status = booking.getAttribute('data-status');
                const trail = booking.getAttribute('data-trail');
                const date = booking.getAttribute('data-date');
                
                let show = true;
                
                if (statusFilter && status !== statusFilter) show = false;
                if (trailFilter && trail !== trailFilter) show = false;
                if (dateFilter && date !== dateFilter) show = false;
                
                if (show) {
                    booking.style.display = 'block';
                    visibleCount++;
                } else {
                    booking.style.display = 'none';
                }
            });
            
            document.getElementById('bookingCount').textContent = `Showing ${visibleCount} bookings`;
        }

        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('trailFilter').value = '';
            document.getElementById('dateFilter').value = '';
            filterBookings();
        }

        // Booking actions
        function updateBookingStatus(bookingId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus} this booking?`)) {
                // Here you would make an AJAX call to update the status
                // For demo purposes, we'll just show an alert
                showNotification(`Booking status updated to ${newStatus}`, 'success');
                
                // In real implementation:
                // fetch(`/admin/bookings/${bookingId}/status`, {
                //     method: 'PATCH',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                //     },
                //     body: JSON.stringify({ status: newStatus })
                // }).then(() => location.reload());
            }
        }

        function viewBookingDetails(bookingId) {
            // In real implementation, fetch booking details from server
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong>Booking ID:</strong> ${bookingId}</div>
                        <div><strong>Reference:</strong> HK2024${String(bookingId).padStart(3, '0')}</div>
                    </div>
                    <div><strong>Full Details:</strong></div>
                    <p class="text-gray-600">Complete booking information would be loaded here from the server...</p>
                </div>
            `;
            document.getElementById('bookingModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        function sendEmail(bookingId) {
            showNotification(`Email sent to booking ID: ${bookingId}`, 'success');
            // In real implementation:
            // fetch(`/admin/bookings/${bookingId}/send-email`, { method: 'POST' })
        }

        function exportBookings() {
            showNotification('Exporting bookings to CSV...', 'success');
            // In real implementation:
            // window.location.href = '/admin/bookings/export';
        }

        function showNotification(message, type = 'success') {
            // Remove existing notifications
            document.querySelectorAll('.notification').forEach(n => n.remove());
            
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-500 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        // Event listeners
        document.getElementById('statusFilter').addEventListener('change', filterBookings);
        document.getElementById('trailFilter').addEventListener('change', filterBookings);
        document.getElementById('dateFilter').addEventListener('change', filterBookings);

        // Close modal when clicking outside
        document.getElementById('bookingModal').addEventListener('click', (e) => {
            if (e.target.id === 'bookingModal') {
                closeModal();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            filterBookings();
        });
    </script>
</x-app-layout>
</body>
</html>