<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        // Retrieve all documents from the database
        // and pass them to the view
        $documents = \App\Admin\Document::all();

        return view('admin.documents.index', compact('documents')); // compact() is used to pass variables to the view

    }

    public function create()
    {
        // Return the form for creating a new document
        return view('admin.documents.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'title' =>'required|max:255',
            'description' =>'required',
            'file' =>'required|file|mimes:pdf,docx,xlsx'
        ]);

        // Save the document to the database
        $document = new \App\Admin\Document();
        $document->title = $request->title;
        $document->description = $request->description;

        // Upload the file to a temporary location
        $file = $request->file('file');
        $path = 'uploads/'. $file->getClientOriginalName();
        $file->move(public_path('uploads'), $file->getClientOriginalName());

        // Set the file path in the database
        $document->file_path = $path;

        $document->save();

        // Redirect to the document index page
        return redirect()->route('admin.documents.index')->with('success', 'Document created successfully!');   
    }

    public function edit($id)
    {
        // Retrieve the document from the database by its ID
        $document = \App\Admin\Document::find($id);

        // Return the form for editing the document
        return view('admin.documents.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'title' =>'required|max:255',
            'description' =>'required',
        ]);

        // Find the document in the database by its ID
        $document = \App\Admin\Document::find($id);

        // Update the document's attributes
        $document->title = $request->title;
        $document->description = $request->description;

        // If a new file is uploaded, delete the old file and upload the new one

    }

    public function destroy($id)
    {
        // Find the document in the database by its ID
        $document = \App\Admin\Document::find($id);

        // Delete the document from the database
        $document->delete();

        // Redirect to the document index page
        return redirect()->route('admin.documents.index')->with('success', 'Document deleted successfully!');
    }   

}
