/**
 * Global Dashboard Switch Utility
 * Handles switching between different admin dashboards
 */

class DashboardSwitch {
    constructor() {
        this.currentId = null;
        this.currentType = null;
        this.modalId = 'globalSwitchModal';
        this.init();
    }

    init() {
        // Create modal if it doesn't exist
        if (!document.getElementById(this.modalId)) {
            this.createModal();
        }
    }

    createModal() {
        const modal = document.createElement('div');
        modal.id = this.modalId;
        modal.className = 'hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-sign-in-alt text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900 text-center mb-2" id="switchModalTitle">Switch Dashboard</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 text-center mb-4" id="switchModalMessage">
                            You are about to login as <strong id="switchModalUserName"></strong> to their <strong id="switchModalDashboardType"></strong>.
                        </p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs text-yellow-700">
                                        <strong>Warning:</strong> This will open a new tab and log you in as this user. You will remain logged in as Admin in this tab.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 px-4 py-3">
                        <button onclick="dashboardSwitch.close()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button onclick="dashboardSwitch.confirm()" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            <i class="fas fa-sign-in-alt mr-1"></i> Proceed
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.close();
            }
        });
    }

    /**
     * Open the switch modal
     * @param {number} id - The ID of the admin/user to switch to
     * @param {string} userName - The name of the user
     * @param {string} dashboardType - Type of dashboard (e.g., 'Regional Admin', 'Country Admin')
     * @param {string} switchUrl - The URL endpoint to call for switching
     */
    open(id, userName, dashboardType, switchUrl) {
        this.currentId = id;
        this.currentType = dashboardType;
        this.switchUrl = switchUrl;

        document.getElementById('switchModalUserName').textContent = userName;
        document.getElementById('switchModalDashboardType').textContent = dashboardType + ' Dashboard';
        document.getElementById('switchModalTitle').textContent = 'Switch to ' + dashboardType + ' Dashboard';
        document.getElementById(this.modalId).classList.remove('hidden');
    }

    close() {
        document.getElementById(this.modalId).classList.add('hidden');
        this.currentId = null;
        this.currentType = null;
        this.switchUrl = null;
    }

    confirm() {
        if (!this.currentId || !this.switchUrl) return;

        // Show loading state
        const modal = document.getElementById(this.modalId);
        const modalContent = modal.querySelector('.relative');
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                <p class="text-gray-700 font-medium">Preparing ${this.currentType} Dashboard...</p>
            </div>
        `;

        // Make AJAX request
        fetch(this.switchUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open in new tab
                window.open(data.login_url, '_blank');

                // Close modal
                this.close();

                // Reset modal content
                this.init();

                // Show success notification
                this.showNotification(data.message, 'success');
            } else {
                throw new Error(data.message || 'Failed to switch dashboard');
            }
        })
        .catch(error => {
            this.close();
            this.init();
            this.showNotification(error.message, 'error');
        });
    }

    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.content : '';
    }

    showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-50' : 'bg-red-50';
        const borderColor = type === 'success' ? 'border-green-200' : 'border-red-200';
        const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${bgColor} border ${borderColor} ${textColor} px-4 py-3 rounded-lg shadow-lg z-50`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas ${icon}"></i>
                <span class="font-medium">${message}</span>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, type === 'success' ? 3000 : 5000);
    }
}

// Initialize global instance
const dashboardSwitch = new DashboardSwitch();

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardSwitch;
}
