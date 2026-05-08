<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);

        $request->validate([
            'document_type' => 'required|string',
            'title' => 'required|string',
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');

        $path = $file->store('documents', 'public');

        $case->documents()->create([
            'document_type' => $request->document_type,
            'title' => $request->title,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $request->validate([
            'document_type' => 'required|string',
            'title' => 'required|string',
        ]);

        $document->update([
            'document_type' => $request->document_type,
            'title' => $request->title,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        return response()->json(['success' => true]);
    }
}