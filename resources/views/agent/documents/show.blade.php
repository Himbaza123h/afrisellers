@extends('layouts.home')

@section('page-content')
<div class="space-y-5 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3">
            <a href="{{ route('agent.documents.index') }}"
               class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors mt-0.5 flex-shrink-0">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-gray-900 leading-snug">{{ $document->title }}</h1>
                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $document->file_name }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            <a href="{{ route('agent.documents.download', $document->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold shadow-sm">
                <i class="fas fa-download"></i> Download
            </a>
            <form action="{{ route('agent.documents.destroy', $document->id) }}" method="POST"
                  onsubmit="return confirm('Delete this document permanently?')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-sm font-semibold shadow-sm">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Expiry alert --}}
    @if($document->isExpired())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 flex-shrink-0"></i>
            <p class="text-sm text-red-800 font-medium">
                This document expired on <strong>{{ $document->expires_at->format('M d, Y') }}</strong>.
                Please upload an updated version.
            </p>
        </div>
    @elseif($document->isExpiringSoon())
        <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 flex items-center gap-3">
            <i class="fas fa-clock text-amber-500 flex-shrink-0"></i>
            <p class="text-sm text-amber-800 font-medium">
                This document expires on <strong>{{ $document->expires_at->format('M d, Y') }}</strong>
                ({{ $document->expires_at->diffForHumans() }}).
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Preview / Icon Panel --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- File Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center">
                <div class="w-20 h-20 bg-gray-50 border-2 border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas {{ $document->icon }} text-4xl"></i>
                </div>
                <p class="text-sm font-bold text-gray-800">{{ Str::limit($document->file_name, 30) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $document->formatted_size }}</p>

                <div class="flex flex-wrap justify-center gap-2 mt-3">
                    @if($document->is_shared)
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-share-alt mr-1"></i> Shared
                        </span>
                    @endif
                    @if($document->isExpired())
                        <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded-full">
                            <i class="fas fa-times-circle mr-1"></i> Expired
                        </span>
                    @elseif($document->isExpiringSoon())
                        <span class="px-2 py-1 bg-amber-100 text-amber-600 text-xs font-semibold rounded-full">
                            <i class="fas fa-clock mr-1"></i> Expiring Soon
                        </span>
                    @endif
                </div>

                <a href="{{ route('agent.documents.download', $document->id) }}"
                   class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm transition-colors">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>

            {{-- Meta --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">File Info</h3>
                <dl class="space-y-3">
                    @foreach([
                        ['Type',      strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION))],
                        ['MIME',      $document->file_type],
                        ['Size',      $document->formatted_size],
                        ['Category',  ucfirst($document->category)],
                        ['Uploaded',  $document->created_at->format('M d, Y')],
                        ['Expires',   $document->expires_at?->format('M d, Y') ?? 'Never'],
                    ] as [$label, $value])
                        <div class="flex items-start justify-between gap-2">
                            <dt class="text-xs text-gray-400 flex-shrink-0">{{ $label }}</dt>
                            <dd class="text-xs font-medium text-gray-700 text-right break-all">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        {{-- Details Panel --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Description --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Description</h3>
                @if($document->description)
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $document->description }}</p>
                @else
                    <p class="text-sm text-gray-400 italic">No description provided.</p>
                @endif
            </div>

            {{-- Tags --}}
            @if($document->tags && count($document->tags) > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($document->tags as $tag)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                            <i class="fas fa-tag text-[10px]"></i> {{ $tag }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Sharing info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Sharing</h3>
                @if($document->is_shared)
                    <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <i class="fas fa-share-alt text-purple-500 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Shared with vendors</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                All vendors you manage can view and download this document.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <i class="fas fa-lock text-gray-400 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Private</p>
                            <p class="text-xs text-gray-400 mt-0.5">Only visible to you.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quick actions --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Actions</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <a href="{{ route('agent.documents.download', $document->id) }}"
                       class="flex items-center gap-3 px-4 py-3 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fas fa-download text-blue-600 w-4 text-center"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Download</p>
                            <p class="text-xs text-blue-500">Save to your device</p>
                        </div>
                    </a>
                    <a href="{{ route('agent.documents.upload') }}"
                       class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-100 rounded-lg hover:bg-green-100 transition-colors">
                        <i class="fas fa-upload text-green-600 w-4 text-center"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Upload New Version</p>
                            <p class="text-xs text-green-500">Replace with updated file</p>
                        </div>
                    </a>
                    <form action="{{ route('agent.documents.destroy', $document->id) }}" method="POST"
                          onsubmit="return confirm('Delete this document permanently?')" class="contents">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors text-left w-full">
                            <i class="fas fa-trash text-red-500 w-4 text-center"></i>
                            <div>
                                <p class="text-sm font-semibold text-red-700">Delete</p>
                                <p class="text-xs text-red-400">Permanently remove</p>
                            </div>
                        </button>
                    </form>
                    <a href="{{ route('agent.documents.index') }}"
                       class="flex items-center gap-3 px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-arrow-left text-gray-500 w-4 text-center"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Back to Library</p>
                            <p class="text-xs text-gray-400">View all documents</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
