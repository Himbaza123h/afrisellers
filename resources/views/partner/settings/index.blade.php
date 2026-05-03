@extends('layouts.home')
@section('page-content')

<div class="mb-6">
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
        <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-gray-600 font-semibold">Settings</span>
    </div>
    <h1 class="text-lg font-black text-gray-900">Settings</h1>
    <p class="text-xs text-gray-500 mt-0.5">Manage your account preferences</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @foreach([
        [
            'title'       => 'General',
            'description' => 'Name, email and contact info',
            'icon'        => 'fa-user-cog',
            'color'       => 'text-blue-600',
            'bg'          => 'bg-blue-50',
            'route'       => 'partner.settings.general',
        ],
        [
            'title'       => 'Security',
            'description' => 'Password and account security',
            'icon'        => 'fa-shield-alt',
            'color'       => 'text-green-600',
            'bg'          => 'bg-green-50',
            'route'       => 'partner.settings.security',
        ],
        [
            'title'       => 'Notifications',
            'description' => 'Email and browser alerts',
            'icon'        => 'fa-bell',
            'color'       => 'text-amber-600',
            'bg'          => 'bg-amber-50',
            'route'       => 'partner.settings.notifications',
        ],
    ] as $card)
    <a href="{{ route($card['route']) }}"
       class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex items-center gap-4 hover:border-[#ff0808] hover:shadow-md transition-all group">
        <div class="w-12 h-12 {{ $card['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="fas {{ $card['icon'] }} {{ $card['color'] }} text-lg"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">{{ $card['title'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $card['description'] }}</p>
        </div>
        <i class="fas fa-chevron-right text-gray-300 text-xs ml-auto group-hover:text-gray-500 transition-all"></i>
    </a>
    @endforeach
</div>
@endsection
