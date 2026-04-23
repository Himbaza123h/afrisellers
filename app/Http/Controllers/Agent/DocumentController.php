<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    // ─── Allowed file types ───────────────────────────────────────────
    private const ALLOWED_MIMES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv',
        'txt', 'png', 'jpg', 'jpeg', 'gif', 'webp',
        'zip', 'rar', 'ppt', 'pptx',
    ];

    private const MAX_SIZE_KB = 20480; // 20 MB

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();

        $query = AgentDocument::forAgent($agentId)
            ->when($request->search, fn($q) =>
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('file_name', 'like', "%{$request->search}%")
            )
            ->when($request->category, fn($q) =>
                $q->where('category', $request->category)
            )
            ->when($request->shared, fn($q) =>
                $q->where('is_shared', true)
            );

        // Sort
        $sort = $request->sort ?? 'latest';
        match($sort) {
            'oldest' => $query->oldest(),
            'name'   => $query->orderBy('title'),
            'size'   => $query->orderByDesc('file_size'),
            default  => $query->latest(),
        };

        $documents = $query->paginate(16)->withQueryString();

        // Stats
        $stats = [
            'total'          => AgentDocument::forAgent($agentId)->count(),
            'shared'         => AgentDocument::forAgent($agentId)->shared()->count(),
            'expiring_soon'  => AgentDocument::forAgent($agentId)->expiringSoon()->count(),
            'expired'        => AgentDocument::forAgent($agentId)->expired()->count(),
            'total_size'     => AgentDocument::forAgent($agentId)->sum('file_size'),
        ];

        // Category breakdown
        $categoryBreakdown = AgentDocument::forAgent($agentId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return view('agent.documents.index', compact(
            'documents', 'stats', 'categoryBreakdown'
        ));
    }

    // ─── UPLOAD (form) ────────────────────────────────────────────────
    public function upload()
    {
        return view('agent.documents.upload');
    }

    // ─── STORE ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category'    => 'required|in:contract,invoice,identity,agreement,report,license,other',
            'file'        => 'required|file|mimes:' . implode(',', self::ALLOWED_MIMES)
                             . '|max:' . self::MAX_SIZE_KB,
            'tags'        => 'nullable|string|max:500',
            'is_shared'   => 'nullable|boolean',
            'expires_at'  => 'nullable|date|after:today',
        ]);

        $file     = $request->file('file');
        $folder   = 'agent-documents/' . auth()->id();
        $fileName = $file->getClientOriginalName();
        $stored   = $file->storeAs(
            $folder,
            Str::uuid() . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        // Parse comma-separated tags
        $tags = null;
        if ($request->filled('tags')) {
            $tags = array_values(array_filter(
                array_map('trim', explode(',', $request->tags))
            ));
        }

        AgentDocument::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'file_name'   => $fileName,
            'file_path'   => $stored,
            'file_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
            'category'    => $request->category,
            'tags'        => $tags,
            'is_shared'   => $request->boolean('is_shared'),
            'requires_attention' => $request->boolean('requires_attention'),
            'expires_at'  => $request->expires_at ?: null,
        ]);

        return redirect()
            ->route('agent.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show($document)
    {
        $document = AgentDocument::forAgent(auth()->id())
            ->findOrFail($document);

        return view('agent.documents.show', compact('document'));
    }

    // ─── DOWNLOAD ─────────────────────────────────────────────────────
    public function download($document)
    {
        $document = AgentDocument::forAgent(auth()->id())
            ->findOrFail($document);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found on storage.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    // ─── DESTROY ──────────────────────────────────────────────────────
    public function destroy($document)
    {
        $document = AgentDocument::forAgent(auth()->id())
            ->findOrFail($document);

        // Deletes file from storage via model override
        $document->delete();

        return redirect()
            ->route('agent.documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
