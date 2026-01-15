@extends('layouts.app')

@section('title', 'All Countries')

@section('content')
<div class="py-8 min-h-screen bg-gray-50">
    <div class="container px-4 mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500">All Countries</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-lg font-bold text-gray-900">Browse by Country</h1>
            <p class="text-gray-600 mt-1">Discover verified suppliers from around the world</p>
        </div>

        <!-- Countries Grid -->
        <div class="grid grid-cols-2 gap-6 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8">
            @forelse($countries as $country)
                <a href="{{ route('country.business-profiles', $country->id) }}" class="flex flex-col items-center group">
                    <div class="flex overflow-hidden justify-center items-center mb-3 w-20 h-20 bg-white rounded-full shadow-sm transition-shadow duration-200 transform hover:shadow-md group-hover:scale-105">
                        @if(isset($country->flag_url) && $country->flag_url)
                            <img src="{{ $country->flag_url }}" alt="{{ $country->name }} flag" class="object-cover w-full h-full rounded-full" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-4xl\' style=\'color: #6B7280;\'><i class=\'fas fa-globe\'></i></span>';">
                        @else
                            <span class="text-4xl text-gray-400"><i class="fas fa-globe"></i></span>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-center text-gray-900 group-hover:text-blue-600 transition-colors">{{ $country->name }}</span>
                    <span class="text-xs text-gray-500 mt-1">{{ $country->business_profiles_count }} {{ $country->business_profiles_count === 1 ? 'supplier' : 'suppliers' }}</span>
                </a>
            @empty
                <div class="col-span-full py-12 text-center">
                    <i class="mb-4 text-5xl text-gray-300 fas fa-globe"></i>
                    <p class="text-gray-500">{{ __('messages.no_countries_available') ?? 'No countries available' }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

