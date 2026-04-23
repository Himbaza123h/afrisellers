@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-4">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.configurations.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Create Configuration</h1>
            <p class="mt-1 text-xs text-gray-500">Add a new system configuration</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
       <form action="{{ route('admin.configurations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('title') border-red-500 @enderror"
                    placeholder="e.g., Site Name, Max Products Per User" required>
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">A human-readable title for this configuration</p>
            </div>

            <!-- Unique ID -->
            <div>
                <label for="unique_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Unique ID
                </label>
                <input type="text" name="unique_id" id="unique_id" value="{{ old('unique_id') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('unique_id') border-red-500 @enderror"
                    placeholder="e.g., site_name, max_products_per_user">
                @error('unique_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from title. Use underscores, no spaces.</p>
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('type') border-red-500 @enderror"
                    required onchange="updateValuePlaceholder()">
                    <option value="">Select Type</option>
                    <option value="string"  {{ old('type') === 'string'  ? 'selected' : '' }}>String</option>
                    <option value="integer" {{ old('type') === 'integer' ? 'selected' : '' }}>Integer</option>
                    <option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                    <option value="array"   {{ old('type') === 'array'   ? 'selected' : '' }}>Array</option>
                    <option value="json"    {{ old('type') === 'json'    ? 'selected' : '' }}>JSON</option>
                    <option value="text"    {{ old('type') === 'text'    ? 'selected' : '' }}>Text</option>
                    <option value="file"    {{ old('type') === 'file'    ? 'selected' : '' }}>File</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Value -->
            <div>
                <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                    Value <span class="text-red-500">*</span>
                </label>

                <!-- String/Integer Input -->
                <input type="text" name="value" id="value-input" value="{{ old('value') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('value') border-red-500 @enderror"
                    placeholder="Enter value" required>

                <!-- Text Area (hidden by default) -->
                <textarea name="value" id="value-textarea" rows="4" style="display: none;"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('value') border-red-500 @enderror"
                    placeholder="Enter value">{{ old('value') }}</textarea>

                <!-- Boolean Select (hidden by default) -->
                <select name="value" id="value-boolean" style="display: none;"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('value') border-red-500 @enderror">
                    <option value="1" {{ old('value') == '1' ? 'selected' : '' }}>True</option>
                    <option value="0" {{ old('value') == '0' ? 'selected' : '' }}>False</option>
                </select>

                <!-- File Input — multiple (hidden by default) -->
                <input type="file" name="files[]" id="value-file" style="display: none;" multiple
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('files') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-400" id="files-hint" style="display:none;">
                    <i class="fas fa-info-circle mr-1"></i> You can select multiple files (e.g. slides/images)
                </p>

                @error('value')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('files')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('files.*')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1 text-xs text-gray-500" id="value-help">Enter the configuration value</p>
            </div>

            <!-- Country (optional) -->
            <div>
                <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Country <span class="text-gray-400 font-normal text-xs">(optional — leave blank for global)</span>
                </label>
                <select name="country_id" id="country_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">Global (All Countries)</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808]">
                <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium text-sm">
                    <i class="fas fa-save mr-1"></i> Create Configuration
                </button>
                <a href="{{ route('admin.configurations.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateValuePlaceholder() {
    const type = document.getElementById('type').value;
    const valueInput     = document.getElementById('value-input');
    const valueTextarea  = document.getElementById('value-textarea');
    const valueBoolean   = document.getElementById('value-boolean');
    const valueFile      = document.getElementById('value-file');
    const filesHint      = document.getElementById('files-hint');
    const valueHelp      = document.getElementById('value-help');

    // Hide all
    valueInput.style.display    = 'none';
    valueTextarea.style.display = 'none';
    valueBoolean.style.display  = 'none';
    valueFile.style.display     = 'none';
    filesHint.style.display     = 'none';

    // Remove required
    valueInput.removeAttribute('required');
    valueTextarea.removeAttribute('required');
    valueBoolean.removeAttribute('required');
    valueFile.removeAttribute('required');

    // Clear names
    valueInput.removeAttribute('name');
    valueTextarea.removeAttribute('name');
    valueBoolean.removeAttribute('name');

    switch(type) {
        case 'file':
            valueFile.style.display = 'block';
            valueFile.setAttribute('required', 'required');
            filesHint.style.display = 'block';
            valueHelp.textContent = 'Select one or more files (max 10MB each)';
            break;
        case 'boolean':
            valueBoolean.style.display = 'block';
            valueBoolean.setAttribute('required', 'required');
            valueBoolean.setAttribute('name', 'value');
            valueHelp.textContent = 'Select true or false';
            break;
        case 'text':
            valueTextarea.style.display = 'block';
            valueTextarea.setAttribute('required', 'required');
            valueTextarea.setAttribute('name', 'value');
            valueHelp.textContent = 'Enter long text content';
            break;
        case 'array':
            valueTextarea.style.display = 'block';
            valueTextarea.setAttribute('required', 'required');
            valueTextarea.setAttribute('name', 'value');
            valueTextarea.setAttribute('placeholder', '["item1", "item2", "item3"]');
            valueHelp.textContent = 'Enter a valid JSON array, e.g., ["US", "UK", "RW"]';
            break;
        case 'json':
            valueTextarea.style.display = 'block';
            valueTextarea.setAttribute('required', 'required');
            valueTextarea.setAttribute('name', 'value');
            valueTextarea.setAttribute('placeholder', '{"key": "value"}');
            valueHelp.textContent = 'Enter valid JSON, e.g., {"name": "John", "age": 30}';
            break;
        case 'integer':
            valueInput.style.display = 'block';
            valueInput.setAttribute('required', 'required');
            valueInput.setAttribute('name', 'value');
            valueInput.setAttribute('type', 'number');
            valueInput.setAttribute('placeholder', 'e.g., 100');
            valueHelp.textContent = 'Enter a whole number';
            break;
        case 'string':
        default:
            valueInput.style.display = 'block';
            valueInput.setAttribute('required', 'required');
            valueInput.setAttribute('name', 'value');
            valueInput.setAttribute('type', 'text');
            valueInput.setAttribute('placeholder', 'Enter value');
            valueHelp.textContent = 'Enter a text value';
            break;
    }
}

document.getElementById('title').addEventListener('input', function() {
    const uniqueIdInput = document.getElementById('unique_id');
    if (!uniqueIdInput.value) {
        uniqueIdInput.value = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    updateValuePlaceholder();
});
</script>
@endsection
