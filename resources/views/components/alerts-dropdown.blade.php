<div class="relative">
    <button id="alerts-btn" class="relative p-1.5 sm:p-2 hover:bg-gray-100 rounded-lg transition-colors"
        title="System Alerts">
        <i class="fas fa-exclamation-triangle text-base sm:text-lg lg:text-xl text-orange-600"></i>
        <span
            class="absolute top-0 right-0 bg-orange-600 text-white text-[9px] sm:text-xs w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center font-bold">3</span>
    </button>

    <!-- Alerts Dropdown -->
    <div id="alerts-dropdown"
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[32rem] overflow-hidden">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-orange-50">
            <h3 class="text-sm font-bold text-gray-900">System Alerts</h3>
            <button class="text-xs text-orange-600 hover:text-orange-700 font-medium">Clear all</button>
        </div>

        <!-- Alerts List -->
        <div class="overflow-y-auto max-h-96">
            <!-- Critical Alert -->
            <div
                class="flex items-start gap-3 px-4 py-3 bg-red-50 border-b border-gray-100 border-l-4 border-l-red-500">
                <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-red-900">Critical</p>
                        <span class="px-2 py-0.5 bg-red-200 text-red-800 text-xs font-semibold rounded">URGENT</span>
                    </div>
                    <p class="text-xs text-gray-800 mt-1 font-medium">Server Response Time Degraded</p>
                    <p class="text-xs text-gray-600 mt-1">API response time increased to 5.2s (threshold: 2s)</p>
                    <p class="text-xs text-gray-500 mt-1.5">5 minutes ago</p>
                </div>
            </div>

            <!-- High Priority Alert -->
            <div
                class="flex items-start gap-3 px-4 py-3 bg-orange-50 border-b border-gray-100 border-l-4 border-l-orange-500">
                <div class="flex-shrink-0 w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-orange-900">High Priority</p>
                        <span class="px-2 py-0.5 bg-orange-200 text-orange-800 text-xs font-semibold rounded">HIGH</span>
                    </div>
                    <p class="text-xs text-gray-800 mt-1 font-medium">Database Connection Pool Exhausted</p>
                    <p class="text-xs text-gray-600 mt-1">95% of database connections are currently in use</p>
                    <p class="text-xs text-gray-500 mt-1.5">12 minutes ago</p>
                </div>
            </div>

            <!-- Warning Alert -->
            <div
                class="flex items-start gap-3 px-4 py-3 bg-yellow-50 border-b border-gray-100 border-l-4 border-l-yellow-500">
                <div class="flex-shrink-0 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-yellow-900">Warning</p>
                        <span
                            class="px-2 py-0.5 bg-yellow-200 text-yellow-800 text-xs font-semibold rounded">MEDIUM</span>
                    </div>
                    <p class="text-xs text-gray-800 mt-1 font-medium">Disk Space Running Low</p>
                    <p class="text-xs text-gray-600 mt-1">Server storage at 82% capacity (threshold: 80%)</p>
                    <p class="text-xs text-gray-500 mt-1.5">1 hour ago</p>
                </div>
            </div>

            <!-- Resolved Alerts -->
            <div class="px-4 py-2 bg-gray-50">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Resolved Today</p>
            </div>

            <!-- Resolved Alert 1 -->
            <div class="flex items-start gap-3 px-4 py-3 bg-green-50 border-b border-gray-100 opacity-75">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-green-900">Resolved</p>
                        <span class="px-2 py-0.5 bg-green-200 text-green-800 text-xs font-semibold rounded">FIXED</span>
                    </div>
                    <p class="text-xs text-gray-800 mt-1 font-medium">SSL Certificate Expiration</p>
                    <p class="text-xs text-gray-600 mt-1">Certificate renewed successfully for afrisellers.com</p>
                    <p class="text-xs text-gray-500 mt-1.5">3 hours ago</p>
                </div>
            </div>

            <!-- Resolved Alert 2 -->
            <div class="flex items-start gap-3 px-4 py-3 bg-green-50 border-b border-gray-100 opacity-75">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-green-900">Resolved</p>
                        <span class="px-2 py-0.5 bg-green-200 text-green-800 text-xs font-semibold rounded">FIXED</span>
                    </div>
                    <p class="text-xs text-gray-800 mt-1 font-medium">Email Service Outage</p>
                    <p class="text-xs text-gray-600 mt-1">SMTP service restored, all queued emails sent</p>
                    <p class="text-xs text-gray-500 mt-1.5">5 hours ago</p>
                </div>
            </div>

            <!-- System Info -->
            <div class="flex items-start gap-3 px-4 py-3 bg-blue-50 border-b border-gray-100 opacity-75">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-info-circle text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-blue-900">Info</p>
                        <span class="px-2 py-0.5 bg-blue-200 text-blue-800 text-xs font-semibold rounded">INFO</span>
                    </div>
                    <p class="text-xs text-gray-800 mt-1 font-medium">Scheduled Maintenance</p>
                    <p class="text-xs text-gray-600 mt-1">Database optimization scheduled for Dec 8, 2:00 AM</p>
                    <p class="text-xs text-gray-500 mt-1.5">1 day ago</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <a href="#" class="text-xs text-orange-600 hover:text-orange-700 font-medium text-center block">
                View System Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertsBtn = document.getElementById('alerts-btn');
        const alertsDropdown = document.getElementById('alerts-dropdown');

        if (alertsBtn && alertsDropdown) {
            alertsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                alertsDropdown.classList.toggle('hidden');

                // Close other dropdowns
                const notificationsDropdown = document.getElementById('notifications-dropdown');
                if (notificationsDropdown) notificationsDropdown.classList.add('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!alertsBtn.contains(e.target) && !alertsDropdown.contains(e.target)) {
                    alertsDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
