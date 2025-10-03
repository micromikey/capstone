<?php

namespace App\Http\Controllers;

use App\Models\OrganizationPaymentCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrganizationPaymentController extends Controller
{
    /**
     * Show the payment setup page
     */
    public function index()
    {
        $orgId = Auth::id();
        
        // Get or create payment credentials for this organization
        $credentials = OrganizationPaymentCredential::firstOrCreate(
            ['user_id' => $orgId],
            [
                'active_gateway' => 'paymongo',
                'is_active' => true,
            ]
        );

        return view('org.payment.index', compact('credentials'));
    }

    /**
     * Update payment credentials
     */
    public function update(Request $request)
    {
        $orgId = Auth::id();

        $request->validate([
            'active_gateway' => 'required|in:paymongo,xendit',
            'paymongo_secret_key' => 'nullable|string',
            'paymongo_public_key' => 'nullable|string',
            'xendit_api_key' => 'nullable|string',
        ]);

        $credentials = OrganizationPaymentCredential::firstOrCreate(
            ['user_id' => $orgId],
            ['active_gateway' => 'paymongo', 'is_active' => true]
        );

        // Only update fields that are provided (not empty)
        if ($request->filled('paymongo_secret_key')) {
            $credentials->paymongo_secret_key = $request->input('paymongo_secret_key');
        }
        
        if ($request->filled('paymongo_public_key')) {
            $credentials->paymongo_public_key = $request->input('paymongo_public_key');
        }
        
        if ($request->filled('xendit_api_key')) {
            $credentials->xendit_api_key = $request->input('xendit_api_key');
        }

        $credentials->active_gateway = $request->input('active_gateway');
        
        // Ensure payment_method is set to automatic when using gateway
        if ($request->filled('payment_method')) {
            $credentials->payment_method = $request->input('payment_method');
        } else {
            $credentials->payment_method = 'automatic';
        }
        
        $credentials->save();

        Log::info('Organization updated payment credentials', [
            'organization_id' => $orgId,
            'active_gateway' => $credentials->active_gateway
        ]);

        return redirect()->route('org.payment.index')->with('success', 'Payment credentials updated successfully.');
    }

    /**
     * Test payment gateway connection
     */
    public function test(Request $request)
    {
        $orgId = Auth::id();
        $gateway = $request->input('gateway', 'paymongo');

        $credentials = OrganizationPaymentCredential::where('user_id', $orgId)->first();

        if (!$credentials) {
            return response()->json([
                'success' => false,
                'message' => 'No payment credentials found. Please configure your payment gateway first.'
            ]);
        }

        try {
            if ($gateway === 'paymongo') {
                if (!$credentials->hasPaymongoConfigured()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PayMongo credentials are not configured.'
                    ]);
                }

                // Test PayMongo connection (simplified test)
                // In production, you would make an actual API call to verify credentials
                return response()->json([
                    'success' => true,
                    'message' => 'PayMongo credentials are configured. (Note: Full API test not implemented in this demo)'
                ]);
            } elseif ($gateway === 'xendit') {
                if (!$credentials->hasXenditConfigured()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Xendit credentials are not configured.'
                    ]);
                }

                // Test Xendit connection (hardcoded for now)
                return response()->json([
                    'success' => true,
                    'message' => 'Xendit credentials are configured. (Note: Using hardcoded credentials for demo)'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid payment gateway selected.'
            ]);
        } catch (\Exception $e) {
            Log::error('Payment gateway test failed', [
                'organization_id' => $orgId,
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear/reset payment credentials for a specific gateway
     */
    public function clear(Request $request)
    {
        $orgId = Auth::id();
        $gateway = $request->input('gateway');

        $request->validate([
            'gateway' => 'required|in:paymongo,xendit'
        ]);

        $credentials = OrganizationPaymentCredential::where('user_id', $orgId)->first();

        if (!$credentials) {
            return redirect()->route('org.payment.index')->with('error', 'No payment credentials found.');
        }

        if ($gateway === 'paymongo') {
            $credentials->paymongo_secret_key = null;
            $credentials->paymongo_public_key = null;
        } elseif ($gateway === 'xendit') {
            $credentials->xendit_api_key = null;
        }

        $credentials->save();

        Log::info('Organization cleared payment credentials', [
            'organization_id' => $orgId,
            'gateway' => $gateway
        ]);

        return redirect()->route('org.payment.index')->with('success', ucfirst($gateway) . ' credentials cleared successfully.');
    }

    /**
     * Update manual payment settings (QR code)
     */
    public function updateManual(Request $request)
    {
        $orgId = Auth::id();

        $request->validate([
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'manual_payment_instructions' => 'nullable|string|max:1000',
        ]);

        $credentials = OrganizationPaymentCredential::firstOrCreate(
            ['user_id' => $orgId],
            ['active_gateway' => 'paymongo', 'is_active' => true]
        );

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($credentials->qr_code_path) {
                Storage::disk('public')->delete($credentials->qr_code_path);
            }

            // Store new QR code
            $path = $request->file('qr_code')->store('qr_codes', 'public');
            $credentials->qr_code_path = $path;
        }

        // Update manual payment instructions
        if ($request->filled('manual_payment_instructions')) {
            $credentials->manual_payment_instructions = $request->input('manual_payment_instructions');
        }

        // Set payment method to manual
        $credentials->payment_method = 'manual';
        $credentials->save();

        Log::info('Organization updated manual payment settings', [
            'organization_id' => $orgId,
            'has_qr_code' => !empty($credentials->qr_code_path)
        ]);

        return redirect()->route('org.payment.index')->with('success', 'Manual payment settings updated successfully!');
    }

    /**
     * Toggle payment method preference
     */
    public function togglePaymentMethod(Request $request)
    {
        $orgId = Auth::id();

        $credentials = OrganizationPaymentCredential::firstOrCreate(
            ['user_id' => $orgId],
            ['active_gateway' => 'paymongo', 'is_active' => true]
        );

        // Toggle based on checkbox value
        if ($request->has('payment_method') && $request->input('payment_method') === 'automatic') {
            $credentials->payment_method = 'automatic';
            $message = 'Payment method switched to Automatic Payment. Hikers will use the payment gateway.';
        } else {
            $credentials->payment_method = 'manual';
            $message = 'Payment method switched to Manual Payment. Hikers will see your QR code.';
        }

        $credentials->save();

        Log::info('Organization toggled payment method', [
            'organization_id' => $orgId,
            'payment_method' => $credentials->payment_method
        ]);

        return redirect()->route('org.payment.index')->with('success', $message);
    }
}
