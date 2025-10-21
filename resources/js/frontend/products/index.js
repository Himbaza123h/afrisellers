
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => {
                b.classList.remove('active', 'text-gray-900', 'border-gray-900');
                b.classList.add('text-gray-500', 'border-transparent');
            });
            this.classList.add('active', 'text-gray-900', 'border-gray-900');
            this.classList.remove('text-gray-500', 'border-transparent');
        });
    });

    // Sort Dropdown
    const sortBtn = document.querySelector('.sort-dropdown-btn');
    const sortDropdown = document.querySelector('.sort-dropdown');

    if (sortBtn && sortDropdown) {
        sortBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sortDropdown.classList.toggle('hidden');
        });

        sortDropdown.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                sortBtn.querySelector('span').textContent = 'Sort by ' + this.textContent;
                sortDropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', function() {
            sortDropdown.classList.add('hidden');
        });
    }

    // View Toggle
    const viewToggles = document.querySelectorAll('.view-toggle');
    const productsContainer = document.getElementById('products-container');

    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            viewToggles.forEach(t => t.classList.remove('active', 'bg-gray-200'));
            this.classList.add('active', 'bg-gray-200');

            const view = this.getAttribute('data-view');
            if (view === 'list') {
                productsContainer.classList.remove('md:grid-cols-2', 'xl:grid-cols-3');
                productsContainer.classList.add('grid-cols-1');
            } else {
                productsContainer.classList.remove('grid-cols-1');
                productsContainer.classList.add('md:grid-cols-2', 'xl:grid-cols-3');
            }
        });
    });

    // Wishlist Toggle
    document.querySelectorAll('.fa-heart').forEach(heart => {
        heart.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            if (heart.classList.contains('far')) {
                heart.classList.remove('far', 'text-gray-600');
                heart.classList.add('fas', 'text-red-500');
                this.classList.add('bg-red-50');
            } else {
                heart.classList.remove('fas', 'text-red-500');
                heart.classList.add('far', 'text-gray-600');
                this.classList.remove('bg-red-50');
            }
        });
    });

    // Scroll to Top
    const scrollTopBtn = document.getElementById('scroll-top');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.remove('opacity-0', 'pointer-events-none');
            scrollTopBtn.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            scrollTopBtn.classList.remove('opacity-100', 'pointer-events-auto');
            scrollTopBtn.classList.add('opacity-0', 'pointer-events-none');
        }
    });

    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Filter animations
    document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(input => {
        input.addEventListener('change', function() {
            // Add a subtle animation effect when filters change
            productsContainer.style.opacity = '0.5';
            setTimeout(() => {
                productsContainer.style.opacity = '1';
            }, 200);
        });
    });
});
