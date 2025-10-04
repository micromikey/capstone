<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Slip - Booking #{{ $booking->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            padding: 15px;
        }

        .container {
            border: 2px solid #000;
            padding: 0;
            max-width: 100%;
        }

        .header {
            border-bottom: 2px solid #000;
            padding: 12px 15px;
            text-align: center;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            font-size: 9px;
            margin-top: 2px;
        }

        .logo-section {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .logo-left {
            display: table-cell;
            width: 25%;
            text-align: left;
            vertical-align: middle;
            font-size: 18px;
            font-weight: bold;
        }

        .logo-left img {
            height: 40px;
            width: auto;
            max-width: 100px;
        }

        .logo-center {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: middle;
        }

        .logo-right {
            display: table-cell;
            width: 25%;
            text-align: right;
            vertical-align: middle;
            font-size: 9px;
        }

        .trip-info {
            padding: 10px 15px;
            border-bottom: 1px solid #000;
        }

        .trip-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .trip-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .trip-col.right {
            text-align: right;
        }

        .label {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .value {
            font-size: 11px;
            font-weight: bold;
        }

        .booking-info {
            padding: 8px 15px;
            border-bottom: 1px solid #000;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 8px;
            font-size: 9px;
            border: 1px solid #000;
        }

        .info-table .label-cell {
            font-weight: bold;
            width: 35%;
            background-color: #f5f5f5;
        }

        .info-table .value-cell {
            width: 65%;
        }

        .passenger-section {
            padding: 10px 15px;
            border-bottom: 1px solid #000;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .passenger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .passenger-table th,
        .passenger-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-size: 9px;
        }

        .passenger-table th {
            font-weight: bold;
            background-color: #e5e5e5;
        }

        .payment-section {
            padding: 8px 15px;
            border-bottom: 1px solid #000;
        }

        .payment-grid {
            display: table;
            width: 100%;
        }

        .payment-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 10px;
        }

        .payment-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            border-left: 1px solid #000;
            padding-left: 10px;
        }

        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .payment-label {
            display: table-cell;
            font-size: 8px;
            padding: 2px 0;
        }

        .payment-value {
            display: table-cell;
            text-align: right;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 0;
        }

        .total-row {
            border-top: 2px solid #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        .total-row .payment-value {
            font-size: 11px;
        }

        .qr-code {
            text-align: center;
            padding: 5px;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #000;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
        }

        .notes-section {
            padding: 8px 15px;
            border-bottom: 1px solid #000;
        }

        .note-item {
            font-size: 8px;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .footer {
            padding: 8px 15px;
            text-align: center;
            font-size: 8px;
        }

        .footer-note {
            margin-bottom: 3px;
            font-weight: bold;
        }

        .print-info {
            margin-top: 5px;
            font-size: 7px;
            color: #666;
        }

        .status-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
        }

        .inline-label {
            font-weight: bold;
            font-size: 9px;
        }
    </style>
</head>
<body>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo-left">
                    @if(file_exists(public_path('img/icon1.png')))
                        <img src="{{ public_path('img/icon1.png') }}" alt="HikeThere">
                    @else
                        <div style="font-size: 24px; font-weight: bold; color: #17a2b8;">🏔️</div>
                    @endif
                </div>
                <div class="logo-center">
                    <div class="header-title">BOOKING RECEIPT</div>
                    <div class="header-subtitle">HikeThere - Your Adventure Companion</div>
                </div>
                <div class="logo-right">
                    Booking ID<br>
                    <strong>{{ $booking->id }}</strong>
                </div>
            </div>
        </div>

        <!-- Trip Information -->
        <div class="trip-info">
            <div class="trip-row">
                <div class="trip-col">
                    <div class="label">Trail/Destination</div>
                    <div class="value">{{ strtoupper($booking->trail?->trail_name ?? 'N/A') }}</div>
                </div>
                <div class="trip-col right">
                    <div class="label">Hiking Date & Time</div>
                    <div class="value">
                        {{ $booking->date ? \Carbon\Carbon::parse($booking->date)->format('d-M-Y (D)') : 'TBD' }}
                        @if($booking->batch && $booking->batch->starts_at)
                            <br><span style="font-size: 9px;">{{ $booking->batch->starts_at->format('g:i A') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="trip-row">
                <div class="trip-col">
                    <div class="label">Organization</div>
                    <div class="value">{{ strtoupper($booking->trail?->user?->organization_name ?? 'N/A') }}</div>
                </div>
                <div class="trip-col right">
                    <div class="label">Status</div>
                    <div class="value">
                        <span class="status-box">{{ strtoupper($booking->status) }}</span>
                    </div>
                </div>
            </div>
            <div class="trip-row" style="margin-bottom: 0;">
                <div class="trip-col">
                    <div class="label">Party Size</div>
                    <div class="value">{{ $booking->party_size }} {{ $booking->party_size == 1 ? 'PERSON' : 'PEOPLE' }}</div>
                </div>
                <div class="trip-col right">
                    <div class="label">Payment Status</div>
                    <div class="value">
                        <span class="status-box">{{ strtoupper($booking->payment_status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="booking-info">
            <table class="info-table">
                <tr>
                    <td class="label-cell">PNR/Booking Number:</td>
                    <td class="value-cell">{{ $booking->id }}</td>
                    <td class="label-cell">Payment Status:</td>
                    <td class="value-cell">{{ strtoupper($booking->payment_status) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Booking Date:</td>
                    <td class="value-cell">{{ $booking->created_at->format('d-M-Y H:i') }}</td>
                    <td class="label-cell">
                        @if($booking->payment_verified_at)
                        Payment Verified:
                        @else
                        Transaction No:
                        @endif
                    </td>
                    <td class="value-cell">
                        @if($booking->payment_verified_at)
                        {{ \Carbon\Carbon::parse($booking->payment_verified_at)->format('d-M-Y H:i') }}
                        @else
                        {{ $booking->transaction_number ?? 'N/A' }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Hiker Details -->
        <div class="passenger-section">
            <div class="section-title">Hiker Details:</div>
            <table class="passenger-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Name</th>
                        <th style="width: 30%;">Email</th>
                        <th style="width: 20%;">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>{{ strtoupper($booking->user->name) }}</td>
                        <td>{{ $booking->user->email }}</td>
                        <td>{{ $booking->user->phone ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
            @if($booking->notes)
            <div style="margin-top: 6px; font-size: 8px;">
                <span class="inline-label">Additional Notes:</span> {{ $booking->notes }}
            </div>
            @endif
        </div>

        <!-- Payment Details -->
        <div class="payment-section">
            <div class="section-title">Payment Details:</div>
            <div class="payment-grid">
                <div class="payment-left">
                    @php
                        // Get amount from payment relationship first, then fall back to booking price_cents
                        $amount = 0;
                        if ($booking->payment && $booking->payment->amount) {
                            $amount = $booking->payment->amount;
                        } elseif ($booking->price_cents) {
                            $amount = $booking->price_cents / 100; // Convert cents to pesos
                        } elseif (method_exists($booking, 'getAmountInPesos')) {
                            $amount = $booking->getAmountInPesos();
                        }
                        
                        // Calculate breakdown (you can adjust these calculations based on actual business logic)
                        $baseFare = $amount;
                        $serviceCharge = 0; // No service charge for now
                        $processingFee = 0; // No processing fee for now
                        $totalAmount = $baseFare + $serviceCharge + $processingFee;
                    @endphp
                    
                    @if($amount > 0)
                        <div class="payment-row">
                            <div class="payment-label">Base Fare:</div>
                            <div class="payment-value">₱{{ number_format($baseFare, 2) }}</div>
                        </div>
                        <div class="payment-row">
                            <div class="payment-label">Service Charge:</div>
                            <div class="payment-value">₱{{ number_format($serviceCharge, 2) }}</div>
                        </div>
                        <div class="payment-row">
                            <div class="payment-label">Processing Fee:</div>
                            <div class="payment-value">₱{{ number_format($processingFee, 2) }}</div>
                        </div>
                        <div class="payment-row total-row">
                            <div class="payment-label">Total Fare:</div>
                            <div class="payment-value">₱{{ number_format($totalAmount, 2) }}</div>
                        </div>
                        <div style="margin-top: 6px; font-size: 7px; font-style: italic;">
                            Payment Method: 
                            @if($booking->usesManualPayment())
                                Manual Payment (QR Code)
                            @elseif($booking->payment)
                                Online Payment (PayMongo)
                            @else
                                {{ $booking->payment_method_used ?? 'Not specified' }}
                            @endif
                            @if($booking->transaction_number)
                                <br>Transaction: {{ $booking->transaction_number }}
                            @endif
                            @if($booking->payment && $booking->payment->paid_at)
                                <br>Paid: {{ $booking->payment->paid_at->format('d-M-Y H:i') }}
                            @endif
                        </div>
                    @else
                        <div class="payment-row">
                            <div class="payment-label">Base Fare:</div>
                            <div class="payment-value">---</div>
                        </div>
                        <div class="payment-row">
                            <div class="payment-label">Service Charge:</div>
                            <div class="payment-value">---</div>
                        </div>
                        <div class="payment-row">
                            <div class="payment-label">Processing Fee:</div>
                            <div class="payment-value">---</div>
                        </div>
                        <div class="payment-row total-row">
                            <div class="payment-label">Total Fare:</div>
                            <div class="payment-value">---</div>
                        </div>
                        <div style="margin-top: 6px; font-size: 7px; font-style: italic; color: #999;">
                            Payment information not yet recorded
                        </div>
                    @endif
                </div>
                <div class="payment-right">
                    <div class="qr-code">
                        <div style="font-size: 8px; font-weight: bold; margin-bottom: 4px;">VERIFICATION CODE</div>
                        <div style="text-align: center; padding: 10px; border: 1px solid #000; background: #fff;">
                            <div style="font-family: 'Courier New', monospace; font-size: 11px; font-weight: bold; letter-spacing: 2px; line-height: 1.6;">
                                {{ strtoupper(substr(md5($booking->id . $booking->created_at), 0, 4)) }}-{{ strtoupper(substr(md5($booking->id . $booking->created_at), 4, 4)) }}<br>
                                {{ strtoupper(substr(md5($booking->id . $booking->created_at), 8, 4)) }}-{{ strtoupper(substr(md5($booking->id . $booking->created_at), 12, 4)) }}
                            </div>
                        </div>
                        <div style="font-size: 7px; margin-top: 4px;">Verification Code</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="notes-section">
            <div class="section-title">Important Instructions:</div>
            <div class="note-item">• Please present this reservation slip on the day of your hike along with a valid government-issued ID.</div>
            <div class="note-item">• Arrive at the meeting point at least 30 minutes before the scheduled departure time.</div>
            <div class="note-item">• This reservation is non-transferable. The name on the booking must match the ID presented.</div>
            <div class="note-item">• Check weather conditions before your hike. Organizers reserve the right to cancel/reschedule due to safety concerns.</div>
            <div class="note-item">• Follow all trail regulations and guide instructions during the hike.</div>
            <div class="note-item">• Prescribed ID proof is required while hiking. SMS/VRM/ERS otherwise will be treated as without ticket and penalized.</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-note">
                This is a computer-generated reservation slip and does not require a signature.
            </div>
            <div class="footer-note">
                For inquiries or support, please contact the trail organization or HikeThere customer service.
            </div>
            <div class="print-info">
                Generated: {{ now()->format('d-M-Y H:i:s') }} | Booking Reference: {{ $booking->id }} | HikeThere © {{ now()->year }}
            </div>
        </div>
    </div>
</body>
</html>
