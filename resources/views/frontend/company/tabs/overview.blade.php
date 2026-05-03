{{-- resources/views/frontend/company/tabs/overview.blade.php --}}
@extends('layouts.app')

@section('content')
    @include('frontend.company.partials.nav', ['profile' => $profile])

    <div class="container px-3 mx-auto py-5 md:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 md:gap-6">

            {{-- LEFT: About + Stats --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- About Card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 md:p-6">
                    <h2 class="flex items-center gap-2 text-sm md:text-base font-black text-gray-900 mb-3">
                        <i class="fas fa-info-circle text-[#ff0808] text-xs"></i>
                        About {{ $profile->business_name }}
                    </h2>
                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                        {{ $profile->description ?? 'No description provided yet.' }}
                    </p>
                </div>

                {{-- Stats Row --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $stats = [
                            ['label' => 'Products',      'value' => $profile->user?->products->count() ?? 0, 'icon' => 'fa-box',        'color' => 'red'],
                            ['label' => 'Avg Rating',    'value' => $ratings['avg'] ?: 'N/A',               'icon' => 'fa-star',       'color' => 'yellow'],
                            ['label' => 'Reviews',       'value' => $ratings['count'],                       'icon' => 'fa-comments',   'color' => 'blue'],
                            ['label' => 'Est.',          'value' => $profile->year_established ?? 'N/A',     'icon' => 'fa-calendar',   'color' => 'green'],
                        ];
                        $statColors = ['red' => 'text-[#ff0808] bg-red-50', 'yellow' => 'text-yellow-600 bg-yellow-50', 'blue' => 'text-blue-600 bg-blue-50', 'green' => 'text-green-600 bg-green-50'];
                    @endphp
                    @foreach($stats as $stat)
                        @php $sc = $statColors[$stat['color']]; @endphp
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3 md:p-4 text-center">
                            <div class="flex justify-center mb-2">
                                <div class="w-8 h-8 rounded-full {{ $sc }} flex items-center justify-center">
                                    <i class="fas {{ $stat['icon'] }} text-[11px]"></i>
                                </div>
                            </div>
                            <p class="text-base md:text-lg font-black text-gray-900">{{ $stat['value'] }}</p>
                            <p class="text-[10px] md:text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Recent Products --}}
                @if($profile->user?->products->count())
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 md:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="flex items-center gap-2 text-sm font-black text-gray-900">
                                <i class="fas fa-box text-[#ff0808] text-xs"></i> Latest Products
                            </h2>
                            <a href="{{ route('business-profile.products', $profile->id) }}"
                               class="text-xs text-[#ff0808] font-bold hover:text-red-700">View All →</a>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($profile->user->products->take(8) as $product)
                                @php
                                    $img = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                @endphp
                                <a href="{{ route('products.show', $product->slug) }}" class="group">
                                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-100 hover:border-[#ff0808] hover:shadow-md transition-all">
                                        @if($img)
                                            <img src="{{ $img->image_url }}" alt="{{ $product->name }}"
                                                 class="w-full h-20 object-cover">
                                        @else
                                            <div class="w-full h-20 bg-gray-100 flex items-center justify-center">
                                                <span class="text-2xl">📦</span>
                                            </div>
                                        @endif
                                        <div class="p-2">
                                            <p class="text-[10px] font-semibold text-gray-800 group-hover:text-[#ff0808] line-clamp-2 transition-colors">
                                                {{ $product->name }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 mt-0.5">
                                                {{ number_format($product->base_price, 2) }} {{ $product->currency }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="space-y-4">

                {{-- Verification Badge --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full
                            {{ $profile->verification_status === 'verified' ? 'bg-green-50' : 'bg-gray-50' }}
                            flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $profile->verification_status === 'verified' ? 'fa-shield-alt text-green-600' : 'fa-clock text-gray-400' }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-900">
                                {{ $profile->verification_status === 'verified' ? 'Verified Supplier' : 'Pending Verification' }}
                            </p>
                            <p class="text-[10px] text-gray-500">
                                {{ $profile->is_admin_verified ? 'Admin approved' : 'Awaiting admin review' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quick Info --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <h3 class="text-xs font-black text-gray-900 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-bolt text-[#ff0808] text-[11px]"></i> Quick Info
                    </h3>
                    <dl class="space-y-2.5">
                        @php
                            $quickInfo = [
                                ['label' => 'Country',       'value' => $profile->country?->name,          'icon' => 'fa-flag'],
                                ['label' => 'Industry',      'value' => $profile->industry,                'icon' => 'fa-industry'],
                                ['label' => 'Business Type', 'value' => $profile->business_type,           'icon' => 'fa-briefcase'],
                                ['label' => 'Employees',     'value' => $profile->number_of_employees,     'icon' => 'fa-users'],
                                ['label' => 'Annual Revenue','value' => $profile->annual_revenue,          'icon' => 'fa-chart-line'],
                            ];
                        @endphp
                        @foreach($quickInfo as $info)
                            @if(!empty($info['value']))
                                <div class="flex gap-2">
                                    <i class="fas {{ $info['icon'] }} text-gray-400 text-[10px] mt-0.5 w-3 flex-shrink-0"></i>
                                    <div class="min-w-0">
                                        <dt class="text-[9px] text-gray-400 uppercase tracking-wide">{{ $info['label'] }}</dt>
                                        <dd class="text-xs font-semibold text-gray-800 truncate">{{ $info['value'] }}</dd>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>

                {{-- Reviews --}}
                @if($ratings['count'] > 0)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                        <h3 class="text-xs font-black text-gray-900 mb-3 flex items-center gap-1.5">
                            <i class="fas fa-star text-yellow-500 text-[11px]"></i>
                            Customer Reviews
                        </h3>
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-2xl font-black text-gray-900">{{ $ratings['avg'] }}</span>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-[10px] {{ $i <= round($ratings['avg']) ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                @endfor
                                <p class="text-[10px] text-gray-500">{{ $ratings['count'] }} reviews</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach($ratings['items'] as $review)
                                <div class="border-t border-gray-50 pt-2">
                                    <p class="text-[10px] text-gray-600 line-clamp-2">
                                        {{ $review->review ?? 'No comment.' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                <a href="{{ route('request-quote.show', $profile->id) }}"
                   class="block w-full text-center py-3 bg-[#ff0808] text-white text-xs font-black rounded-xl hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fas fa-file-invoice mr-1.5"></i> Request a Quote
                </a>
            </div>
        </div>
    </div>
@endsection
