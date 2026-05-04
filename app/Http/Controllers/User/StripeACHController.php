<?php

namespace App\Http\Controllers\User;


use App\Services\StripeACHService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\User; // Assuming User model is used for admin and investor
use Illuminate\Support\Facades\Http;

class StripeACHController extends Controller
{
    protected $stripeService;

    public function __construct(StripeACHService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function initiateACH(Request $request, $id)
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
        $investment = Investment::find($id);
        $offering = $investment->offering;
        if (!$offering) {
            return response()->json(['error' => 'Offering not found for this investment'], 404);
        }

        if (!$investment) {
            return response()->json(['error' => 'Investment not found'], 404);
        }
        $data = $validate->validated()['achForm']; // ✅ extract validated input
        $result = $this->stripeService->createACHPaymentMethod($data, null,$investment, $offering);

        return response()->json(['status' => 'pending', 'data' => $result]);
    }


    public function verifyMicroDeposits(Request $request)
    {
        $request->validate([
            'amounts' => 'required|array|size:2',
            'bank_id' => 'required|string',
        ]);

        $customerId = auth()->user()->stripe_customer_id;

        $result = $this->stripeService->verifyDeposits($customerId, $request->bank_id, $request->amounts);
        return response()->json($result);
    }
    


}
