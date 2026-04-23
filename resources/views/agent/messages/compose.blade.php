@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.messages.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">New Message</h1>
            <p class="text-xs text-gray-500 mt-0.5">Send a direct message to a vendor or admin</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agent.messages.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">

            {{-- Recipient --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    To <span class="text-red-500">*</span>
                </label>

                {{-- Search-style selector --}}
                <div class="relative">
                    <input type="text" id="recipientSearch" placeholder="Search by name or email…"
                        autocomplete="off"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <input type="hidden" name="recipient_id" id="recipientId" value="{{ old('recipient_id') }}" required>

                    <div id="recipientDropdown"
                         class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto hidden">
                        @foreach($recipients as $user)
                            <div class="recipient-option flex items-center gap-3 px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors"
                                 data-id="{{ $user->id }}"
                                 data-name="{{ $user->name }}"
                                 data-email="{{ $user->email }}">
                                <div class="w-8 h-8 rounded-full bg-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                                @foreach($user->roles ?? [] as $role)
                                    <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full font-semibold
                                        {{ in_array($role->slug, ['admin','super-admin']) ? 'bg-red-100 text-red-600' : 'bg-purple-100 text-purple-600' }}">
                                        {{ ucfirst($role->slug) }}
                                    </span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Selected recipient chip --}}
                <div id="selectedRecipient" class="mt-2 hidden">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center">
                            <span class="text-white text-[9px] font-bold" id="selectedInitial"></span>
                        </div>
                        <span class="text-sm font-medium text-blue-800" id="selectedName"></span>
                        <button type="button" onclick="clearRecipient()"
                            class="text-blue-400 hover:text-blue-600 ml-1">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>

                @error('recipient_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="6" required
                    placeholder="Write your message here…"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 leading-relaxed resize-none">{{ old('message') }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Press Enter to send from the conversation view. Max 5,000 characters.</p>
                @error('message')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.messages.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const options      = document.querySelectorAll('.recipient-option');
const searchInput  = document.getElementById('recipientSearch');
const dropdown     = document.getElementById('recipientDropdown');
const recipientId  = document.getElementById('recipientId');
const selectedDiv  = document.getElementById('selectedRecipient');
const selectedName = document.getElementById('selectedName');
const selectedInit = document.getElementById('selectedInitial');

searchInput.addEventListener('focus', () => dropdown.classList.remove('hidden'));
searchInput.addEventListener('input', () => {
    const term = searchInput.value.toLowerCase();
    let any = false;
    options.forEach(opt => {
        const match = opt.dataset.name.toLowerCase().includes(term)
                   || opt.dataset.email.toLowerCase().includes(term);
        opt.style.display = match ? '' : 'none';
        if (match) any = true;
    });
    dropdown.classList.toggle('hidden', !any && term === '');
});

options.forEach(opt => {
    opt.addEventListener('click', () => {
        recipientId.value  = opt.dataset.id;
        selectedName.textContent = opt.dataset.name;
        selectedInit.textContent = opt.dataset.name.charAt(0).toUpperCase();
        selectedDiv.classList.remove('hidden');
        searchInput.classList.add('hidden');
        dropdown.classList.add('hidden');
    });
});

function clearRecipient() {
    recipientId.value = '';
    selectedDiv.classList.add('hidden');
    searchInput.classList.remove('hidden');
    searchInput.value = '';
    options.forEach(opt => opt.style.display = '');
}

document.addEventListener('click', e => {
    if (!e.target.closest('#recipientSearch') && !e.target.closest('#recipientDropdown')) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endpush
