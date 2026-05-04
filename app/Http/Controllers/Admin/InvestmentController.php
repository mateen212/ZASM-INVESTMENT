<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Services\DocumensoService;
use App\Models\Investor;
use App\Models\InvestorProfile;
use App\Models\Offering;
use Illuminate\Support\Facades\Log;
use App\Models\ESignTemplateRecipient;
use App\Models\ESignTemplateField;
use App\Notifications\InvestmentCreatedNotification;

use App\Http\Controllers\Controller;
use Validator;
use App\Traits\Utills;

class InvestmentController extends Controller
{
    use Utills;

    public function index()
    {
        $pageTitle = 'Investments';
        // Retrieve all deals or filter as needed
        $investment = Investment::paginate(10);

        // Return the view with the deals data
        return view('admin.deals.summary', compact('investments', 'pageTitle'));
    }

    protected $documensoService;

    public function __construct(DocumensoService $documensoService)
    {
        $this->documensoService = $documensoService;
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'deal_id' => 'required',
            'investor_id' => 'required',
            'deal_class_id' => 'required',
            'investment_amount' => 'required',
            'investment_status' => 'required',
            'date_placed' => 'required',
            'offering_id' => 'required|exists:offerings,id',
            'investor_profile_id' => 'nullable|exists:investor_profiles,id',
        ], [
            'deal_id.required' => 'Deal is required',
            'investor_id.required' => 'Investor is required',
            'deal_class_id.required' => 'Deal class is required',
            'investment_amount.required' => 'Investment amount is required',
            'investment_status.required' => 'Investment status is required',
            'date_placed.required' => 'Date placed is required',
            'offering_id.required' => 'Offering is required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        $deal = Offering::find($request->offering_id)->deal; // Assuming Offering has a deal relationship
        if (!$deal) {
            Log::error('Deal not found', ['offering_id' => $request->offering_id]);
            return response()->json(['success' => 'Investment creation failed', 'error' => 'Deal not found'], 404);
        }

        $data = $this->moneyToDouble($request->all());
        $data['investment_in_progress'] = ($request->investment_in_progress == 'true' && $request->investment_status == 'document_started');

        $investment = Investment::create($data);

        $userId = auth('admin')->id();
        $tags = explode(',', $request->investment_tags);
        $investment->attachTagsForUser($tags, $userId);

        $token = null;
        $recipients = [];

        if ($request->investment_status == 'document_started' && $this->documensoService->isEnabled()) {
            try {
                $investor = Investor::find($request->investor_id);
                $profile = $request->investor_profile_id
                    ? InvestorProfile::find($request->investor_profile_id)
                    : $investor->investor_profiles()->first();

                if (!$profile) {
                    Log::warning('No investor profile found', ['investor_id' => $request->investor_id]);
                    return response()->json(['success' => 'Investment created but no profile found', 'investment' => $investment], 200);
                }

                $offering = Offering::find($request->offering_id);
                $template = $offering->esignTemplates()->where('template_type', $profile->profile_type)->first();

                if (!$template || !$template->documenso_document_id) {
                    Log::warning('No matching template found', [
                        'offering_id' => $request->offering_id,
                        'profile_type' => $profile->profile_type
                    ]);
                    return response()->json(['success' => 'Investment created but no matching template found', 'investment' => $investment], 200);
                }

                // Validate required investor data
                if (!$investor->investor_email || !$investor->investor_fname || !$investor->investor_lname) {
                    Log::error('Missing required investor data', [
                        'investor_id' => $investor->id,
                        'email' => $investor->investor_email,
                        'fname' => $investor->investor_fname,
                        'lname' => $investor->investor_lname
                    ]);
                    return response()->json(['success' => 'Investment created but failed to assign document', 'investment' => $investment, 'error' => 'Missing required investor data'], 200);
                }

                // Handle recipient data based on profile type
                if ($profile->profile_type === 'custodian') {
                    $recipients[] = [
                        'email' => $investor->investor_email,
                        'name' => $profile->profile_ira_name,
                        'role' => 'SIGNER',
                        'signingOrder' => 0
                    ];
                } elseif ($profile->profile_type === 'lcps_property') {

                    $recipients[] = [
                        'email' => $investor->investor_email,
                        'name' => $profile->profile_entity_name,
                        'role' => 'SIGNER',
                        'signingOrder' => 0
                    ];

                } else {
                    if (!$profile->profile_fname || !$profile->profile_lname) {
                        Log::error('Missing profile data for non-custodian/lcps_property', [
                            'profile_id' => $profile->id,
                            'fname' => $profile->profile_fname,
                            'lname' => $profile->profile_lname
                        ]);
                        return response()->json(['success' => 'Investment created but failed to assign document', 'investment' => $investment, 'error' => 'Missing profile data'], 200);
                    }
                    $recipients[] = [
                        'email' => $investor->investor_email,
                        'name' => trim($profile->profile_fname . ' ' . $profile->profile_lname),
                        'role' => 'SIGNER',
                        'signingOrder' => 0
                    ];
                }

                // Handle joint tenancy second recipient
                if ($profile->profile_type === 'join_tenancy' && $profile->profile_email2) {
                    if (!$profile->profile_fname2 || !$profile->profile_lname2) {
                        Log::warning('Missing second recipient data for joint tenancy', [
                            'profile_id' => $profile->id,
                            'email2' => $profile->profile_email2,
                            'fname2' => $profile->profile_fname2,
                            'lname2' => $profile->profile_lname2
                        ]);
                    } else {
                        $recipients[] = [
                            'email' => $profile->profile_email2,
                            'name' => trim($profile->profile_fname2 . ' ' . $profile->profile_lname2),
                            'role' => 'SIGNER',
                            'signingOrder' => 1
                        ];
                    }
                }

                Log::info('Recipient data before Documenso call', [
                    'count' => count($recipients),
                    'is_multi_recipient' => count($recipients) > 1,
                    'recipients' => $recipients,
                    'document_id' => $template->documenso_document_id
                ]);

                $recipientResponse = $this->documensoService->createRecipient($template->documenso_document_id, $recipients);

                Log::debug('Documenso createRecipient response', ['response' => $recipientResponse]);

                if (isset($recipientResponse['error'])) {
                    Log::error('Failed to create recipients in Documenso', [
                        'document_id' => $template->documenso_document_id,
                        'recipients' => $recipients,
                        'response' => $recipientResponse
                    ]);
                    return response()->json([
                        'success' => 'Investment created but failed to assign document',
                        'investment' => $investment,
                        'error' => 'Failed to create recipients: ' . ($recipientResponse['details'] ?? $recipientResponse['error'])
                    ], 200);
                }

                // Normalize response: handle single recipient or array of recipients
                $recipientList = [];
                if (isset($recipientResponse['recipients']) && is_array($recipientResponse['recipients'])) {
                    $recipientList = $recipientResponse['recipients'];
                } elseif (isset($recipientResponse['id']) && isset($recipientResponse['email'])) {
                    $recipientList = [$recipientResponse];
                }

                if (empty($recipientList)) {
                    Log::error('No valid recipients in Documenso response', [
                        'document_id' => $template->documenso_document_id,
                        'recipients_sent' => $recipients,
                        'response' => $recipientResponse
                    ]);
                    return response()->json([
                        'success' => 'Investment created but failed to assign document',
                        'investment' => $investment,
                        'error' => 'No valid recipients returned from Documenso'
                    ], 200);
                }

                foreach ($recipientList as $index => $recipientData) {
                    ESignTemplateRecipient::create([
                        'e_sign_templates_id' => $template->id,
                        'investment_id' => $investment->id,
                        'investor_id' => $investor->id,
                        'name' => $recipientData['name'] ?? 'Unknown',
                        'email' => $recipientData['email'] ?? 'Unknown',
                        'role' => $recipientData['role'] ?? 'SIGNER',
                        'signing_order' => $recipientData['signingOrder'] ?? $index,
                        'token' => $recipientData['token'] ?? null,
                        'recipient_type' => $profile->profile_type,
                        'documenso_recipient_id' => $recipientData['id'] ?? null,
                        'status' => 'pending',
                    ]);
                }

                // Fetch fields from database
                $templateFields = ESignTemplateField::where('e_sign_templates_id', $template->id)->get()->toArray();
                $recipientFields = [];

                if (!empty($templateFields)) {
                    // dd($templateFields);
                    foreach ($recipientList as $recipientData) {
                        $recipientId = $recipientData['id'] ?? null;
                        if (!$recipientId) {
                            Log::warning('No recipient ID for fields assignment', [
                                'document_id' => $template->documenso_document_id,
                                'recipient' => $recipientData
                            ]);
                            continue;
                        }
                        $ppi = 73.5;
                        $fields = array_map(function ($field) use ($recipientId, $ppi) {
                            $proportion = $field['pageHeight'] / $field['pageWidth'];
                            $data = [
                                'recipientId' => $recipientId,
                                'type' => strtoupper($field['type']),
                                'pageNumber' => $field['page'],
                                'pageX' => ($field['x'] * 15 / $ppi) * 0.7009,
                                'pageY' => ($field['y'] * (15 / $proportion) / $ppi) * 0.7009,
                                'pageWidth' => 10,
                                'pageHeight' => 5,
                                'value' => $field['value'] ?? null,
                            ];

                            if (in_array(strtolower($field['type']), ['text'])) {
                                $data['fieldMeta'] = [
                                    'label' => ucfirst($field['type']),
                                    'required' => true,
                                    'readOnly' => false,
                                    'type' => 'text',
                                    'characterLimit' => 40,
                                    'placeholder' => 'string',
                                    'text' => $field['value'] ?? 'string',
                                ];
                            }

                            return $data;
                        }, $templateFields);

                        $recipientFields[$recipientId] = $fields;

                        foreach ($fields as $field) {
                            $response = $this->documensoService->addFieldsToDocument($template->documenso_document_id, $field);
                            if (isset($response['error'])) {
                                Log::error('Failed to add field for recipient', [
                                    'document_id' => $template->documenso_document_id,
                                    'recipient_id' => $recipientId,
                                    'field' => $field,
                                    'response' => $response
                                ]);
                            }
                        }
                    }
                } else {
                    Log::warning('No fields found for template', [
                        'e_sign_templates_id' => $template->id,
                        'documenso_document_id' => $template->documenso_document_id
                    ]);
                }

                $distributeResponse = $this->documensoService->distributeDocument($template->documenso_document_id);
                if (isset($distributeResponse['error'])) {
                    Log::error('Failed to distribute document', [
                        'document_id' => $template->documenso_document_id,
                        'response' => $distributeResponse
                    ]);
                }
                // dd($distributeResponse);
                // $tokenResponse = $this->documensoService->getToken($template->documenso_document_id);
                // $token = isset($tokenResponse['error']) ? null : $tokenResponse['token'];
                // if (isset($tokenResponse['error'])) {
                //     Log::error('Failed to get document token', [
                //         'document_id' => $template->documenso_document_id,
                //         'response' => $tokenResponse
                //     ]);
                // }

            } catch (\Exception $e) {
                Log::error('Error in document assignment', [
                    'investment_id' => $investment->id,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => 'Investment created but error in document assignment',
                    'investment' => $investment,
                    'error' => $e->getMessage()
                ], 200);
            }
        }
        $dealOwner = $deal->user;
        if ($dealOwner) {
            \Log::info('Sending InvestmentCreatedNotification to deal owner', [
                'deal_owner_id' => $dealOwner->id,
                'deal_owner_email' => $dealOwner->email,
                'deal_id' => $deal->id,
            ]);
            $dealOwner->notify(new InvestmentCreatedNotification($investment));
        } else {
            \Log::warning('No deal owner found for deal', ['deal_id' => $deal->id]);
        }
        $recipientFieldsData = null;
        if ($request->investment_status == 'document_started' && $this->documensoService->isEnabled()) {
            $recipientFieldsData = $recipientFields ?? null;
        }

        return response()->json([
            'success' => 'Investment created successfully',
            'investment' => $investment,
            'document_token' => $token ?? null,
            'recipient_count' => count($recipients),
            'document_link' => $token ? "https://app.documenso.com/sign/{$token}" : null,
            'recipient_fields' => $recipientFieldsData,
        ], 200);
    }

    // Assuming this method exists in your controller
    protected function moneyToDouble($data)
    {
        // Your existing implementation
        return $data;
    }

    public function deleteInvestment($id)
    {
        $investment = Investment::find($id);
        if (!$investment) {
            return response()->json(['error' => 'Investment not found'], 404);
        }
        $investment->delete();

        return response()->json(['success' => 'Investment deleted successfully'], 200);
    }


}
