<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;

class PlaidService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;
    protected $stripe;

    public function __construct()
    {
        $this->clientId = config('plaid.client_id');
        $this->secret = config('plaid.secret');
        $this->baseUrl = config('plaid.base_url');
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    private function post($endpoint, $data)
    {
        $data['client_id'] = $this->clientId;
        $data['secret'] = $this->secret;
        return Http::post("{$this->baseUrl}/$endpoint", $data)->json();
    }

    public function createLinkToken($userId)
    {
        return $this->post('link/token/create', [
            'user' => ['client_user_id' => (string) $userId],
            'client_name' => 'My App',
            'products' => ['auth'], // Ensure 'auth' is included for bank accounts
            'country_codes' => ['US'],
            'language' => 'en',
        ]);
    }

    public function exchangePublicToken($publicToken)
    {
        return $this->post('item/public_token/exchange', [
            'public_token' => $publicToken,
        ]);
    }

    public function getAccounts($accessToken)
    {
        return $this->post('accounts/get', [
            'access_token' => $accessToken,
        ]);
    }

    public function createStripeBankAccountToken($accessToken, $accountId)
    {
        return $this->post('processor/stripe/bank_account_token/create', [
            'access_token' => $accessToken,
            'account_id' => $accountId,
        ]);
    }

    public function createStripePayment($bankAccountToken, $amount, $currency = 'usd')
    {
        try {
            // Create a Stripe customer (if not already created)
            $customer = $this->stripe->customers->create([
                'description' => 'Customer for Plaid+Stripe integration',
            ]);

            // Attach bank account to customer
            $bankAccount = $this->stripe->customers->createSource($customer->id, [
                'source' => $bankAccountToken,
            ]);

            // Create a charge (for ACH, ensure bank account is verified as per Stripe's requirements)
            $charge = $this->stripe->charges->create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => $currency,
                'customer' => $customer->id,
                'source' => $bankAccount->id,
                'description' => 'Add funds via Plaid+Stripe',
            ]);

            return ['success' => true, 'charge' => $charge];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
}