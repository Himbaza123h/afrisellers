<!-- Tab 0: Basic Information -->
<div class="tab-content active" id="tab-0">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-5">
            <!-- Title -->
            <div class="form-section">
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-heading text-blue-500 mr-1"></i>
                    Title <span class="text-red-600">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $article->title ?? '') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition-all"
                    placeholder="Enter article title">
                @error('title')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="form-section">
                <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-link text-purple-500 mr-1"></i>
                    Slug (Auto-generated)
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $article->slug ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-sm font-mono"
                    placeholder="article-slug" readonly>
                @error('slug')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div class="form-section">
                <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-folder text-yellow-500 mr-1"></i>
                    Category
                </label>
                <input type="text" name="category" id="category" value="{{ old('category', $article->category ?? '') }}"
                    list="categories"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Enter or select category">
                <datalist id="categories">
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
                @error('category')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-5">
            <!-- Reading Time -->
            <div class="form-section">
                <label for="reading_time" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clock text-green-500 mr-1"></i>
                    Reading Time (minutes)
                </label>
                <input type="number" name="reading_time_minutes" id="reading_time"
                    value="{{ old('reading_time_minutes', $article->reading_time_minutes ?? '') }}" min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Auto-calculated">
                @error('reading_time_minutes')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags -->
            <div class="form-section">
                <label for="tags_input" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tags text-pink-500 mr-1"></i>
                    Tags
                </label>
                <div id="tags-container" class="flex flex-wrap gap-2 mb-3">
                    @if(isset($article) && $article->tags)
                        @foreach($article->tags as $tag)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-medium">
                                {{ $tag }}
                                <button type="button" onclick="removeTag(this, '{{ $tag }}')" class="hover:text-blue-900 ml-1">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <input type="hidden" name="tags[]" value="{{ $tag }}">
                            </span>
                        @endforeach
                    @endif
                </div>
                <div class="flex gap-2">
                    <input type="text" id="tags_input"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Type tag and press Enter">
                    <button type="button" onclick="addTag()" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-all">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>
                @error('tags')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Short Description with Rich Text -->
    <div class="form-section mt-6">
        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-align-left text-indigo-500 mr-1"></i>
            Short Description
        </label>
        <div id="description-editor" class="bg-white"></div>
        <textarea name="description" id="description" class="hidden">{{ old('description', $article->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Tab 1: Content -->
<div class="tab-content" id="tab-1">
    <div class="form-section">
        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-file-alt text-blue-500 mr-1"></i>
            Article Content <span class="text-red-600">*</span>
        </label>
        <div id="content-editor" class="bg-white"></div>
        <textarea name="content" id="content" class="hidden" required>{{ old('content', $article->content ?? '') }}</textarea>
        @error('content')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Tab 2: Author & Media -->
<div class="tab-content" id="tab-2">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column - Author Info -->
        <div class="space-y-5">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 pb-3 border-b">
                <i class="fas fa-user-edit text-purple-600"></i>
                Author Information
            </h3>

            <!-- Author Name -->
            <div class="form-section">
                <label for="author_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-blue-500 mr-1"></i>
                    Author Name
                </label>
                <input type="text" name="author_name" id="author_name"
                    value="{{ old('author_name', $article->author_name ?? auth()->user()->name) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Enter author name">
                @error('author_name')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author Title -->
            <div class="form-section">
                <label for="author_title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-briefcase text-purple-500 mr-1"></i>
                    Author Title
                </label>
                <input type="text" name="author_title" id="author_title"
                    value="{{ old('author_title', $article->author_title ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="e.g., Senior Writer">
                @error('author_title')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author Bio -->
            <div class="form-section">
                <label for="author_bio" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-info-circle text-green-500 mr-1"></i>
                    Author Bio
                </label>
                <textarea name="author_bio" id="author_bio" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Brief author biography">{{ old('author_bio', $article->author_bio ?? '') }}</textarea>
                @error('author_bio')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author Avatar -->
            <div class="form-section">
                <label for="author_avatar" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user-circle text-indigo-500 mr-1"></i>
                    Author Avatar
                </label>
                @if(isset($article) && $article->author_avatar)
                    <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                        <img src="{{ Storage::url($article->author_avatar) }}" alt="Current avatar" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300 shadow-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remove_author_avatar" value="1" class="rounded text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-600">Remove current avatar</span>
                        </label>
                    </div>
                @endif
                <input type="file" name="author_avatar" id="author_avatar" accept="image/*"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Recommended: 200x200px, max 2MB
                </p>
                @error('author_avatar')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Right Column - Featured Image -->
        <div class="space-y-5">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 pb-3 border-b">
                <i class="fas fa-image text-pink-600"></i>
                Featured Image
            </h3>

            @if(isset($article) && $article->featured_image)
                @php
                    $imageUrl = (str_starts_with($article->featured_image, 'http://') || str_starts_with($article->featured_image, 'https://'))
                        ? $article->featured_image
                        : Storage::url($article->featured_image);
                @endphp
                <div class="form-section">
                    <div class="relative rounded-lg overflow-hidden border-2 border-gray-200 shadow-sm">
                        <img src="{{ $imageUrl }}" alt="Current featured image" class="w-full h-56 object-cover">
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 bg-green-500 text-white text-xs font-medium rounded-md">Current</span>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 mt-3 p-2 bg-gray-50 rounded-lg cursor-pointer">
                        <input type="checkbox" name="remove_featured_image" value="1" class="rounded text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-600">Remove current image</span>
                    </label>
                </div>
            @endif

            <!-- Upload New Image -->
            <div class="form-section">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-upload text-blue-500 mr-1"></i>
                    Upload New Image
                </label>
                <input type="file" name="featured_image" id="featured_image" accept="image/*"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                    onchange="previewFeaturedImage(this)">
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Recommended: 1200x630px, max 5MB
                </p>
                @error('featured_image')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Preview -->
            <div id="featured-preview" class="hidden form-section">
                <p class="text-sm font-semibold text-gray-700 mb-2">Preview:</p>
                <div class="rounded-lg overflow-hidden border-2 border-gray-200 shadow-sm">
                    <img id="featured-preview-img" class="w-full h-56 object-cover">
                </div>
            </div>

            <!-- Image Caption -->
            <div class="form-section">
                <label for="featured_image_caption" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-closed-captioning text-yellow-500 mr-1"></i>
                    Image Caption
                </label>
                <input type="text" name="featured_image_caption" id="featured_image_caption"
                    value="{{ old('featured_image_caption', $article->featured_image_caption ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Optional image caption">
                @error('featured_image_caption')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<!-- Tab 3: SEO & Publishing -->
<div class="tab-content" id="tab-3">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column - SEO -->
        <div class="space-y-5">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 pb-3 border-b">
                <i class="fas fa-search text-green-600"></i>
                SEO Settings
            </h3>

            <!-- Meta Title -->
            <div class="form-section">
                <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-heading text-blue-500 mr-1"></i>
                    Meta Title
                </label>
                <input type="text" name="meta_title" id="meta_title"
                    value="{{ old('meta_title', $article->meta_title ?? '') }}" maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Leave empty to use article title">
                @error('meta_title')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Meta Description -->
            <div class="form-section">
                <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left text-purple-500 mr-1"></i>
                    Meta Description
                </label>
                <textarea name="meta_description" id="meta_description" rows="3" maxlength="500"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="SEO description (max 500 characters)">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
                @error('meta_description')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Meta Keywords -->
            <div class="form-section">
                <label for="meta_keywords" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-key text-yellow-500 mr-1"></i>
                    Meta Keywords
                </label>
                <input type="text" name="meta_keywords" id="meta_keywords"
                    value="{{ old('meta_keywords', $article->meta_keywords ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="keyword1, keyword2, keyword3">
                @error('meta_keywords')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Right Column - Publishing Options -->
        <div class="space-y-5">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 pb-3 border-b">
                <i class="fas fa-cog text-gray-600"></i>
                Publishing Options
            </h3>

            <!-- Status -->
            <div class="form-section">
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-toggle-on text-green-500 mr-1"></i>
                    Status <span class="text-red-600">*</span>
                </label>
                <select name="status" id="status" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="draft" {{ old('status', $article->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $article->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Publish Date -->
            <div class="form-section">
                <label for="published_at" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-blue-500 mr-1"></i>
                    Publish Date
                </label>
                <input type="datetime-local" name="published_at" id="published_at"
                    value="{{ old('published_at', isset($article->published_at) ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                @error('published_at')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Options -->
            <div class="form-section space-y-3 p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-lg transition-all">
                    <input type="checkbox" name="is_featured" value="1"
                        {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }}
                        class="w-5 h-5 text-yellow-600 rounded focus:ring-2 focus:ring-yellow-500">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-star text-yellow-500 mr-1"></i> Feature this article
                    </span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-lg transition-all">
                    <input type="checkbox" name="allow_comments" value="1"
                        {{ old('allow_comments', $article->allow_comments ?? true) ? 'checked' : '' }}
                        class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-comments text-blue-500 mr-1"></i> Allow comments
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>
