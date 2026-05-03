<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = PartnerDocument::where('user_id', auth()->id());

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by type
        if ($request->filled('type') && array_key_exists($request->type, PartnerDocument::$types)) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_verified', false);
            }
        }

        $documents = $query->latest()->get();

        // Statistics (always from full set, not filtered)
        $allDocs     = PartnerDocument::where('user_id', auth()->id());
        $stats = [
            'total'      => (clone $allDocs)->count(),
            'verified'   => (clone $allDocs)->where('is_verified', true)->count(),
            'pending'    => (clone $allDocs)->where('is_verified', false)->count(),
            'total_size' => (clone $allDocs)->sum('file_size'),
        ];

        return view('partner.documents.index', compact('documents', 'stats'));
    }

    public function upload()
    {
        return view('partner.documents.upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|in:' . implode(',', array_keys(PartnerDocument::$types)),
            'file'  => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'notes' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $path = $file->store('partner-documents/' . auth()->id(), 'public');

        PartnerDocument::create([
            'user_id'   => auth()->id(),
            'title'     => $request->title,
            'type'      => $request->type,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'notes'     => $request->notes,
        ]);

        return redirect()->route('partner.documents.index')
                         ->with('success', 'Document uploaded successfully.');
    }

    public function show(PartnerDocument $document)
    {
        $this->authorise($document);
        return view('partner.documents.show', compact('document'));
    }

    public function download(PartnerDocument $document)
    {
        $this->authorise($document);
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(PartnerDocument $document)
    {
        $this->authorise($document);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('partner.documents.index')
                         ->with('success', 'Document deleted.');
    }

    private function authorise(PartnerDocument $document): void
    {
        abort_if($document->user_id !== auth()->id(), 403);
    }
}
