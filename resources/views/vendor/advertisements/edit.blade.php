@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('vendor.advertisements.index') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Advertisement</h1>
            <p class="mt-1 text-xs text-gray-500">{{ $advertisement->title }}</p>
        </div>
    </div>

    @if($advertisement->status === 'rejected')
    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
        <p class="text-sm font-semibold text-red-800"><i class="fas fa-times-circle mr-1"></i>Rejection Reason</p>
        <p class="text-sm text-red-700 mt-1">{{ $advertisement->rejection_reason }}</p>
        <p class="text-xs text-red-500 mt-2">Edit and resubmit to have your ad reviewed again.</p>
    </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <form action="{{ route('vendor.advertisements.update', $advertisement) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $advertisement->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm">
            </div>

            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-600">
                <span class="font-semibold">Position:</span> {{ $positions[$advertisement->position]['label'] ?? $advertisement->position }}
                &nbsp;|&nbsp;
                <span class="font-semibold">Size:</span> {{ $positions[$advertisement->position]['size'] ?? '—' }}
                &nbsp;|&nbsp;
                <span class="font-semibold">Type:</span> {{ ucfirst($advertisement->type) }}
            </div>

            @if($advertisement->media_url)
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Current Media</label>
                @if(in_array($advertisement->type, ['image','gif']))
                    <img src="{{ $advertisement->media_url }}" class="h-16 rounded border border-gray-200 object-cover">
                @elseif($advertisement->type === 'video')
                    <video src="{{ $advertisement->media_url }}" class="h-16 rounded border border-gray-200" controls></video>
                @endif
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Replace Media</label>
                <input type="file" name="media" accept="image/*,video/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <p class="mt-1 text-xs text-gray-500">Leave blank to keep existing media</p>
            </div>

            @if($advertisement->type === 'text')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Background Gradient</label>
                <input type="text" name="bg_gradient" value="{{ old('bg_gradient', $advertisement->bg_gradient) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Headline</label>
                    <input type="text" name="headline" value="{{ old('headline', $advertisement->headline) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sub Text</label>
                    <input type="text" name="sub_text" value="{{ old('sub_text', $advertisement->sub_text) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Badge</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', $advertisement->badge_text) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination URL</label>
                <input type="url" name="destination_url" value="{{ old('destination_url', $advertisement->destination_url) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                <input type="color" name="accent_color" value="{{ old('accent_color', $advertisement->accent_color) }}"
                       class="w-10 h-10 border border-gray-300 rounded cursor-pointer">
            </div>

            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] font-medium text-sm">
                    <i class="fas fa-save mr-1"></i>
                    {{ $advertisement->status === 'rejected' ? 'Resubmit for Review' : 'Update Advertisement' }}
                </button>
                <a href="{{ route('vendor.advertisements.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
