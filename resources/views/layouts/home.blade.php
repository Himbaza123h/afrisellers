@extends('layouts.main')

@section('title', auth()->user()->name)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="flex flex-col h-screen overflow-hidden">
    <!-- Top Navigation Bar -->
    @include('partials.topbar')

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden transition-opacity duration-300"></div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6">
            <div class="max-w-[2000px] mx-auto">
                @yield('page-content')
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const closeSidebarBtn = document.getElementById('close-sidebar');

    function toggleSidebar() {
        const icon = mobileMenuBtn.querySelector('i');
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');

        if (icon.classList.contains('fa-bars')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }

    function closeSidebar() {
        const icon = mobileMenuBtn.querySelector('i');
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }

    mobileMenuBtn.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', closeSidebar);
    }

    // Profile dropdown
    const profileDropdownBtn = document.getElementById('profile-dropdown-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    profileDropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!profileDropdownBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.add('hidden');
        }
    });
</script>
@endpush
