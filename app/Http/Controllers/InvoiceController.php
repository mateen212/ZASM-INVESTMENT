<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function downloadInvoice(Request $request)
    {
        // Validate input
        $request->validate([
            'investment_id' => 'required',
            'receiving_bank' => 'required',
            'bank_address' => 'required',
            'routing_no' => 'required',
            'account_no' => 'required',
            'account_type' => 'required',
            'beneficiary_account_name' => 'required',
            'beneficiary_address' => 'required',
        ]);

        // Get username (from auth or fallback)
        $username = auth()->check() ? auth()->user()->username : 'guest';

        // Prepare data
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
            'username' => $username,
            'generated_at' => date('Y-m-d H:i:s'), // PHP command for timestamp
        ];

        // Generate PDF
        $pdf = Pdf::loadView('invoices.invoice', $data);

        // Define save path
        $invoiceDir = storage_path("app/public/invoices/{$username}");
        $filename = "Investment_{$data['investment_id']}_Invoice.pdf";
        $fullPath = "{$invoiceDir}/{$filename}";

        // Create directory
        if (!file_exists($invoiceDir)) {
            mkdir($invoiceDir, 0755, true); // PHP command for directory creation
        }

        // Save PDF
        file_put_contents($fullPath, $pdf->output()); // PHP command to save file

        // Log action
        file_put_contents(
            storage_path('logs/laravel.log'),
            "[" . date('Y-m-d H:i:s') . "] Invoice generated for {$username}: {$fullPath}\n",
            FILE_APPEND
        ); // PHP command for logging

        // Download PDF
        return $pdf->download($filename);
    }
}