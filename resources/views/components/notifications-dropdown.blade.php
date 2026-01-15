<div class="relative">
    <button id="notifications-btn" class="relative p-1.5 sm:p-2 hover:bg-gray-100 rounded-lg transition-colors"
        title="Notifications">
        <i class="far fa-bell text-base sm:text-lg lg:text-xl text-gray-600"></i>
        <span
            class="absolute top-0 right-0 bg-[#ff0808] text-white text-[9px] sm:text-xs w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center font-bold">12</span>
    </button>

    <!-- Notifications Dropdown -->
    <div id="notifications-dropdown"
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[32rem] overflow-hidden">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
            <button class="text-xs text-[#ff0808] hover:text-red-700 font-medium">Mark all as read</button>
        </div>

        <!-- Notifications List -->
        <div class="overflow-y-auto max-h-96">
            <!-- New Order -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 bg-blue-50">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">New Order Received</p>
                    <p class="text-xs text-gray-600 mt-0.5">Order #12345 has been placed by John Doe</p>
                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                </div>
                <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></span>
            </a>

            <!-- Product Review -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 bg-green-50">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">New Product Review</p>
                    <p class="text-xs text-gray-600 mt-0.5">Sarah rated "Wireless Headphones" 5 stars</p>
                    <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                </div>
                <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></span>
            </a>

            <!-- Payment Received -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 bg-purple-50">
                <div class="flex-shrink-0 w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Payment Received</p>
                    <p class="text-xs text-gray-600 mt-0.5">$250.00 payment confirmed for Order #12340</p>
                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                </div>
                <span class="flex-shrink-0 w-2 h-2 bg-purple-500 rounded-full mt-2"></span>
            </a>

            <!-- Low Stock Alert -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                <div class="flex-shrink-0 w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Low Stock Alert</p>
                    <p class="text-xs text-gray-600 mt-0.5">"Nike Air Max" has only 5 units remaining</p>
                    <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                </div>
            </a>

            <!-- Product Approved -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                <div class="flex-shrink-0 w-10 h-10 bg-teal-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Product Approved</p>
                    <p class="text-xs text-gray-600 mt-0.5">Your product "Smart Watch Pro" has been approved</p>
                    <p class="text-xs text-gray-500 mt-1">5 hours ago</p>
                </div>
            </a>

            <!-- Shipping Update -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Order Shipped</p>
                    <p class="text-xs text-gray-600 mt-0.5">Order #12338 is on its way to the customer</p>
                    <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                </div>
            </a>

            <!-- Refund Request -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-undo text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Refund Request</p>
                    <p class="text-xs text-gray-600 mt-0.5">Customer requested refund for Order #12335</p>
                    <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                </div>
            </a>

            <!-- New Follower -->
            <a href="#"
                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                <div class="flex-shrink-0 w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">New Store Follower</p>
                    <p class="text-xs text-gray-600 mt-0.5">Mike Johnson started following your store</p>
                    <p class="text-xs text-gray-500 mt-1">2 days ago</p>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <a href="#" class="text-xs text-[#ff0808] hover:text-red-700 font-medium text-center block">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationsBtn = document.getElementById('notifications-btn');
        const notificationsDropdown = document.getElementById('notifications-dropdown');

        if (notificationsBtn && notificationsDropdown) {
            notificationsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationsDropdown.classList.toggle('hidden');

                // Close other dropdowns
                const alertsDropdown = document.getElementById('alerts-dropdown');
                if (alertsDropdown) alertsDropdown.classList.add('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationsBtn.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                    notificationsDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
