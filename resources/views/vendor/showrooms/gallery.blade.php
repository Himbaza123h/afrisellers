@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('vendor.showrooms.show', $showroom->id) }}"
           class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-black text-gray-900 uppercase lg:text-lg">Gallery Management</h1>
    </div>
    <p class="text-sm text-gray-600 ml-11">{{ $showroom->name }}</p>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="p-4 mb-6 bg-green-50 rounded-md border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="p-4 mb-6 bg-red-50 rounded-md border border-red-300">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-6 bg-red-50 rounded-md border border-red-300">
        <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Gallery Images -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Gallery Images ({{ $showroom->images ? count($showroom->images) : 0 }})</h2>
            </div>
            @if($showroom->images && count($showroom->images) > 0)
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($showroom->images as $image)
                            <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ $image }}"
                                     alt="Gallery Image"
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                    <button onclick="deleteImage('{{ $image }}')"
                                            class="opacity-0 group-hover:opacity-100 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-all">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-images text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600">No gallery images yet</p>
                    <p class="text-sm text-gray-500 mt-1">Upload images using the form on the right</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Form -->
    <div class="space-y-6">
        <!-- Featured Images -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Featured Images</h2>
            </div>
            <div class="p-6 space-y-4">
                @if($showroom->primary_image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primary Image</label>
                        <img src="{{ $showroom->primary_image }}"
                             alt="Primary"
                             class="w-full h-32 object-cover rounded-md border border-gray-200">
                    </div>
                @endif

                @if($showroom->logo_image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                        <img src="{{ $showroom->logo_image }}"
                             alt="Logo"
                             class="w-20 h-20 object-cover rounded-md border border-gray-200">
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ route('vendor.showrooms.edit', $showroom->id) }}"
                       class="block text-center text-sm text-purple-600 hover:text-purple-700 font-medium">
                        Update Featured Images <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Upload New Images -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Upload Images</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('vendor.showrooms.gallery.upload', $showroom->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Images *</label>
                        <input type="file"
                               name="images[]"
                               id="images"
                               accept="image/*"
                               multiple
                               required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            You can select multiple images. Max 2MB each.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full mt-6 px-6 py-2.5 bg-[#ff0808] text-white font-semibold rounded-md hover:bg-purple-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload Images
                    </button>
                </form>
            </div>
        </div>

        <!-- Gallery Tips -->
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-lightbulb text-blue-600 text-lg mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-bold text-blue-900 mb-2">Gallery Tips</h3>
                    <ul class="text-xs text-blue-800 space-y-1">
                        <li>• Use high-quality images for better presentation</li>
                        <li>• Show different angles of your showroom</li>
                        <li>• Include product displays and facilities</li>
                        <li>• Keep images well-lit and professional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-image-form" action="{{ route('vendor.showrooms.gallery.delete', $showroom->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
    <input type="hidden" name="image" id="image-to-delete">
</form>

<script>
function deleteImage(imagePath) {
    if (confirm('Are you sure you want to delete this image?')) {
        document.getElementById('image-to-delete').value = imagePath;
        document.getElementById('delete-image-form').submit();
    }
}
</script>
@endsection
