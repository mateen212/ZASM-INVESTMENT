<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Models\ESignTemplate;
use App\Models\Deal;
use App\Models\ESignTemplateField;
use App\Http\Controllers\Controller;
use App\Services\DocumensoService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class ESignTemplateController extends Controller
{
    protected $documensoService;

    public function __construct(DocumensoService $documensoService)
    {
        $this->documensoService = $documensoService;
    }

    public function uploadTemplate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'template_type' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,doc,docx,jpeg,png|max:2048',
            'deal_id' => 'required|exists:deals,id',
            'offering_id' => 'required|exists:offerings,id',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        // 🚨 Prevent duplicate template types per offering
        $existing = ESignTemplate::where('offering_id', $request->input('offering_id'))
            ->where('template_type', $request->input('template_type'))
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'A template of this type already exists for the selected offering.'
            ], 409);
        }


        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = 'e_sign_template/' . $fileName;
            $file->move(public_path('e_sign_template'), $fileName);
            $fileUrl = asset($path);

            if (!$this->documensoService->isEnabled()) {
                return response()->json(['error' => 'Documenso API is not enabled'], 400);
            }
            $user = auth('admin')->user();
            try {
                $template = ESignTemplate::create([
                    'template_name' => $request->input('template_name'),
                    'template_type' => $request->input('template_type'),
                    'file_path' => $path,
                    'deal_id' => $request->input('deal_id'),
                    'offering_id' => $request->input('offering_id'),
                    // 'documenso_document_id' => $documensoData['documentId'],
                ]);
                $documensoData = $this->documensoService->createDocument(
                    $request->input('template_name'),
                    $fileUrl,
                    [
                        [
                            'name' => $user->name,
                            'email' => $user->email
                        ]
                    ],
                    ['externalId' => $template->id] // Set externalId to template ID by default

                );

                if (isset($documensoData['error']) || !isset($documensoData['documentId'])) {
                    Log::error('Documenso document creation failed', ['response' => $documensoData]);
                    return response()->json(['error' => 'Failed to create document in Documenso', 'details' => $documensoData], 400);
                }
                $template->documenso_document_id = $documensoData['documentId'];
                $template->save();
                $uploadUrl = $documensoData['uploadUrl'];
                $filePath = public_path($path);
                $fileContents = file_get_contents($filePath);

                // dd($documensoData);
                $uploadResponse = Http::withBody($fileContents, $file->getClientMimeType())->put($uploadUrl);
                if ($uploadResponse->failed()) {
                    Log::error('Failed to upload file to S3', ['status' => $uploadResponse->status(), 'body' => $uploadResponse->body()]);
                    return response()->json(['error' => 'Failed to upload file to S3', 'details' => $uploadResponse->body()], 400);
                }



                return response()->json([
                    'message' => 'Template uploaded and document created successfully',
                    'template_id' => $template->id,
                    'file_path' => asset($path),
                    'documenso_document_id' => $documensoData['documentId'],
                    $documensoData,
                ], 200);

            } catch (\Exception $e) {
                Log::error('Error in uploadTemplate', ['exception' => $e->getMessage()]);
                return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 'Invalid or missing file'], 400);
    }


    public function getDocumentPreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:e_sign_templates,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $template = ESignTemplate::find($request->template_id);
        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        if (!$this->documensoService->isEnabled()) {
            return response()->json(['error' => 'Documenso API is not enabled'], 400);
        }

        try {
            $tokenData = $this->documensoService->getToken($template->documenso_document_id);
            if (isset($tokenData['error'])) {
                Log::error('Failed to retrieve token', ['response' => $tokenData]);
                return response()->json(['error' => 'Failed to retrieve token', 'details' => $tokenData], 400);
            }

            return response()->json([
                'message' => 'Token retrieved successfully',
                'token' => $tokenData['token'],
                'document_id' => $template->documenso_document_id,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in getDocumentPreview', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a Documenso document from an existing template
     */
    public function createDocumensoDocument(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:e_sign_templates,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $template = ESignTemplate::find($request->template_id);
        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $fileUrl = asset($template->file_path);

        if (!$this->documensoService->isEnabled()) {
            return response()->json(['error' => 'Documenso API is not enabled'], 400);
        }

        try {
            $response = $this->documensoService->createDocument(
                $template->template_name,
                $fileUrl,
                [] // No recipients for now
            );

            if (isset($response['error']) || !isset($response['id'])) {
                Log::error('Documenso document creation failed', ['response' => $response]);
                return response()->json([
                    'error' => 'Failed to create document in Documenso',
                    'details' => $response
                ], 400);
            }

            return response()->json([
                'message' => 'Document created successfully',
                'documenso_document_id' => $response['id'],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in createDocumensoDocument', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteTemplate(Request $request, ESignTemplate $template)
    {
        if (!$this->documensoService->isEnabled()) {
            return response()->json(['error' => 'Documenso API is not enabled'], 400);
        }

        try {
            if ($template->documenso_document_id) {
                $response = $this->documensoService->deleteDocument($template->documenso_document_id);

                if (isset($response['error'])) {
                    Log::error('Failed to delete document in Documenso', [
                        'document_id' => $template->documenso_document_id,
                        'response' => $response
                    ]);
                    return response()->json([
                        'error' => 'Failed to delete document in Documenso',
                        'details' => $response['details'] ?? $response['error']
                    ], $response['status'] ?? 400);
                }
            }

            $template->delete();

            return response()->json([
                'message' => 'Template and associated Documenso document deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in deleteTemplate', [
                'exception' => $e->getMessage(),
                'document_id' => $template->documenso_document_id ?? 'N/A'
            ]);
            return response()->json([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get a Documenso token for a specific document
     */
    public function getDocumensoToken(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$this->documensoService->isEnabled()) {
            return response()->json(['error' => 'Documenso API is not enabled'], 400);
        }

        try {
            $response = $this->documensoService->getToken($request->document_id);

            if (isset($response['error']) || !isset($response['token'])) {
                Log::error('Documenso token retrieval failed', ['response' => $response]);
                return response()->json([
                    'error' => 'Failed to retrieve Documenso token',
                    'details' => $response
                ], 400);
            }

            return response()->json([
                'message' => 'Token retrieved successfully',
                'token' => $response['token'],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in getDocumensoToken', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function saveFieldsToDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:e_sign_templates,id',
            'fields' => 'required|array',
            'fields.*.type' => 'required|string|in:signature,name,text,date',
            'fields.*.x' => 'required|numeric',
            'fields.*.y' => 'required|numeric',
            'fields.*.page' => 'required|integer|min:1',
            'fields.*.value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $template = ESignTemplate::find($request->template_id);
        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }
        $template->fields()->delete(); // Clear existing fields
        // Save fields to the database
        foreach ($request->fields as $fieldData) {
            ESignTemplateField::create([
                'e_sign_templates_id' => $template->id,
                'type' => $fieldData['type'],
                'x' => $fieldData['x'],
                'y' => $fieldData['y'],
                'page' => $fieldData['page'],
                'pageHeight' => $fieldData['pageHeight'],
                'pageWidth' => $fieldData['pageWidth'],
                'value' => $fieldData['value'] ?? null,
            ]);
        }

        // Fetch document details to get recipient ID
        $documentDetails = $this->documensoService->getDocument($template->documenso_document_id);
        if (isset($documentDetails['error']) || empty($documentDetails['recipients'])) {
            return response()->json(['error' => 'No recipients found or failed to fetch document'], 400);
        }

        $recipientId = $documentDetails['recipients'][0]['id'];
        $ppi = 73.5;  // Use 300 PPI for standard print resolution
        // width 50.54
        // 1.415 proportional
        // Map fields to Documenso API format
        // dd($request->fields);
        $fields = array_map(function ($field) use ($recipientId, $ppi) {
            $proportion = $field['pageHeight'] / $field['pageWidth']; // 1.415 proportional
            $data = [
                'recipientId' => $recipientId,
                'type' => strtoupper($field['type']),
                'pageNumber' => $field['page'],

                // Adjust coordinates for Documenso API (300 PPI)
                'pageX' => ($field['x'] * 15 / $ppi) * 0.7009,  // Adjusted X-coordinate
                'pageY' => ($field['y'] * (15 / $proportion) / $ppi) * 0.7009,   // Adjusted Y-coordinate

                // Adjust field dimensions for Documenso API (300 PPI)
                'pageWidth' => 10,
                'pageHeight' => 5,  // No need for 0.5 multiplier
            ];

            // Add metadata for 'text' fields (if necessary)
            if (in_array(strtolower($field['type']), ['text'])) {
                $data['fieldMeta'] = [
                    'label' => ucfirst($field['type']),
                    'required' => true,
                    'readOnly' => false,
                    "type" => "text",
                    "characterLimit" => 40,
                    "placeholder" => "string",
                    "text" => "string",
                ];
            }

            return $data;
        }, array: $request->fields);


        $responses = [];
        // Add fields to Documenso
        foreach ($fields as $field) {
            $response = $this->documensoService->addFieldsToDocument($template->documenso_document_id, $field);
            if (isset($response['error'])) {
                Log::error('Failed to add field', ['field' => $field, 'response' => $response]);
                return response()->json(['error' => 'Failed to add fields to Documenso', 'details' => $response], 400);
            }
            $responses[] = $response;
        }

        // Fetch updated document details with v2 API
        // $documensoDocument = $this->documensoService->getDocumentV2($template->documenso_document_id);
        // if (isset($documensoDocument['error'])) {
        //     Log::error('Failed to fetch document', ['response' => $documensoDocument]);
        //     return response()->json(['error' => 'Failed to fetch document from Documenso', 'details' => $documensoDocument], 400);
        // }

        // Distribute document to generate a link (no email)
        $distributeResponse = $this->documensoService->distributeDocument($template->documenso_document_id);
        if (isset($distributeResponse['error'])) {
            Log::error('Failed to distribute document', [
                'document_id' => $template->documenso_document_id,
                'response' => $distributeResponse
            ]);
            // Continue without failing, just log the error
        }

        // Get token for the document
        $tokenResponse = $this->documensoService->getToken($template->documenso_document_id);
        $token = isset($tokenResponse['error']) ? null : $tokenResponse['token'];
        if (isset($tokenResponse['error'])) {
            Log::error('Failed to get document token', [
                'document_id' => $template->documenso_document_id,
                'response' => $tokenResponse
            ]);
        }

        return response()->json([
            'message' => 'Fields saved to database and Documenso',
            'fields' => $responses,
            // 'document' => $documensoDocument,
            'document_token' => $token,
            'document_link' => $token ? "https://app.documenso.com/sign/{$token}" : null
        ], 200);
    }

    public function updateTemplate(Request $request, ESignTemplate $template)
    {
        try {
            $validator = Validator::make($request->all(), [
                'template_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $template->template_name = $request->template_name;
            $template->save();

            // Update Documenso document if it exists
            if ($template->documenso_document_id && $this->documensoService->isEnabled()) {
                $response = $this->documensoService->updateDocument(
                    $template->documenso_document_id,
                    ['title' => $request->template_name]
                );

                if (isset($response['error'])) {
                    Log::error('Failed to update Documenso document', [
                        'document_id' => $template->documenso_document_id,
                        'response' => $response
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'data' => $template
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in updateTemplate', [
                'exception' => $e->getMessage(),
                'template_id' => $template->id
            ]);
            return response()->json([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function viewTemplate(ESignTemplate $template)
    {
        return view('admin.deals.offerings.view_template', compact('template'));
    }

    public function test_notification(Request $request)
    {
        $investment = \App\Models\Investment::latest()->first();
        $dealOwner = $investment->deal->user;
        if ($dealOwner) {
            $dealOwner->notify(new \App\Notifications\InvestmentCreatedNotification($investment));
            return 'Notification sent';
        }
        return 'No deal owner found';
    }

}