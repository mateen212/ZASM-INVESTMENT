<?php

namespace App\Services;

use App\Models\ApiIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DocumensoService
{
    protected $api;
    protected $baseUrl;
    protected $baseUrlV2;
    protected $apiKey;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api = ApiIntegration::where('code', 'documenso')->first();

        if ($this->api && $this->api->status) {
            $this->baseUrl = $this->api->getCredentialValue('api_url');
            $this->apiKey = $this->api->getCredentialValue('api_key');
            $this->baseUrlV2 = 'https://app.documenso.com/api/v2-beta';
        }
    }

    /**
     * Check if Documenso API is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->api && $this->api->status && $this->apiKey;
    }

    /**
     * Make an HTTP request to the Documenso API
     *
     * @param string $method HTTP method (GET, POST, DELETE, etc.)
     * @param string $endpoint API endpoint (relative to base URL)
     * @param array $payload Request payload (optional)
     * @return \Illuminate\Http\Client\Response
     */
    private function makeRequest($method, $endpoint, $payload = [], $version = 'v1')
    {
        // dd($this->baseUrl . $endpoint, $payload);
        if ($version == 'v2') {
            $url = $this->baseUrl . '/v2-beta';
        } else {
            $url = $this->baseUrl . '/v1';
        }
        // dd([
        //     'method' => $method,
        //     'url' => $url . $endpoint,
        //     'headers' => [
        //         'Authorization' => $this->apiKey,
        //         'Content-Type' => 'application/json',
        //     ],
        //     'payload' => $payload,
        // ]);
        return Http::withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json',

                ])
                    ->timeout(30)
                    ->retry(3, 100)
            ->{$method}($url . $endpoint, $payload);
    }

    /**
     * Create a new document
     * 
     * @param string $title Document title
     * @param string $fileUrl URL to the file
     * @param array $recipients Array of recipient objects
     * @param array $meta Additional metadata
     * @return array Response from API
     */
    public function createDocument($title, $fileUrl, $recipients = [], $meta = [])
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $formattedRecipients = [];
            foreach ($recipients as $index => $recipient) {
                $formattedRecipients[] = [
                    'name' => $recipient['name'] ?? '',
                    'email' => $recipient['email'] ?? '',
                    'role' => 'APPROVER',
                    'signingOrder' => $index
                ];
            }

            $payload = [
                'title' => $title,
                'file_url' => $fileUrl,
                'recipients' => $formattedRecipients,
                'meta' => $meta,
            ];

            Log::info('Documenso createDocument request', ['payload' => json_encode($payload)]);
            // dd($this->baseUrl . '/documents', $payload);
            $response = $this->makeRequest('post', '/documents', $payload);
            // dd($this->baseUrl . '/documents', $payload);


            if ($response->failed()) {
                Log::error('Documenso API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to create document', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso API exception', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteDocument($documentId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }
        try {
            $response = $this->makeRequest('delete', "/documents/{$documentId}", [
                'documentId' => $documentId
            ]);
            if ($response->failed()) {
                Log::error('Documenso API error deleting document', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [
                    'error' => 'Failed to delete document',
                    'status' => $response->status(),
                    'details' => $response->json()
                ];
            }

            Log::info('Documenso document deleted successfully', [
                'document_id' => $documentId,
                'status' => $response->status()
            ]);

            return [
                'status' => $response->status(),
                'message' => 'Document deleted successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Documenso API exception deleting document', [
                'message' => $e->getMessage(),
                'document_id' => $documentId
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get a token for a document
     * 
     * @param string $documentId Document ID
     * @return array Response from API
     */
    public function getToken($documentId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('post', "/documents/{$documentId}/token", [
                'purpose' => 'embed'
            ]);

            $responseData = $response->json();

            Log::info('Documenso token response', [
                'response' => $responseData,
                'status' => $response->status()
            ]);

            if ($response->failed()) {
                Log::error('Documenso token API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to get token', 'details' => $responseData];
            }

            $token = $responseData['token'] ?? $responseData['embed_token'] ?? null;
            if (!$token) {
                return ['error' => 'Token not found in response'];
            }

            return ['token' => $token];
        } catch (\Exception $e) {
            Log::error('Documenso token API exception', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all documents
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Response from API
     */
    public function getDocuments($page = 1, $perPage = 10)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('get', '/documents', [
                'page' => $page,
                'perPage' => $perPage
            ]);

            if ($response->failed()) {
                Log::error('Documenso get documents API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to get documents', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso get documents API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get a specific document
     * 
     * @param string $documentId Document ID
     * @return array Response from API
     */
    public function getDocument($documentId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('get', "/documents/{$documentId}");

            if ($response->failed()) {
                Log::error('Documenso get document API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to get document', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso get document API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Test the Documenso API connection
     * 
     * @return array Response from API or error
     */
    public function testConnection()
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('get', '/health');

            if ($response->successful()) {
                return ['status' => 'API connection successful', 'response' => $response->json()];
            }

            Log::error('Documenso API health check failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return ['error' => 'API connection failed', 'details' => $response->json()];
        } catch (\Exception $e) {
            Log::error('Documenso API health check exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Add fields to a document
     * 
     * @param string $documentId Document ID
     * @param array $fields Fields to be added
     * @return array Response from API
     */
    public function addFieldsToDocument($documentId, $fields)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('post', "/documents/{$documentId}/fields", $fields);

            if ($response->failed()) {
                Log::error('Documenso API error adding fields', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['error' => 'Failed to add fields to document', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso API exception adding fields', [
                'message' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create Many fields to a document
     * 
     * @param string $documentId Document ID
     * @param array $fields Fields to be added
     * @return array Response from API
     */
    public function createFields($documentId, $fields)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $payload = [
                'documentId' => $documentId,
                'fields' => $fields
            ];

            $response = $this->makeRequest('post', '/document/field/create-many', $payload);

            if ($response->failed()) {
                Log::error('Documenso API error creating fields', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['error' => 'Failed to create fields', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso API exception creating fields', [
                'message' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete a field from a document
     *
     * @param string $documentId Document ID
     * @param string $fieldId Field ID
     * @return array Response from API
     */
    public function deleteField($documentId, $fieldId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('delete', "/documents/{$documentId}/fields/{$fieldId}");

            if ($response->failed()) {
                Log::error('Documenso API error deleting field', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['error' => 'Failed to delete field', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso API exception deleting field', [
                'message' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    // ========== V2 API Methods ==========
    /**
     * Get all documents from V2 API
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Response from API
     */
    public function getDocumentsV2($page = 1, $perPage = 10)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('get', '/documents', [
                'page' => $page,
                'perPage' => $perPage
            ], 'v2');

            if ($response->failed()) {
                Log::error('Documenso get documents V2 API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to get documents', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso get documents V2 API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get a specific document from V2 API
     * 
     * @param string $documentId Document ID
     * @return array Response from API
     */
    public function getDocumentV2($documentId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('get', "/document/{$documentId}", [], 'v2');

            if ($response->failed()) {
                Log::error('Documenso get document V2 API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to get document', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso get document V2 API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }


    /**
     * Create Recipient for document 
     * 
     * @param string $documentId Document ID
     * @param array $recipient recipient object or Array of objects
     * @return array Response from API
     */
    public function createRecipient($documentId, $recipients)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $endpoint = "/documents/{$documentId}/recipients";
            $recipientList = [];

            // Ensure recipients is always an array
            $recipients = is_array($recipients) ? $recipients : [$recipients];

            Log::info('Documenso create recipient request start', [
                'document_id' => $documentId,
                'recipient_count' => count($recipients)
            ]);

            // Iterate over each recipient and make individual POST requests
            foreach ($recipients as $index => $rec) {
                $formattedRecipient = [
                    'name' => $rec['name'] ?? '',
                    'email' => $rec['email'] ?? '',
                    'role' => $rec['role'] ?? 'SIGNER',
                    'signingOrder' => $rec['signingOrder'] ?? $index
                ];

                // Only include authOptions for Enterprise accounts (optional)
                if (isset($rec['authOptions'])) {
                    $formattedRecipient['authOptions'] = $rec['authOptions'];
                }

                $data = $formattedRecipient;

                Log::info('Documenso create recipient request', [
                    'document_id' => $documentId,
                    'recipient_index' => $index,
                    'payload' => json_encode($data)
                ]);

                // Make the API request for a single recipient
                $response = $this->makeRequest('post', $endpoint, $data);

                if ($response->failed()) {
                    Log::error('Documenso create recipient API error', [
                        'document_id' => $documentId,
                        'recipient_index' => $index,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return [
                        'error' => 'Failed to create recipient',
                        'details' => $response->json(),
                        'recipient_index' => $index
                    ];
                }

                $recipientResponse = $response->json();
                $recipientList[] = $recipientResponse;
            }

            Log::debug('Documenso createRecipient response', [
                'document_id' => $documentId,
                'recipients' => $recipientList
            ]);

            // Return response in a format compatible with the original code
            return ['recipients' => $recipientList];

        } catch (\Exception $e) {
            Log::error('Documenso create recipient API exception', [
                'document_id' => $documentId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get Document Recipient
     * 
     * @param string $recipientId Recipient ID
     * @return array Response from API
     */
    public function getDocumentRecipient($recipientId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('get', "/document/recipient/{$recipientId}", [], 'v2');

            if ($response->failed()) {
                Log::error('Documenso get document recipient API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to get document recipient', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso get document recipient API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Update Document Recipient
     * 
     * @param string $recipientId Recipient ID
     * @param array $data Data to update
     * @return array Response from API
     */
    public function updateDocumentRecipient(Request $request)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $documentId = $request->input('documentId');
            $data = $request->input('data');

            $payload = [
                'recipient' => $data,
                'documentId' => $documentId
            ];
            $response = $this->makeRequest('put', "/document/recipient/update", $payload, 'v2');

            if ($response->failed()) {
                Log::error('Documenso update document recipient API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to update document recipient', 'details' => $response->json()];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso update document recipient API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete Document Recipient
     * 
     * @param string $recipientId Recipient ID
     * @return array Response from API
     */
    public function deleteDocumentRecipient($recipientId)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $response = $this->makeRequest('post', "/document/recipient/delete", [
                'recipientId' => $recipientId
            ], 'v2');
            if ($response->failed()) {
                Log::error('Documenso delete document recipient API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['error' => 'Failed to delete document recipient', 'details' => $response->json()];
            }
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Documenso delete document recipient API exception', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    public function updateDocument($documentId, $data)
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $payload = [
                'documentId' => $documentId,
                'data' => $data
            ];

            // Using v2 API endpoint as per the provided API spec
            $response = $this->makeRequest('post', '/document/update', $payload, 'v2');

            if ($response->failed()) {
                Log::error('Documenso API error updating document', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'document_id' => $documentId
                ]);
                return [
                    'error' => 'Failed to update document',
                    'status' => $response->status(),
                    'details' => $response->json()
                ];
            }

            Log::info('Documenso document updated successfully', [
                'document_id' => $documentId,
                'status' => $response->status()
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Documenso API exception updating document', [
                'message' => $e->getMessage(),
                'document_id' => $documentId
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    public function distributeDocument($documentId, $meta = [])
    {
        if (!$this->isEnabled()) {
            return ['error' => 'Documenso API is not enabled'];
        }

        try {
            $payload = [
                'sendEmail' => true,
                'sendCompletionEmails' => true
            ];

            Log::info('Documenso distribute document request', ['payload' => json_encode($payload)]);

            $response = $this->makeRequest('post', "/documents/{$documentId}/send", $payload);

            if ($response->failed()) {
                Log::error('Documenso distribute document API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);
                return ['error' => 'Failed to distribute document', 'details' => $response->json()];
            }

            $responseData = $response->json();

            Log::info('Documenso document distributed successfully', ['response' => $responseData]);
            return $responseData;

        } catch (\Exception $e) {
            Log::error('Documenso distribute document API exception', [
                'message' => $e->getMessage(),
                'document_id' => $documentId
            ]);
            return ['error' => $e->getMessage()];
        }
    }


}
