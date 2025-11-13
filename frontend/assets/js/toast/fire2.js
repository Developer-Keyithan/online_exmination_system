
// Toast configuration
export const toastConfig = {
    success: {
        title: 'Success!',
        message: 'Your action was completed successfully.',
        icon: 'fa-check-circle'
    },
    error: {
        title: 'Error!',
        message: 'Something went wrong. Please try again.',
        icon: 'fa-times-circle'
    },
    info: {
        title: 'Information',
        message: 'Here is some important information for you.',
        icon: 'fa-info-circle'
    },
    warning: {
        title: 'Warning!',
        message: 'This action requires your attention.',
        icon: 'fa-exclamation-triangle'
    },
    dark: {
        title: 'Dark Theme',
        message: 'This is a toast with dark styling.',
        icon: 'fa-moon'
    },
    light: {
        title: 'Light Theme',
        message: 'This is a toast with light styling.',
        icon: 'fa-sun'
    },
    topCenter: {
        title: 'Top Center',
        message: 'This toast appears at the top center.',
        icon: 'fa-arrow-up'
    },
    bottomCenter: {
        title: 'Bottom Center',
        message: 'This toast appears at the bottom center.',
        icon: 'fa-arrow-down'
    }
};

// Track active toasts by position
let toastStacks = {
    'top-right': [],
    'top-left': [],
    'top-center': [],
    'bottom-right': [],
    'bottom-left': [],
    'bottom-center': [],
    'right-center': [],
    'left-center': []
};
let toastCounter = 0;

// Get appropriate icon for toast type
function getIcon(type) {
    return `<i class="fas ${toastConfig[type].icon}"></i>`;
}

// Update background states for toasts in a specific position stack
function updateToastStack(position) {
    const stack = toastStacks[position];
    
    // Sort toasts by creation time (newest first)
    stack.sort((a, b) => b.id - a.id);

    // Update each toast's visual state based on its position in stack
    stack.forEach((toast, index) => {
        const element = toast.element;

        // Remove all background classes
        element.classList.remove('background', 'far-background');

        // Apply appropriate state based on position in stack
        if (index === 0) {
            // Front toast - full visibility and normal position
            element.style.zIndex = 1000 + index;
            element.style.opacity = '1';
            element.style.scale = '1';
        } else if (index === 1) {
            // First background toast
            element.classList.add('background');
            element.style.scale = '0.9';
            element.style.zIndex = 1000 - index;
        } else {
            // Further background toasts
            element.classList.add('far-background');
            element.style.scale = '0.8';
            element.style.zIndex = 1000 - index;
        }
    });
}

// Show toast function with position-based stack management
export function toast2(type, title, message, position = 'top-right', toastTime = 5) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-card toast-${type} toast-${position}`;
    toast.dataset.toastId = ++toastCounter;

    // Set toast content
    toast.innerHTML = `
        <div class="toast-icon">
            ${getIcon(type)}
        </div>
        <div class="flex flex-col gap-1 flex-grow">
            <h1 class="toast-title">${title}</h1>
            <p>${message}</p>
        </div>
        <div class="toast-close">
            <i class="fas fa-times"></i>
        </div>
        <div class="toast-progress">
            <div class="toast-progress-bar"></div>
        </div>
    `;

    // Add to container
    document.body.appendChild(toast);

    // Add to position-specific stack
    const toastData = {
        id: toastCounter,
        element: toast,
        type: type,
        position: position,
        createdAt: Date.now()
    };
    
    toastStacks[position].push(toastData);

    // Update stack visual states for this position only
    updateToastStack(position);

    // Animate in
    setTimeout(() => toast.classList.add('show'), 10);

    // Set up auto dismiss
    const dismissTimeout = setTimeout(() => dismissToast(toastData), toastTime * 1000);

    // Set up progress bar animation
    setTimeout(() => {
        const progressBar = toast.querySelector('.toast-progress-bar');
        if (progressBar) {
            progressBar.style.transform = 'scaleX(0)';
            progressBar.style.transition = `transform ${toastTime}s linear`;
        }
    }, 10);

    // Set up close button
    toast.querySelector('.toast-close').addEventListener('click', () => {
        clearTimeout(dismissTimeout);
        dismissToast(toastData);
    });

    return toastData;
}

// Dismiss toast function
function dismissToast(toastData) {
    const toast = toastData.element;
    const position = toastData.position;

    // Remove show class to trigger exit animation
    toast.classList.remove('show');

    // Remove from position-specific stack
    const stack = toastStacks[position];
    const index = stack.findIndex(t => t.id === toastData.id);
    if (index !== -1) {
        stack.splice(index, 1);
    }

    // Update remaining toasts in this position after animation
    setTimeout(() => {
        updateToastStack(position);

        // Remove from DOM
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Clear all toasts
export function clearAllToasts() {
    // Clear all position stacks
    Object.keys(toastStacks).forEach(position => {
        const stack = [...toastStacks[position]];
        stack.forEach(toast => dismissToast(toast));
    });
}

// Clear toasts from specific position
export function clearPositionToasts(position) {
    const stack = [...toastStacks[position]];
    stack.forEach(toast => dismissToast(toast));
}