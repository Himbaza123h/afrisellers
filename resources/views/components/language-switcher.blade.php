<div class="relative inline-block">
    <select
        onchange="window.location.href='{{ url('/language') }}/' + this.value"
        class="bg-transparent border-none text-black text-sm focus:outline-none cursor-pointer appearance-none pr-6"
    >
        @foreach(['en' => 'English', 'fr' => 'Français', 'sw' => 'Kiswahili'] as $code => $name)
            <option value="{{ $code }}" {{ app()->getLocale() == $code ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    <i class="fas fa-chevron-down absolute right-0 top-1/2 transform -translate-y-1/2 text-xs  mt-2"></i>
</div>
