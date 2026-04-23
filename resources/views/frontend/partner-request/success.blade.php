@extends('layouts.app')

@section('title', 'Home - Africa\'s Leading B2B Marketplace')

@section('content')


    <main class="flex-1 flex items-center justify-center py-16 px-4">
        <div class="max-w-md w-full text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mb-3">Request Submitted!</h1>
            <p class="text-sm text-gray-500 mb-8">
                Thank you for your interest in partnering with Afrisellers. Our team will review your application and get back to you within 2-3 business days.
            </p>
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-all">
                <i class="fas fa-home"></i> Back to Homepage
            </a>
        </div>
    </main>

@endsection
