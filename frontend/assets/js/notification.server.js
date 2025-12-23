app.factory('NotificationService', ['$timeout', function ($timeout) {
    return {
        success: function (message) {
            this.showNotification(message, 'success');
        },

        error: function (message) {
            this.showNotification(message, 'error');
        },

        info: function (message) {
            this.showNotification(message, 'info');
        },

        warning: function (message) {
            this.showNotification(message, 'warning');
        },

        showNotification: function (message, type) {
            // Remove existing notifications
            const existing = document.querySelectorAll('.global-notification');
            existing.forEach(el => el.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `global-notification fixed top-8 right-4 z-[999999] px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full max-w-sm`;

            const typeClasses = {
                success: 'bg-green-600 text-white border-l-4 border-green-700',
                error: 'bg-red-600 text-white border-l-4 border-red-700',
                info: 'bg-blue-600 text-white border-l-4 border-blue-700',
                warning: 'bg-yellow-600 text-white border-l-4 border-yellow-700'
            };

            notification.className += ' ' + typeClasses[type];
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas text-lg ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            $timeout(() => {
                notification.classList.remove('translate-x-full');
                notification.classList.add('translate-x-0');
            }, 10);

            // Auto-remove after 5 seconds
            $timeout(() => {
                if (notification.parentElement) {
                    notification.classList.remove('translate-x-0');
                    notification.classList.add('translate-x-full');
                    $timeout(() => {
                        if (notification.parentElement) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        }
    };
}]);