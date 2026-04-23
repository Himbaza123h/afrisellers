@extends('layouts.home')

@section('page-content')
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Emoji Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.css">

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Messages</h1>
            <p class="mt-1 text-xs text-gray-500">Stay connected with your contacts</p>
        </div>
        <button onclick="openConversationTypeModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
            <i class="fas fa-plus"></i>
            <span>New Conversation</span>
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Conversations</p>
                    <p class="text-xl font-bold text-gray-900">{{ $groups->count() + $privateChats->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-lg text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Unread Messages</p>
                    <p class="text-xl font-bold text-gray-900">{{ $unreadCount }}</p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-lg text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Groups</p>
                    <p class="text-xl font-bold text-gray-900">{{ $groups->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-lg text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Conversations List -->
        <div class="lg:col-span-1 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <!-- Search Bar -->
            <div class="p-4 border-b border-gray-200">
                <div class="relative">
                    <input type="text" id="searchMessages" placeholder="Search conversations..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Tabs Navigation (Inside Left Sidebar) -->
            <div class="flex border-b border-gray-200">
                <button onclick="switchConversationType('groups')" id="sidebar-tab-groups" class="conversation-type-tab flex-1 px-4 py-3 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50 transition-all">
                    <i class="fas fa-users mr-2"></i>Groups
                    <span class="ml-1 text-xs">({{ $groups->count() }})</span>
                </button>
                <button onclick="switchConversationType('direct')" id="sidebar-tab-direct" class="conversation-type-tab flex-1 px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-all">
                    <i class="fas fa-user mr-2"></i>Direct
                    <span class="ml-1 text-xs">({{ $privateChats->count() }})</span>
                </button>
            </div>

            <div class="overflow-y-auto max-h-[600px]">
                <!-- Groups Tab Content -->
                <div id="sidebar-content-groups" class="conversation-type-content">
                    @forelse($groups as $group)
                        @include('messages.partials.group-item', ['group' => $group])
                    @empty
                        @include('messages.partials.empty-state', ['message' => 'No groups yet'])
                    @endforelse
                </div>

                <!-- Direct Messages Tab Content -->
                <div id="sidebar-content-direct" class="conversation-type-content hidden">
                    @forelse($privateChats as $chat)
                        @include('messages.partials.chat-item', ['chat' => $chat])
                    @empty
                        @include('messages.partials.empty-state', ['message' => 'No direct messages yet'])
                    @endforelse
                </div>
            </div>
        </div>

<!-- Chat Panel (Hidden by default, shown when conversation selected) -->
<div id="chatPanel" class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hidden">
    <div id="chatContent" class="h-full flex flex-col">
        <!-- Chat content will be loaded here -->
    </div>
</div>

<!-- Loading Indicator -->
<div id="chatLoading" class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm flex items-center justify-center p-12 hidden">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-sm text-gray-600">Loading conversation...</p>
    </div>
</div>
    </div>
</div>

<!-- Conversation Type Modal -->
<div id="conversationTypeModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Start New Conversation</h2>
                <button onclick="closeConversationTypeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-3">
            <button onclick="openDirectMessageModal()" class="w-full flex items-center gap-4 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all">
                <div class="w-12 h-12 bg-green-400 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div class="flex-1 text-left">
                    <h3 class="font-semibold text-gray-900">Direct Message</h3>
                    <p class="text-xs text-gray-600">Send a private message to a single person</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>

            <button onclick="openNewGroupModal()" class="w-full flex items-center gap-4 p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all">
                <div class="w-12 h-12 bg-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div class="flex-1 text-left">
                    <h3 class="font-semibold text-gray-900">Create Group</h3>
                    <p class="text-xs text-gray-600">Start a conversation with multiple people</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>
        </div>
    </div>
</div>

<!-- Direct Message Modal -->
<div id="directMessageModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-green-400 to-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">New Direct Message</h2>
                        <p class="text-xs text-gray-600">Select a person to message</p>
                    </div>
                </div>
                <button onclick="closeDirectMessageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="p-4">
            <div class="relative mb-3">
                <input type="text" id="dmUserSearch" placeholder="Search users..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <div id="dmUserResults" class="max-h-96 overflow-y-auto space-y-2"></div>
        </div>
    </div>
</div>

<!-- New Group Modal -->
<div id="newGroupModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl my-8">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Create Group</h2>
                        <p class="text-xs text-gray-600">Start a new conversation</p>
                    </div>
                </div>
                <button onclick="closeNewGroupModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('messages.group.create') }}" method="POST" class="p-4">
            @csrf
            <div class="space-y-3">
                <!-- Group Name & Type in Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Group Name *</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                        <select name="type" id="groupType" required onchange="handleGroupTypeChange()" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="private">Private</option>
                            <option value="vendor">Vendors</option>
                            <option value="public">Public</option>
                        </select>
                    </div>
                </div>

                <!-- Description with Emoji -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <div id="descriptionEditor" class="bg-white border border-gray-300 rounded-lg" style="height: 100px;"></div>
                    <input type="hidden" name="description" id="descriptionInput">
                    <button type="button" onclick="openEmojiPicker('description')" class="mt-1 px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 text-xs">
                        <i class="far fa-smile"></i> Add Emoji
                    </button>
                </div>

                <!-- Manual Members Section -->
                <div id="manualMembersSection">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Add Members <span id="membersOptional" class="text-gray-500">(Optional)</span></label>
                    <input type="text" id="memberSearch" placeholder="Search users..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-2">
                    <div id="memberResults" class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg hidden"></div>
                    <div id="selectedMembers" class="flex flex-wrap gap-2 mt-2"></div>
                    <div id="membersInputContainer"></div>
                </div>

                <!-- Auto Members Info -->
                <div id="autoMembersInfo" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-600 text-sm mt-0.5"></i>
                        <div>
                            <p class="text-xs font-semibold text-blue-900" id="autoMembersTitle">Auto-Adding Members</p>
                            <p class="text-xs text-blue-700 mt-0.5" id="autoMembersText"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="closeNewGroupModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                    <i class="fas fa-check mr-1"></i>Create
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Emoji Picker Modal -->
<div id="emojiPickerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-4 shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-900">Select Emoji</h3>
            <button onclick="closeEmojiPicker()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <emoji-picker id="emojiPickerElement"></emoji-picker>
    </div>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<!-- Emoji Picker -->
<script type="module">
  import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';
</script>

<style>
.conversation-type-tab {
    position: relative;
}

.conversation-type-tab.active {
    border-bottom-color: #2563eb;
    color: #2563eb;
    background-color: #eff6ff;
}

.conversation-type-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.ql-editor {
    min-height: 70px;
    font-size: 13px;
}

.ql-toolbar {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.ql-container {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}
</style>

<script>
let selectedMembers = [];
let descriptionQuill;
let currentEmojiTarget = null;

// Initialize Quill Editor
document.addEventListener('DOMContentLoaded', function() {
    descriptionQuill = new Quill('#descriptionEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline']
            ]
        },
        placeholder: 'Describe your group...'
    });
    window.descriptionQuill = descriptionQuill;

    // Auto-close emoji picker when clicking in editor
    descriptionQuill.on('selection-change', function(range) {
        if (range) {
            closeEmojiPicker();
        }
    });

    // Setup emoji picker event
const picker = document.getElementById('emojiPickerElement');
if (picker) {
    picker.addEventListener('emoji-click', event => {
        if (currentEmojiTarget === 'description') {
            const quill = window.descriptionQuill;
            const range = quill.getSelection(true) || { index: quill.getLength() };
            quill.insertText(range.index, event.detail.unicode);
            quill.setSelection(range.index + event.detail.unicode.length);
        } else if (currentEmojiTarget === 'message') {
            const quill = window.messageQuill;
            if (quill) {
                const range = quill.getSelection(true) || { index: quill.getLength() };
                quill.insertText(range.index, event.detail.unicode);
                quill.setSelection(range.index + event.detail.unicode.length);
            }
        }
        closeEmojiPicker();
    });
}
});

// Switch between Groups and Direct Messages in sidebar
function switchConversationType(type) {
    // Update tab buttons
    document.querySelectorAll('.conversation-type-tab').forEach(tab => {
        tab.classList.remove('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
        tab.classList.add('border-transparent', 'text-gray-600');
    });

    // Hide all content
    document.querySelectorAll('.conversation-type-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Show selected tab and content
    const activeTab = document.getElementById(`sidebar-tab-${type}`);
    activeTab.classList.add('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
    activeTab.classList.remove('border-transparent', 'text-gray-600');

    document.getElementById(`sidebar-content-${type}`).classList.remove('hidden');
}

// Conversation Type Modal
function openConversationTypeModal() {
    document.getElementById('conversationTypeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeConversationTypeModal() {
    document.getElementById('conversationTypeModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Direct Message Modal
function openDirectMessageModal() {
    closeConversationTypeModal();
    document.getElementById('directMessageModal').classList.remove('hidden');
    document.getElementById('dmUserSearch').focus();
}

function closeDirectMessageModal() {
    document.getElementById('directMessageModal').classList.add('hidden');
    document.getElementById('dmUserResults').innerHTML = '';
    document.getElementById('dmUserSearch').value = '';
    document.body.style.overflow = '';
}

// Group Modal
function openNewGroupModal() {
    closeConversationTypeModal();
    document.getElementById('newGroupModal').classList.remove('hidden');
}

function closeNewGroupModal() {
    document.getElementById('newGroupModal').classList.add('hidden');
    document.body.style.overflow = '';
    selectedMembers = [];
    document.getElementById('selectedMembers').innerHTML = '';
    document.getElementById('membersInputContainer').innerHTML = '';
    if (window.descriptionQuill) {
        descriptionQuill.setContents([]);
    }
}

function handleGroupTypeChange() {
    const type = document.getElementById('groupType').value;
    const manualSection = document.getElementById('manualMembersSection');
    const autoInfo = document.getElementById('autoMembersInfo');
    const autoTitle = document.getElementById('autoMembersTitle');
    const autoText = document.getElementById('autoMembersText');

    if (type === 'vendor') {
        manualSection.classList.add('hidden');
        autoInfo.classList.remove('hidden');
        autoTitle.textContent = 'All Vendors Will Be Added';
        autoText.textContent = 'This group will automatically include all users with vendor accounts.';
        selectedMembers = [];
        document.getElementById('selectedMembers').innerHTML = '';
        document.getElementById('membersInputContainer').innerHTML = '';
    } else if (type === 'public') {
        manualSection.classList.add('hidden');
        autoInfo.classList.remove('hidden');
        autoTitle.textContent = 'All Users Will Be Added';
        autoText.textContent = 'This group will automatically include all platform users.';
        selectedMembers = [];
        document.getElementById('selectedMembers').innerHTML = '';
        document.getElementById('membersInputContainer').innerHTML = '';
    } else {
        manualSection.classList.remove('hidden');
        autoInfo.classList.add('hidden');
    }
}

// Direct Message User Search
document.getElementById('dmUserSearch')?.addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length < 2) {
        document.getElementById('dmUserResults').innerHTML = '';
        return;
    }

    fetch(`{{ route('messages.search-users') }}?q=${query}`)
        .then(res => res.json())
        .then(users => {
            const resultsDiv = document.getElementById('dmUserResults');
            if (users.length === 0) {
                resultsDiv.innerHTML = '<p class="p-4 text-sm text-gray-500 text-center">No users found</p>';
                return;
            }

            resultsDiv.innerHTML = users.map(user => `
                <a href="/messages/private/${user.id}" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg border border-gray-200 transition-colors">
                    <div class="w-10 h-10 bg-green-400 to-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold">${user.name.charAt(0)}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">${user.name}</p>
                        <p class="text-xs text-gray-500 truncate">${user.email}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            `).join('');
        });
});

// Member search for groups
document.getElementById('memberSearch')?.addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length < 2) {
        document.getElementById('memberResults').classList.add('hidden');
        return;
    }

    fetch(`{{ route('messages.search-users') }}?q=${query}`)
        .then(res => res.json())
        .then(users => {
            const resultsDiv = document.getElementById('memberResults');
            if (users.length === 0) {
                resultsDiv.innerHTML = '<p class="p-2 text-xs text-gray-500">No users found</p>';
                resultsDiv.classList.remove('hidden');
                return;
            }

            resultsDiv.innerHTML = users.map(user => `
                <div class="p-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="addMember(${user.id}, '${user.name.replace(/'/g, "\\'")}', '${user.email}')">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-xs">${user.name.charAt(0)}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-900 truncate">${user.name}</p>
                            <p class="text-xs text-gray-500 truncate">${user.email}</p>
                        </div>
                    </div>
                </div>
            `).join('');
            resultsDiv.classList.remove('hidden');
        });
});

function addMember(id, name, email) {
    if (selectedMembers.includes(id)) return;

    selectedMembers.push(id);
    updateMemberInputs();

    const membersDiv = document.getElementById('selectedMembers');
    const memberTag = document.createElement('div');
    memberTag.className = 'inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs';
    memberTag.id = `member-tag-${id}`;
    memberTag.innerHTML = `
        <span>${name}</span>
        <button type="button" onclick="removeMember(${id})" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-times text-xs"></i>
        </button>
    `;
    membersDiv.appendChild(memberTag);

    document.getElementById('memberSearch').value = '';
    document.getElementById('memberResults').classList.add('hidden');
}

function removeMember(id) {
    selectedMembers = selectedMembers.filter(m => m !== id);
    updateMemberInputs();
    document.getElementById(`member-tag-${id}`)?.remove();
}

function updateMemberInputs() {
    const container = document.getElementById('membersInputContainer');
    container.innerHTML = '';

    selectedMembers.forEach(memberId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'members[]';
        input.value = memberId;
        container.appendChild(input);
    });
}

function openEmojiPicker(target) {
    currentEmojiTarget = target;
    document.getElementById('emojiPickerModal').classList.remove('hidden');
}

function closeEmojiPicker() {
    document.getElementById('emojiPickerModal').classList.add('hidden');
    currentEmojiTarget = null;
}

// Before form submit, save Quill content
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('descriptionInput').value = descriptionQuill.root.innerHTML;
});
</script>
<script>
let currentChatType = null;
let currentChatId = null;

function loadPrivateChat(userId, event) {
    currentChatType = 'private';
    currentChatId = userId;

    // Show loading
    document.getElementById('chatPanel').classList.add('hidden');
    document.getElementById('chatLoading').classList.remove('hidden');

    fetch(`/messages/load-private/${userId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('chatContent').innerHTML = data.html;
        document.getElementById('chatLoading').classList.add('hidden');
        document.getElementById('chatPanel').classList.remove('hidden');

        // Mark conversation as active
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'bg-blue-100');
        });
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('bg-blue-50');
        }

        // Re-initialize Quill
        setTimeout(() => {
            if (typeof Quill !== 'undefined' && document.getElementById('messageEditor')) {
                try {
                    window.messageQuill = new Quill('#messageEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline']
                            ]
                        },
                        placeholder: 'Type a message...'
                    });

                    // Enter to send
                    window.messageQuill.root.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendPrivateMessage(userId);
                        }
                    });
                } catch (e) {
                    console.log('Quill already initialized');
                }
            }

            // Scroll to bottom
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }, 100);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('chatLoading').classList.add('hidden');
        document.getElementById('chatPanel').classList.remove('hidden');
        document.getElementById('chatContent').innerHTML = `
            <div class="flex items-center justify-center h-full p-12">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-3"></i>
                    <p class="text-sm text-gray-600">Failed to load conversation</p>
                    <button onclick="loadPrivateChat(${userId}, event)" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Try Again
                    </button>
                </div>
            </div>
        `;
    });
}

function loadGroupChat(groupId, event) {
    currentChatType = 'group';
    currentChatId = groupId;

    // Show loading
    document.getElementById('chatPanel').classList.add('hidden');
    document.getElementById('chatLoading').classList.remove('hidden');

    fetch(`/messages/load-group/${groupId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('chatContent').innerHTML = data.html;
        document.getElementById('chatLoading').classList.add('hidden');
        document.getElementById('chatPanel').classList.remove('hidden');

        // Mark conversation as active
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'bg-blue-100');
        });
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('bg-blue-50');
        }

        // Re-initialize Quill
        setTimeout(() => {
            if (typeof Quill !== 'undefined' && document.getElementById('messageEditor') && !window.messageQuill) {
                try {
                    window.messageQuill = new Quill('#messageEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline']
                            ]
                        },
                        placeholder: 'Type a message...'
                    });

                    // Enter to send
                    window.messageQuill.root.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendGroupMessage(groupId);
                        }
                    });
                } catch (e) {
                    console.log('Quill already initialized or cannot initialize');
                }
            }

            // Scroll to bottom
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }, 100);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('chatLoading').classList.add('hidden');
        document.getElementById('chatPanel').classList.remove('hidden');
        document.getElementById('chatContent').innerHTML = `
            <div class="flex items-center justify-center h-full p-12">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-3"></i>
                    <p class="text-sm text-gray-600">Failed to load conversation</p>
                    <button onclick="loadGroupChat(${groupId}, event)" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Try Again
                    </button>
                </div>
            </div>
        `;
    });
}

function sendPrivateMessage(receiverId) {
    const quill = window.messageQuill;
    if (!quill) {
        console.error('Quill not initialized');
        return;
    }

    const message = quill.root.innerHTML;

    if (!message || message === '<p><br></p>') {
        return;
    }

    fetch('{{ route("messages.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            receiver_id: receiverId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messagesDiv = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="max-w-[70%]">
                    <div class="px-3 py-2 rounded-lg bg-blue-600 text-white">
                        <div class="text-sm whitespace-pre-wrap">${data.message.message}</div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 text-right">Just now</p>
                </div>
            `;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            quill.setContents([]);
        }
    })
    .catch(error => console.error('Error:', error));
}

function sendGroupMessage(groupId) {
    const quill = window.messageQuill;
    if (!quill) {
        console.error('Quill not initialized');
        return;
    }

    const message = quill.root.innerHTML;

    if (!message || message === '<p><br></p>') {
        return;
    }

    fetch('{{ route("messages.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            group_id: groupId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messagesDiv = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="flex gap-2 max-w-[70%] flex-row-reverse">
                    <div>
                        <div class="px-3 py-2 rounded-lg bg-blue-600 text-white">
                            <div class="text-sm">${data.message.message}</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-right">Just now</p>
                    </div>
                </div>
            `;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            quill.setContents([]);
        } else {
            alert(data.error || 'Failed to send message');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Emoji picker functions (keep these global)
function openMessageEmojiPicker() {
    currentEmojiTarget = 'message';
    document.getElementById('emojiPickerModal').classList.remove('hidden');
}

function closeMessageEmojiPicker() {
    document.getElementById('emojiPickerModal').classList.add('hidden');
    currentEmojiTarget = null;
}


function loadGroupSettings(groupId) {
    // You can implement group settings modal or load settings panel
    window.location.href = `/messages/group/${groupId}/settings`;
}
</script>
@endsection
