<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? null;

        if ($eventType === 'customer.source.verified') {
            $data = $payload['data']['object'];
            $customerId = $data['customer'];
            $bankId = $data['id'];

            // 🔁 Update your database or status flags here
            // Example: mark the bank account as verified for that customer
            \Log::info("Bank account verified for customer: {$customerId}, bank: {$bankId}");

            // Example: Update your ACHSettings model
            DB::table('deal_ach_settings')
                ->where('stripe_customer_id', $customerId)
                ->update(['is_verified' => true]);
        }

        return response()->json(['status' => 'success']);
    }
}
