<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Offering;
use Illuminate\Support\Facades\Validator;
use App\Models\InvestorProfile;
use App\Models\Investor;
use App\Models\Investment;
use App\Models\QuestionnaireAddress;
use App\Models\InvestmentQuestionnaire;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\Utills;
use App\Models\ESignTemplateRecipient;



use App\Models\Deal;

class OfferingDetailController extends Controller
{
    use Utills;

    public function offering($offering)
    {
        $pageTitle = 'Offering Detail';

        $offering = Offering::where('uuid', $offering)->first();

        if (!$offering) {
            return redirect()->route('user.deals.mydeals')->with('error', 'Offering not found');
        }
        // Load the deal relationship and assets with their media
        $offering->load(['deal', 'assets.assetMedia']);
        $user = auth()->user();

        $investor = $user ? $user->investor()->with('investor_profiles')->first() : null;

        // Return the view with the necessary data
        return view('Template::user.deals.offerings.offering', compact(
            'offering',
            'pageTitle',
            'investor'
        ));
    }

    public function investNow(Offering $offering)
    {
        $pageTitle = 'Offering Detail';
        $offering->load('deal', 'funding_info', 'manageoffering', 'classes', 'investment_questionnaires', 'deal');
        $user = auth()->user();

        if ($offering->funding_info) {
            $fundingMethods = collect(json_decode($offering->funding_info->funding_methods, true))
                ->filter(function ($value) {
                    return $value === true;
                })
                ->keys()
                ->toArray();
        } else {
            $fundingMethods = [];
        }

        $investor = $user->investor()->with('investor_profiles')->first();

        // Fetch the latest investment for this investor and offering
        $investment = Investment::where('offering_id', $offering->id)
            ->where('investor_id', $investor->id)
            ->where('investment_status', 'document_started')
            ->latest()
            ->first();

        // Fetch the Documenso token from ESignTemplateRecipient
        $documentToken = null;
        if ($investment) {
            $recipient = ESignTemplateRecipient::where('investment_id', $investment->id)
                ->where('investor_id', $investor->id)
                ->first();
            $documentToken = $recipient ? $recipient->token : null;
            \Log::info('Document Token for Investment', [
                'investment_id' => $investment->id,
                'token' => $documentToken,
            ]);
        }
        // dd($documentToken);
        // dd($offering->deal);
        // dd($investment_questionnaires = $offering->investment_questionnaires);
        $manageoffering = $offering->manageoffering;
        
        return view('Template::user.deals.offerings.offering_invest_now', compact(
            'offering',
            'pageTitle',
            'investor',
            'fundingMethods',
            'manageoffering',
            'documentToken',
            'investment',
            'user',
        ));
    }
    public function stripeSuccess(Request $request)
    {
        $pageTitle = 'Stripe Success';
        return view('Template::user.deals.offerings.stripe_success', compact('pageTitle'));
    }
    public function storeProfile(Request $request, Offering $offering)
    {
        // Add validation rules for required fields
        $validate = Validator::make($request->all(), [
            //'investor_name' => 'required|string|max:255',
            //'investment_class' => 'required|string|max:255',
            //'investment_amount' => 'required|numeric',
            //'investment_offering' => 'required|string|max:255',
            // Add the other fields as necessary
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        $user = auth()->user();

        $investor = Investor::firstOrCreate(['investor_email' => $user->email], [
            'investor_fname' => $user->name,
            'investor_email' => $user->email,
            'user_id' => $user->id
        ]);

        $data = $request->all();
        $data['profile_email'] = $user->email;


        // Create the investment entry
        $profile = $investor->investor_profiles()->create($data);
        $profiles = $investor->investor_profiles;
        // Return a success response
        return response()->json(['success' => 'profile created successfully', 'profiles' => $profiles], 200);

    }

    public function storeQuestionnaire(Request $request, Offering $offering)
    {
        // Add validation rules for required fields
        $validate = Validator::make($request->all(), [
            // Add the other fields as necessary
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        // Create the investment entry
        $questionnaire = $offering->investment_questionnaires()->create($request->all());
        $questionnaires = $offering->investment_questionnaires;


        // Return a success response
        return response()->json(['success' => 'Questionnaire created successfully', 'questionnaires' => $questionnaires], 200);
    }

    public function storeQuestionnaireAddress(Request $request, Offering $offering)
    {
        // Validate the request data
        $validate = Validator::make($request->all(), [
            // 'address_line1' => 'required|string|max:255',
            // 'address_line2' => 'nullable|string|max:255',
            // 'city' => 'required|string|max:255',
            // 'state' => 'required|string|max:255',
            // 'zipcode' => 'required|string|max:20',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        // Create the address directly related to the offering
        $address = $offering->questionnaire_addresses()->create($request->all());
        $addresses = $offering->questionnaire_addresses;

        // Return a success response
        return response()->json(['success' => 'Questionnaire address created successfully', 'addresses' => $addresses], 200);
    }
    public function storeQuestionnaireForm(Request $request, Offering $offering)
    {
        // Check what data is being received
        // dd($request->all()); 

        // Add validation rules for required fields
        $validate = Validator::make($request->all(), [
            // 'name' => 'required|string',
            // 'address' => 'required|string',
            // 'social_security_number' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        // Create the investment entry
        $wform = $offering->questionnaire_forms()->create($request->all());

        // Return a success response
        $wforms = $offering->questionnaire_forms;
        return response()->json(['success' => 'W-9 form created successfully', 'wforms' => $wforms], 200);
    }
    public function storeInvestment(Request $request, Offering $offering)
    {
        $validate = Validator::make($request->all(), [
            'investor_profile_id' => 'required',
            'deal_class_id' => 'required',
            'investment_amount' => 'required',
            // Add the other fields as necessary
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $data = $request->all();
        $data = $this->moneyToDouble($data);

        $investotmentProfile = InvestorProfile::find($data['investor_profile_id']);
        $data['investor_id'] = $investotmentProfile->investor_id;
        $data['deal_id'] = $offering->deal_id;
        $fundingMethods = [
            'wireTransfer' => 'wire_transfer',
            'achPayment' => 'ach_payment',
            'check' => 'check_payment',
        ];
        $data['contribution_method'] = $fundingMethods[$data['funding_method']];
        $data['investment_status'] = 'pending';
        $data['investment_questionnaire_id'] = $data['questionnaire_id'];
        unset($data['_token']);
        unset($data['questionnaire_id']);
        $investment = $offering->investments()->create($data);
        // Return a success response
        return response()->json(['success' => 'Investment created successfully', 'investment' => $investment], 200);
    }
    public function updateInvestment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'initiate_wire_transfer_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $investment = Investment::find($id);

        if (!$investment) {
            return response()->json(['error' => 'Investment not found'], 404);
        }

        $investment->initiate_wire_transfer_date = $request->input('initiate_wire_transfer_date');
        $investment->save();

        return response()->json([
            'success' => 'Investment updated successfully',
            'investment_id' => $investment->id,
        ], 200);
    }
    public function uploadInvoice(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'invoice_images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'transaction_details' => 'required|string|max:1000',
            // 'investment_id' => 'required|exists:investments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $investment = Investment::find($id);

        if (!$investment) {
            return response()->json(['message' => 'Investment not found'], 404);
        }

        // Handle multiple image uploads
        $imagePaths = $investment->invoice_images ?? [];
        if ($request->hasFile('invoice_images')) {
            foreach ($request->file('invoice_images') as $file) {
                $path = $file->store('invoices', 'public');
                $imagePaths[] = $path;
            }
        }

        // Update investment
        $investment->invoice_images = $imagePaths;
        $investment->transaction_details = $request->input('transaction_details');
        $investment->wire_transfer_status = 'Pending';
        $investment->save();

        return response()->json(['message' => 'Wire transfer details uploaded successfully'], 200);
    }
    public function downloadInvoice(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|string',
            'receiving_bank' => 'required|string',
            'bank_address' => 'required|string',
            'routing_no' => 'required|string',
            'account_no' => 'required|string',
            'account_type' => 'required|string',
            'beneficiary_account_name' => 'required|string',
            'beneficiary_address' => 'required|string',
        ]);

        // Prepare data for the invoice
        $data = [
            'investment_id' => $request->investment_id,
            'payment_method' => 'Wire Transfer',
            'receiving_bank' => $request->receiving_bank,
            'bank_address' => $request->bank_address,
            'routing_no' => $request->routing_no,
            'account_no' => $request->account_no,
            'account_type' => $request->account_type,
            'beneficiary_account_name' => $request->beneficiary_account_name,
            'beneficiary_address' => $request->beneficiary_address,
        ];
        // dd($pdf = Pdf::loadView('invoices.invoice', $data));

        // Load a view for the PDF (you'll create this next)
        $pdf = Pdf::loadView('invoices.invoice', $data);
        // Download the PDF
        return $pdf->download('Investment_' . $data['investment_id'] . '_Invoice.pdf');
    }

}