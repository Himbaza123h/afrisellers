console.log('🔵 AfriSellers JavaScript loaded!');

document.addEventListener('DOMContentLoaded', function() {

    console.log('Product page JavaScript loading...');

    // ========================================
    // DUMMY DATA - Products, Suppliers, Worldwide
    // ========================================

    const DUMMY_DATA = {
        products: [
            {
                id: 1,
                name: "Premium Wireless Bluetooth Headphones - High Quality Sound",
                image: "https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$45.99",
                original_price: "$89.99",
                discount: "-49%",
                min_order: "50 pieces",
                supplier: "TechGear Electronics Co., Ltd",
                rating: 4.8,
                reviews: "2.5K reviews",
                years: 8,
                country: "cn",
                sold: "15K sold",
                delivery: "Est. delivery: 15-25 days",
                badge: "Hot Seller",
                slug: "premium-wireless-bluetooth-headphones-high-quality-sound"

            },
            {
                id: 2,
                name: "Smart Watch Fitness Tracker with Heart Rate Monitor",
                image: "https://images.pexels.com/photos/437037/pexels-photo-437037.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$29.50",
                min_order: "100 pieces",
                supplier: "SmartTech Industries",
                rating: 4.6,
                reviews: "1.8K reviews",
                years: 5,
                country: "cn",
                sold: "8K sold",
                delivery: "Est. delivery: 10-20 days",
                badge: "Trending",
                slug: "smart-watch-fitness-tracker-with-heart-rate-monitor"
            },
            {
                id: 3,
                name: "USB-C Fast Charging Cable 6ft Premium Quality",
                image: "https://images.pexels.com/photos/3945683/pexels-photo-3945683.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$8.99",
                original_price: "$15.99",
                discount: "-44%",
                min_order: "500 pieces",
                supplier: "Cable Connect Manufacturing",
                rating: 4.9,
                reviews: "3.2K reviews",
                years: 6,
                country: "cn",
                sold: "25K sold",
                delivery: "Est. delivery: 12-18 days",
                badge: "Best Seller",
                slug: "usb-c-fast-charging-cable-6ft-premium-quality"
            },
            {
                id: 4,
                name: "Portable Power Bank 20000mAh Fast Charge",
                image: "https://images.pexels.com/photos/4223030/pexels-photo-4223030.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$19.99",
                min_order: "200 pieces",
                supplier: "PowerPlus Technology",
                rating: 4.7,
                reviews: "1.5K reviews",
                years: 7,
                country: "cn",
                delivery: "Est. delivery: 15-22 days",
                slug: "portable-power-bank-20000mah-fast-charge"
            },
            {
                id: 5,
                name: "Wireless Gaming Mouse RGB LED Professional",
                image: "https://images.pexels.com/photos/2115257/pexels-photo-2115257.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$35.50",
                original_price: "$59.99",
                discount: "-41%",
                min_order: "50 pieces",
                supplier: "GameTech Peripherals Ltd",
                rating: 4.8,
                reviews: "2.1K reviews",
                years: 4,
                country: "cn",
                sold: "12K sold",
                badge: "New Arrival",
                slug: "wireless-gaming-mouse-rgb-led-professional"
            },
            {
                id: 6,
                name: "Mechanical Gaming Keyboard RGB Backlit",
                image: "https://images.pexels.com/photos/1714208/pexels-photo-1714208.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$52.00",
                min_order: "30 pieces",
                supplier: "KeyMaster Electronics",
                rating: 4.9,
                reviews: "1.9K reviews",
                years: 9,
                country: "cn",
                sold: "9K sold",
                delivery: "Est. delivery: 18-28 days",
                slug: "mechanical-gaming-keyboard-rgb-backlit"
            },
            {
                id: 7,
                name: "4K Webcam with Microphone HD Pro",
                image: "https://images.pexels.com/photos/2582937/pexels-photo-2582937.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$68.00",
                min_order: "25 pieces",
                supplier: "VideoTech Solutions",
                rating: 4.7,
                reviews: "987 reviews",
                years: 5,
                country: "cn",
                sold: "4K sold",
                delivery: "Est. delivery: 20-30 days",
            },
            {
                id: 8,
                name: "Bluetooth Speaker Waterproof Portable",
                image: "https://images.pexels.com/photos/1279406/pexels-photo-1279406.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$42.50",
                min_order: "80 pieces",
                supplier: "AudioMax Industries",
                rating: 4.8,
                reviews: "2.3K reviews",
                years: 6,
                country: "cn",
                delivery: "Est. delivery: 14-20 days",
                slug: "bluetooth-speaker-waterproof-portable"
            },
            {
                id: 9,
                name: "LED Ring Light with Tripod Stand",
                image: "https://images.pexels.com/photos/4553111/pexels-photo-4553111.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$34.99",
                original_price: "$59.99",
                discount: "-42%",
                min_order: "40 pieces",
                supplier: "LightPro Studios",
                rating: 4.6,
                reviews: "1.4K reviews",
                years: 4,
                country: "cn",
                badge: "Popular",
                slug: "led-ring-light-with-tripod-stand"
            }
        ],
        suppliers: [
            {
                id: 1,
                name: "Premium Wireless Bluetooth Headphones - High Quality Sound",
                image: "https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$45.99",
                min_order: "50 pieces",
                supplier: "TechGear Electronics Co., Ltd",
                rating: 4.8,
                reviews: "2.5K reviews",
                years: 8,
                country: "cn",
                products_count: 248,
                response_time: "< 2 hours",
                badge: "Gold Supplier",
                slug: "premium-wireless-bluetooth-headphones-high-quality-sound"
            },
            {
                id: 2,
                name: "Smart Watch Fitness Tracker with Heart Rate Monitor",
                image: "https://images.pexels.com/photos/437037/pexels-photo-437037.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$29.50",
                min_order: "100 pieces",
                supplier: "SmartTech Industries",
                rating: 4.6,
                reviews: "1.8K reviews",
                years: 5,
                country: "cn",
                products_count: 156,
                response_time: "< 4 hours",
                badge: "Verified Supplier",
            },
            {
                id: 3,
                name: "Portable Power Bank 20000mAh Fast Charge",
                image: "https://images.pexels.com/photos/4223030/pexels-photo-4223030.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$19.99",
                min_order: "200 pieces",
                supplier: "PowerPlus Technology",
                rating: 4.7,
                reviews: "1.5K reviews",
                years: 7,
                country: "cn",
                products_count: 312,
                response_time: "< 1 hour",
                badge: "Verified Pro",
                slug: "portable-power-bank-20000mah-fast-charge"
            },
            {
                id: 4,
                name: "Wireless Gaming Mouse RGB LED Professional",
                image: "https://images.pexels.com/photos/2115257/pexels-photo-2115257.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$35.50",
                min_order: "50 pieces",
                supplier: "GameTech Peripherals Ltd",
                rating: 4.8,
                reviews: "2.1K reviews",
                years: 4,
                country: "cn",
                products_count: 89,
                response_time: "< 3 hours",
                badge: "New Arrival",
                slug: "wireless-gaming-mouse-rgb-led-professional"
            }
        ],
        worldwide: [
            {
                id: 1,
                name: "Premium Leather Wallet Handcrafted Designer",
                image: "https://images.pexels.com/photos/1078958/pexels-photo-1078958.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$75.00",
                min_order: "20 pieces",
                supplier: "Italian Leather Goods Inc.",
                rating: 4.9,
                reviews: "890 reviews",
                years: 12,
                country: "it",
                delivery: "Global Express: 5-10 days",
                badge: "Premium Quality",
                slug: "premium-leather-wallet-handcrafted-designer"
            },
            {
                id: 2,
                name: "Organic Cotton T-Shirts Sustainable Fashion",
                image: "https://images.pexels.com/photos/1040945/pexels-photo-1040945.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$18.50",
                min_order: "100 pieces",
                supplier: "EcoWear USA",
                rating: 4.7,
                reviews: "1.2K reviews",
                years: 6,
                country: "us",
                sold: "5K sold",
                delivery: "Global shipping: 7-14 days",
                badge: "Eco-Friendly",
                slug: "organic-cotton-t-shirts-sustainable-fashion"
            },
            {
                id: 3,
                name: "Handmade Ceramic Coffee Mugs Artisan Design",
                image: "https://images.pexels.com/photos/1251175/pexels-photo-1251175.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$22.00",
                original_price: "$35.00",
                discount: "-37%",
                min_order: "50 pieces",
                supplier: "Portuguese Pottery Studio",
                rating: 5.0,
                reviews: "654 reviews",
                years: 15,
                country: "pt",
                delivery: "International: 10-18 days",
                slug: "handmade-ceramic-coffee-mugs-artisan-design"
            },
            {
                id: 4,
                name: "Premium Chocolate Gift Box Gourmet Selection",
                image: "https://images.pexels.com/photos/3776942/pexels-photo-3776942.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$45.00",
                min_order: "30 pieces",
                supplier: "Swiss Chocolatiers Ltd",
                rating: 4.9,
                reviews: "756 reviews",
                years: 20,
                country: "ch",
                sold: "3K sold",
                delivery: "Express worldwide: 5-12 days",
                badge: "Gourmet",
                slug: "premium-chocolate-gift-box-gourmet-selection"
            },
            {
                id: 5,
                name: "Luxury Spa Gift Set Natural Ingredients",
                image: "https://images.pexels.com/photos/3738386/pexels-photo-3738386.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$38.00",
                min_order: "40 pieces",
                supplier: "French Beauty Co.",
                rating: 4.8,
                reviews: "1.1K reviews",
                years: 10,
                country: "fr",
                delivery: "Global delivery: 8-15 days",
                slug: "luxury-spa-gift-set-natural-ingredients"
            },
            {
                id: 6,
                name: "Professional Camera Lens Kit Photography",
                image: "https://images.pexels.com/photos/414781/pexels-photo-414781.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$299.00",
                original_price: "$450.00",
                discount: "-34%",
                min_order: "10 pieces",
                supplier: "German Optics GmbH",
                rating: 4.9,
                reviews: "423 reviews",
                years: 18,
                country: "de",
                sold: "1.2K sold",
                delivery: "Worldwide: 6-10 days",
                badge: "Professional",
                slug: "professional-camera-lens-kit-photography"
            },
            {
                id: 7,
                name: "Handwoven Wool Rugs Traditional Design",
                image: "https://images.pexels.com/photos/1350789/pexels-photo-1350789.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$180.00",
                min_order: "15 pieces",
                supplier: "Turkish Textile Traders",
                rating: 4.8,
                reviews: "567 reviews",
                years: 25,
                country: "tr",
                delivery: "International: 12-20 days",
                slug: "handwoven-wool-rugs-traditional-design"
            },
            {
                id: 8,
                name: "Artisan Coffee Beans Premium Roast",
                image: "https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg?auto=compress&cs=tinysrgb&w=600",
                price: "$28.00",
                min_order: "50 pieces",
                supplier: "Colombian Coffee Exports",
                rating: 4.9,
                reviews: "934 reviews",
                years: 8,
                country: "co",
                sold: "2.5K sold",
                delivery: "Global: 8-14 days",
                badge: "Organic",
                slug: "artisan-coffee-beans-premium-roast"
            }
        ]
    };

    // ========================================
    // Tab Switching with Content Loading
    // ========================================
    const tabBtns = document.querySelectorAll('.tab-btn');
    const productsContainer = document.getElementById('products-container');
    const resultsHeading = document.querySelector('h1');

    console.log('Found tab buttons:', tabBtns.length);
    console.log('Found products container:', productsContainer ? 'Yes' : 'No');

    let currentView = 'products';

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active state from all tabs
            tabBtns.forEach(b => {
                b.classList.remove('active', 'text-gray-900', 'border-[#ff0808]', 'font-bold');
                b.classList.add('text-gray-600', 'border-transparent', 'font-medium');
            });

            // Add active state to clicked tab
            this.classList.add('active', 'text-gray-900', 'border-[#ff0808]', 'font-bold');
            this.classList.remove('text-gray-600', 'border-transparent', 'font-medium');

            // Get tab name
            const tabName = this.getAttribute('data-tab');
            currentView = tabName;

            // Load content based on tab
            loadTabContent(tabName);

            // Update sidebar based on tab
            updateSidebar(tabName);
        });
    });

    function loadTabContent(tabName) {
        console.log('Loading tab content for:', tabName);

        // Show loading state
        productsContainer.style.opacity = '0.5';
        productsContainer.style.pointerEvents = 'none';

        // Simulate loading delay
        setTimeout(() => {
            const data = DUMMY_DATA[tabName] || DUMMY_DATA.products;
            console.log('Rendering', data.length, 'items for', tabName);
            renderProducts(data, tabName);
            updateResultsCount(data.length, tabName);

            productsContainer.style.opacity = '1';
            productsContainer.style.pointerEvents = 'auto';

            // Scroll to top smoothly
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 300);
    }

    function renderProducts(products, viewType) {
        productsContainer.innerHTML = '';

        products.forEach(product => {
            const card = createProductCard(product, viewType);
            productsContainer.appendChild(card);
        });

        // Reinitialize event listeners
        initializeProductCardListeners();
    }

    function createProductCard(product, viewType) {
        const card = document.createElement('div');
        card.className = 'product-card bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-300 group overflow-hidden';

        // Add view-specific badge
        let viewBadge = '';
        if (viewType === 'suppliers') {
            viewBadge = `<div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded shadow-sm z-20">
                Supplier
            </div>`;
        } else if (viewType === 'worldwide') {
            viewBadge = `<div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded shadow-sm z-20">
                Worldwide
            </div>`;
        }

        // Supplier-specific content
        const supplierInfo = viewType === 'suppliers' && product.products_count ? `
            <div class="bg-gray-50 border border-gray-200 p-2 rounded mt-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">${product.products_count} Products</span>
                    <span class="text-gray-700 font-medium">${product.response_time}</span>
                </div>
            </div>
        ` : '';

        card.innerHTML = `
            <a href="/products/${product.slug}" class="block relative overflow-hidden bg-gray-50" style="height: 180px;">
                <img src="${product.image}"
                        alt="${product.name}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy">
                </a>

                <button class="wishlist-btn absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-all z-20">
                    <i class="far fa-heart text-gray-500 text-sm"></i>
                </button>

                ${viewBadge}
                ${product.badge ? `
                    <div class="absolute ${viewType === 'suppliers' || viewType === 'worldwide' ? 'top-10' : 'top-2'} left-2 bg-[#ff0808] text-white text-xs px-2 py-1 rounded shadow-sm z-10">
                        ${product.badge}
                    </div>
                ` : ''}
            </div>

            <div class="p-3">
                <h3 class="text-gray-800 font-medium mb-2 line-clamp-2 group-hover:text-[#ff0808] transition-colors text-sm leading-tight">
                    ${product.name}
                </h3>

                <div class="mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-lg font-bold text-gray-900">${product.price}</span>
                        ${product.discount ? `<span class="text-xs text-white bg-red-500 px-1.5 py-0.5 rounded font-medium">${product.discount}</span>` : ''}
                    </div>
                    ${product.original_price ? `<span class="text-xs text-gray-400 line-through">${product.original_price}</span>` : ''}
                    <div class="text-xs text-gray-500 mt-1">Min. order: ${product.min_order}</div>
                    ${product.sold ? `<div class="text-xs text-gray-400 mt-0.5">${product.sold}</div>` : ''}
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <div class="text-xs text-gray-600 mb-2 hover:text-[#ff0808] cursor-pointer truncate">
                        ${product.supplier}
                    </div>

                    <div class="flex items-center justify-between text-xs mb-2">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1">
                                <div class="flex text-yellow-400">
                                    ${generateStars(product.rating)}
                                </div>
                                <span class="text-gray-700 font-medium">${product.rating}</span>
                            </div>
                            <span class="text-gray-500">(${product.reviews})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500">${product.years} yrs</span>
                            <img src="https://flagcdn.com/16x12/${product.country.toLowerCase()}.png"
                                 alt="${product.country}"
                                 class="w-4 h-3 rounded shadow-sm"
                                 loading="lazy">
                        </div>
                    </div>

                    ${product.delivery ? `
                        <div class="text-xs text-gray-500 flex items-center gap-1 bg-gray-50 p-1.5 rounded mb-2">
                            <i class="fas fa-shipping-fast text-[#ff0808] text-xs"></i>
                            <span>${product.delivery}</span>
                        </div>
                    ` : ''}

                    ${supplierInfo}

                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-600 hover:text-[#ff0808] cursor-pointer transition-colors">Contact supplier</span>
                    </div>
                </div>
            </div>
        `;

        return card;
    }

    function generateStars(rating) {
        let stars = '';
        for (let i = 0; i < 5; i++) {
            if (i < Math.floor(rating)) {
                stars += '<i class="fas fa-star" style="font-size: 10px;"></i>';
            } else {
                stars += '<i class="far fa-star" style="font-size: 10px;"></i>';
            }
        }
        return stars;
    }

    function updateResultsCount(total, viewType) {
        if (resultsHeading) {
            const viewText = viewType === 'suppliers' ? 'suppliers' : viewType === 'worldwide' ? 'worldwide products' : 'products';
            const searchQuery = resultsHeading.textContent.match(/"([^"]+)"/)?.[1] || 'electronics';
            resultsHeading.textContent = `Showing ${total}+ ${viewText} from global suppliers for "${searchQuery}"`;
        }
    }

    // ========================================
    // Update Sidebar Based on Tab
    // ========================================
    function updateSidebar(tabName) {
        const sidebar = document.querySelector('aside');
        if (!sidebar) return;

        const sidebarContainer = sidebar.querySelector('.bg-white');

        if (tabName === 'suppliers') {
            sidebarContainer.innerHTML = `
                <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-4 md:mb-6">Supplier Filters</h2>

                <!-- Verified Suppliers -->
                <div class="mb-4 md:mb-6 pb-4 md:pb-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-3 md:mb-4 text-sm md:text-base">Verification Status</h3>
                    <div class="space-y-2 md:space-y-3">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" class="mr-2 filter-checkbox w-4 h-4">
                            <span class="text-blue-600 font-semibold group-hover:underline text-sm md:text-base">Verified Supplier</span>
                            <i class="fas fa-check-circle text-blue-600 ml-1 text-xs"></i>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" class="mr-2 filter-checkbox w-4 h-4">
                            <span class="text-blue-600 font-semibold group-hover:underline text-sm md:text-base">Gold Supplier</span>
                            <span class="bg-yellow-400 text-white text-xs px-2 py-0.5 ml-1 rounded font-bold">GOLD</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" class="mr-2 filter-checkbox w-4 h-4">
                            <span class="text-blue-600 font-semibold group-hover:underline text-sm md:text-base">Pro Verified</span>
                            <span class="bg-blue-600 text-white text-xs px-2 py-0.5 ml-1 rounded font-bold">PRO</span>
                        </label>
                    </div>
                </div>

                <!-- Years in Business -->
                <div class="mb-4 md:mb-6 pb-4 md:pb-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-3 md:mb-4 text-sm md:text-base">Years in Business</h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="radio" name="years" class="mr-2 filter-radio w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">5+ years</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="radio" name="years" class="mr-2 filter-radio w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">10+ years</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="radio" name="years" class="mr-2 filter-radio w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">15+ years</span>
                        </label>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="mb-4 md:mb-6 pb-4 md:pb-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-3 md:mb-4 text-sm md:text-base">Response Time</h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="checkbox" class="mr-2 filter-checkbox w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">Within 1 hour</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="checkbox" class="mr-2 filter-checkbox w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">Within 24 hours</span>
                        </label>
                    </div>
                </div>

                <!-- Supplier Rating -->
                <div class="mb-4 md:mb-6 pb-4 md:pb-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-2 md:mb-3 text-sm md:text-base">Supplier Rating</h3>
                    <p class="text-xs md:text-sm text-gray-600 mb-3">Based on overall performance</p>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="radio" name="supplier-rating" class="mr-2 filter-radio w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">4.5 & up</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="radio" name="supplier-rating" class="mr-2 filter-radio w-4 h-4">
                            <span class="text-gray-700 text-sm md:text-base">4.8 & up</span>
                        </label>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                <button class="clear-filters-btn w-full bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-2.5 md:py-3 px-4 rounded-lg transition-colors text-sm md:text-base">
                    Clear all filters
                </button>
            `;
        } else if (tabName === 'worldwide') {
            sidebarContainer.innerHTML = `
                <h2 class="text-xl font-bold text-gray-900 mb-6">Worldwide Filters</h2>

                <!-- Region -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Region</h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">North America</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">South America</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">Asia</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">Africa</span>
                        </label>
                    </div>
                </div>

                <!-- Shipping Options -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Shipping Options</h3>
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">Global Express Shipping</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">Free International Shipping</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">DHL/FedEx Available</span>
                        </label>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Certifications</h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">ISO Certified</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">Fair Trade</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-gray-700">Organic Certified</span>
                        </label>
                    </div>
                </div>

                <!-- Product Rating -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Product Rating</h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" name="worldwide-rating" class="mr-2 filter-radio">
                            <span class="text-gray-700">4.5 & up</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" name="worldwide-rating" class="mr-2 filter-radio">
                            <span class="text-gray-700">4.8 & up</span>
                        </label>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                <button class="clear-filters-btn w-full bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-2 px-4 rounded-lg transition-colors">
                    Clear all filters
                </button>
            `;
        } else {
            // Default Products sidebar
            sidebarContainer.innerHTML = `
                <h2 class="text-xl font-bold text-gray-900 mb-6">Filters</h2>

                <!-- Trade Assurance -->
                <div class="mb-6 pb-6 border-b">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-5 h-5 bg-yellow-400 rounded flex items-center justify-center mt-0.5">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                        </div>
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="mr-2 filter-checkbox">
                                <span class="font-semibold text-gray-900">Trade Assurance</span>
                            </label>
                            <p class="text-sm text-gray-600 mt-1">Protects your orders on Alibaba.com</p>
                        </div>
                    </div>
                </div>

                <!-- Supplier Features -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Supplier features</h3>
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-blue-600 font-medium group-hover:underline">Verified Supplier</span>
                            <i class="fas fa-info-circle text-gray-400 ml-1 text-xs"></i>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" class="mr-2 filter-checkbox">
                            <span class="text-blue-600 font-medium group-hover:underline">Verified <span class="bg-blue-600 text-white text-xs px-1 rounded">PRO</span> Supplier</span>
                            <i class="fas fa-info-circle text-gray-400 ml-1 text-xs"></i>
                        </label>
                    </div>
                </div>

                <!-- Merge Results -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Merge results</h3>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="mr-2 mt-1 filter-checkbox">
                        <div>
                            <span class="font-medium text-gray-900 block">Merge by supplier</span>
                            <span class="text-sm text-gray-600">Only the most relevant item from each supplier will be shown</span>
                        </div>
                    </label>
                </div>

                <!-- Store Reviews -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Store reviews</h3>
                    <p class="text-sm text-gray-600 mb-3">Based on a 5-star rating system</p>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" name="rating" class="mr-2 filter-radio">
                            <span class="text-gray-700">4.0 & up</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" name="rating" class="mr-2 filter-radio">
                            <span class="text-gray-700">4.5 & up</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" name="rating" class="mr-2 filter-radio">
                            <span class="text-gray-700">5.0</span>
                        </label>
                    </div>
                </div>

                <!-- Product Features -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-gray-900 mb-4">Product features</h3>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                        <input type="checkbox" class="mr-2 filter-checkbox">
                        <span class="text-gray-700">Paid samples</span>
                    </label>
                </div>

                <!-- Clear Filters Button -->
                <button class="clear-filters-btn w-full bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-2 px-4 rounded-lg transition-colors">
                    Clear all filters
                </button>
            `;
        }

        // Reinitialize filter listeners
        initializeFilterListeners();
    }

    // ========================================
    // Initialize Product Card Listeners
    // ========================================
    function initializeProductCardListeners() {
        // Wishlist
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('.fa-heart');

                if (icon.classList.contains('far')) {
                    icon.classList.remove('far', 'text-gray-500');
                    icon.classList.add('fas', 'text-red-500');
                    this.classList.add('bg-red-50');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far', 'text-gray-500');
                    this.classList.remove('bg-red-50');
                }
            });
        });
    }

    // ========================================
    // Initialize Filter Listeners
    // ========================================
    function initializeFilterListeners() {
        document.querySelectorAll('.filter-checkbox, .filter-radio').forEach(input => {
            input.addEventListener('change', function() {
                const container = document.getElementById('products-container');
                container.style.opacity = '0.5';

                setTimeout(() => {
                    container.style.opacity = '1';
                    console.log('Filter applied:', this.checked);
                }, 300);
            });
        });

        // Clear filters button
        const clearBtn = document.querySelector('.clear-filters-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                document.querySelectorAll('.filter-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.filter-radio').forEach(rb => rb.checked = false);

                const container = document.getElementById('products-container');
                container.style.opacity = '0.5';

                setTimeout(() => {
                    container.style.opacity = '1';
                    console.log('All filters cleared');
                }, 300);
            });
        }
    }

    // ========================================
    // Sort Dropdown
    // ========================================
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
                const sortText = this.textContent.trim();
                sortBtn.querySelector('span').textContent = 'Sort by ' + sortText;
                sortDropdown.classList.add('hidden');

                const container = document.getElementById('products-container');
                container.style.opacity = '0.5';

                setTimeout(() => {
                    container.style.opacity = '1';
                }, 300);
            });
        });

        document.addEventListener('click', function() {
            sortDropdown.classList.add('hidden');
        });
    }

    // ========================================
    // View Toggle (Grid/List)
    // ========================================
    const viewToggles = document.querySelectorAll('.view-toggle');

    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            viewToggles.forEach(t => {
                t.classList.remove('active', 'bg-gray-200');
            });

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

    // ========================================
    // Scroll to Top Button
    // ========================================
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
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ========================================
    // Mobile Messenger Button
    // ========================================
    const messengerBtn = document.getElementById('messenger-btn');

    if (messengerBtn) {
        messengerBtn.addEventListener('click', function() {
            alert('Opening messenger...');
        });
    }

    // Initialize filter listeners on page load
    initializeFilterListeners();

    // ========================================
    // LOAD INITIAL PRODUCTS ON PAGE LOAD
    // ========================================

    // Check if container exists
    if (!productsContainer) {
        console.error('ERROR: Products container not found! Make sure element with id="products-container" exists.');
        return;
    }

    // Load products tab by default when page loads
    console.log('Loading initial products...');
    setTimeout(() => {
        const data = DUMMY_DATA.products;
        console.log('Initial data loaded:', data.length, 'products');
        renderProducts(data, 'products');
        updateResultsCount(data.length, 'products');
    }, 100);

    console.log('✅ Product page JavaScript initialized successfully!');
});
