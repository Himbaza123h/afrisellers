@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.show', $group->id) }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Group Settings</h1>
                <p class="mt-1 text-xs text-gray-500">Manage {{ $group->name }}</p>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-[#2563eb] mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-[#2563eb] hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button onclick="switchTab('overview')"
                    id="tab-overview"
                    class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#2563eb] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    <span>Overview</span>
                </div>
            </button>
            <button onclick="switchTab('members')"
                    id="tab-members"
                    class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#2563eb] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-users"></i>
                    <span>Members</span>
                    <span class="ml-1 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $group->members->count() }}</span>
                </div>
            </button>
            @if($isAdmin)
            <button onclick="switchTab('settings')"
                    id="tab-settings"
                    class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#2563eb] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </div>
            </button>
            @endif
            @if(auth()->id() !== $group->created_by)
            <button onclick="switchTab('danger')"
                    id="tab-danger"
                    class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#2563eb] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Danger Zone</span>
                </div>
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Content -->
    <div class="relative">
        <!-- Overview Tab -->
        <div id="content-overview" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Group Info - 2 columns -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-[#2563eb]"></i>
                            Group Information
                        </h3>

                        @if($isAdmin)
                            <form action="{{ route('messages.group.update', $group->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Group Name</label>
                                    <input type="text"
                                           name="name"
                                           value="{{ $group->name }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2563eb] focus:border-[#2563eb]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                                    <textarea name="description"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2563eb] focus:border-[#2563eb]">{{ strip_tags($group->description) }}</textarea>
                                </div>
                                <button type="submit"
                                        class="px-4 py-2 bg-[#2563eb] text-white rounded-lg hover:bg-[#dd0606] text-sm font-medium transition-all shadow-sm">
                                    Update Group
                                </button>
                            </form>
                        @else
                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-500 mb-1">Group Name</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $group->name }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-500 mb-1">Description</p>
                                    <p class="text-sm text-gray-700">{!! $group->description !!}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stats - 1 column -->
                <div>
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-[#2563eb]"></i>
                            Statistics
                        </h3>
                        <div class="space-y-4">
                            <div class="bg-blue-50 to-blue-100 rounded-lg p-4">
                                <p class="text-xs text-blue-600 mb-1">Members</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $group->members->count() }}</p>
                            </div>
                            <div class="bg-purple-50 to-purple-100 rounded-lg p-4">
                                <p class="text-xs text-purple-600 mb-1">Admins</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $group->admins->count() }}</p>
                            </div>
                            <div class="bg-green-50 to-green-100 rounded-lg p-4">
                                <p class="text-xs text-green-600 mb-1">Messages</p>
                                <p class="text-2xl font-bold text-green-900">{{ $group->messages->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Tab -->
        <div id="content-members" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900">Group Members</h2>
                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-[#2563eb] bg-red-50 rounded-full border border-red-100">
                            {{ $group->members->count() }} {{ Str::plural('member', $group->members->count()) }}
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($group->members as $member)
                        <div class="p-5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-blue-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-lg">{{ substr($member->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($member->pivot->role === 'admin')
                                        <span class="px-2.5 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                            <i class="fas fa-shield-alt mr-1"></i>Admin
                                        </span>
                                    @endif
                                    @if($member->id === $group->created_by)
                                        <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                            <i class="fas fa-crown mr-1"></i>Owner
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($isAdmin && $member->id !== $group->created_by && $member->id !== auth()->id())
                                <div class="flex items-center gap-2">
                                    @if($member->pivot->role === 'member')
                                        <form action="{{ route('messages.group.make-admin', [$group->id, $member->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-all font-medium">
                                                <i class="fas fa-user-shield"></i>
                                                Make Admin
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('messages.group.remove-admin', [$group->id, $member->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-600 text-white text-xs rounded-lg hover:bg-gray-700 transition-all font-medium">
                                                <i class="fas fa-user-times"></i>
                                                Remove Admin
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('messages.group.remove-member', [$group->id, $member->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Remove this member?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition-all font-medium">
                                            <i class="fas fa-user-minus"></i>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        @if($isAdmin)
        <div id="content-settings" class="tab-content hidden">
            <div class="grid grid-cols-1 gap-6">
                <!-- Group Controls -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900">Group Controls</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-lock text-gray-600"></i>
                                    <p class="text-sm font-semibold text-gray-900">Lock Group</p>
                                </div>
                                <p class="text-xs text-gray-600">Only admins can send messages when locked</p>
                            </div>
                            <form action="{{ route('messages.group.toggle-lock', $group->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $group->is_locked ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    <i class="fas fa-{{ $group->is_locked ? 'lock-open' : 'lock' }} mr-1"></i>
                                    {{ $group->is_locked ? 'Unlock' : 'Lock' }}
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-link text-gray-600"></i>
                                    <p class="text-sm font-semibold text-gray-900">Invite Link</p>
                                </div>
                                <p class="text-xs text-gray-600">Generate a link for users to join this group</p>
                            </div>
                            <button onclick="openInviteModal()"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all">
                                <i class="fas fa-plus-circle"></i>
                                Generate Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Member -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-900">Add Member</h2>
                    </div>
                    <div class="p-5">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text"
                                   id="memberSearch"
                                   placeholder="Search users by name or email..."
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg mb-2 text-sm focus:ring-2 focus:ring-[#2563eb] focus:border-[#2563eb]">
                        </div>
                        <div id="memberResults" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg hidden"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Danger Zone Tab -->
        @if(auth()->id() !== $group->created_by)
        <div id="content-danger" class="tab-content hidden">
            <div class="bg-white rounded-lg border-2 border-red-300 shadow-sm overflow-hidden">
                <div class="p-5 bg-red-50 border-b border-red-200">
                    <h3 class="text-base font-bold text-red-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-xl text-[#2563eb]"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Leave Group</h4>
                            <p class="text-xs text-gray-600 mb-4">Once you leave, you will no longer receive messages from this group. You can be added back by an admin.</p>
                            <form action="{{ route('messages.group.leave', $group->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to leave this group?')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Leave Group
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Invite Modal -->
<div id="inviteModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl animate-modal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Invite Link</h3>
                <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            @if($group->isInviteCodeValid())
                <div class="mb-4">
                    <p class="text-xs text-gray-600 mb-2">Share this code with users:</p>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               id="inviteCodeDisplay"
                               value="{{ $group->invite_code }}"
                               readonly
                               class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg font-mono text-lg text-center">
                        <button onclick="copyInviteCode()"
                                class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <i class="fas fa-clock"></i>
                        Expires: {{ $group->invite_code_expires_at->format('M d, Y') }}
                    </p>
                </div>
            @endif

            <form action="{{ route('messages.group.generate-invite', $group->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium">
                    <i class="fas fa-plus-circle mr-2"></i>
                    {{ $group->isInviteCodeValid() ? 'Generate New Link' : 'Generate Invite Link' }}
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Tab Styling */
    .tab-button {
        position: relative;
    }

    .tab-button.active {
        color: #ff0808;
        border-bottom-color: #ff0808;
        background-color: #fff5f5;
    }

    /* Tab Content Animations */
    .tab-content {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content.hidden {
        display: none;
    }

    /* Modal Animation */
    .animate-modal {
        animation: modalSlideDown 0.3s ease-out;
    }

    @keyframes modalSlideDown {
        0% {
            opacity: 0;
            transform: translateY(-30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<script>
// Tab Switching Function
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active class to selected tab
    document.getElementById('tab-' + tabName).classList.add('active');

    // Save to localStorage
    localStorage.setItem('activeGroupTab', tabName);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Restore last active tab or default to overview
    const savedTab = localStorage.getItem('activeGroupTab') || 'overview';
    switchTab(savedTab);
});

// Modal Functions
function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInviteModal() {
    document.getElementById('inviteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function copyInviteCode() {
    const input = document.getElementById('inviteCodeDisplay');
    input.select();
    document.execCommand('copy');

    // Show feedback
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => {
        btn.innerHTML = originalHTML;
    }, 2000);
}

// Close modal on outside click
document.getElementById('inviteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeInviteModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeInviteModal();
    }
});

// Member search
document.getElementById('memberSearch')?.addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length < 2) {
        document.getElementById('memberResults').classList.add('hidden');
        return;
    }

    fetch(`{{ route('messages.group.search-users', $group->id) }}?q=${query}`)
        .then(res => res.json())
        .then(users => {
            const resultsDiv = document.getElementById('memberResults');
            if (users.length === 0) {
                resultsDiv.innerHTML = '<p class="p-4 text-xs text-gray-500 text-center">No users found</p>';
                resultsDiv.classList.remove('hidden');
                return;
            }

            resultsDiv.innerHTML = users.map(user => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors" onclick="addMember(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold">${user.name.charAt(0)}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${user.name}</p>
                            <p class="text-xs text-gray-500 truncate">${user.email}</p>
                        </div>
                        <i class="fas fa-plus-circle text-[#2563eb]"></i>
                    </div>
                </div>
            `).join('');
            resultsDiv.classList.remove('hidden');
        });
});

function addMember(userId, userName) {
    if (!confirm(`Add ${userName} to the group?`)) return;

    fetch('{{ route("messages.group.add-member", $group->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(res => res.json())
    .then(data => {
        location.reload();
    });
}
</script>
@endsection
