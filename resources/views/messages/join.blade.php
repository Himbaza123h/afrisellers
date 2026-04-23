@extends('layouts.home')

@section('page-content')
<div class="max-w-md mx-auto mt-12">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Join a Group</h2>
            <p class="text-sm text-gray-600 mt-1">Enter the invite code to join</p>
        </div>

        <form action="{{ route('messages.group.join') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Invite Code</label>
                <input type="text" name="invite_code" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center font-mono text-lg uppercase" placeholder="XXXXXXXX" maxlength="8">
            </div>
            <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Join Group
            </button>
        </form>
    </div>
</div>
@endsection
