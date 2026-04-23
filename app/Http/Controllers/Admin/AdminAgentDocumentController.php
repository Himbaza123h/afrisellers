<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAgentDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = AgentDocument::with('user')
            ->orderByDesc('requires_attention') // attention docs float first
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('file_name', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) =>
                      $u->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                  );
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('attention')) {
            $query->where('requires_attention', true);
        }
        if ($request->filled('shared')) {
            $query->where('is_shared', true);
        }

        $documents = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => AgentDocument::count(),
            'attention' => AgentDocument::where('requires_attention', true)->count(),
            'shared'    => AgentDocument::where('is_shared', true)->count(),
            'expired'   => AgentDocument::whereNotNull('expires_at')->where('expires_at', '<', now())->count(),
        ];

        $filterUser = $request->filled('user_id')
            ? \App\Models\User::find($request->user_id)
            : null;

        return view('admin.documents.index', compact('documents', 'stats', 'filterUser'));
    }

    public function show($document)
    {
        $document = AgentDocument::with('user')->findOrFail($document);
        return view('admin.documents.show', compact('document'));
    }

    public function download($document)
    {
        $document = AgentDocument::findOrFail($document);
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function clearAttention($document)
    {
        $document = AgentDocument::findOrFail($document);
        $document->update(['requires_attention' => false]);
        return back()->with('success', 'Attention flag cleared.');
    }

    public function destroy($document)
    {
        $document = AgentDocument::findOrFail($document);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('admin.documents.index')->with('success', 'Document deleted.');
    }
}
