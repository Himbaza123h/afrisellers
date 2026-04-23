@extends('layouts.app')

@section('title', $car->full_name . ' - LoadBoard')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        <nav class="mb-6 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#ff0808]">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('loadboard.cars.index') }}" class="hover:text-[#ff0808]">Vehicles</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $car->full_name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4">

                <!-- Image Gallery -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="relative h-80 bg-gray-100 cursor-pointer" onclick="openGallery(0)">
                        @if($car->primary_image)
                            <img src="{{ $car->primary_image }}"
                                 alt="{{ $car->full_name }}"
                                 class="w-full h-full object-cover hover:opacity-95 transition-opacity">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <span class="text-8xl">🚚</span>
                            </div>
                        @endif

                        @if($car->is_featured)
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                ⭐ FEATURED
                            </span>
                        @endif

                        <!-- Zoom Indicator -->
                        <div class="absolute bottom-3 right-3 px-2.5 py-1 bg-black bg-opacity-50 text-white text-xs rounded-lg">
                            <i class="fas fa-search-plus"></i> Click to enlarge
                        </div>
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if($car->images && count($car->images) > 1)
                        <div class="p-3 flex gap-2 overflow-x-auto">
                            @foreach($car->images as $index => $image)
                                <img src="{{ $image }}"
                                     alt="Vehicle image"
                                     onclick="openGallery({{ $index }})"
                                     class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:ring-2 hover:ring-[#ff0808] transition-all flex-shrink-0">
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Vehicle Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $car->full_name }}</h1>
                    <p class="text-sm text-gray-600 mb-4">{{ $car->vehicle_type }}</p>

                    <!-- Rating -->
                    @if($car->rating > 0)
                        <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100">
                            <div class="flex items-center gap-1.5 text-amber-500">
                                <i class="fas fa-star"></i>
                                <span class="font-semibold text-gray-900 text-base">{{ number_format($car->rating, 1) }}</span>
                            </div>
                            <span class="text-sm text-gray-600">{{ $car->reviews_count }} reviews</span>
                            <span class="text-gray-300">|</span>
                            <span class="text-sm text-gray-600">{{ $car->completed_trips }} trips</span>
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="mb-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $car->description }}</p>
                    </div>

                    <!-- Route Information -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">Route Information</h3>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                            <div class="flex-1">
                                <div class="text-xs text-gray-600 mb-1">From</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->from_location }}</div>
                            </div>
                            <i class="fas fa-arrow-right text-xl text-[#ff0808]"></i>
                            <div class="flex-1">
                                <div class="text-xs text-gray-600 mb-1">To</div>
                                <div class="font-semibold text-gray-900 text-sm">
                                    {{ $car->to_location ?? 'Flexible Destination' }}
                                </div>
                            </div>
                        </div>

                        @if($car->flexible_destination && $car->preferred_routes)
                            <div class="mt-2 p-2.5 bg-green-50 rounded-lg">
                                <div class="text-xs text-green-700 font-medium mb-1.5">Preferred Routes:</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($car->preferred_routes as $route)
                                        <span class="px-2 py-0.5 bg-white text-gray-700 text-xs font-medium rounded border border-green-200">
                                            {{ $route }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Specifications Grid -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">Specifications</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                            <div class="p-2.5 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-0.5">Year</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->year }}</div>
                            </div>
                            <div class="p-2.5 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-0.5">Transmission</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->transmission }}</div>
                            </div>
                            <div class="p-2.5 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-0.5">Fuel Type</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->fuel_type }}</div>
                            </div>
                            <div class="p-2.5 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-0.5">Mileage</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->formatted_mileage }}</div>
                            </div>
                            <div class="p-2.5 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-0.5">Cargo Capacity</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->formatted_capacity }}</div>
                            </div>
                            <div class="p-2.5 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-0.5">Color</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $car->color }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    @if($car->features && count($car->features) > 0)
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Features</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($car->features as $feature)
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg border border-blue-200">
                                        <i class="fas fa-check-circle mr-1"></i>{{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Driver & Insurance -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-user-tie text-blue-600"></i>
                                <h4 class="font-semibold text-gray-900 text-sm">Driver</h4>
                            </div>
                            @if($car->driver_included)
                                <p class="text-xs text-gray-700 leading-relaxed">
                                    Professional driver included<br>
                                    Experience: {{ $car->driver_experience }}<br>
                                    Languages: {{ implode(', ', $car->driver_languages ?? []) }}
                                </p>
                            @else
                                <p class="text-xs text-gray-600">Driver not included</p>
                            @endif
                        </div>

                        <div class="p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-shield-alt text-green-600"></i>
                                <h4 class="font-semibold text-gray-900 text-sm">Insurance & Permits</h4>
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-2">
                                    <i class="fas {{ $car->has_insurance ? 'fa-check text-green-600' : 'fa-times text-red-600' }}"></i>
                                    <span>Vehicle Insurance</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas {{ $car->has_goods_transit_insurance ? 'fa-check text-green-600' : 'fa-times text-red-600' }}"></i>
                                    <span>Goods in Transit Insurance</span>
                                </div>
                                @if($car->permits && count($car->permits) > 0)
                                    <div class="mt-1.5 text-xs text-gray-600">
                                        Permits: {{ implode(', ', $car->permits) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-4">

                    <!-- Price -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        @if($car->price)
                            <div class="text-lg font-bold text-gray-900 mb-1">
                                ${{ number_format($car->price, 0) }}
                            </div>
                            <div class="text-sm text-gray-600">per {{ $car->pricing_type }}</div>
                        @else
                            <div class="text-xl font-semibold text-gray-900">Price Negotiable</div>
                        @endif

                        @if($car->price_negotiable)
                            <span class="inline-block mt-2 px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg border border-green-200">
                                Open to Negotiation
                            </span>
                        @endif
                    </div>

                    <!-- Availability -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h4 class="font-semibold text-gray-900 text-sm mb-2">Availability</h4>
                        @if($car->available_from && $car->available_until)
                            <p class="text-xs text-gray-700">
                                {{ $car->available_from->format('M d, Y') }} - {{ $car->available_until->format('M d, Y') }}
                            </p>
                        @else
                            <p class="text-xs text-green-600 font-medium">Available Now</p>
                        @endif
                    </div>

                    <!-- Contact Buttons -->
                    @auth
                        <button onclick="openInquiryModal()" class="w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors mb-2">
                            <i class="fas fa-envelope mr-1.5"></i>Send Inquiry
                        </button>
                        <button onclick="openBookingModal()" class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                            <i class="fas fa-calendar-check mr-1.5"></i>Book Now
                        </button>
                    @else
                        <a href="{{ route('auth.signin') }}" class="block w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors text-center">
                            <i class="fas fa-sign-in-alt mr-1.5"></i>Sign In to Inquire
                        </a>
                    @endauth

                    <!-- Owner Info -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <h4 class="font-semibold text-gray-900 text-sm mb-3">Vehicle Owner</h4>
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr($car->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $car->user->name }}</div>
                                <div class="text-xs text-gray-600">Member since {{ $car->user->created_at->format('Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="mt-5 pt-5 border-t border-gray-100 grid grid-cols-2 gap-3 text-center">
                        <div>
                            <div class="text-xl font-bold text-gray-900">{{ $car->views_count }}</div>
                            <div class="text-xs text-gray-600">Views</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-900">{{ $car->inquiries_count }}</div>
                            <div class="text-xs text-gray-600">Inquiries</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Vehicles -->
        @if($similarCars->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Similar Vehicles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($similarCars as $similar)
                        <a href="{{ route('loadboard.cars.show', $similar->listing_number) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all">
                            <div class="h-36 bg-gray-100 rounded-t-xl overflow-hidden">
                                @if($similar->primary_image)
                                    <img src="{{ $similar->primary_image }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $similar->full_name }}</h3>
                                <p class="text-xs text-gray-600 mb-2">{{ $similar->route }}</p>
                                <div class="text-base font-bold text-gray-900">
                                    ${{ number_format($similar->price, 0) }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Full Screen Gallery Modal -->
<div id="galleryModal" class="hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center">
    <button onclick="closeGallery()" class="absolute top-4 right-4 text-white text-lg hover:text-gray-300 z-10">
        <i class="fas fa-times"></i>
    </button>

    <button onclick="prevImage()" class="absolute left-4 text-white text-4xl hover:text-gray-300 z-10">
        <i class="fas fa-chevron-left"></i>
    </button>

    <button onclick="nextImage()" class="absolute right-4 text-white text-4xl hover:text-gray-300 z-10">
        <i class="fas fa-chevron-right"></i>
    </button>

    <div class="max-w-6xl max-h-screen p-4">
        <img id="galleryImage" src="" alt="Gallery image" class="max-w-full max-h-screen object-contain">
    </div>

    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm">
        <span id="imageCounter">1 / 1</span>
    </div>
</div>

<script>
let currentImageIndex = 0;
const images = @json($car->images ?? [$car->primary_image]);

function openGallery(index) {
    currentImageIndex = index;
    updateGalleryImage();
    document.getElementById('galleryModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeGallery() {
    document.getElementById('galleryModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function updateGalleryImage() {
    document.getElementById('galleryImage').src = images[currentImageIndex];
    document.getElementById('imageCounter').textContent = `${currentImageIndex + 1} / ${images.length}`;
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
    updateGalleryImage();
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    updateGalleryImage();
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('galleryModal').classList.contains('hidden')) {
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'Escape') closeGallery();
    }
});
</script>

@endsection
