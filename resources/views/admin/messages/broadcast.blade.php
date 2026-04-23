@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.messages.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Broadcast Message</h1>
            <p class="mt-1 text-xs text-gray-500">Send a message to multiple users</p>
        </div>
    </div>

    <!-- Broadcast Form -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-purple-50 to-blue-50 border-b">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-500 to-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-bullhorn text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Create Broadcast</h2>
                    <p class="text-xs text-gray-600">Send important announcements to your users</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.messages.broadcast.send') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Recipient Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Recipients *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-all">
                        <input type="radio" name="recipient_type" value="all" class="sr-only peer" required onchange="toggleRecipientSelection()">
                        <div class="flex items-center gap-3 w-full peer-checked:text-purple-600">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center peer-checked:bg-purple-600">
                                <i class="fas fa-globe text-purple-600 peer-checked:text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm">All Users</p>
                                <p class="text-xs text-gray-500">{{ $users->count() }} users</p>
                            </div>
                        </div>
                        <div class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-purple-600 peer-checked:bg-purple-600 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all">
                        <input type="radio" name="recipient_type" value="vendors" class="sr-only peer" onchange="toggleRecipientSelection()">
                        <div class="flex items-center gap-3 w-full peer-checked:text-blue-600">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center peer-checked:bg-blue-600">
                                <i class="fas fa-store text-blue-600 peer-checked:text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm">All Vendors</p>
                                <p class="text-xs text-gray-500">{{ $vendors->count() }} vendors</p>
                            </div>
                        </div>
                        <div class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-all">
                        <input type="radio" name="recipient_type" value="buyers" class="sr-only peer" onchange="toggleRecipientSelection()">
                        <div class="flex items-center gap-3 w-full peer-checked:text-green-600">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center peer-checked:bg-green-600">
                                <i class="fas fa-users text-green-600 peer-checked:text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm">All Buyers</p>
                                <p class="text-xs text-gray-500">{{ $buyers->count() }} buyers</p>
                            </div>
                        </div>
                        <div class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-green-600 peer-checked:bg-green-600 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-orange-500 transition-all">
                        <input type="radio" name="recipient_type" value="specific" class="sr-only peer" onchange="toggleRecipientSelection()">
                        <div class="flex items-center gap-3 w-full peer-checked:text-orange-600">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center peer-checked:bg-orange-600">
                                <i class="fas fa-user-check text-orange-600 peer-checked:text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm">Specific Users</p>
                                <p class="text-xs text-gray-500">Choose manually</p>
                            </div>
                        </div>
                        <div class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-orange-600 peer-checked:bg-orange-600 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Specific Users Selection -->
            <div id="specificUsersSection" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Specific Users</label>
                <select name="recipients[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" size="8">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple users</p>
            </div>

            <!-- Message -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                <textarea name="message" rows="6" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Type your broadcast message here..."></textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t">
                <a href="{{ route('admin.messages.index') }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-center">
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send Broadcast
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRecipientSelection() {
    const specificSection = document.getElementById('specificUsersSection');
    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;

    if (recipientType === 'specific') {
        specificSection.classList.remove('hidden');
    } else {
        specificSection.classList.add('hidden');
    }
}
</script>
@endsection
