@extends('layouts.home')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">
<style>
    .slide-up {
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: slideUp 0.4s ease-out;
    }

    .tab-button {
        position: relative;
        transition: all 0.3s ease;
        padding: 14px 28px;
        border-bottom: 3px solid transparent;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
    }

    .tab-button:hover {
        background: #f9fafb;
        color: #374151;
    }

    .tab-button.active {
        color: #ff0808;
        font-weight: 600;
        border-bottom-color: #ff0808;
        background: #fff5f5;
    }

    .ql-editor {
        min-height: 180px;
        font-size: 15px;
        line-height: 1.6;
    }

    .ql-toolbar.ql-snow {
        border-radius: 8px 8px 0 0;
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .ql-container.ql-snow {
        border-radius: 0 0 8px 8px;
        border-color: #d1d5db;
    }

    #content-editor .ql-editor {
        min-height: 400px;
    }

    .form-section {
        opacity: 0;
        transform: translateY(20px);
        animation: slideUp 0.5s ease-out forwards;
    }

    .form-section:nth-child(1) { animation-delay: 0.05s; }
    .form-section:nth-child(2) { animation-delay: 0.1s; }
    .form-section:nth-child(3) { animation-delay: 0.15s; }
    .form-section:nth-child(4) { animation-delay: 0.2s; }
    .form-section:nth-child(5) { animation-delay: 0.25s; }
    .form-section:nth-child(6) { animation-delay: 0.3s; }
</style>
@endpush

@section('page-content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between slide-up">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.articles.show', $article) }}"
               class="p-2.5 text-gray-600 rounded-lg hover:bg-gray-100 transition-all hover:scale-105">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Article</h1>
                <p class="mt-1 text-sm text-gray-500">Update article information</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 {{ $article->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs font-semibold rounded-full">
                <i class="fas fa-circle text-xs mr-1"></i>
                {{ ucfirst($article->status) }}
            </span>
            @if($article->is_featured)
                <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full flex items-center gap-1">
                    <i class="fas fa-star"></i> Featured
                </span>
            @endif
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3 slide-up shadow-sm">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5 text-lg"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-900 mb-2">Please fix the following errors:</p>
                <ul class="text-sm text-red-700 space-y-1.5">
                    @foreach($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <i class="fas fa-circle text-xs mt-1.5"></i>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <button onclick="this.parentElement.remove()"
                    class="text-red-600 hover:text-red-800 transition-colors p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden slide-up" style="animation-delay: 0.2s;">
        <form action="{{ route('vendor.articles.update', $article) }}" method="POST" enctype="multipart/form-data" id="articleForm">
            @csrf
            @method('PUT')

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 bg-gray-50">
                <div class="flex overflow-x-auto">
                    <button type="button" class="tab-button active" onclick="switchTab(0)">
                        <i class="fas fa-info-circle mr-2"></i>
                        Basic Info
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab(1)">
                        <i class="fas fa-align-left mr-2"></i>
                        Content
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab(2)">
                        <i class="fas fa-user-edit mr-2"></i>
                        Author & Media
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab(3)">
                        <i class="fas fa-search mr-2"></i>
                        SEO & Publish
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                @include('vendor.articles.form')
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex gap-3 justify-end items-center">
                <a href="{{ route('vendor.articles.show', $article) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition-all text-sm">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-all font-semibold shadow-md text-sm hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <span>Update Article</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>
<script>
// Tab Switching
function switchTab(index) {
    // Remove active class from all tabs and contents
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    // Add active class to selected tab and content
    document.querySelectorAll('.tab-button')[index].classList.add('active');
    document.getElementById('tab-' + index).classList.add('active');
}

// Initialize Quill editors
let descriptionQuill, contentQuill;

document.addEventListener('DOMContentLoaded', function() {
    // Description Rich Text Editor
    descriptionQuill = new Quill('#description-editor', {
        theme: 'snow',
        placeholder: 'Brief description of your article...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link']
            ]
        }
    });

    // Set initial content for description
    const descValue = document.getElementById('description').value;
    if (descValue) {
        descriptionQuill.root.innerHTML = descValue;
    }

    // Content Rich Text Editor
    contentQuill = new Quill('#content-editor', {
        theme: 'snow',
        placeholder: 'Write your article content here...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['blockquote', 'code-block'],
                ['clean']
            ]
        }
    });

    // Set initial content for content
    const contentValue = document.getElementById('content').value;
    if (contentValue) {
        contentQuill.root.innerHTML = contentValue;
    }

    // Update hidden textareas before form submit
    document.getElementById('articleForm').addEventListener('submit', function() {
        document.getElementById('description').value = descriptionQuill.root.innerHTML;
        document.getElementById('content').value = contentQuill.root.innerHTML;
    });
});

// Auto-generate slug
document.getElementById('title').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});

// Tags management
function addTag() {
    const input = document.getElementById('tags_input');
    const tag = input.value.trim();

    if (!tag) return;

    const container = document.getElementById('tags-container');

    // Check if tag already exists
    const existingTags = Array.from(container.querySelectorAll('input[name="tags[]"]')).map(i => i.value);
    if (existingTags.includes(tag)) {
        alert('Tag already added');
        return;
    }

    const tagElement = document.createElement('span');
    tagElement.className = 'inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-medium';
    tagElement.innerHTML = `
        ${tag}
        <button type="button" onclick="removeTag(this, '${tag}')" class="hover:text-blue-900 ml-1">
            <i class="fas fa-times text-xs"></i>
        </button>
        <input type="hidden" name="tags[]" value="${tag}">
    `;

    container.appendChild(tagElement);
    input.value = '';
}

function removeTag(button, tag) {
    button.closest('span').remove();
}

// Enter key to add tag
document.getElementById('tags_input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addTag();
    }
});

// Featured image preview
function previewFeaturedImage(input) {
    const preview = document.getElementById('featured-preview');
    const previewImg = document.getElementById('featured-preview-img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
