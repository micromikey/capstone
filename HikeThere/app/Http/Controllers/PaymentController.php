<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingPayment;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show the payment form
     */
    public function create(Request $request)
    {
        $booking = null;
        
        // If booking_id is provided, load the booking and pre-fill the form
        if ($request->has('booking_id')) {
            $booking = Booking::with(['trail', 'user', 'batch'])->findOrFail($request->booking_id);
            
            // Check if this booking already has a payment
            if ($booking->payment) {
                if ($booking->payment->isPaid()) {
                    return redirect()->route('booking.show', $booking)
                        ->with('info', 'This booking has already been paid.');
                }
                // If payment exists but is pending/failed, we can retry
            }
            
            // Ensure the current user owns this booking
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }
        
        return view('payment.pay', compact('booking'));
    }

    /**
     * Process the payment with PayMongo
     */
    public function processPayment(Request $request)
    {
        // ✅ Validate user input
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'mountain' => 'required|string|max:255',
            'amount' => 'required|integer|min:1', // pesos input
            'hike_date' => 'required|date',
            'participants' => 'required|integer|min:1',
        ]);

        $booking = null;
        
        // If booking_id provided, verify ownership
        if (!empty($validated['booking_id'])) {
            $booking = Booking::findOrFail($validated['booking_id']);
            
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to this booking.');
            }
            
            // Check if already paid
            if ($booking->payment && $booking->payment->isPaid()) {
                return redirect()->route('booking.show', $booking)
                    ->with('info', 'This booking has already been paid.');
            }
        }

        // ✅ Save/update payment record with pending status
        $paymentData = array_merge($validated, [
            'user_id' => Auth::id(),
            'payment_status' => 'pending'
        ]);

        // If booking exists and already has a payment, update it; otherwise create new
        if ($booking && $booking->payment) {
            $payment = $booking->payment;
            $payment->update($paymentData);
        } else {
            $payment = BookingPayment::create($paymentData);
        }

        // ✅ Convert amount to centavos for PayMongo
        $amountInCentavos = $validated['amount'] * 100;

        // ✅ Get PayMongo secret key from config
        $secretKey = config('services.paymongo.secret_key');

        // ✅ Call PayMongo API
        $curl = curl_init();

        // Prepare payment link attributes
        $linkAttributes = [
            'amount' => $amountInCentavos,
            'description' => 'Booking for ' . $validated['mountain'] . ' - ' . $validated['participants'] . ' pax',
            'remarks' => 'Payment ID: ' . $payment->id . 
                         ($booking ? ' | Booking #' . $booking->id : ''),
        ];

        // Add billing information for e-wallet support (GCash, GrabPay, etc.)
        $linkAttributes['billing'] = [
            'name' => $validated['fullname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/links",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'data' => [
                    'attributes' => $linkAttributes
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic " . base64_encode($secretKey . ":"),
                "content-type: application/json"
            ],
            // ⚠️ For local testing only — disable SSL verification
            CURLOPT_SSL_VERIFYPEER => config('app.env') === 'production',
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // ✅ Handle cURL errors
        if ($err) {
            Log::error('PayMongo cURL Error', [
                'error' => $err,
                'payment_id' => $payment->id,
                'booking_id' => $booking?->id
            ]);
            return back()->with('error', 'Payment system error. Please try again.');
        }

        $decoded = json_decode($response, true);

        // ✅ Log the response for debugging
        Log::info('PayMongo Response', [
            'payment_id' => $payment->id,
            'booking_id' => $booking?->id,
            'response' => $decoded
        ]);

        // ✅ Save PayMongo link ID
        if (isset($decoded['data']['id'])) {
            $payment->update([
                'paymongo_link_id' => $decoded['data']['id']
            ]);
        }

        // ✅ Redirect to PayMongo checkout page if available
        if (isset($decoded['data']['attributes']['checkout_url'])) {
            return redirect()->away($decoded['data']['attributes']['checkout_url']);
        }

        // ❌ Otherwise return error
        Log::error('PayMongo Failed to Create Link', [
            'payment_id' => $payment->id,
            'booking_id' => $booking?->id,
            'response' => $decoded
        ]);

        return back()->with('error', 'Unable to create payment link. Please try again.');
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $bookingId = $request->query('booking_id');
        
        $payment = null;
        $booking = null;

        if ($paymentId) {
            $payment = BookingPayment::with('booking.trail')->find($paymentId);
        } elseif ($bookingId) {
            // Legacy support - find by booking_id
            $booking = Booking::with(['payment', 'trail'])->find($bookingId);
            $payment = $booking?->payment;
        }

        return view('payment.success', compact('payment', 'booking'));
    }

    /**
     * PayMongo webhook handler
     * This will be called by PayMongo when payment status changes
     */
    public function webhook(Request $request)
    {
        // ✅ Log webhook data
        Log::info('PayMongo Webhook Received', [
            'payload' => $request->all()
        ]);

        $data = $request->input('data');

        if (!$data) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $eventType = $data['attributes']['type'] ?? null;
        $paymentData = $data['attributes']['data'] ?? null;

        // ✅ Handle payment.paid event
        if ($eventType === 'link.payment.paid' && $paymentData) {
            // Try to extract payment ID from remarks field
            $remarks = $paymentData['attributes']['remarks'] ?? '';
            
            // Parse "Payment ID: X | Booking #Y" format
            if (preg_match('/Payment ID: (\d+)/', $remarks, $matches)) {
                $paymentId = (int) $matches[1];
                $payment = BookingPayment::with('booking.batch')->find($paymentId);

                if ($payment && $payment->isPending()) {
                    $payment->markAsPaid($paymentData['id'] ?? null);
                    
                    Log::info('Payment Marked as Paid', [
                        'payment_id' => $paymentId,
                        'booking_id' => $payment->booking_id
                    ]);
                    
                    // If linked to a booking, update booking status and reserve slots
                    if ($payment->booking) {
                        $booking = $payment->booking;
                        $booking->update(['status' => 'confirmed']);
                        
                        // Reserve slots in the batch
                        if ($booking->batch) {
                            $slotsReserved = $booking->batch->reserveSlots($booking->party_size);
                            
                            if ($slotsReserved) {
                                Log::info('Slots Reserved Successfully', [
                                    'booking_id' => $booking->id,
                                    'batch_id' => $booking->batch_id,
                                    'party_size' => $booking->party_size,
                                    'slots_remaining' => $booking->batch->getAvailableSlots()
                                ]);
                            } else {
                                // This shouldn't happen if validation worked correctly
                                Log::error('Failed to Reserve Slots After Payment', [
                                    'booking_id' => $booking->id,
                                    'batch_id' => $booking->batch_id,
                                    'party_size' => $booking->party_size,
                                    'available_slots' => $booking->batch->getAvailableSlots()
                                ]);
                            }
                        }
                        
                        Log::info('Booking Status Updated to Confirmed', [
                            'booking_id' => $payment->booking_id
                        ]);
                    }
                }
            } else {
                Log::warning('Could not extract payment ID from webhook', [
                    'remarks' => $remarks,
                    'reference_number' => $paymentData['attributes']['reference_number'] ?? 'N/A'
                ]);
            }
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }

    /**
     * Test helper to manually confirm payment (for local development)
     * 
     * @param int $paymentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testConfirmPayment($paymentId)
    {
        \Log::info("=== TEST PAYMENT CONFIRMATION ===", ['payment_id' => $paymentId]);
        
        $payment = BookingPayment::with(['booking.batch'])->find($paymentId);
        
        if (!$payment) {
            \Log::error("Payment not found", ['payment_id' => $paymentId]);
            return redirect()->back()->with('error', 'Payment not found');
        }
        
        \Log::info("Payment found", [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'current_status' => $payment->payment_status,
            'party_size' => $payment->booking->party_size ?? 'N/A'
        ]);
        
        if ($payment->payment_status === 'paid') {
            \Log::warning("Payment already marked as paid");
            return redirect()->back()->with('info', 'Payment already confirmed');
        }
        
        // Mark payment as paid
        $payment->markAsPaid();
        \Log::info("✓ Payment marked as paid", ['payment_id' => $payment->id]);
        
        // Update booking status to confirmed
        $booking = $payment->booking;
        $booking->update(['status' => 'confirmed']);
        \Log::info("✓ Booking status updated to confirmed", ['booking_id' => $booking->id]);
        
        // Reserve slots based on party size
        if ($booking->batch) {
            $partySize = $booking->party_size;
            \Log::info("Attempting to reserve slots", [
                'batch_id' => $booking->batch->id,
                'party_size' => $partySize,
                'current_slots_taken' => $booking->batch->slots_taken,
                'capacity' => $booking->batch->capacity
            ]);
            
            $booking->batch->reserveSlots($partySize);
            $booking->batch->refresh();
            
            \Log::info("✓ Slots reserved successfully", [
                'batch_id' => $booking->batch->id,
                'new_slots_taken' => $booking->batch->slots_taken,
                'available_slots' => $booking->batch->getAvailableSlots()
            ]);
        } else {
            \Log::warning("No batch associated with booking");
        }
        
        \Log::info("=== TEST PAYMENT CONFIRMATION COMPLETE ===");
        
        return redirect()
            ->route('payment.success')
            ->with('success', "TEST: Payment #{$payment->id} confirmed, {$booking->party_size} slots reserved!");
    }
}

