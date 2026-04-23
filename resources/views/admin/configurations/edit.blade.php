@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-4">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.configurations.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Configuration</h1>
            <p class="mt-1 text-xs text-gray-500">Update configuration: {{ $configuration->unique_id }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.configurations.update', $configuration) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Unique ID -->
            <div>
                <label for="unique_id" class="block text-sm font-medium text-gray-700 mb-1">Unique ID</label>
                <input type="text" name="unique_id" id="unique_id" value="{{ old('unique_id', $configuration->unique_id) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('unique_id') border-red-500 @enderror"
                    placeholder="e.g., site_name, max_products_per_user">
                @error('unique_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Use underscores, no spaces.</p>
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
                    <option value="string"  {{ old('type', $configuration->type) === 'string'  ? 'selected' : '' }}>String</option>
                    <option value="integer" {{ old('type', $configuration->type) === 'integer' ? 'selected' : '' }}>Integer</option>
                    <option value="boolean" {{ old('type', $configuration->type) === 'boolean' ? 'selected' : '' }}>Boolean</option>
                    <option value="array"   {{ old('type', $configuration->type) === 'array'   ? 'selected' : '' }}>Array</option>
                    <option value="json"    {{ old('type', $configuration->type) === 'json'    ? 'selected' : '' }}>JSON</option>
                    <option value="text"    {{ old('type', $configuration->type) === 'text'    ? 'selected' : '' }}>Text</option>
                    <option value="file"    {{ old('type', $configuration->type) === 'file'    ? 'selected' : '' }}>File</option>
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

                @php
                    $currentValue = old('value', $configuration->value);
                    if (in_array($configuration->type, ['array', 'json']) && is_array($currentValue)) {
                        $currentValue = json_encode($currentValue, JSON_PRETTY_PRINT);
                    }
                @endphp

                <!-- String/Integer Input -->
                <input type="text" name="value" id="value-input" value="{{ $currentValue }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('value') border-red-500 @enderror"
                    placeholder="Enter value" required>

                <!-- Text Area (hidden by default) -->
                <textarea name="value" id="value-textarea" rows="6" style="display: none;"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('value') border-red-500 @enderror font-mono text-sm"
                    placeholder="Enter value">{{ $currentValue }}</textarea>

                <!-- Boolean Select (hidden by default) -->
                <select name="value" id="value-boolean" style="display: none;"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('value') border-red-500 @enderror">
                    <option value="1" {{ $configuration->value == true  ? 'selected' : '' }}>True</option>
                    <option value="0" {{ $configuration->value == false ? 'selected' : '' }}>False</option>
                </select>

                <!-- File Input — multiple (hidden by default) -->
                <div id="value-file-container" style="display: none;">
                    <input type="file" name="files[]" id="value-file" multiple
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('files') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Select new files to replace all existing ones. You can select multiple (e.g. slides).
                    </p>

                    {{-- Show existing files --}}
                    @if($configuration->type === 'file' && $configuration->files)
                        <div class="mt-3">
                            <p class="text-xs font-medium text-gray-600 mb-2">
                                Current files ({{ count($configuration->files) }}):
                            </p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-2">
                                @foreach($configuration->files as $file)
                                    @php $ext = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp','svg']))
                                        <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square group">
                                            <img src="{{ $file['url'] }}" class="w-full h-full object-cover">
                                            <p class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-[9px] px-1 py-0.5 truncate">
                                                {{ $file['name'] }}
                                            </p>
                                            <label class="absolute top-1 right-1 bg-white/90 rounded px-1 py-0.5 flex items-center gap-1 cursor-pointer shadow text-[10px] text-red-600 font-medium">
                                                <input type="checkbox" name="remove_files[]" value="{{ $file['path'] }}" class="w-3 h-3 accent-red-600">
                                                Remove
                                            </label>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-1 p-2 border border-gray-200 rounded-lg text-xs">
                                            <a href="{{ $file['url'] }}" target="_blank"
                                            class="flex items-center gap-1 text-blue-600 hover:underline truncate">
                                                <i class="fas fa-file text-[10px]"></i> {{ $file['name'] }}
                                            </a>
                                            <label class="flex items-center gap-1 text-red-600 cursor-pointer mt-1">
                                                <input type="checkbox" name="remove_files[]" value="{{ $file['path'] }}" class="w-3 h-3 accent-red-600">
                                                Remove
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif($configuration->type === 'file' && $configuration->value)
                        {{-- Legacy single file --}}
                        <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                            <p class="text-xs text-gray-600">Current file:</p>
                            <a href="{{ Storage::url($configuration->value) }}" target="_blank" class="text-xs text-[#ff0808] hover:underline">
                                {{ basename($configuration->value) }}
                            </a>
                        </div>
                    @endif
                </div>

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
                        <option value="{{ $country->id }}"
                            {{ old('country_id', $configuration->country_id) == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $configuration->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808]">
                <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
            </div>

            <!-- Metadata -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <h3 class="text-xs font-semibold text-gray-700 mb-2">Metadata</h3>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="text-gray-900 font-medium ml-1">{{ $configuration->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created:</span>
                        <span class="text-gray-900 font-medium ml-1">{{ $configuration->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Last Updated:</span>
                        <span class="text-gray-900 font-medium ml-1">{{ $configuration->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($configuration->country_id)
                    <div>
                        <span class="text-gray-500">Country:</span>
                        <span class="text-gray-900 font-medium ml-1">{{ optional($configuration->country)->name ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>
            </div>

<!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium text-sm">
                    <i class="fas fa-save mr-1"></i> Update Configuration
                </button>
                <a href="{{ route('admin.configurations.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
            </div>
        </form>

        {{-- Delete form is OUTSIDE the update form --}}
        <form action="{{ route('admin.configurations.destroy', $configuration) }}" method="POST"
              class="mt-3 flex justify-end"
              onsubmit="return confirm('Are you sure you want to delete this configuration? This cannot be undone.');">
            @csrf @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-medium text-sm">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<script>
function updateValuePlaceholder() {
    const type = document.getElementById('type').value;
    const valueInput          = document.getElementById('value-input');
    const valueTextarea       = document.getElementById('value-textarea');
    const valueBoolean        = document.getElementById('value-boolean');
    const valueFileContainer  = document.getElementById('value-file-container');
    const valueFile           = document.getElementById('value-file');
    const valueHelp           = document.getElementById('value-help');

    let currentValue = '';
    if (valueInput.style.display !== 'none')         currentValue = valueInput.value;
    else if (valueTextarea.style.display !== 'none') currentValue = valueTextarea.value;
    else if (valueBoolean.style.display !== 'none')  currentValue = valueBoolean.value;

    valueInput.style.display         = 'none';
    valueTextarea.style.display      = 'none';
    valueBoolean.style.display       = 'none';
    valueFileContainer.style.display = 'none';

    valueInput.removeAttribute('required');
    valueTextarea.removeAttribute('required');
    valueBoolean.removeAttribute('required');
    valueFile.removeAttribute('required');

    valueInput.removeAttribute('name');
    valueTextarea.removeAttribute('name');
    valueBoolean.removeAttribute('name');

    switch(type) {
        case 'file':
            valueFileContainer.style.display = 'block';
            valueHelp.textContent = 'Select new files to replace existing ones (optional). Multiple files allowed.';
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
            if (currentValue) valueTextarea.value = currentValue;
            valueHelp.textContent = 'Enter long text content';
            break;
        case 'array':
            valueTextarea.style.display = 'block';
            valueTextarea.setAttribute('required', 'required');
            valueTextarea.setAttribute('name', 'value');
            if (currentValue) valueTextarea.value = currentValue;
            valueTextarea.setAttribute('placeholder', '["item1", "item2", "item3"]');
            valueHelp.textContent = 'Enter a valid JSON array, e.g., ["US", "UK", "RW"]';
            break;
        case 'json':
            valueTextarea.style.display = 'block';
            valueTextarea.setAttribute('required', 'required');
            valueTextarea.setAttribute('name', 'value');
            if (currentValue) valueTextarea.value = currentValue;
            valueTextarea.setAttribute('placeholder', '{"key": "value"}');
            valueHelp.textContent = 'Enter valid JSON, e.g., {"name": "John", "age": 30}';
            break;
        case 'integer':
            valueInput.style.display = 'block';
            valueInput.setAttribute('required', 'required');
            valueInput.setAttribute('name', 'value');
            valueInput.setAttribute('type', 'number');
            if (currentValue) valueInput.value = currentValue;
            valueInput.setAttribute('placeholder', 'e.g., 100');
            valueHelp.textContent = 'Enter a whole number';
            break;
        case 'string':
        default:
            valueInput.style.display = 'block';
            valueInput.setAttribute('required', 'required');
            valueInput.setAttribute('name', 'value');
            valueInput.setAttribute('type', 'text');
            if (currentValue) valueInput.value = currentValue;
            valueInput.setAttribute('placeholder', 'Enter value');
            valueHelp.textContent = 'Enter a text value';
            break;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateValuePlaceholder();
});
</script>
@endsection
