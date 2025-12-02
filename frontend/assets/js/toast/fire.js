// window.Toast = Toast;

// Toast configuration
const toastConfig = {
    success: {
        icon: 'fa-check-circle'
    },
    error: {
        icon: 'fa-times-circle'
    },
    info: {
        icon: 'fa-info-circle'
    },
    warning: {
        icon: 'fa-exclamation-triangle'
    },
    dark: {
        icon: 'fa-moon'
    },
    light: {
        icon: 'fa-sun'
    },
    topCenter: {
        icon: 'fa-arrow-up'
    },
    bottomCenter: {
        icon: 'fa-arrow-down'
    }
};

// Track active toasts by position
const activeToasts = {
    'top-right': [],
    'top-left': [],
    'bottom-right': [],
    'bottom-left': [],
    'top': [],
    'bottom': [],
    'left': [],
    'right': []
};

// Get appropriate icon for toast type
function getIcon(type) {
    return `<i class="fas ${toastConfig[type].icon}"></i>`;
}

// Calculate offset for stacking toasts
function calculateToastOffset(position, toast) {
    const toastHeight = toast.offsetHeight;
    const margin = 10; // Space between toasts
    const existingToasts = activeToasts[position];

    if (existingToasts.length === 0) {
        return 0;
    }

    // Calculate offset based on previous toasts
    return existingToasts.reduce((total, current) => {
        return total + current.element.offsetHeight + margin;
    }, 0);
}

// Apply position offset to toast
function applyPositionOffset(toast, position, offset) {
    const isVertical = position.includes('top') || position.includes('bottom');
    const isHorizontal = position.includes('left') || position.includes('right');

    if (isVertical && !position.includes('center')) {
        // For top/bottom positions, adjust top or bottom property
        if (position.includes('top')) {
            toast.style.top = `calc(1rem + ${offset}px)`;
        } else {
            toast.style.bottom = `calc(1rem + ${offset}px)`;
        }
    } else if (isHorizontal) {
        // For left/right positions, adjust top property
        toast.style.top = `calc(50% - ${toast.offsetHeight / 2}px + ${offset}px)`;
    } else if (position === 'top') {
        // For top center
        toast.style.top = `calc(1rem + ${offset}px)`;
    } else if (position === 'bottom') {
        // For bottom center
        toast.style.bottom = `calc(1rem + ${offset}px)`;
    }
}

// Adjust positions of remaining toasts after one is dismissed
function adjustToastPositions(position) {
    const toasts = activeToasts[position];

    toasts.forEach((toastObj, index) => {
        // Calculate new offset for this toast
        let newOffset = 0;
        for (let i = 0; i < index; i++) {
            newOffset += toasts[i].element.offsetHeight + 10; // 10px margin
        }

        // Apply new position
        applyPositionOffset(toastObj.element, position, newOffset);
        toastObj.offset = newOffset;
    });
}

// Show toast function with position-based animations
export function toast(type, title, message, position = 'top-right', toastTime = 5) {
    const toast = document.createElement('div');
    toast.className = `toast-card toast-${type} toast-${position}`;

    toast.innerHTML = `
        <div class="toast-icon">${getIcon(type)}</div>
        <div class="flex flex-col gap-1 flex-grow">
            <h1 class="toast-title">${title}</h1>
            <p>${message}</p>
        </div>
        <div class="toast-close"><i class="fas fa-times"></i></div>
        <div class="toast-progress"><div class="toast-progress-bar"></div></div>
    `;

    document.body.appendChild(toast);

    const offset = calculateToastOffset(position, toast);
    applyPositionOffset(toast, position, offset);
    activeToasts[position].push({ element: toast, offset: offset });

    setTimeout(() => toast.classList.add('show'), 10);

    const progressBar = toast.querySelector('.toast-progress-bar');
    progressBar.style.transform = 'scaleX(1)'; // start full
    progressBar.style.transformOrigin = 'left';

    let startTime = Date.now();
    let remaining = toastTime * 1000;

    // trigger animation
    setTimeout(() => {
        progressBar.style.transition = `transform ${remaining / 1000}s linear`;
        progressBar.style.transform = 'scaleX(0)'; // animate to 0
    }, 50);

    let dismissTimeout = setTimeout(() => dismissToast(toast, position), remaining);

    function pauseProgress() {
        clearTimeout(dismissTimeout);
        const elapsed = Date.now() - startTime;
        remaining -= elapsed;
        progressBar.style.transition = 'none';
        const scale = remaining / (toastTime * 1000);
        progressBar.style.transform = `scaleX(${scale})`;
    }

    function resumeProgress() {
        startTime = Date.now();
        progressBar.style.transition = `transform ${remaining / 1000}s linear`;
        progressBar.style.transform = 'scaleX(0)';
        dismissTimeout = setTimeout(() => dismissToast(toast, position), remaining);
    }

    toast.addEventListener('mouseenter', pauseProgress);
    toast.addEventListener('mouseleave', resumeProgress);

    toast.querySelector('.toast-close').addEventListener('click', () => {
        clearTimeout(dismissTimeout);
        dismissToast(toast, position);
    });

    return toast;
}


// Dismiss toast function with position-based animations
function dismissToast(toast, position) {
    // Remove show class to trigger exit animation
    toast.classList.remove('show');

    // Remove from active toasts
    const index = activeToasts[position].findIndex(t => t.element === toast);
    if (index !== -1) {
        activeToasts[position].splice(index, 1);
    }

    // Adjust positions of remaining toasts after animation completes
    setTimeout(() => {
        adjustToastPositions(position);
    }, 300);

    // Remove from DOM after animation
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}