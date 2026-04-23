document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const productsContainer = document.getElementById('products-container');
    const suppliersContainer = document.getElementById('suppliers-container');
    const filterCountBadge = document.getElementById('filter-count-badge');

    // Get all filter inputs
    const filterInputs = document.querySelectorAll('.filter-checkbox, .filter-radio');

    // Function to show loading state
    function showLoadingState() {
        const container = productsContainer || suppliersContainer;
        if (container) {
            // Add loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'loading-overlay';
            loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50';
            loadingOverlay.innerHTML = `
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600 font-medium">Applying filters...</p>
                </div>
            `;

            // Make container relative for overlay positioning
            container.style.position = 'relative';
            container.style.opacity = '0.5';
            container.appendChild(loadingOverlay);
        }
    }

    // Function to update filter count badge
    function updateFilterCount() {
        const activeFilters = Array.from(filterInputs).filter(input => input.checked).length;

        if (filterCountBadge) {
            if (activeFilters > 0) {
                filterCountBadge.textContent = activeFilters;
                filterCountBadge.classList.remove('hidden');
            } else {
                filterCountBadge.classList.add('hidden');
            }
        }
    }

    // Auto-submit form when filters change
    filterInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            // Update filter count immediately
            updateFilterCount();

            // Show loading state
            showLoadingState();

            // Small delay for better UX (optional)
            setTimeout(() => {
                filterForm.submit();
            }, 300);
        });
    });

    // Clear all filters
    const clearFiltersBtn = document.querySelector('.clear-filters-btn');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Show loading state
            showLoadingState();

            // Uncheck all checkboxes and radio buttons
            filterInputs.forEach(input => {
                input.checked = false;
            });

            // Get base URL without query parameters
            const baseUrl = window.location.pathname;
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'products';
            const sort = urlParams.get('sort');

            // Build URL with preserved parameters
            let redirectUrl = `${baseUrl}?tab=${tab}`;
            if (sort) {
                redirectUrl += `&sort=${sort}`;
            }

            // Redirect to clean URL
            window.location.href = redirectUrl;
        });
    }

    // Update filter count on page load
    updateFilterCount();

    // Sort Dropdown
    const sortBtn = document.querySelector('.sort-dropdown-btn');
    const sortDropdown = document.querySelector('.sort-dropdown');

    if (sortBtn && sortDropdown) {
        sortBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sortDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!sortBtn.contains(e.target) && !sortDropdown.contains(e.target)) {
                sortDropdown.classList.add('hidden');
            }
        });

        // Prevent dropdown from closing when clicking inside
        sortDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // View Toggle
    const viewToggles = document.querySelectorAll('.view-toggle');
    const container = productsContainer || suppliersContainer;

    if (viewToggles.length && container) {
        viewToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                viewToggles.forEach(t => {
                    t.classList.remove('active', 'bg-gray-200');
                    t.classList.add('bg-transparent');
                });
                this.classList.add('active', 'bg-gray-200');
                this.classList.remove('bg-transparent');

                const view = this.getAttribute('data-view');
                if (view === 'list') {
                    container.classList.remove('md:grid-cols-2', 'xl:grid-cols-3');
                    container.classList.add('grid-cols-1');

                    // Update product cards for list view
                    const productCards = container.querySelectorAll('.product-card, a[href*="products"]');
                    productCards.forEach(card => {
                        card.classList.add('flex', 'flex-row');
                        const img = card.querySelector('div[class*="h-48"]');
                        if (img) {
                            img.classList.remove('h-48');
                            img.classList.add('h-32', 'w-32');
                        }
                    });
                } else {
                    container.classList.remove('grid-cols-1');
                    container.classList.add('md:grid-cols-2', 'xl:grid-cols-3');

                    // Reset product cards for grid view
                    const productCards = container.querySelectorAll('.product-card, a[href*="products"]');
                    productCards.forEach(card => {
                        card.classList.remove('flex', 'flex-row');
                        const img = card.querySelector('div[class*="h-32"]');
                        if (img) {
                            img.classList.remove('h-32', 'w-32');
                            img.classList.add('h-48');
                        }
                    });
                }
            });
        });
    }

    // Scroll to Top
    const scrollTopBtn = document.getElementById('scroll-top');

    if (scrollTopBtn) {
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
    }

    // Add smooth transition to filter changes
    if (container) {
        container.style.transition = 'opacity 0.3s ease-in-out';
    }

    // Highlight active filters with animation
    filterInputs.forEach(input => {
        if (input.checked) {
            const label = input.closest('label');
            if (label) {
                label.classList.add('bg-blue-50', 'border-l-4', 'border-blue-600', 'pl-2');
            }
        }

        input.addEventListener('change', function() {
            const label = this.closest('label');
            if (label) {
                if (this.checked) {
                    label.classList.add('bg-blue-50', 'border-l-4', 'border-blue-600', 'pl-2');
                } else {
                    label.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-600', 'pl-2');
                }
            }
        });
    });

    // Add animation to checkboxes on change
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.parentElement.classList.add('scale-105');
            setTimeout(() => {
                this.parentElement.classList.remove('scale-105');
            }, 200);
        });
    });

    // Save scroll position before filter submission
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            sessionStorage.setItem('scrollPosition', window.pageYOffset);
        });
    }

    // Restore scroll position after page load (if filters were applied)
    const savedScrollPosition = sessionStorage.getItem('scrollPosition');
    if (savedScrollPosition && window.location.search.includes('verified') ||
        window.location.search.includes('rating') ||
        window.location.search.includes('price_range')) {
        window.scrollTo(0, parseInt(savedScrollPosition));
        sessionStorage.removeItem('scrollPosition');
    }
});
