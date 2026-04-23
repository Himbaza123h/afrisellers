@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                Search Results
            </h1>
            <p class="text-gray-600">
                Found <span class="font-semibold text-[#ff0808]">{{ $results->total() }}</span> results for
                <span class="font-semibold">"{{ $query }}"</span>
            </p>
        </div>

        <!-- Search Again Bar -->
        <div class="mb-8">
            <form action="{{ route('global.search') }}" method="GET" class="max-w-2xl">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="query"
                        value="{{ $query }}"
                        placeholder="Search again..."
                        class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#ff0808] focus:outline-none"
                    >
                    <button type="submit" class="px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition font-bold">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        @if($results->count() > 0)
            <!-- Results Grid -->
            <div class="space-y-4">
                @foreach($results as $result)
                    <a href="{{ $result->url }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200 overflow-hidden group">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <!-- Type Badge -->
                                    <div class="mb-2">
                                        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full
                                            {{ class_basename($result->searchable_type) === 'Product' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ class_basename($result->searchable_type) === 'Load' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ class_basename($result->searchable_type) === 'Car' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ class_basename($result->searchable_type) === 'Showroom' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ class_basename($result->searchable_type) === 'Tradeshow' ? 'bg-pink-100 text-pink-700' : '' }}
                                        ">
                                            <i class="fas fa-{{
                                                class_basename($result->searchable_type) === 'Product' ? 'box' :
                                                (class_basename($result->searchable_type) === 'Load' ? 'truck' :
                                                (class_basename($result->searchable_type) === 'Car' ? 'car' :
                                                (class_basename($result->searchable_type) === 'Showroom' ? 'store' : 'calendar')))
                                            }} mr-1"></i>
                                            {{ class_basename($result->searchable_type) }}
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition truncate">
                                        {{ $result->title }}
                                    </h3>

                                    <!-- Description -->
                                    @if($result->description)
                                        <p class="text-gray-600 text-sm line-clamp-2">
                                            {{ $result->description }}
                                        </p>
                                    @endif

                                    <!-- URL Preview -->
                                    <div class="mt-3">
                                        <span class="text-xs text-gray-500">
                                            {{ url($result->url) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Arrow Icon -->
                                <div class="flex-shrink-0">
                                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-[#ff0808] group-hover:translate-x-1 transition-all"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $results->appends(['query' => $query])->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No results found</h3>
                <p class="text-gray-600 mb-6">
                    We couldn't find anything matching "<span class="font-semibold">{{ $query }}</span>"
                </p>
                <div class="space-y-2 text-sm text-gray-500">
                    <p>Try:</p>
                    <ul class="list-disc list-inside">
                        <li>Using different keywords</li>
                        <li>Checking your spelling</li>
                        <li>Using more general terms</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
