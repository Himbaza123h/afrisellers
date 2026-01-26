@extends('layouts.app')

@section('title', 'Home - Africa\'s Leading B2B Marketplace')

@section('content')
    @include('frontend.home.sections.hero')
    @include('frontend.home.sections.browse-by-region')
    @include('frontend.home.sections.categories')
    @include('frontend.home.sections.recommended-suppliers-hot-deals')
    @include('frontend.home.sections.featured-suppliers')
    @include('frontend.home.sections.trending-products')
    {{-- @include('frontend.home.sections.weekly-special-offers') --}}
    @include('frontend.home.sections.regional-showcases')
    @include('frontend.home.sections.join-afrisellers')
    @include('frontend.home.sections.why-choose')
@endsection
