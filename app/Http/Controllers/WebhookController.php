<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            // Validate webhook secret (optional, for realism)
            $secretKey = env('WEBHOOK_SECRET', 'm123321');
            $receivedSecret = $request->header('X-Documenso-Secret');
            Log::info('Received X-Documenso-Secret', ['received' => $receivedSecret]);
            // dd($secretKey);
            if ($secretKey && $receivedSecret !== $secretKey) {
                Log::warning('Invalid webhook secret');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid secret',
                ], 401);
            }

            // Get webhook payload
            $payload = $request->all();
            
            // Validate payload structure
            if (!isset($payload['event']) || !isset($payload['payload']) || !isset($payload['createdAt'])) {
                Log::warning('Invalid webhook payload structure');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payload structure',
                ], 400);
            }

            $event = $payload['event'];
            $documentId = $payload['payload']['id'] ?? 'N/A';
            $title = $payload['payload']['title'] ?? 'N/A';

            // Log the received webhook event
            Log::info("Mock webhook received: Event={$event}, DocumentID={$documentId}, Title={$title}");

            // Simulate processing for different events
            switch ($event) {
                case 'DOCUMENT_CREATED':
                    Log::info("Mock document created: ID={$documentId}, Title={$title}");
                    // Example: Save to database
                    \App\Models\Document::updateOrCreate(
                        ['external_id' => $documentId],
                        [
                            'title' => $title,
                            'status' => $payload['payload']['status'] ?? 'DRAFT',
                            'recipient_email' => $payload['payload']['Recipient'][0]['email'] ?? null,
                            'created_at' => $payload['payload']['createdAt'],
                        ]
                    );
                    break;

                case 'DOCUMENT_SENT':
                    Log::info("Mock document sent: ID={$documentId}");
                    // Example: Update document status
                    \App\Models\Document::where('external_id', $documentId)
                        ->update(['status' => 'PENDING']);
                    break;

                case 'DOCUMENT_OPENED':
                    Log::info("Mock document opened: ID={$documentId}");
                    // Example: Log analytics
                    break;

                case 'DOCUMENT_SIGNED':
                    Log::info("Mock document signed: ID={$documentId}");
                    \App\Models\Document::where('external_id', $documentId)
                        ->update(['status' => 'SIGNED']);
                    break;

                case 'DOCUMENT_COMPLETED':
                    Log::info("Mock document completed: ID={$documentId}");
                    \App\Models\Document::where('external_id', $documentId)
                        ->update(['status' => 'COMPLETED']);
                    break;

                case 'DOCUMENT_REJECTED':
                    Log::info("Mock document rejected: ID={$documentId}, Reason={$payload['payload']['Recipient'][0]['rejectionReason']}");
                    \App\Models\Document::where('external_id', $documentId)
                        ->update(['status' => 'REJECTED']);
                    break;

                case 'DOCUMENT_CANCELLED':
                    Log::info("Mock document cancelled: ID={$documentId}");
                    \App\Models\Document::where('external_id', $documentId)
                        ->update(['status' => 'CANCELLED']);
                    break;

                default:
                    Log::warning("Unhandled  webhook event: {$event}");
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Mock webhook processed successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mock webhook processing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Mock webhook processing failed',
            ], 500);
        }
    }
}