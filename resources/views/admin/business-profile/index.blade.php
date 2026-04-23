@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Vendor Profiles</h1>
            <p class="mt-1 text-xs text-gray-500">Review and verify vendor applications</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
    <a href="{{ route('admin.vendors.create') }}"
        class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
        <i class="fas fa-plus"></i>
        <span>Add Vendor</span>
    </a>
    <button onclick="openNotifyModal(null)"
        class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm text-sm">
        <i class="fas fa-bell"></i>
        <span>Notify All Vendors</span>
    </button>
    <button onclick="printReport()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
        <i class="fas fa-print"></i>
        <span>Print</span>
    </button>
</div>
    </div>

    <!-- Tab Navigation -->
    <!-- Tab Navigation -->
<div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-[#ff0808] border-b-2 border-[#ff0808] transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('table')" id="tab-table" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Table
        </button>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

{{-- Rejection reason display (if any vendor is rejected) --}}
{{-- @if(isset($latestRejected) && $latestRejected)
    <div class="p-4 bg-red-50 border border-red-200 rounded-lg no-print">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800 mb-1">
                    <i class="fas fa-ban mr-1"></i> Rejected: {{ $latestRejected->business_name }}
                </p>
                @if($latestRejected->rejection_reason)
                    <p class="text-xs text-red-700 mb-3">
                        <span class="font-medium">Reason:</span> {{ $latestRejected->rejection_reason }}
                    </p>
                @endif
                @if($latestRejected->reason_reply)
                    <p class="text-xs text-gray-700 bg-white border border-gray-200 rounded p-2 mb-2">
                        <span class="font-medium">Vendor Reply:</span> {{ $latestRejected->reason_reply }}
                    </p>
                @endif
                <form method="POST" action="{{ route('admin.business-profile.reply', $latestRejected) }}">
                    @csrf
                    <textarea name="reason_reply" rows="2"
                        placeholder="Write a reply to the vendor..."
                        class="w-full text-xs border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-red-400 mb-2">{{ $latestRejected->reason_reply }}</textarea>
                    <button type="submit"
                        class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-reply mr-1"></i> Save Reply
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif --}}



@if($errors->any())
    <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Stats Section (shared across tabs) -->
    <div id="stats-section" class="stats-container">
        @include('admin.business-profile.partials.stats')
    </div>

    <!-- Table Section (shared across tabs) -->
    <div id="table-section" class="table-container">
        @include('admin.business-profile.partials.table')
    </div>
</div>

<!-- Notify Modal -->
<div id="notifyModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-base font-bold text-gray-900" id="notifyModalTitle">Notify All Vendors</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="notifyModalSubtitle">This message will be sent to all vendor email addresses.</p>
            </div>
            <button onclick="closeNotifyModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="notifyForm" method="POST" action="{{ route('admin.business-profile.notify') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="vendor_id" id="notifyVendorId">

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Subject <span class="text-red-500">*</span></label>
                <input type="text" name="subject" required
                    placeholder="Enter email subject..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                <div id="notifyEditor" style="height:200px; border:1px solid #d1d5db; border-radius:8px; overflow:hidden;"></div>
                <input type="hidden" name="message" id="notifyMessage">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Attachment <span class="text-gray-400">(optional, max 10MB)</span></label>
                <input type="file" name="attachment"
                    class="w-full text-sm text-gray-600 border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="flex gap-2 justify-end pt-2">
                <button type="button" onclick="closeNotifyModal()"
                    class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Cancel
                </button>
                <button type="submit" id="notifySubmitBtn"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium inline-flex items-center gap-2">
                    <i class="fas fa-paper-plane" id="notifyBtnIcon"></i>
                    <span id="notifyBtnText">Send Notification</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-base font-bold text-gray-900 mb-1">Reject Business Profile</h3>
        <p class="text-xs text-gray-500 mb-4">Please provide a reason so the vendor knows what to fix.</p>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <textarea name="rejection_reason" rows="4" required
                placeholder="Enter rejection reason..."
                class="w-full text-sm border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400 mb-4 resize-none"></textarea>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Cancel
                </button>
                <button type="submit" id="rejectSubmitBtn"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium inline-flex items-center gap-2">
                    <i class="fas fa-ban" id="rejectBtnIcon"></i>
                    <span id="rejectBtnText">Confirm Reject</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Load Quill rich text editor
(function() {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
    document.head.appendChild(link);
    const script = document.createElement('script');
    script.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
    script.onload = function() {
        window.notifyQuill = new Quill('#notifyEditor', {
            theme: 'snow',
            placeholder: 'Write your message here...',
            modules: { toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]}
        });
    };
    document.head.appendChild(script);
})();

function openNotifyModal(vendorId, vendorName) {
    document.getElementById('notifyVendorId').value = vendorId || '';
    if (vendorId && vendorName) {
        document.getElementById('notifyModalTitle').textContent = 'Notify: ' + vendorName;
        document.getElementById('notifyModalSubtitle').textContent = 'This message will be sent only to this vendor.';
    } else {
        document.getElementById('notifyModalTitle').textContent = 'Notify All Vendors';
        document.getElementById('notifyModalSubtitle').textContent = 'This message will be sent to all vendor email addresses.';
    }
    document.getElementById('notifyModal').classList.remove('hidden');
    document.getElementById('notifyBtnIcon').className = 'fas fa-paper-plane';
    document.getElementById('notifyBtnText').textContent = 'Send Notification';
    document.getElementById('notifySubmitBtn').disabled = false;
}
function closeNotifyModal() {
    document.getElementById('notifyModal').classList.add('hidden');
}
document.getElementById('notifyForm').addEventListener('submit', function () {
    if (window.notifyQuill) {
        document.getElementById('notifyMessage').value = notifyQuill.root.innerHTML;
    }
    const btn  = document.getElementById('notifySubmitBtn');
    const icon = document.getElementById('notifyBtnIcon');
    const text = document.getElementById('notifyBtnText');
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    text.textContent = 'Sending...';
});

function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/business-profiles/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    // Reset button state
    document.getElementById('rejectBtnIcon').className = 'fas fa-ban';
    document.getElementById('rejectBtnText').textContent = 'Confirm Reject';
    document.getElementById('rejectSubmitBtn').disabled = false;
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
document.getElementById('rejectForm').addEventListener('submit', function () {
    const btn = document.getElementById('rejectSubmitBtn');
    const icon = document.getElementById('rejectBtnIcon');
    const text = document.getElementById('rejectBtnText');
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    text.textContent = 'Rejecting...';
});

function switchTab(tab) {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');
        btn.classList.add('text-gray-600');
    });

    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-[#ff0808]', 'border-b-2', 'border-[#ff0808]');

    const statsSection = document.getElementById('stats-section');
    const tableSection = document.getElementById('table-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'block';
            tableSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            tableSection.style.display = 'none';
            break;
        case 'table':
            statsSection.style.display = 'none';
            tableSection.style.display = 'block';
            break;
    }
}

function printReport() {
    window.open('{{ route("admin.business-profiles.print") }}', '_blank');
}

{{-- ✅ ADDED: Switch to vendor JS --}}
function switchToVendor(businessProfileId) {
    if (!businessProfileId) return;

    fetch(`/admin/business-profiles/${businessProfileId}/switch-to-vendor`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.login_url) {
            window.open(data.login_url, '_blank');
        } else {
            alert(data.message || 'Failed to switch to vendor.');
        }
    })
    .catch(() => alert('Something went wrong.'));
}
</script>
@endsection
