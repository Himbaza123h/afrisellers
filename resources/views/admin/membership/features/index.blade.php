@extends('layouts.home')

@section('page-content')

@php
    $allKeys = config('membership_feature_keys', []);
@endphp

<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex gap-4 items-center">
        <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 rounded-lg transition-colors hover:bg-gray-100">
            <i class="text-gray-600 fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Plan Features: {{ $membershipPlan->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Manage features and limits for this plan</p>
        </div>
        <a href="{{ route('admin.memberships.feature-catalog.index') }}" class="inline-flex gap-2 items-center px-3 py-2 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100">
            <i class="fas fa-book"></i> Feature catalog
        </a>
    </div>

<!-- Plan Info -->
<div class="p-6 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl border border-purple-200">
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-900">{{ $membershipPlan->name }}</h3>
            <p class="mt-1 text-sm text-gray-600">${{ number_format($membershipPlan->price, 2) }} / {{ $membershipPlan->duration_days }} days</p>
        </div>
        <div class="flex flex-wrap gap-6 items-center">
            <div class="text-center">
                <p id="membership-plan-feature-count" class="text-2xl font-bold text-purple-600">{{ $membershipPlan->features()->count() }}</p>
                <p class="text-xs text-gray-600">Features</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $membershipPlan->subscriptions()->where('status', 'active')->count() }}</p>
                <p class="text-xs text-gray-600">Active Users</p>
            </div>
            <!-- Bonus Days -->
            <form action="{{ route('admin.memberships.plans.bonus-days', $membershipPlan) }}" method="POST"
                  class="flex gap-2 items-center">
                @csrf
                <div class="text-center">
                    <p class="mb-1 text-xs text-gray-600">Bonus Days <span class="text-gray-400">(±)</span></p>
                    <div class="flex gap-2 items-center">
                        <input
                            type="number"
                            name="bonus_days"
                            value="{{ $membershipPlan->bonus_days }}"
                            placeholder="0"
                            class="px-3 py-1.5 w-24 text-sm font-medium text-center rounded-lg border border-purple-300 focus:ring-2 focus:ring-purple-400"
                        >
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-medium text-white whitespace-nowrap bg-purple-600 rounded-lg transition-colors hover:bg-purple-700">
                            Save
                        </button>
                    </div>
                    @if($membershipPlan->bonus_days != 0)
                        <p class="text-xs mt-1 {{ $membershipPlan->bonus_days > 0 ? 'text-green-600' : 'text-red-500' }} font-medium">
                            {{ $membershipPlan->bonus_days > 0 ? '+' : '' }}{{ $membershipPlan->bonus_days }} days on next purchase
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="flex gap-3 items-start p-4 bg-green-50 rounded-lg border border-green-200">
            <i class="mt-0.5 text-green-600 fas fa-check-circle"></i>
            <p class="flex-1 text-sm font-medium text-green-900">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="flex gap-3 items-start p-4 bg-red-50 rounded-lg border border-red-200">
            <i class="mt-0.5 text-red-600 fas fa-exclamation-circle"></i>
            <div class="flex-1">
                <p class="mb-1 text-sm font-medium text-red-900">Please fix the following errors:</p>
                <ul class="space-y-1 text-sm text-red-700">
                    @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
                </ul>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    <div id="ajaxFlashHost" class="space-y-2"></div>

    <!-- Add features: active + supported catalog rows not yet on this plan -->
    <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Plan catalog features</h3>
                <p class="mt-1 text-xs text-gray-500">All <span class="font-medium">active</span> and <span class="font-medium">supported</span> definitions. Already on this plan: checked — use <span class="font-medium">Remove</span> to unassign. Not on plan: select a value and <span class="font-medium">Add</span> or use <span class="font-medium">Add selected features</span>.</p>
            </div>
            @if($catalogFeatures->isNotEmpty())
                <div class="relative w-full sm:w-56">
                    <i class="absolute left-3 top-1/2 text-xs text-gray-400 -translate-y-1/2 fas fa-search"></i>
                    <input type="text" id="catalogFeatureSearch" placeholder="Filter by name or key…" autocomplete="off"
                           class="py-2 pr-3 pl-8 w-full text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-200">
                </div>
            @endif
        </div>

        @if($catalogFeatures->isEmpty())
            <div class="p-4 text-sm text-amber-900 bg-amber-50 rounded-lg border border-amber-200">
                There are no active, supported features in the catalog yet.
                <a href="{{ route('admin.memberships.feature-catalog.create') }}" class="font-semibold underline">Create a feature definition</a> first.
            </div>
        @else
            @error('selected')
                <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <form action="{{ route('admin.memberships.features.store', $membershipPlan) }}" method="POST" id="addFeaturesBatchForm">
                @csrf
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-3 py-3 w-10 text-xs font-semibold text-left text-gray-600 uppercase">On</th>
                                <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Name</th>
                                {{-- <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Key</th> --}}
                                <th class="hidden px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase md:table-cell">Description</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase min-w-[200px]">Value</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase min-w-[200px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($catalogFeatures as $cat)
                                @php
                                    $vType = $cat->resolvedValueType();
                                    $planAssignment = $planFeaturesByFeatureId->get($cat->id);
                                    $isOnPlan = $planAssignment !== null;
                                    $oldSelected = old('selected', []);
                                    $isChecked = $isOnPlan || in_array((string) $cat->id, array_map('strval', (array) $oldSelected), true)
                                        || in_array($cat->id, (array) $oldSelected, true);
                                @endphp
                                <tr class="catalog-feature-row hover:bg-gray-50/80 {{ $isOnPlan ? 'bg-green-50/40' : '' }}" data-catalog-row
                                    data-feature-key="{{ $cat->feature_key }}"
                                    data-value-type="{{ $vType }}"
                                    data-row-id="{{ $cat->id }}"
                                    data-on-plan="{{ $isOnPlan ? '1' : '0' }}"
                                    data-search-text="{{ e(strtolower($cat->name.' '.$cat->feature_key.' '.strip_tags($cat->description ?? ''))) }}">
                                    <td class="px-3 py-3 align-top">
                                        @if($isOnPlan)
                                            <input type="checkbox" checked disabled
                                                   class="text-gray-400 rounded border-gray-300 opacity-80 cursor-not-allowed"
                                                   title="Already on this plan"
                                                   aria-label="Already on this plan">
                                        @else
                                            <input type="checkbox" name="selected[]" value="{{ $cat->id }}"
                                                   class="text-red-600 rounded border-gray-300 catalog-row-check focus:ring-red-500"
                                                   @checked($isChecked)>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 font-medium text-gray-900 align-top">{{ $cat->name }}</td>
                                    {{-- <td class="px-3 py-3 align-top">
                                        <code class="px-2 py-0.5 text-xs text-blue-700 bg-blue-50 rounded">{{ $cat->feature_key }}</code>
                                    </td> --}}
                                    <td class="hidden px-3 py-3 max-w-xs text-xs text-gray-600 align-top md:table-cell">
                                        {{ $cat->description ? \Illuminate\Support\Str::limit($cat->description, 120) : '—' }}
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        @if($isOnPlan)
                                            @if($vType === 'number')
                                                <div class="space-y-2 catalog-inline-value" data-plan-feature-id="{{ $planAssignment->id }}">
                                                    <input type="number" step="any" value="{{ $planAssignment->feature_value }}"
                                                           class="w-full min-w-[120px] px-3 py-2 text-sm rounded-lg border border-gray-300 catalog-inline-input focus:ring-2 focus:ring-red-500"
                                                           placeholder="e.g. 10">
                                                    <p class="hidden mt-1 text-xs text-red-600 catalog-inline-err"></p>
                                                </div>
                                            @else
                                                <span class="text-sm font-medium text-gray-900">{{ $planAssignment->feature_value }}</span>
                                                {{-- <p class="mt-1 text-[11px] text-gray-500">Use <span class="font-medium">Edit</span> below to change the value.</p> --}}
                                            @endif
                                        @elseif($vType === 'boolean')
                                            <p class="text-sm text-gray-700"><code class="px-1.5 py-0.5 text-xs font-semibold text-green-800 bg-green-50 rounded">true</code></p>
                                        @elseif($vType === 'number')
                                            <input type="number"
                                                   name="values[{{ $cat->id }}]"
                                                   value="{{ old('values.'.$cat->id) }}"
                                                   step="any"
                                                   data-row-value
                                                   oninput="validateValue(this, 'row-msg-{{ $cat->id }}', 'number')"
                                                   class="row-value-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-sm @error('values.'.$cat->id) border-red-500 @enderror"
                                                   placeholder="e.g. 10">
                                        @elseif($vType === 'number_or_unlimited')
                                            <input type="text"
                                                   name="values[{{ $cat->id }}]"
                                                   value="{{ old('values.'.$cat->id) }}"
                                                   inputmode="decimal"
                                                   data-row-value
                                                   autocomplete="off"
                                                   oninput="validateValue(this, 'row-msg-{{ $cat->id }}', 'number_or_unlimited')"
                                                   class="row-value-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-sm @error('values.'.$cat->id) border-red-500 @enderror"
                                                   placeholder="number or unlimited">
                                        @else
                                            <input type="text"
                                                   name="values[{{ $cat->id }}]"
                                                   value="{{ old('values.'.$cat->id) }}"
                                                   data-row-value
                                                   oninput="validateValue(this, 'row-msg-{{ $cat->id }}', 'text')"
                                                   class="row-value-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-sm @error('values.'.$cat->id) border-red-500 @enderror"
                                                   placeholder="Value">
                                        @endif
                                        @if(! $isOnPlan && $vType !== 'boolean')
                                            <p id="row-msg-{{ $cat->id }}" class="hidden mt-1 text-xs"></p>
                                        @endif
                                        @error('values.'.$cat->id)
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        @if($isOnPlan)
                                            @if($vType === 'number')
                                                <div class="flex flex-wrap gap-2 items-center catalog-inline-actions">
                                                    <button type="button"
                                                            data-plan-feature-id="{{ $planAssignment->id }}"
                                                            class="catalog-inline-save inline-flex shrink-0 justify-center items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-[#ff0808] rounded-lg border border-transparent hover:bg-[#e60707] transition-colors">
                                                        <i class="fas fa-save text-[10px]"></i> Edit and save
                                                    </button>
                                                    <button type="button"
                                                            data-plan-feature-id="{{ $planAssignment->id }}"
                                                            class="inline-flex gap-1.5 justify-center items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-white rounded-lg border border-red-200 transition-colors catalog-remove-btn shrink-0 hover:bg-red-50"
                                                            title="Remove feature from this plan">
                                                        <i class="fas fa-times text-[10px]"></i> Remove
                                                    </button>
                                                </div>
                                            @else
                                                <button type="button"
                                                        data-plan-feature-id="{{ $planAssignment->id }}"
                                                        class="inline-flex gap-1.5 justify-center items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-white rounded-lg border border-red-200 transition-colors catalog-remove-btn hover:bg-red-50"
                                                        title="Remove feature from this plan">
                                                    <i class="fas fa-times text-[10px]"></i> Remove
                                                </button>
                                            @endif
                                        @else
                                            <button type="button"
                                                    onclick="addSingleCatalogRow({{ $cat->id }})"
                                                    class="inline-flex justify-center items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-white bg-[#ff0808] rounded-lg border border-transparent hover:bg-[#e60707] transition-colors"
                                                    title="Add only this feature to the plan">
                                                <i class="fas fa-plus text-[10px]"></i> Add
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- <div class="flex flex-wrap gap-3 items-center mt-4">
                    <button type="submit" id="catalogBatchSubmitBtn"
                            class="inline-flex justify-center items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-[#ff0808] rounded-lg transition-all hover:bg-[#e60707]">
                        <i class="fas fa-plus"></i> Add selected features
                    </button>
                    <p id="batchFormHint" class="text-xs text-gray-500"></p>
                </div> --}}
            </form>
        @endif

        <div class="pt-5 mt-6 border-t" style="display: none">
            <div class="flex flex-wrap gap-3 justify-between items-center mb-4">
                <p class="text-sm font-semibold text-gray-800">
                    Reference: known keys
                    <span class="ml-1 text-xs font-normal text-gray-400">— click to select the row (boolean flags need no value field)</span>
                </p>
                <div class="relative">
                    <i class="absolute left-3 top-1/2 mt-2 text-xs text-gray-400 -translate-y-1/2 pointer-events-none fas fa-search"></i>
                    <input
                        type="text"
                        id="keySearch"
                        placeholder="Search…"
                        oninput="filterKeys(this.value)"
                        class="py-1.5 pr-3 pl-8 w-48 text-xs rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>
            </div>

            <div id="keyGrid" class="flex flex-wrap gap-2">
                @foreach($allKeys as $key => $meta)
                <button
                    type="button"
                    onclick="pickKey('{{ $key }}', '{{ $meta['type'] }}')"
                    data-key="{{ $key }}"
                    data-label="{{ strtolower($meta['label']) }}"
                    data-type="{{ $meta['type'] }}"
                    title="{{ $key }}"
                    class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg transition-colors key-chip hover:bg-blue-600 hover:text-white"
                >
                    {{ $meta['label'] }}
                </button>
                @endforeach
            </div>
            <p id="noResults" class="hidden mt-3 text-sm italic text-gray-400">No matching features found.</p>
        </div>
    </div>

    <!-- Features Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm" style="display: none">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Key</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Value</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Added</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="assigned-plan-features-tbody" class="divide-y">
                    @forelse($features as $planFeature)
                        <tr class="hover:bg-gray-50" data-plan-feature-row-id="{{ $planFeature->id }}" data-catalog-feature-id="{{ $planFeature->feature_id }}">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $planFeature->feature?->name ?? '—' }}</span>
                                @if($planFeature->feature?->description)
                                    <p class="mt-0.5 text-xs leading-snug text-gray-500">{{ \Illuminate\Support\Str::limit($planFeature->feature->description, 100) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <code class="px-3 py-1 font-mono text-sm font-semibold text-blue-600 bg-blue-50 rounded">{{ $planFeature->feature_key }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $planFeature->feature_value }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">{{ $planFeature->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2 justify-center items-center">
                                    <button type="button" onclick="openEdit({{ $planFeature->id }}, @json($planFeature->feature_key), @json($planFeature->feature_value), @json(optional($planFeature->feature)->resolvedValueType() ?? 'text'))"
                                            class="p-2 text-blue-600 rounded-lg hover:bg-blue-50" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                            onclick="removeAssignedPlanFeatureFromTable({{ $planFeature->id }}, {{ $planFeature->feature_id }})"
                                            class="p-2 text-red-600 rounded-lg hover:bg-red-50" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="flex justify-center items-center mb-3 w-16 h-16 bg-gray-100 rounded-full">
                                        <i class="text-2xl text-gray-300 fas fa-list"></i>
                                    </div>
                                    <p class="mb-1 text-base font-semibold text-gray-900">No features added yet</p>
                                    <p class="text-sm text-gray-500">Add features to define what this plan includes</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($features, 'hasPages') && $features->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t">{{ $features->links() }}</div>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="flex hidden fixed inset-0 z-50 justify-center items-center p-4 bg-black bg-opacity-50">
    <div class="p-6 w-full max-w-md bg-white rounded-xl shadow-xl">
        <h3 class="mb-4 text-lg font-bold text-gray-900">Edit Feature</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Feature Key</label>
                    <input type="text" id="edit_key"
                           class="px-4 py-2.5 w-full font-mono text-sm text-gray-500 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Feature Value
                        <span id="editValueHint" class="ml-1 text-xs font-normal text-gray-400"></span>
                    </label>
                    <input type="text" id="edit_value" name="feature_value"
                           oninput="validateValue(this, 'editValueMsg', editType)"
                           class="px-4 py-2.5 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500">
                    <p id="editValueMsg" class="hidden mt-1 text-xs"></p>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeEdit()"
                        class="flex-1 px-4 py-2.5 font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
const KEY_MAP   = @json(collect($allKeys)->map(fn($m) => ['label' => $m['label'], 'type' => $m['type']]));
const PLAN_FEATURE_UPDATE_BASE = @json(url('/admin/memberships/plan-features'));
const PLAN_FEATURE_STORE_URL = @json(route('admin.memberships.features.store', $membershipPlan));
const CSRF_TOKEN = @json(csrf_token());
let editType    = null;

const HINTS = {
    boolean:            '— true or false',
    number:             '— numeric value',
    number_or_unlimited:'— number or "unlimited"',
    text:               '— any text',
};

function ajaxJsonHeaders() {
    return {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF_TOKEN,
    };
}

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
}

function collectValidationErrors(errors) {
    if (!errors || typeof errors !== 'object') return '';
    return Object.values(errors).flat().filter(Boolean).join(' ');
}

function flashAjaxMessage(text, kind) {
    const host = document.getElementById('ajaxFlashHost');
    if (!host || !text) return;
    const div = document.createElement('div');
    const isErr = kind === 'error';
    div.className = isErr
        ? 'flex gap-3 items-start p-4 rounded-lg border border-red-200 bg-red-50'
        : 'flex gap-3 items-start p-4 rounded-lg border border-green-200 bg-green-50';
    div.innerHTML = '<i class="mt-0.5' + (isErr ? 'text-red-600 fas fa-exclamation-circle' : 'text-green-600 fas fa-check-circle') + '"></i>'
        + '<p class="flex-1 text-sm font-medium' + (isErr ? 'text-red-900' : 'text-green-900') + '">' + escapeHtml(text) + '</p>'
        + '<button type="button" class="' + (isErr ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800') + '"><i class="fas fa-times"></i></button>';
    div.querySelector('button').addEventListener('click', () => div.remove());
    host.prepend(div);
    setTimeout(() => div.remove(), 5000);
}

async function postCatalogStore(formData) {
    const res = await fetch(PLAN_FEATURE_STORE_URL, {
        method: 'POST',
        headers: ajaxJsonHeaders(),
        body: formData,
    });
    let data = {};
    try { data = await res.json(); } catch (e) {}
    if (!res.ok) {
        const msg = data.message || collectValidationErrors(data.errors) || 'Could not add feature(s).';
        flashAjaxMessage(msg, 'error');
        return null;
    }
    return data;
}

function bumpFeatureCount(delta) {
    const el = document.getElementById('membership-plan-feature-count');
    if (!el) return;
    const n = (parseInt(el.textContent, 10) || 0) + delta;
    el.textContent = String(Math.max(0, n));
}

function updateBatchHint() {
    const el = document.getElementById('batchFormHint');
    if (!el) return;
    const n = document.querySelectorAll('.catalog-row-check:checked').length;
    el.textContent = n ? n + ' feature(s) selected' : '';
}

document.getElementById('catalogFeatureSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('[data-catalog-row]').forEach(row => {
        const hay = row.getAttribute('data-search-text') || '';
        row.style.display = (!q || hay.includes(q)) ? '' : 'none';
    });
});

document.getElementById('addFeaturesBatchForm')?.addEventListener('change', function(e) {
    if (e.target.classList && e.target.classList.contains('catalog-row-check')) updateBatchHint();
});
document.addEventListener('click', function(e) {
    const rm = e.target.closest('.catalog-remove-btn');
    if (rm) {
        e.preventDefault();
        removeCatalogPlanFeature(rm);
        return;
    }
    const sv = e.target.closest('.catalog-inline-save');
    if (sv) {
        e.preventDefault();
        saveCatalogInlineValue(sv);
    }
});
updateBatchHint();

function checkboxCellOnPlan() {
    return '<input type="checkbox" checked disabled class="text-gray-400 rounded border-gray-300 opacity-80 cursor-not-allowed" title="Already on this plan" aria-label="Already on this plan">';
}

function checkboxCellOffPlan(catId) {
    return '<input type="checkbox" name="selected[]" value="' + escapeHtml(catId) + '" class="text-red-600 rounded border-gray-300 catalog-row-check focus:ring-red-500">';
}

function catalogValueCellOffPlanHtml(catId, vType, previousValue) {
    const v = previousValue == null ? '' : String(previousValue);
    if (vType === 'boolean') {
        return '<p class="text-sm text-gray-700"><code class="px-1.5 py-0.5 text-xs font-semibold text-green-800 bg-green-50 rounded">true</code></p>';
    }
    if (vType === 'number') {
        return '<input type="number" name="values[' + escapeHtml(catId) + ']" value="' + escapeHtml(v) + '" step="any" data-row-value oninput="validateValue(this, \'row-msg-' + escapeHtml(catId) + '\', \'number\')" class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 row-value-input focus:ring-2 focus:ring-red-500" placeholder="e.g. 10">'
            + '<p id="row-msg-' + escapeHtml(catId) + '" class="hidden mt-1 text-xs"></p>';
    }
    if (vType === 'number_or_unlimited') {
        return '<input type="text" name="values[' + escapeHtml(catId) + ']" value="' + escapeHtml(v) + '" inputmode="decimal" data-row-value autocomplete="off" oninput="validateValue(this, \'row-msg-' + escapeHtml(catId) + '\', \'number_or_unlimited\')" class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 row-value-input focus:ring-2 focus:ring-red-500" placeholder="number or unlimited">'
            + '<p id="row-msg-' + escapeHtml(catId) + '" class="hidden mt-1 text-xs"></p>';
    }
    return '<input type="text" name="values[' + escapeHtml(catId) + ']" value="' + escapeHtml(v) + '" data-row-value oninput="validateValue(this, \'row-msg-' + escapeHtml(catId) + '\', \'text\')" class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 row-value-input focus:ring-2 focus:ring-red-500" placeholder="Value">'
        + '<p id="row-msg-' + escapeHtml(catId) + '" class="hidden mt-1 text-xs"></p>';
}

function catalogActionsOnPlan(planFeatureId, vType) {
    if (vType === 'number') {
        const pid = escapeHtml(planFeatureId);
        return '<div class="flex flex-wrap gap-2 items-center catalog-inline-actions">'
            + '<button type="button" data-plan-feature-id="' + pid + '" class="catalog-inline-save inline-flex shrink-0 justify-center items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-[#ff0808] rounded-lg border border-transparent hover:bg-[#e60707] transition-colors"><i class="fas fa-save text-[10px]"></i> Edit and save</button>'
            + '<button type="button" data-plan-feature-id="' + pid + '" class="inline-flex gap-1.5 justify-center items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-white rounded-lg border border-red-200 transition-colors catalog-remove-btn shrink-0 hover:bg-red-50" title="Remove feature from this plan"><i class="fas fa-times text-[10px]"></i> Remove</button>'
            + '</div>';
    }
    return '<button type="button" data-plan-feature-id="' + escapeHtml(planFeatureId) + '" class="inline-flex gap-1.5 justify-center items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-white rounded-lg border border-red-200 transition-colors catalog-remove-btn hover:bg-red-50" title="Remove feature from this plan">'
        + '<i class="fas fa-times text-[10px]"></i> Remove</button>';
}

function catalogActionsOffPlan(catId) {
    return '<button type="button" onclick="addSingleCatalogRow(' + JSON.stringify(Number(catId)) + ')" class="inline-flex justify-center items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-white bg-[#ff0808] rounded-lg border border-transparent hover:bg-[#e60707] transition-colors" title="Add only this feature to the plan">'
        + '<i class="fas fa-plus text-[10px]"></i> Add</button>';
}

function catalogValueCellOnPlanHtml(planFeatureId, vType, featureValue) {
    const v = featureValue == null ? '' : String(featureValue);
    if (vType === 'number') {
        const err = '<p class="hidden mt-1 text-xs text-red-600 catalog-inline-err"></p>';
        const control = '<input type="number" step="any" value="' + escapeHtml(v) + '" class="w-full min-w-[120px] px-3 py-2 text-sm rounded-lg border border-gray-300 catalog-inline-input focus:ring-2 focus:ring-red-500" placeholder="e.g. 10">';
        return '<div class="space-y-2 catalog-inline-value" data-plan-feature-id="' + escapeHtml(planFeatureId) + '">'
            + control + err + '</div>';
    }
    return '<span class="text-sm font-medium text-gray-900">' + escapeHtml(v) + '</span>'
        + '<p class="mt-1 text-[11px] text-gray-500">Use <span class="font-medium">Edit</span> below to change the value.</p>';
}

function transformCatalogRowToOnPlan(row, pf) {
    row.setAttribute('data-on-plan', '1');
    row.classList.add('bg-green-50/40');
    const tds = row.querySelectorAll('td');
    if (tds.length < 5) return;
    const vType = row.getAttribute('data-value-type') || 'text';
    tds[0].innerHTML = checkboxCellOnPlan();
    tds[3].innerHTML = catalogValueCellOnPlanHtml(pf.id, vType, pf.feature_value);
    tds[4].innerHTML = catalogActionsOnPlan(pf.id, vType);
    updateBatchHint();
}

function transformCatalogRowToOffPlan(row) {
    const catId = row.getAttribute('data-row-id');
    const vType = row.getAttribute('data-value-type') || 'text';
    let prev = '';
    const inlineInp = row.querySelector('.catalog-inline-input');
    if (inlineInp) prev = String(inlineInp.value || '').trim();
    else {
        const span = row.querySelector('td:nth-child(4) span.text-sm');
        if (span) prev = span.textContent.trim();
    }

    row.setAttribute('data-on-plan', '0');
    row.classList.remove('bg-green-50/40');
    const tds = row.querySelectorAll('td');
    if (tds.length < 5) return;
    tds[0].innerHTML = checkboxCellOffPlan(catId);
    tds[3].innerHTML = catalogValueCellOffPlanHtml(catId, vType, prev);
    tds[4].innerHTML = catalogActionsOffPlan(catId);
    updateBatchHint();
}

function removeAssignedFeatureRow(planFeatureId) {
    const tr = document.querySelector('#assigned-plan-features-tbody tr[data-plan-feature-row-id="' + planFeatureId + '"]');
    if (tr) tr.remove();
    const tbody = document.getElementById('assigned-plan-features-tbody');
    if (!tbody || tbody.querySelector('tr[data-plan-feature-row-id]')) return;
    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-20 text-center"><div class="flex flex-col items-center">'
        + '<div class="flex justify-center items-center mb-3 w-16 h-16 bg-gray-100 rounded-full"><i class="text-2xl text-gray-300 fas fa-list"></i></div>'
        + '<p class="mb-1 text-base font-semibold text-gray-900">No features added yet</p>'
        + '<p class="text-sm text-gray-500">Add features to define what this plan includes</p></div></td></tr>';
}

function insertAssignedFeatureRow(pf, catalogRow) {
    const tbody = document.getElementById('assigned-plan-features-tbody');
    if (!tbody) return;
    const empty = tbody.querySelector('td[colspan="5"]');
    if (empty) empty.closest('tr').remove();

    let nameHtml = '<span class="text-sm font-medium text-gray-900">—</span>';
    let descHtml = '';
    if (catalogRow && catalogRow.cells[1]) {
        const nameText = catalogRow.cells[1].textContent.trim();
        nameHtml = '<span class="text-sm font-medium text-gray-900">' + escapeHtml(nameText) + '</span>';
        const descTd = catalogRow.cells[2];
        if (descTd && descTd.textContent.trim() && descTd.textContent.trim() !== '—') {
            descHtml = '<p class="mt-0.5 text-xs leading-snug text-gray-500">' + descTd.innerHTML + '</p>';
        }
    }

    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50';
    tr.dataset.planFeatureRowId = String(pf.id);
    tr.dataset.catalogFeatureId = String(pf.feature_id);

    const tdName = document.createElement('td');
    tdName.className = 'px-6 py-4';
    tdName.innerHTML = nameHtml + descHtml;

    const tdKey = document.createElement('td');
    tdKey.className = 'px-6 py-4';
    tdKey.innerHTML = '<code class="px-3 py-1 font-mono text-sm font-semibold text-blue-600 bg-blue-50 rounded">' + escapeHtml(pf.feature_key) + '</code>';

    const tdVal = document.createElement('td');
    tdVal.className = 'px-6 py-4';
    tdVal.innerHTML = '<span class="text-sm font-medium text-gray-900">' + escapeHtml(pf.feature_value) + '</span>';

    const tdAdded = document.createElement('td');
    tdAdded.className = 'px-6 py-4';
    tdAdded.innerHTML = '<span class="text-sm text-gray-500">' + escapeHtml(pf.created_at_display) + '</span>';

    const tdAct = document.createElement('td');
    tdAct.className = 'px-6 py-4';
    const wrap = document.createElement('div');
    wrap.className = 'flex gap-2 justify-center items-center';

    const editBtn = document.createElement('button');
    editBtn.type = 'button';
    editBtn.className = 'p-2 text-blue-600 rounded-lg hover:bg-blue-50';
    editBtn.title = 'Edit';
    editBtn.innerHTML = '<i class="fas fa-edit"></i>';
    editBtn.addEventListener('click', () => openEdit(pf.id, pf.feature_key, pf.feature_value, pf.resolved_value_type));

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'p-2 text-red-600 rounded-lg hover:bg-red-50';
    delBtn.title = 'Delete';
    delBtn.innerHTML = '<i class="fas fa-trash"></i>';
    delBtn.addEventListener('click', () => removeAssignedPlanFeatureFromTable(pf.id, pf.feature_id));

    wrap.appendChild(editBtn);
    wrap.appendChild(delBtn);
    tdAct.appendChild(wrap);

    tr.appendChild(tdName);
    tr.appendChild(tdKey);
    tr.appendChild(tdVal);
    tr.appendChild(tdAdded);
    tr.appendChild(tdAct);

    tbody.insertBefore(tr, tbody.firstChild);
}

function syncAssignedTableValueCell(planFeatureId, newValue) {
    const tr = document.querySelector('#assigned-plan-features-tbody tr[data-plan-feature-row-id="' + planFeatureId + '"]');
    if (!tr || !tr.cells[2]) return;
    tr.cells[2].innerHTML = '<span class="text-sm font-medium text-gray-900">' + escapeHtml(newValue) + '</span>';
}

async function saveCatalogInlineValue(saveBtn) {
    const row = saveBtn.closest('tr.catalog-feature-row');
    if (!row) return;
    const planFeatureId = saveBtn.getAttribute('data-plan-feature-id');
    if (!planFeatureId) return;
    const input = row.querySelector('.catalog-inline-input');
    const errEl = row.querySelector('.catalog-inline-err');
    if (errEl) {
        errEl.textContent = '';
        errEl.classList.add('hidden');
    }
    let featureValue = '';
    if (input) {
        featureValue = String(input.value ?? '').trim();
    }
    saveBtn.disabled = true;
    const res = await fetch(PLAN_FEATURE_UPDATE_BASE + '/' + planFeatureId, {
        method: 'PUT',
        headers: Object.assign({}, ajaxJsonHeaders(), { 'Content-Type': 'application/json' }),
        body: JSON.stringify({ feature_value: featureValue }),
    });
    let data = {};
    try { data = await res.json(); } catch (e) {}
    saveBtn.disabled = false;
    if (!res.ok) {
        const msg = data.message || collectValidationErrors(data.errors) || 'Could not save value.';
        if (errEl) {
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        } else {
            flashAjaxMessage(msg, 'error');
        }
        return;
    }
    const saved = data.feature_value != null ? String(data.feature_value) : featureValue;
    if (input && input.type === 'number') {
        input.value = saved;
    }
    syncAssignedTableValueCell(planFeatureId, saved);
    flashAjaxMessage(data.message || 'Updated.', 'success');
}

async function removeCatalogPlanFeature(btn) {
    const id = btn.getAttribute('data-plan-feature-id');
    if (!id || !confirm('Remove this feature from the plan?')) return;
    btn.disabled = true;
    const res = await fetch(PLAN_FEATURE_UPDATE_BASE + '/' + id, { method: 'DELETE', headers: ajaxJsonHeaders() });
    let data = {};
    try { data = await res.json(); } catch (e) {}
    btn.disabled = false;
    if (!res.ok) {
        flashAjaxMessage(data.message || 'Could not remove feature.', 'error');
        return;
    }
    const catalogRow = document.querySelector('tr.catalog-feature-row[data-row-id="' + data.feature_id + '"]');
    if (catalogRow) transformCatalogRowToOffPlan(catalogRow);
    removeAssignedFeatureRow(id);
    bumpFeatureCount(-1);
    flashAjaxMessage(data.message || 'Feature removed.', 'success');
    updateBatchHint();
}

async function removeAssignedPlanFeatureFromTable(planFeatureId, catalogFeatureId) {
    if (!confirm('Delete this feature?')) return;
    const res = await fetch(PLAN_FEATURE_UPDATE_BASE + '/' + planFeatureId, { method: 'DELETE', headers: ajaxJsonHeaders() });
    let data = {};
    try { data = await res.json(); } catch (e) {}
    if (!res.ok) {
        flashAjaxMessage(data.message || 'Could not delete feature.', 'error');
        return;
    }
    const fid = data.feature_id != null ? data.feature_id : catalogFeatureId;
    const catalogRow = document.querySelector('tr.catalog-feature-row[data-row-id="' + fid + '"]');
    if (catalogRow) transformCatalogRowToOffPlan(catalogRow);
    removeAssignedFeatureRow(planFeatureId);
    bumpFeatureCount(-1);
    flashAjaxMessage(data.message || 'Feature deleted successfully!', 'success');
    updateBatchHint();
}

document.getElementById('addFeaturesBatchForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('catalogBatchSubmitBtn');
    if (btn) btn.disabled = true;
    const fd = new FormData(this);
    const data = await postCatalogStore(fd);
    if (btn) btn.disabled = false;
    if (!data || !data.plan_features) return;
    data.plan_features.forEach(pf => {
        const r = document.querySelector('tr.catalog-feature-row[data-row-id="' + pf.feature_id + '"]');
        if (r) transformCatalogRowToOnPlan(r, pf);
        insertAssignedFeatureRow(pf, r);
    });
    bumpFeatureCount(data.plan_features.length);
    flashAjaxMessage(data.message, 'success');
    this.querySelectorAll('.catalog-row-check').forEach(cb => { cb.checked = false; });
    updateBatchHint();
});

/** Add a single catalog row via AJAX (no full page reload). */
async function addSingleCatalogRow(rowId) {
    const row = document.querySelector('tr.catalog-feature-row[data-row-id="' + rowId + '"]');
    if (!row || row.style.display === 'none') return;
    if (row.getAttribute('data-on-plan') === '1') return;

    const vType = row.getAttribute('data-value-type') || 'text';
    const valInput = row.querySelector('.row-value-input');

    if (vType !== 'boolean') {
        if (!valInput || !String(valInput.value).trim()) {
            alert('Enter a value for this feature before adding it to the plan.');
            if (valInput) valInput.focus();
            return;
        }
    }

    const fd = new FormData();
    fd.append('_token', CSRF_TOKEN);
    fd.append('selected[]', String(rowId));
    if (valInput) fd.append('values[' + rowId + ']', valInput.value);

    const data = await postCatalogStore(fd);
    if (!data || !data.plan_features || !data.plan_features[0]) return;
    const pf = data.plan_features[0];
    transformCatalogRowToOnPlan(row, pf);
    insertAssignedFeatureRow(pf, row);
    bumpFeatureCount(1);
    flashAjaxMessage(data.message, 'success');
    updateBatchHint();
}

// ── Pick a reference chip: check matching catalog row and focus value ───────
function pickKey(key, type) {
    const row = document.querySelector(`tr.catalog-feature-row[data-feature-key="${key}"]`);
    const hintEl = document.getElementById('batchFormHint');
    if (!row) {
        if (hintEl) {
            hintEl.textContent = 'That key is not in the list above (inactive or not in catalog).';
            hintEl.classList.add('text-amber-700');
            setTimeout(() => { hintEl.textContent = ''; hintEl.classList.remove('text-amber-700'); updateBatchHint(); }, 4000);
        }
        return;
    }
    if (row.getAttribute('data-on-plan') === '1') {
        if (hintEl) {
            hintEl.textContent = '';
            hintEl.classList.add('text-amber-700');
            setTimeout(() => { hintEl.textContent = ''; hintEl.classList.remove('text-amber-700'); updateBatchHint(); }, 5000);
        }
        return;
    }
    row.style.display = '';
    const check = row.querySelector('.catalog-row-check');
    if (check) check.checked = true;
    const valInput = row.querySelector('.row-value-input');
    if (valInput) valInput.focus();
    updateBatchHint();

    document.querySelectorAll('.key-chip').forEach(c => {
        const active = c.dataset.key === key;
        c.classList.toggle('bg-blue-600',   active);
        c.classList.toggle('text-white',    active);
        c.classList.toggle('bg-gray-100',   !active);
        c.classList.toggle('text-gray-700', !active);
    });
}

// ── Value validation ────────────────────────────────────────────────────────
function validateValue(input, msgId, type) {
    const raw = input.value.trim();
    const val = raw.toLowerCase();
    const msg = document.getElementById(msgId);

    if (!raw) { hide(msg); resetBorder(input); return; }
    if (!type) { hide(msg); resetBorder(input); return; }

    if (type === 'boolean') {
        if (val === 'true' || val === 'false') {
            showMsg(msg, '✓ Valid — true or false', 'green'); setBorder(input, 'green');
        } else if (/^\d/.test(val)) {
            showMsg(msg, '✗ This field requires true or false, not a number.', 'red'); setBorder(input, 'red');
        } else {
            showMsg(msg, '⚠ Expected true or false.', 'yellow'); setBorder(input, 'red');
        }

    } else if (type === 'number') {
        if (val === 'true' || val === 'false') {
            showMsg(msg, '✗ This field requires a number, not true/false.', 'red'); setBorder(input, 'red');
        } else if (val === 'unlimited') {
            showMsg(msg, '✗ This field requires a specific number, not "unlimited".', 'red'); setBorder(input, 'red');
        } else if (!isNaN(raw) && raw !== '') {
            showMsg(msg, '✓ Valid number', 'green'); setBorder(input, 'green');
        } else {
            showMsg(msg, '⚠ Expected a numeric value.', 'yellow'); setBorder(input, 'red');
        }

    } else if (type === 'number_or_unlimited') {
        if (val === 'true' || val === 'false') {
            showMsg(msg, '✗ This field requires a number or "unlimited", not true/false.', 'red'); setBorder(input, 'red');
        } else if (!isNaN(raw) || val === 'unlimited') {
            showMsg(msg, '✓ Valid', 'green'); setBorder(input, 'green');
        } else {
            showMsg(msg, '⚠ Expected a number or "unlimited".', 'yellow'); setBorder(input, 'red');
        }

    } else {
        // text
        showMsg(msg, '✓ OK', 'green'); setBorder(input, 'green');
    }
}

// ── Filter chips ─────────────────────────────────────────────────────────────
function filterKeys(query) {
    const q = query.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.key-chip').forEach(chip => {
        const match = !q || chip.dataset.label.includes(q) || chip.dataset.key.includes(q);
        chip.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noResults').classList.toggle('hidden', visible > 0);
}

// ── Edit modal ────────────────────────────────────────────────────────────────
function openEdit(id, key, value, valueType) {
    editType = valueType || (KEY_MAP[key] ? KEY_MAP[key].type : 'text');
    document.getElementById('editForm').action = PLAN_FEATURE_UPDATE_BASE + '/' + id;
    document.getElementById('edit_key').value   = key;
    document.getElementById('edit_value').value = value;
    document.getElementById('editValueHint').textContent = HINTS[editType] || '';
    const msgEl = document.getElementById('editValueMsg');
    hide(msgEl);
    resetBorder(document.getElementById('edit_value'));
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEdit() {
    document.getElementById('editModal').classList.add('hidden');
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});

// ── Helpers ───────────────────────────────────────────────────────────────────
function showMsg(el, text, color) {
    const c = { green: 'text-green-600', yellow: 'text-yellow-600', red: 'text-red-500' };
    el.textContent = text;
    el.className   = `mt-1 text-xs ${c[color]}`;
}
function hide(el)         { el.classList.add('hidden'); }
function setBorder(el, c) {
    el.classList.remove('border-green-400', 'border-red-400', 'border-gray-300');
    el.classList.add(c === 'green' ? 'border-green-400' : 'border-red-400');
}
function resetBorder(el)  {
    el.classList.remove('border-green-400', 'border-red-400');
    el.classList.add('border-gray-300');
}
</script>
@endsection
