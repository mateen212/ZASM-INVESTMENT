<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PlaidService;

class PlaidController extends Controller
{
    protected $plaid;

    public function __construct(PlaidService $plaid)
    {
        $this->plaid = $plaid;
    }

    public function createLinkToken(Request $request)
    {
        $userId = auth('admin')->user()->id;

        // dd($userId);
        $response = $this->plaid->createLinkToken($userId);
        return response()->json($response);
    }

    public function exchangeToken(Request $request)
    {
        $publicToken = $request->public_token;

        if (empty($publicToken) || !is_string($publicToken)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or missing public token',
            ], 400);
        }

        $response = $this->plaid->exchangePublicToken($publicToken);

        // Debugging: Log or inspect response
        \Log::info('Exchange Token Response:', $response);

        if (isset($response['access_token'])) {
            return response()->json([
                'success' => true,
                'access_token' => $response['access_token'],
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $response['error_message'] ?? 'Failed to exchange public token',
        ], 400);
    }

    public function getAccounts(Request $request)
    {
        $response = $this->plaid->getAccounts($request->access_token);
        return response()->json($response);
    }
    public function createPayment(Request $request)
    {
        $accessToken = $request->access_token;
        $accountId = $request->account_id;
        $amount = $request->amount ?: 100; // Example: $1.00, adjust as needed

        // Validate inputs
        if (empty($accessToken) || !is_string($accessToken)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or missing access token',
            ], 400);
        }

        if (empty($accountId) || !is_string($accountId)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or missing account ID',
            ], 400);
        }

        // Create Stripe bank account token
        $response = $this->plaid->createStripeBankAccountToken($accessToken, $accountId);

        // Debugging: Remove dd() in production
        // dd($response);

        if (isset($response['stripe_bank_account_token'])) {
            // Process payment with Stripe
            $paymentResponse = $this->plaid->createStripePayment($response['stripe_bank_account_token'], $amount);
            return response()->json($paymentResponse);
        }

        return response()->json([
            'success' => false,
            'error' => $response['error_message'] ?? 'Failed to create bank account token',
        ], 400);
    }
}