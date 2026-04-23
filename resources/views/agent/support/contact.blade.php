@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.support.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Contact Us</h1>
            <p class="text-xs text-gray-500 mt-0.5">Send us a message and we'll respond promptly</p>
        </div>
    </div>

    {{-- Contact Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach([
            ['icon'=>'fa-envelope',   'color'=>'blue',   'label'=>'Email',           'value'=>'support@yourapp.com'],
            ['icon'=>'fa-clock',      'color'=>'green',  'label'=>'Response Time',   'value'=>'Within 24 hours'],
            ['icon'=>'fa-calendar',   'color'=>'purple', 'label'=>'Business Hours',  'value'=>'Mon–Fri 8am–6pm'],
        ] as $info)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="w-10 h-10 bg-{{ $info['color'] }}-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <i class="fas {{ $info['icon'] }} text-{{ $info['color'] }}-600"></i>
            </div>
            <p class="text-xs text-gray-400 mb-0.5">{{ $info['label'] }}</p>
            <p class="text-sm font-semibold text-gray-800">{{ $info['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('agent.support.contact.send') }}" method="POST" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                        value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email"
                        value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    placeholder="What is your message about?"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="6" required
                    placeholder="Tell us how we can help you…"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 resize-none leading-relaxed">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Note --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-amber-800">
                Your message will automatically create a support ticket so you can track the response.
                For urgent issues, please use
                <a href="{{ route('agent.support.ticket.create') }}" class="underline font-medium">Open a Ticket</a>
                and set priority to <strong>Urgent</strong>.
            </p>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.support.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
        </div>
    </form>
</div>
@endsection
