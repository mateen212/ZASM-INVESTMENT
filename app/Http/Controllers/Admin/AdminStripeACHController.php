<?php

namespace App\Http\Controllers\Admin;


use App\Services\StripeACHService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealAchSetting;

class AdminStripeACHController extends Controller
{
    protected $stripeService;

    public function __construct(StripeACHService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function initiateACH(Request $request, Deal $deal)
    {
        
        $validate = Validator::make($request->all(), [
            'achForm.name' => 'required|string',
            'achForm.routing_number' => 'required|string',
            'achForm.account_number' => 'required|string',
            'achForm.account_type' => 'required|string',
        ]);
        
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        
        $formData = $validate->validated()['achForm']; // ✅ extract validated input
        $data = array_merge($formData, [
            'amount' => '32 , 45',
        ]); 

        $result = $this->stripeService->createACHPaymentMethod($data, $deal);

        $dealAchSetting = DealAchSetting::updateOrCreate([
            'deal_id' => $deal->id,
        ], [
            'verify_confirmation' => 'completed',
        ]);


        return response()->json(['status' => 'pending', 'data' => $result]);
    }


    public function verifyMicroDeposits(Request $request)
    {
        $request->validate([
            'amounts' => 'required|array|size:2',
            'bank_id' => 'required|string',
            'deal_id' => 'required|exists:deals,id', // make sure this is included in the request
        ]);

        $deal = Deal::findOrFail($request->deal_id);
        $entity = auth()->user() ?? auth('admin')->user();
        $customerId = $entity->stripe_customer_id;

        $result = $this->stripeService->verifyDeposits($customerId, $request->bank_id, $request->amounts, $deal);

        return response()->json($result);
    }
    public function againOnboarding(Request $request, Deal $deal)
    {
        $entity = auth()->user() ?? auth('admin')->user();
        $accountId = $deal->achsettings->stripe_account_id;
        $onboardingUrl = $this->stripeService->generateStripeOnboardingLink($accountId, $entity, $deal);
        return response()->json($onboardingUrl);
    }

}

