<?php

namespace App\Http\Controllers\Admin;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Deal;
use App\Models\DocumentSection;

class DealDocumentController extends Controller
{
    /**
     * Display a listing of the documents.
     */
    public function index()
    {
        $documents = Document::all();
        return response()->json($documents);
    }

    /**
     * Store a newly uploaded document in storage.
     */
    public function storeDocument(Request $request)
    {

        $validation = Validator::make($request->all(),[
            'deal_id' => 'nullable|exists:deals,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'section_id' => 'nullable',
            'offering_id' => 'nullable'
        ]);

        if($validation->fails())
        {
            return response()->json(['message' => $validation->errors()], 421);
        }

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        // $filePath = $file->storeAs('documents', $fileName,'public');
        // get only name without extension
        $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
        $deal = Deal::find($request->deal_id);
        $offering = Deal::find($request->offering_id);


        if ($request->section_id !== 'null') {
                $section = DocumentSection::find($request->section_id);
        } else {
           
            if(!$deal->document_sections()->where('name', 'Default')->count()){
                $section = DocumentSection::create([
                    'name' => 'Default',
                    'deal_id' => $deal->id
                ]);
            }
            else {
                $section = $deal->document_sections()->where('name', 'Default')->first();
            }
        }

        if($request->has('offering_id')){
            $section = DocumentSection::firstOrCreate([
                'name' => 'Offering documents',
                'deal_id' => $deal->id
            ]);
        }
        
        $folderPath = 'documents/' . $deal->id . '/' . $section->id . $section->name;
        

        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        $document = Document::create([
            'deal_id' => $request->deal_id,
            'offering_id' => $request->offering_id,
            'name' => $fileNameWithoutExtension,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_extension' => $fileExtension,
            'document_section_id' => $section->id,
            'date_added' => now()->format('Y-m-d'),
            'shared_with' => $request->shared_with,
            'visible_to_lp' => $request->visible_to_lp,
        ]);
        

        return response()->json([
            'message' => 'Document uploaded successfully!',
            'document' => $document,
        ]);
    }

    /**
     * Delete a document.
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();

        return response()->json(['success' => true,'message' => 'Document deleted successfully.']);
    }

    public function destroySection($id)
    {
        $document_section = DocumentSection::findOrFail($id);
        $document_section->documents()->delete();
        $document_section->delete();

        return response()->json(['success' => true,'message' => 'Document deleted successfully.']);
    }

    public function rename(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $document->name = $request->input('new_name');
        $document->save();

        return response()->json(['success' => true]);
    }

    public function renameSection(Request $request, $id)
    {
        $section_name = DocumentSection::findOrFail($id);
        $section_name->name = $request->input('new_name');
        $section_name->save();

        return response()->json(['success' => true]);
    }

    public function view($id)
    {
        $document = Document::findOrFail($id);
        if (filter_var($document->file_path, FILTER_VALIDATE_URL)) {
            return redirect($document->file_path);
        }
        return response()->file(storage_path('app/public/' . $document->file_path));
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'name' => 'required|string|max:255',
        ]);

        $section = DocumentSection::create([
            'deal_id' => $request->deal_id,
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'section' => $section]);
    }


    public function storeLink(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'link' => 'required|url',
        ]);

        if($validation->fails())
        {
            return response()->json(['message' => $validation->errors()], 421);
        }

        if($request->has('offering_id')){
            $section = DocumentSection::firstOrCreate([
                'name' => 'Offering documents',
                'deal_id' => $request->deal_id
            ]);
        }

        $document = Document::create([
            'deal_id' => $request->deal_id,
            'offering_id' => $request->offering_id,
            'name' => $request->name,
            'file_name' => $request->name,
            'file_path' => $request->link,
            'date_added' => now()->format('Y-m-d'),
        ]);

        return response()->json(['success' => true, 'message' => 'Document added successfully']);
    }
}
