<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Gateway;

class StripeACHService
{
    protected $stripeSecret;

    public function __construct()
    {
        $stripeAccSecret = $this->getStripeConfig()['secret_key'] ?? null;
        if (!$stripeAccSecret) {
            throw new \Exception('Stripe ACH gateway secret key not found in configuration.');
        }
        $this->stripeSecret = $stripeAccSecret['value'];
    }

    protected function getStripeConfig()
    {
        $stripeConfig = Gateway::where('code', '114')->first();
        if (!$stripeConfig) {
            throw new \Exception('Stripe ACH gateway configuration not found.');
        }
        return json_decode($stripeConfig->gateway_parameters, true);
    }

    public function createACHPaymentMethod($data, $deal = null, $investment = null, $offering = null)
    {
        $entity = auth()->user() ?? auth('admin')->user();

        // 1. Create Stripe Customer if not exists
        $customerId = $this->getStripeField($entity, 'stripe_customer_id', $deal) ?: $this->createStripeCustomer($entity, $deal);

        $accountId = $this->getStripeField($entity, 'stripe_account_id', $deal) ?: $this->createConnectedAccount($entity, $deal);
        // 2. Create bank token for customer
        $responseCustomerToken = $this->createBankToken($data);

        if (!$responseCustomerToken->successful()) {
            throw new \Exception('Failed to create bank token for customer: ' . json_encode($responseCustomerToken->json()));
        }
        $customerToken = $responseCustomerToken->json()['id'];

        // 3. Attach bank to customer
        $attach = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->post("https://api.stripe.com/v1/customers/{$customerId}/sources", [
                'source' => $customerToken,
            ]);

        if (!$attach->successful()) {
            $error = $attach->json()['error']['code'] ?? null;

            if ($error === 'bank_account_exists') {
                // Stripe won't return the existing bank ID in this response.
                // You need to list bank accounts and pick the most recent or first.
                $listResponse = Http::withBasicAuth($this->stripeSecret, '')
                    ->get("https://api.stripe.com/v1/customers/{$customerId}/sources", [
                        'object' => 'bank_account',
                    ]);

                if (!$listResponse->successful()) {
                    throw new \Exception('Failed to retrieve bank accounts: ' . json_encode($listResponse->json()));
                }

                $banks = $listResponse->json()['data'];
                if (empty($banks)) {
                    throw new \Exception('No bank accounts found, but Stripe says one exists.');
                }

                // Optionally filter or choose based on bank name or last4 digits
                $bankId = $banks[0]['id']; // or search the array for a match
            } else {
                throw new \Exception('Failed to attach bank account to customer: ' . json_encode($attach->json()));
            }
        } else {
            // Successfully attached, get bank ID directly
            $bankId = $attach->json()['id'];
        }
        $achPaymentMethodId = $bankId;

        if ($entity instanceof \App\Models\Admin) {
            $this->saveStripeField($entity, 'ach_payment_method_id', $bankId, $deal);
        } else {
            $this->saveStripeField($entity, 'ach_payment_method_id', $bankId);
        }


        // 4. Create connected account if not exists
        if (!$this->getStripeField($entity, 'stripe_account_id', $deal)) {
            $this->createConnectedAccount($entity, $deal);
        }

        // 5. Retrieve connected account
        // if ($entity->stripe_account_id) {
        //     $this->retriveConnectedAccount($entity);
        // }
        // if ($entity->stripe_account_id) {
        //     $this->deleteConnectedAccount($entity);
        // }
        // 5. Create a new bank token for connected account (cannot reuse the same token)
        $responseAccountToken = $this->createBankToken($data);
        if (!$responseAccountToken->successful()) {
            throw new \Exception('Failed to create bank token for connected account: ' . json_encode($responseAccountToken->json()));
        }
        $accountToken = $responseAccountToken->json()['id'];
        if ($entity instanceof \App\Models\Admin) {
            $dealAccountId = $deal->achsettings->stripe_account_id;
            $dealCustomerId = $deal->achsettings->stripe_customer_id;
        } else {
            $dealAccountId = $entity->stripe_account_id;
            $dealCustomerId = $entity->stripe_customer_id;
        }
        // dd($dealAccountId, $accountToken);
        // 6. Attach bank to connected account
        $bankAttachResponse = $this->attachBankToConnectedAccount($dealAccountId, $accountToken);
        // dd($bankAttachResponse);

        // Test micro-deposit verification (uncomment to test)
        $microPaymentTest = $this->verifyDeposits($dealCustomerId, $bankId, $dealAccountId);
        $verifyResult = $microPaymentTest;

        if ($verifyResult === 'already_verified' || $verifyResult === 'just_verified') {
            if ($entity instanceof \App\Models\Admin) {
                $this->saveStripeField($entity, 'is_verified', true, $deal);
            } else {
                $this->saveStripeField($entity, 'ach_bank_status', true, $deal);
            }
        }
        $status = $this->checkBankVerificationStatus($dealCustomerId, $bankId);

        if ($entity instanceof \App\Models\User) {
            $rawAmount = $investment->getRawOriginal('investment_amount'); // bypass MoneyCast
            $amountInCents = (int) round($rawAmount * 100);

            if ($amountInCents < 50) {
                throw new \Exception("Minimum investment amount must be at least $0.50");
            }

            $options = [
                'idempotency_key' => 'investor_charge_' . $entity->id . '_' . time(),
            ];
            $this->chargeInvestor($amountInCents, $options = [], $entity);
            $AdminAccountId = $offering->deal->achsettings->stripe_account_id;
            //when payment intent is created then transfer funds to connected account
            // dd($AdminAccountId, $amountInCents);
            $this->transferToConnectedAccount($amountInCents, $AdminAccountId);
        }
        // 7. Generate onboarding link
        $onboardingUrl = $this->generateStripeOnboardingLink($accountId, $entity, $deal);

        return [
            'customer_bank' => $attach->json(),
            'onboarding_url' => $onboardingUrl,
            'status' => $status,

        ];
    }

    private function createBankToken($data)
    {
        return Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/tokens', [
                'bank_account[country]' => 'US',
                'bank_account[currency]' => 'usd',
                'bank_account[account_holder_name]' => $data['name'],
                'bank_account[account_holder_type]' => 'individual',
                'bank_account[routing_number]' => $data['routing_number'],
                'bank_account[account_number]' => $data['account_number'],
            ]);
    }




    public function createStripeCustomer($entity, $deal)
    {
        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->asForm()
            ->post('https://api.stripe.com/v1/customers', [
                'name' => $entity->name,
                'email' => $entity->email,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create Stripe customer');
        }

        $customer = $response->json();
        $this->saveStripeField($entity, 'stripe_customer_id', $customer['id'], $deal);


        return $customer['id'];
    }


    public function verifyDeposits($dealCustomerId, $bankId, $dealAccountId)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->post("https://api.stripe.com/v1/customers/{$dealCustomerId}/sources/{$bankId}/verify", [
                'amounts[0]' => 32,
                'amounts[1]' => 45,
            ]);

        $json = $response->json();

        // ✅ Skip if already verified
        if (isset($json['error']['message']) && $json['error']['message'] === 'This bank account has already been verified.') {
            // Move on to the next step
            return 'already_verified';
        }

        if (!$response->successful()) {
            throw new \Exception('Failed to verify bank account: ' . json_encode($json));
        }

        return 'just_verified';
    }



    public function createConnectedAccount($entity, $deal)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/accounts', [
                'type' => 'express',
                'country' => 'US',
                'email' => $entity->email,
                'capabilities[transfers][requested]' => 'true', // ✅ FIXED
                'business_type' => 'individual',
                'individual[first_name]' => $entity->first_name,
                'individual[last_name]' => $entity->last_name,
                'business_profile[url]' => config('app.url'),
                // 'tos_acceptance[date]' => time(),
                // 'tos_acceptance[ip]' => request()->ip(),
            ]);
        if (!$response->successful()) {
            throw new \Exception('Failed to create connected account: ' . json_encode($response->json()));
        }

        $account = $response->json();
        // dd($entity->stripe_account_id);
        $this->saveStripeField($entity, 'stripe_account_id', $account['id'], $deal);


        return $account['id'];
    }

    public function retriveConnectedAccount($entity)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->get('https://api.stripe.com/v1/accounts');

        if (!$response->successful()) {
            throw new \Exception('Failed to create connected account: ' . json_encode($response->json()));
        }

        $account = $response->json();
        return $account['id'];
    }
    public function deleteConnectedAccount($entity)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->delete("https://api.stripe.com/v1/accounts/{$entity->stripe_account_id}");

        if (!$response->successful()) {
            throw new \Exception('Failed to delete connected account: ' . json_encode($response->json()));
        }

        return $response->json();
    }
    public function attachBankToConnectedAccount($dealAccountId, $accountToken)
    {
        // dd($accountId);
        if (empty($dealAccountId)) {
            throw new \Exception('Invalid or missing Stripe account ID.');
        }

        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->withHeaders([
                'Stripe-Account' => $dealAccountId,
            ])
            ->post("https://api.stripe.com/v1/accounts/{$dealAccountId}/external_accounts", [
                'external_account' => $accountToken,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to attach bank: ' . json_encode($response->json()) . ' | Check OAuth scopes, account status, and Stripe-Account header in Stripe Dashboard.');
        }

        return $response->json();
    }
    public function chargeInvestor($amountInCents, $options = [], $entity)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->withHeaders([
                'Idempotency-Key' => $options['idempotency_key'] ?? null,
            ])
            ->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => $amountInCents, // e.g., 1000 for $10.00
                'currency' => 'usd',
                'customer' => $entity->stripe_customer_id,
                'payment_method' => $entity->ach_payment_method_id, // bank account ID
                'off_session' => 'true',
                'confirm' => 'true',
                'mandate_data' => [
                    'customer_acceptance' => [
                        'type' => 'online',
                        'online' => [
                            'ip_address' => request()->ip(), // customer's IP
                            'user_agent' => request()->header('User-Agent'), // browser UA
                        ],
                    ],
                ],
                'payment_method_types' => ['us_bank_account'],
            ]);
        // dd($response->json());
        $paymentIntentId = $response->json()['id'] ?? null;

        if (!$paymentIntentId) {
            throw new \Exception('Payment Intent ID not found in response: ' . json_encode($response->json()));
        }
        if (!$response->successful()) {
            throw new \Exception('Failed to charge investor: ' . json_encode($response->json()));
        }

        return $response->json();
    }

    public function transferToConnectedAccount($amountInCents, $AdminAccountId)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/transfers', [
                'amount' => $amountInCents,
                'currency' => 'usd',
                'destination' => $AdminAccountId, // connected account ID
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to transfer funds to connected account: ' . json_encode($response->json()));
        }

        // Since Stripe transfer object doesn't have a 'status', just log success
        $transfer = $response->json();

        Log::info('Transfer successful', [
            'transfer_id' => $transfer['id'],
            'amount' => $transfer['amount'],
            'destination' => $transfer['destination'],
        ]);

        return $transfer;
    }

    public function generateStripeOnboardingLink($accountId, $entity, $deal)
    {
        if ($entity instanceof \App\Models\Admin) {
            if (auth('admin')->user()->hasRole('partner')) {
                $prefix = 'partner';
            } else {
                $prefix = 'admin';
            }
            $data = [
                'account' => $accountId,
                'refresh_url' => route($prefix . '.stripe.onboarding.refresh'),
                'return_url' => route($prefix . '.deals.edit', ['deal' => $deal->id]),
                'type' => 'account_onboarding',
            ];
        } else {
            $data = [
                'account' => $accountId,
                'refresh_url' => route('user.stripe.onboarding.refresh'),
                'return_url' => route('user.stripe.success'),
                'type' => 'account_onboarding',
            ];
        }

        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->post('https://api.stripe.com/v1/account_links', $data);
        // dd($response->json());
        if (!$response->successful()) {
            throw new \Exception('Failed to generate onboarding link: ' . json_encode($response->json()));
        }

        return $response->json()['url'];
    }
    public function checkOnboardingStatus($accountId)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->get("https://api.stripe.com/v1/accounts/{$accountId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve account status: ' . json_encode($response->json()));
        }

        $account = $response->json();
        $isOnboardingComplete = empty($account['requirements']['currently_due']) && empty($account['requirements']['past_due']);

        return [
            'account_id' => $account['id'],
            'onboarding_complete' => $isOnboardingComplete,
            'requirements' => $account['requirements'],
            'disabled_reason' => $account['requirements']['disabled_reason'] ?? null,
        ];
    }

    public function retrieveBankAccount($customerId, $bankId)
    {
        // dd($customerId, $bankId);
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->get("https://api.stripe.com/v1/customers/{$customerId}/sources/{$bankId}");
        dd($response->json());
        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve bank account: ' . json_encode($response->json()));
        }

        return $response->json();
    }
    public function deleteBankAccount($customerId, $bankId)
    {
        $response = Http::asForm()
            ->withBasicAuth($this->stripeSecret, '')
            ->delete("https://api.stripe.com/v1/customers/{$customerId}/sources/{$bankId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to delete bank account: ' . json_encode($response->json()));
        }

        return $response->json();
    }

    public function checkBankAccount(Request $request)
    {
        $user = auth()->user();

        if (!$user->stripe_customer_id) {
            return response()->json([
                'has_bank_account' => false,
                'is_verified' => false,
                'message' => 'No Stripe customer ID found. Please link a bank account.',
                'stripe_customer_id' => null,
                'stripe_connect_account_id' => $user->stripe_connect_account_id,
                'bank_details' => null,
            ]);
        }

        try {
            // Retrieve bank accounts associated with the customer
            $bankAccounts = Http::withBasicAuth($this->stripeSecret, '')
                ->get("https://api.stripe.com/v1/customers/{$user->stripe_customer_id}/sources", [
                    'object' => 'bank_account',
                ]);

            if (!$bankAccounts->successful()) {
                return response()->json([
                    'has_bank_account' => false,
                    'is_verified' => false,
                    'message' => 'Failed to retrieve bank account details.',
                    'stripe_customer_id' => $user->stripe_customer_id,
                    'stripe_connect_account_id' => $user->stripe_connect_account_id,
                    'bank_details' => null,
                ], 500);
            }

            $banks = $bankAccounts->json()['data'];
            $hasBankAccount = !empty($banks);
            $verifiedBank = collect($banks)->firstWhere('status', 'verified');
            $unverifiedBank = collect($banks)->firstWhere('status', 'new');

            if ($verifiedBank) {
                return response()->json([
                    'has_bank_account' => true,
                    'is_verified' => true,
                    'message' => 'Verified bank account found.',
                    'stripe_customer_id' => $user->stripe_customer_id,
                    'stripe_connect_account_id' => $user->stripe_connect_account_id,
                    'bank_details' => [
                        'bank_id' => $verifiedBank['id'],
                        'last4' => $verifiedBank['last4'],
                        'bank_name' => $verifiedBank['bank_name'],
                        'account_holder_name' => $verifiedBank['account_holder_name'],
                        'account_type' => $verifiedBank['account_type'],
                        'status' => $verifiedBank['status'],
                    ],
                ]);
            } elseif ($unverifiedBank) {
                return response()->json([
                    'has_bank_account' => true,
                    'is_verified' => false,
                    'message' => 'Unverified bank account found. Please verify micro-deposits.',
                    'stripe_customer_id' => $user->stripe_customer_id,
                    'stripe_connect_account_id' => $user->stripe_connect_account_id,
                    'bank_details' => [
                        'bank_id' => $unverifiedBank['id'],
                        'last4' => $unverifiedBank['last4'],
                        'bank_name' => $unverifiedBank['bank_name'],
                        'account_holder_name' => $unverifiedBank['account_holder_name'],
                        'account_type' => $unverifiedBank['account_type'],
                        'status' => $unverifiedBank['status'],
                    ],
                ]);
            }

            return response()->json([
                'has_bank_account' => false,
                'is_verified' => false,
                'message' => 'No bank account linked.',
                'stripe_customer_id' => $user->stripe_customer_id,
                'stripe_connect_account_id' => $user->stripe_connect_account_id,
                'bank_details' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'has_bank_account' => false,
                'is_verified' => false,
                'message' => 'Error checking bank account: ' . $e->getMessage(),
                'stripe_customer_id' => $user->stripe_customer_id,
                'stripe_connect_account_id' => $user->stripe_connect_account_id,
                'bank_details' => null,
            ], 500);
        }
    }
    private function saveStripeField($entity, $field, $value, $deal = null)
    {
        if ($entity instanceof \App\Models\Admin) {
            \DB::table('deal_ach_settings')->updateOrInsert(
                ['deal_id' => $deal?->id],
                [$field => $value]
            );
        } else {
            $entity->$field = $value;
            $entity->save();
        }
    }
    private function getStripeField($entity, $field, $deal)
    {
        if ($entity instanceof \App\Models\Admin) {
            return \DB::table('deal_ach_settings')->where('deal_id', $deal->id)->value($field);
        }
        return $entity->$field;
    }

    public function checkBankVerificationStatus($dealCustomerId, $bankId)
    {
        $response = Http::withBasicAuth($this->stripeSecret, '')
            ->get("https://api.stripe.com/v1/customers/{$dealCustomerId}/sources/{$bankId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve bank account: ' . json_encode($response->json()));
        }

        $data = $response->json();
        $status = $data['status'] ?? 'unknown';
        // dd($response->json());
        return $status; // possible values: 'new', 'verified', 'verification_failed', etc.
    }

}
