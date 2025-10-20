@extends('layouts.app')

@section('title', 'Home - Africa\'s Leading B2B Marketplace')

@section('content')
    @include('frontend.home.sections.hero')
    @include('frontend.home.sections.categories')
    @include('frontend.home.sections.featured-suppliers')
    @include('frontend.home.sections.trending-products')
    @include('frontend.home.sections.regional-showcases')
    @include('frontend.home.sections.top-rfqs')
    @include('frontend.home.sections.why-choose')
@endsection
