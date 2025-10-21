import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const nav = document.getElementById('main-nav');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            nav.classList.add('fixed', 'top-0', 'left-0', 'right-0', 'z-50', 'shadow-lg');
        } else {
            nav.classList.remove('fixed', 'top-0', 'left-0', 'right-0', 'z-50', 'shadow-lg');
        }

        lastScroll = currentScroll;
    });

    // Categories Dropdown - HOVER BASED
    const categoriesBtn = document.getElementById('categories-btn');
    const categoriesMenu = document.getElementById('categories-menu');
    let categoriesTimeout;

    if (categoriesBtn && categoriesMenu) {
        // Show on hover
        categoriesBtn.addEventListener('mouseenter', function() {
            clearTimeout(categoriesTimeout);
            categoriesMenu.classList.remove('hidden');
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
                // Hide all other dropdowns
                document.querySelectorAll('.nav-dropdown-menu').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
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

    // Category Sidebar Navigation
    const categorySidebarBtns = document.querySelectorAll('.category-sidebar-btn');
    const categoryContents = document.querySelectorAll('.category-content');

    categorySidebarBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category');

            // Update sidebar buttons styling
            categorySidebarBtns.forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-white', 'text-gray-700');
            });
            this.classList.remove('bg-white', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white');

            // Show selected category content
            categoryContents.forEach(content => {
                content.classList.add('hidden');
            });
            const selectedContent = document.getElementById('category-' + categoryId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
        });

        // Also handle hover for better UX
        btn.addEventListener('mouseenter', function() {
            const categoryId = this.getAttribute('data-category');

            // Update sidebar buttons styling
            categorySidebarBtns.forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-white', 'text-gray-700');
            });
            this.classList.remove('bg-white', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white');

            // Show selected category content
            categoryContents.forEach(content => {
                content.classList.add('hidden');
            });
            const selectedContent = document.getElementById('category-' + categoryId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
        });
    });

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
