@extends('layouts.app')

@section('title', 'Search Results - Electronics')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/frontend/products/index.css') }}">
@endpush

@section('content')
    @include('frontend.products.sections.top-nav')

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('frontend.products.sections.sidebar')
            @include('frontend.products.sections.product-grid')
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/frontend/products/index.js') }}"></script>
@endpush
