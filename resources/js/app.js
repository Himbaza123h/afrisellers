import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const nav = document.getElementById('main-nav');
    let lastScroll = 0;
    let navHeight = 0;

    // Calculate navigation height
    function updateNavHeight() {
        navHeight = nav ? nav.offsetHeight : 0;
    }

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            nav.classList.add('fixed', 'top-0', 'left-0', 'right-0', 'z-50', 'shadow-lg');
        } else {
            nav.classList.remove('fixed', 'top-0', 'left-0', 'right-0', 'z-50', 'shadow-lg');
            updateNavHeight();
        }

        lastScroll = currentScroll;
    });

    // Initial nav height calculation
    updateNavHeight();

    // Recalculate on window resize
    window.addEventListener('resize', updateNavHeight);

    // Categories Dropdown - HOVER BASED
    const categoriesBtn = document.getElementById('categories-btn');
    const categoriesMenu = document.getElementById('categories-menu');
    let categoriesTimeout;

    if (categoriesBtn && categoriesMenu) {
        // Show on hover
        categoriesBtn.addEventListener('mouseenter', function() {
            clearTimeout(categoriesTimeout);
            // Hide other dropdowns first
            document.querySelectorAll('.nav-dropdown-menu').forEach(m => {
                m.classList.add('hidden');
            });
            categoriesMenu.classList.remove('hidden');

            // Ensure first category is visible when menu opens
            const firstCategoryBtn = document.querySelector('.category-sidebar-btn');
            const firstCategoryContent = document.querySelector('.category-content');
            if (firstCategoryBtn && firstCategoryContent) {
                // Reset all categories
                document.querySelectorAll('.category-sidebar-btn').forEach(b => {
                    b.classList.remove('bg-[#ff0808]', 'text-white', 'font-bold');
                    b.classList.add('bg-white', 'text-gray-700', 'font-semibold');
                });
                document.querySelectorAll('.category-content').forEach(c => {
                    c.classList.add('hidden');
                });

                // Show first category
                firstCategoryBtn.classList.remove('bg-white', 'text-gray-700', 'font-semibold');
                firstCategoryBtn.classList.add('bg-[#ff0808]', 'text-white', 'font-bold');
                firstCategoryContent.classList.remove('hidden');
            }
        });

        // Keep open when hovering over menu
        categoriesMenu.addEventListener('mouseenter', function() {
            clearTimeout(categoriesTimeout);
        });

        // Hide with delay when leaving button
        categoriesBtn.addEventListener('mouseleave', function() {
            categoriesTimeout = setTimeout(() => {
                categoriesMenu.classList.add('hidden');
            }, 200);
        });

        // Hide with delay when leaving menu
        categoriesMenu.addEventListener('mouseleave', function() {
            categoriesTimeout = setTimeout(() => {
                categoriesMenu.classList.add('hidden');
            }, 200);
        });
    }

    // Generic Dropdown Handler for other navigation items
    const navDropdowns = document.querySelectorAll('.nav-dropdown-trigger');

    navDropdowns.forEach(trigger => {
        const menuId = trigger.getAttribute('data-dropdown');
        const menu = document.getElementById(menuId);
        let timeout;

        if (menu) {
            trigger.addEventListener('mouseenter', function() {
                clearTimeout(timeout);
                // Hide all other dropdowns including categories menu
                document.querySelectorAll('.nav-dropdown-menu').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
                if (categoriesMenu && menu !== categoriesMenu) {
                    categoriesMenu.classList.add('hidden');
                }
                menu.classList.remove('hidden');
            });

            menu.addEventListener('mouseenter', function() {
                clearTimeout(timeout);
            });

            trigger.addEventListener('mouseleave', function() {
                timeout = setTimeout(() => {
                    menu.classList.add('hidden');
                }, 200);
            });

            menu.addEventListener('mouseleave', function() {
                timeout = setTimeout(() => {
                    menu.classList.add('hidden');
                }, 200);
            });
        }
    });

    // Category Sidebar Navigation - HOVER BASED (shows products on hover)
    const categorySidebarBtns = document.querySelectorAll('.category-sidebar-btn');
    const categoryContents = document.querySelectorAll('.category-content');
    let categoryHoverTimeout;

    categorySidebarBtns.forEach(btn => {
        // Show category products on hover (mouseenter)
        btn.addEventListener('mouseenter', function() {
            clearTimeout(categoryHoverTimeout);
            const categoryId = this.getAttribute('data-category');

            // Update sidebar buttons styling
            categorySidebarBtns.forEach(b => {
                b.classList.remove('bg-[#ff0808]', 'text-white', 'font-bold');
                b.classList.add('bg-white', 'text-gray-700', 'font-semibold');
            });
            this.classList.remove('bg-white', 'text-gray-700', 'font-semibold');
            this.classList.add('bg-[#ff0808]', 'text-white', 'font-bold');

            // Show selected category content
            categoryContents.forEach(content => {
                content.classList.add('hidden');
            });
            const selectedContent = document.getElementById(categoryId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
        });

        // Keep click functionality as fallback
        btn.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category');

            // Update sidebar buttons styling
            categorySidebarBtns.forEach(b => {
                b.classList.remove('bg-[#ff0808]', 'text-white', 'font-bold');
                b.classList.add('bg-white', 'text-gray-700', 'font-semibold');
            });
            this.classList.remove('bg-white', 'text-gray-700', 'font-semibold');
            this.classList.add('bg-[#ff0808]', 'text-white', 'font-bold');

            // Show selected category content
            categoryContents.forEach(content => {
                content.classList.add('hidden');
            });
            // Use categoryId directly since data-category already includes "category-" prefix
            const selectedContent = document.getElementById(categoryId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
        });
    });

    // Category Search Functionality
    const categorySearchInput = document.getElementById('category-search-input');
    const categoriesList = document.getElementById('categories-list');

    if (categorySearchInput && categoriesList) {
        categorySearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const categoryButtons = categoriesList.querySelectorAll('.category-sidebar-btn');
            let hasVisibleCategory = false;
            let firstVisibleCategory = null;

            categoryButtons.forEach(btn => {
                const categoryName = btn.getAttribute('data-category-name');
                const categoryNameSpan = btn.querySelector('.category-name');

                if (categoryName && categoryName.includes(searchTerm)) {
                    btn.style.display = 'block';
                    hasVisibleCategory = true;

                    // Highlight matching text
                    if (searchTerm && categoryNameSpan) {
                        const originalText = categoryNameSpan.textContent;
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        categoryNameSpan.innerHTML = originalText.replace(regex, '<mark class="bg-yellow-200">$1</mark>');
                    }

                    if (!firstVisibleCategory) {
                        firstVisibleCategory = btn;
                    }
                } else {
                    btn.style.display = 'none';
                    // Reset highlighting
                    if (categoryNameSpan) {
                        categoryNameSpan.innerHTML = categoryNameSpan.textContent;
                    }
                }
            });

            // If search term exists and we have a visible category, show its products
            if (searchTerm && firstVisibleCategory) {
                const categoryId = firstVisibleCategory.getAttribute('data-category');
                const selectedContent = document.getElementById(categoryId);

                // Update sidebar buttons styling
                categoryButtons.forEach(b => {
                    b.classList.remove('bg-[#ff0808]', 'text-white', 'font-bold');
                    b.classList.add('bg-white', 'text-gray-700', 'font-semibold');
                });
                firstVisibleCategory.classList.remove('bg-white', 'text-gray-700', 'font-semibold');
                firstVisibleCategory.classList.add('bg-[#ff0808]', 'text-white', 'font-bold');

                // Show selected category content
                document.querySelectorAll('.category-content').forEach(content => {
                    content.classList.add('hidden');
                });
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                }
            } else if (!searchTerm) {
                // Reset to first category when search is cleared
                const firstBtn = categoriesList.querySelector('.category-sidebar-btn');
                const firstContent = document.querySelector('.category-content');
                if (firstBtn && firstContent) {
                    categoryButtons.forEach(b => {
                        b.classList.remove('bg-[#ff0808]', 'text-white', 'font-bold');
                        b.classList.add('bg-white', 'text-gray-700', 'font-semibold');
                    });
                    firstBtn.classList.remove('bg-white', 'text-gray-700', 'font-semibold');
                    firstBtn.classList.add('bg-[#ff0808]', 'text-white', 'font-bold');

                    document.querySelectorAll('.category-content').forEach(c => {
                        c.classList.add('hidden');
                    });
                    firstContent.classList.remove('hidden');
                }
            }

            // Show "No results" message if no categories match
            let noResultsMsg = categoriesList.querySelector('.no-results-message');
            if (!hasVisibleCategory && searchTerm) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results-message text-center py-8 text-gray-500';
                    noResultsMsg.innerHTML = '<i class="fas fa-search text-lg mb-2"></i><p>No categories found matching "' + searchTerm + '"</p>';
                    categoriesList.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });

        // Clear search on escape key
        categorySearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('input'));
                this.blur();
            }
        });
    }

    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Hero Slider
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    let currentSlide = 0;

    if (slides.length > 0) {
        function showSlide(index) {
            slides.forEach(slide => {
                slide.style.opacity = '0';
            });

            dots.forEach(dot => {
                dot.classList.remove('bg-white');
                dot.classList.add('bg-white/50');
            });

            slides[index].style.opacity = '100';
            if (dots[index]) {
                dots[index].classList.remove('bg-white/50');
                dots[index].classList.add('bg-white');
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        let slideInterval = setInterval(nextSlide, 5000);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);

                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            });
        });

        showSlide(0);
    }

    // Category Slider
    const categorySlider = document.getElementById('category-slider');
    const categorySlides = document.querySelectorAll('.category-slide');
    const categoryPrevBtn = document.getElementById('category-prev');
    const categoryNextBtn = document.getElementById('category-next');
    const categoryDotsContainer = document.getElementById('category-dots');

    if (categorySlider && categorySlides.length > 0) {
        let currentIndex = 0;
        let slidesToShow = 4;
        let autoSlideInterval;

        function updateSlidesToShow() {
            const width = window.innerWidth;
            if (width < 480) {
                slidesToShow = 1;
            } else if (width < 768) {
                slidesToShow = 2;
            } else if (width < 1024) {
                slidesToShow = 3;
            } else {
                slidesToShow = 4;
            }
        }

        function getTotalPages() {
            return Math.ceil(categorySlides.length / slidesToShow);
        }

        function createDots() {
            categoryDotsContainer.innerHTML = '';
            const totalPages = getTotalPages();
            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement('button');
                dot.className = 'w-2 h-2 rounded-full transition-all duration-300';
                dot.classList.add(i === 0 ? 'bg-blue-600 w-8' : 'bg-gray-300');
                dot.addEventListener('click', () => goToSlide(i));
                categoryDotsContainer.appendChild(dot);
            }
        }

        function updateDots() {
            const dots = categoryDotsContainer.querySelectorAll('button');
            const currentPage = Math.floor(currentIndex / slidesToShow);
            dots.forEach((dot, index) => {
                if (index === currentPage) {
                    dot.classList.remove('bg-gray-300', 'w-2');
                    dot.classList.add('bg-blue-600', 'w-8');
                } else {
                    dot.classList.remove('bg-blue-600', 'w-8');
                    dot.classList.add('bg-gray-300', 'w-2');
                }
            });
        }

        function moveSlider() {
            const slideWidth = categorySlides[0].offsetWidth;
            const gap = 16;
            const offset = -(currentIndex * (slideWidth + gap));
            categorySlider.style.transform = `translateX(${offset}px)`;
            updateDots();
        }

        function nextSlide() {
            const maxIndex = categorySlides.length - slidesToShow;
            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            moveSlider();
        }

        function prevSlide() {
            const maxIndex = categorySlides.length - slidesToShow;
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = maxIndex;
            }
            moveSlider();
        }

        function goToSlide(pageIndex) {
            currentIndex = pageIndex * slidesToShow;
            const maxIndex = categorySlides.length - slidesToShow;
            if (currentIndex > maxIndex) {
                currentIndex = maxIndex;
            }
            moveSlider();
        }

        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, 3000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        if (categoryNextBtn) {
            categoryNextBtn.addEventListener('click', () => {
                nextSlide();
                stopAutoSlide();
                startAutoSlide();
            });
        }

        if (categoryPrevBtn) {
            categoryPrevBtn.addEventListener('click', () => {
                prevSlide();
                stopAutoSlide();
                startAutoSlide();
            });
        }

        categorySlider.addEventListener('mouseenter', stopAutoSlide);
        categorySlider.addEventListener('mouseleave', startAutoSlide);

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                updateSlidesToShow();
                createDots();
                currentIndex = 0;
                moveSlider();
            }, 250);
        });

        updateSlidesToShow();
        createDots();
        moveSlider();
        startAutoSlide();
    }
});
