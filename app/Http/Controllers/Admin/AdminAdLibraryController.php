<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminAdLibraryController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = AdMedia::with('uploader')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $media = $query->paginate(24)->withQueryString();

        $stats = [
            'total'    => AdMedia::count(),
            'images'   => AdMedia::whereIn('type', ['image', 'gif'])->count(),
            'videos'   => AdMedia::where('type', 'video')->count(),
            'docs'     => AdMedia::where('type', 'document')->count(),
            'size'     => AdMedia::sum('file_size'),
        ];

        return view('admin.ad-library.index', compact('media', 'stats'));
    }

    // ── Store (upload one or many files) ──────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'files'   => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => [
                'required', 'file',
                'mimes:jpeg,jpg,png,webp,gif,mp4,webm,pdf',
                'max:' . (AdMedia::maxUploadMb() * 1024),
            ],
        ]);

        $uploaded = 0;

        foreach ($request->file('files') as $file) {
            $mime      = $file->getMimeType();
            $type      = AdMedia::typeFromMime($mime);
            $slug      = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $path      = $file->store("ad-library/{$type}s", 'public');

            AdMedia::create([
                'name'          => $file->getClientOriginalName(),
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'disk'          => 'public',
                'mime_type'     => $mime,
                'type'          => $type,
                'file_size'     => $file->getSize(),
                'thumbnail_path'=> null,
                'uploaded_by'   => auth()->id(),
            ]);

            $uploaded++;
        }

        return back()->with('success', "{$uploaded} file(s) uploaded successfully.");
    }

    // ── Destroy ───────────────────────────────────────────────────
    public function destroy(AdMedia $adMedia)
    {
        // Prevent deletion if actively placed
        if ($adMedia->placements()->where('is_active', true)->exists()) {
            return back()->with('error', 'Cannot delete — this file is used in an active placement. Deactivate the placement first.');
        }

        Storage::disk($adMedia->disk)->delete($adMedia->file_path);

        if ($adMedia->thumbnail_path) {
            Storage::disk($adMedia->disk)->delete($adMedia->thumbnail_path);
        }

        $adMedia->delete();

        return back()->with('success', 'File deleted from library.');
    }

    // ── Rename ────────────────────────────────────────────────────
    public function rename(Request $request, AdMedia $adMedia)
    {
        $request->validate(['name' => ['required', 'string', 'max:200']]);

        $adMedia->update(['name' => $request->name]);

        return back()->with('success', 'File renamed.');
    }
}
