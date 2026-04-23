@extends('layouts.home')

@section('page-content')
<div class="space-y-5 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-1 text-xs text-gray-500">Your personal and business information</p>
        </div>
        <a href="{{ route('agent.profile.edit') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-semibold shadow-md">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Avatar + Quick Info --}}
        <div class="space-y-4">

            {{-- Avatar Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center">
                <div class="relative inline-block mb-4">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}"
                             alt="{{ $user->name }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                    @else
                        <div class="w-24 h-24 rounded-full bg-blue-500 to-blue-700 flex items-center justify-center border-4 border-white shadow-md">
                            <span class="text-white text-3xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <h2 class="text-base font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>

                <div class="flex flex-col gap-2 mt-4">
                    {{-- Upload Avatar --}}
                    <form action="{{ route('agent.profile.update-avatar') }}" method="POST"
                          enctype="multipart/form-data" id="avatarForm">
                        @csrf
                        <label class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-xs font-semibold cursor-pointer hover:bg-blue-100 transition-colors">
                            <i class="fas fa-camera"></i> Change Photo
                            <input type="file" name="avatar" class="hidden" accept="image/*"
                                   onchange="document.getElementById('avatarForm').submit()">
                        </label>
                    </form>

                    @if($user->avatar)
                    <form action="{{ route('agent.profile.delete-avatar') }}" method="POST"
                          onsubmit="return confirm('Remove your photo?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-red-50 border border-red-200 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash"></i> Remove Photo
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Account Info</h3>
                <dl class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <dt class="text-xs text-gray-400">Member Since</dt>
                        <dd class="text-xs font-medium text-gray-700">{{ $user->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-2">
                        <dt class="text-xs text-gray-400">Status</dt>
                        <dd>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">
                                Active
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Quick Links</h3>
                <div class="space-y-1">
                    @foreach([
                        ['route'=>'agent.profile.edit',     'icon'=>'fa-user-edit',   'label'=>'Edit Personal Info', 'color'=>'text-blue-500'],
                        ['route'=>'agent.profile.business', 'icon'=>'fa-building',    'label'=>'Business Profile',   'color'=>'text-purple-500'],
                        ['route'=>'agent.settings.security','icon'=>'fa-lock',        'label'=>'Security Settings',  'color'=>'text-amber-500'],
                    ] as $link)
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors group">
                        <i class="fas {{ $link['icon'] }} {{ $link['color'] }} w-4 text-center text-sm"></i>
                        <span class="text-sm text-gray-600 font-medium group-hover:text-gray-900">{{ $link['label'] }}</span>
                        <i class="fas fa-chevron-right text-gray-300 text-xs ml-auto"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Personal Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-800">Personal Information</h3>
                    <a href="{{ route('agent.profile.edit') }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Full Name</dt>
                        <dd class="text-sm font-semibold text-gray-800">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Email Address</dt>
                        <dd class="text-sm font-semibold text-gray-800">{{ $user->email }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Business Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-800">Business Profile</h3>
                    <a href="{{ route('agent.profile.business') }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>

                @if($businessProfile)
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach([
                            ['Business Name',   $businessProfile->business_name],
                            ['Business Email',  $businessProfile->business_email],
                            ['Phone',           $businessProfile->phone_code . ' ' . $businessProfile->phone],
                            ['City',            $businessProfile->city],
                            ['Country',         $businessProfile->country?->name ?? '—'],
                            ['Website',         $businessProfile->website ?? '—'],
                            ['Business Type',   $businessProfile->business_type ?? '—'],
                            ['Year Est.',       $businessProfile->year_established ?? '—'],
                        ] as [$label, $value])
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-gray-800 truncate">{{ $value ?: '—' }}</dd>
                        </div>
                        @endforeach
                    </dl>

                    @if($businessProfile->description)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <dt class="text-xs text-gray-400 mb-1">About</dt>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $businessProfile->description }}</p>
                        </div>
                    @endif

                    {{-- Social Links --}}
                    @php $socials = array_filter([
                        'fa-facebook'  => $businessProfile->facebook_link,
                        'fa-twitter'   => $businessProfile->twitter_link,
                        'fa-linkedin'  => $businessProfile->linkedin_link,
                        'fa-instagram' => $businessProfile->instagram_link,
                    ]); @endphp
                    @if(count($socials))
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                            @foreach($socials as $icon => $url)
                                <a href="{{ $url }}" target="_blank"
                                   class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 hover:bg-blue-100 hover:text-blue-600 transition-colors">
                                    <i class="fab {{ $icon }} text-sm"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-building text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">No business profile yet</p>
                        <p class="text-xs text-gray-400 mt-1 mb-4">Add your business details to complete your profile</p>
                        <a href="{{ route('agent.profile.business') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                            <i class="fas fa-plus"></i> Add Business Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
