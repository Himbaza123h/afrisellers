@extends('layouts.home')

@section('page-content')
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.country.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-lg font-black text-gray-900 uppercase">Country Details</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $country->name }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Flag --}}
        <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-xl border border-gray-100">
            @if($country->flag_url)
                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}"
                     class="w-32 h-24 object-cover rounded-lg border border-gray-200 shadow-sm mb-3"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'128\' height=\'96\'%3E%3Crect fill=\'%23e5e7eb\' width=\'128\' height=\'96\'/%3E%3C/svg%3E'">
            @else
                <div class="w-32 h-24 bg-gray-200 rounded-lg border border-gray-300 flex items-center justify-center mb-3">
                    <i class="fas fa-flag text-gray-400 text-3xl"></i>
                </div>
            @endif
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                         {{ $country->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $country->status === 'active' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ ucfirst($country->status) }}
            </span>
        </div>

        {{-- Core Info --}}
        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Country Name</p>
                <p class="text-lg font-bold text-gray-900">{{ $country->name }}</p>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Country Code</p>
                <p class="text-base font-bold text-gray-800">{{ $country->code ?? '—' }}</p>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Region</p>
                @if($country->region)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-800 rounded-lg text-sm font-semibold border border-blue-100">
                        <i class="fas fa-globe-africa text-blue-500 text-xs"></i>
                        {{ $country->region->name }}
                        @if($country->region->code)
                            <span class="text-blue-400 font-mono text-xs">({{ $country->region->code }})</span>
                        @endif
                    </span>
                @else
                    <span class="text-sm text-gray-400 italic">Not assigned</span>
                @endif
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Vendors</p>
                <p class="text-base font-bold text-gray-800">{{ number_format($country->getVendorsCount()) }}</p>
            </div>

            @if($country->flag_url)
            <div class="sm:col-span-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Flag URL</p>
                <a href="{{ $country->flag_url }}" target="_blank"
                   class="text-xs text-blue-600 hover:underline break-all">{{ $country->flag_url }}</a>
            </div>
            @endif

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Created At</p>
                <p class="text-sm text-gray-700">{{ $country->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Last Updated</p>
                <p class="text-sm text-gray-700">{{ $country->updated_at->format('d M Y, h:i A') }}</p>
            </div>

        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center gap-3 pt-6 mt-6 border-t border-gray-200">
        <a href="{{ route('admin.country.edit', $country) }}"
           class="px-5 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-sm">
            <i class="fas fa-edit mr-2"></i>Edit Country
        </a>

        <form action="{{ route('admin.country.toggle-status', $country) }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                class="px-5 py-2.5 text-white rounded-lg transition-colors font-semibold shadow-sm
                       {{ $country->status === 'active' ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">
                <i class="fas fa-{{ $country->status === 'active' ? 'pause' : 'play' }} mr-2"></i>
                {{ $country->status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
        </form>

        <form action="{{ route('admin.country.destroy', $country) }}" method="POST" class="inline"
              onsubmit="return confirm('Are you sure you want to delete {{ $country->name }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit"
                class="px-5 py-2.5 bg-red-100 text-red-700 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition-colors font-semibold shadow-sm">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        </form>

        <a href="{{ route('admin.country.index') }}"
           class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold ml-auto">
            <i class="fas fa-list mr-2"></i>Back to List
        </a>
    </div>
</div>
@endsection
