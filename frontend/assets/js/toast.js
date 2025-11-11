// Toast configuration
const toastIcons = {
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
    return `<i class="fas ${toastIcons[type].icon}"></i>`;
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
function showToast(type, title, message, position, toastTime) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-card toast-${type} toast-${position}`;

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

    // Calculate position offset for stacking
    const offset = calculateToastOffset(position, toast);
    applyPositionOffset(toast, position, offset);

    // Add to active toasts tracking
    const toastObj = { element: toast, offset: offset };
    activeToasts[position].push(toastObj);

    // Animate in
    setTimeout(() => toast.classList.add('show'), 10);

    // Set up progress bar animation
    const progressBar = toast.querySelector('.toast-progress-bar');
    let progressWidth = 100;
    let progressInterval;
    let remainingTime = toastTime * 1000;
    let startTime = Date.now();

    function startProgressBar() {
        progressBar.style.transition = `none`;
        progressBar.style.transform = `scaleX(${progressWidth / 100})`;

        setTimeout(() => {
            progressBar.style.transition = `transform ${remainingTime}ms linear`;
            progressBar.style.transform = 'scaleX(0)';
        }, 10);

        startTime = Date.now();
    }

    function pauseProgressBar() {
        const elapsed = Date.now() - startTime;
        remainingTime -= elapsed;
        progressWidth = (remainingTime / (toastTime * 1000)) * 100;

        // Remove transition to freeze current state
        progressBar.style.transition = 'none';
        progressBar.style.transform = `scaleX(${progressWidth / 100})`;
    }

    // Set up auto dismiss
    let dismissTimeout = setTimeout(() => dismissToast(toast, position), toastTime * 1000);

    // Start progress bar
    startProgressBar();

    // Set up mouse events
    toast.addEventListener('mouseenter', () => {
        clearTimeout(dismissTimeout);
        pauseProgressBar();
    });

    toast.addEventListener('mouseleave', () => {
        dismissTimeout = setTimeout(() => dismissToast(toast, position), remainingTime);
        startProgressBar();
    });

    // Set up close button
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

export const Toast = {
    popover(type, title, content, options = { confirmText, cancelText, onConfirm, onCancel, content, buttons: { text, onClick, color, background, rounded } }) {
        switch (type) {
            case 'info':
                return Popover.info(title, content, options)
            case 'success':
                return Popover.success(title, content, options)
            case 'content':
                return Popover.content(title, content, options)
            case 'confirm':
                return Popover.confirm(title, content, options)
            default:
                return Popover.info(title, content, options)
        }
    },
    fire({type, title, msg, position = 'top-right', duration = 5}) {
        showToast(type, title, msg, position, duration);
    }
}
window.Toast = Toast;



// // Toast configuration
// const toastIcons = {
//     success: {
//         icon: 'fa-check-circle'
//     },
//     error: {
//         icon: 'fa-times-circle'
//     },
//     info: {
//         icon: 'fa-info-circle'
//     },
//     warning: {
//         icon: 'fa-exclamation-triangle'
//     }
// };


// // Track active toasts by position
// const activeToasts = {
//     'top-right': [],
//     'top-left': [],
//     'bottom-right': [],
//     'bottom-left': [],
//     'top': [],
//     'bottom': [],
//     'left': [],
//     'right': []
// };

// // Get appropriate icon for toast type
// function getIcon(type) {
//     return `<i class="fas ${toastIcons[type].icon}"></i>`;
// }

// // Calculate offset for stacking toasts
// function calculateToastOffset(position, toast) {
//     const toastHeight = toast.offsetHeight;
//     const margin = 10; // Space between toasts
//     const existingToasts = activeToasts[position];

//     if (existingToasts.length === 0) {
//         return 0;
//     }

//     // Calculate offset based on previous toasts
//     return existingToasts.reduce((total, current) => {
//         return total + current.element.offsetHeight + margin;
//     }, 0);
// }

// // Apply position offset to toast
// function applyPositionOffset(toast, position, offset) {
//     const isVertical = position.includes('top') || position.includes('bottom');
//     const isHorizontal = position.includes('left') || position.includes('right');

//     if (isVertical && !position.includes('center')) {
//         // For top/bottom positions, adjust top or bottom property
//         if (position.includes('top')) {
//             toast.style.top = `calc(1rem + ${offset}px)`;
//         } else {
//             toast.style.bottom = `calc(1rem + ${offset}px)`;
//         }
//     } else if (isHorizontal) {
//         // For left/right positions, adjust top property
//         toast.style.top = `calc(50% - ${toast.offsetHeight / 2}px + ${offset}px)`;
//     } else if (position === 'top') {
//         // For top center
//         toast.style.top = `calc(1rem + ${offset}px)`;
//     } else if (position === 'bottom') {
//         // For bottom center
//         toast.style.bottom = `calc(1rem + ${offset}px)`;
//     }
// }

// // Adjust positions of remaining toasts after one is dismissed
// function adjustToastPositions(position) {
//     const toasts = activeToasts[position];

//     toasts.forEach((toastObj, index) => {
//         // Calculate new offset for this toast
//         let newOffset = 0;
//         for (let i = 0; i < index; i++) {
//             newOffset += toasts[i].element.offsetHeight + 10; // 10px margin
//         }

//         // Apply new position
//         applyPositionOffset(toastObj.element, position, newOffset);
//         toastObj.offset = newOffset;
//     });
// }

// // Show toast function with position-based animations
// function showToast(type, title, message, position = 'top-right', toastTime = 5) {
//     // Create toast element
//     const toast = document.createElement('div');
//     toast.className = `toast-card toast-${type} toast-${position}`;

//     // Set toast content
//     toast.innerHTML = `
//                 <div class="toast-icon">
//                     ${getIcon(type)}
//                 </div>
//                 <div class="flex flex-col gap-1 flex-grow">
//                     <h1 class="toast-title">${title}</h1>
//                     <p>${message}</p>
//                 </div>
//                 <div class="toast-close">
//                     <i class="fas fa-times"></i>
//                 </div>
//                 <div class="toast-progress">
//                     <div class="toast-progress-bar"></div>
//                 </div>
//             `;

//     // Add to container
//     document.body.appendChild(toast);

//     // Calculate position offset for stacking
//     const offset = calculateToastOffset(position, toast);
//     applyPositionOffset(toast, position, offset);

//     // Add to active toasts tracking
//     activeToasts[position].push({ element: toast, offset: offset });

//     // Animate in
//     setTimeout(() => toast.classList.add('show'), 10);

//     // Set up auto dismiss
//     const dismissTimeout = setTimeout(() => dismissToast(toast, position), toastTime * 1000);

//     // Set up progress bar animation
//     setTimeout(() => {
//         const progressBar = toast.querySelector('.toast-progress-bar');
//         if (progressBar) {
//             progressBar.style.transform = 'scaleX(0)';
//             progressBar.style.transition = `transform ${toastTime}s linear`;
//         }
//     }, 10);

//     // Set up close button
//     toast.querySelector('.toast-close').addEventListener('click', () => {
//         clearTimeout(dismissTimeout);
//         dismissToast(toast, position);
//     });

//     return toast;
// }

// // Dismiss toast function with position-based animations
// function dismissToast(toast, position) {
//     // Remove show class to trigger exit animation
//     toast.classList.remove('show');

//     // Remove from active toasts
//     const index = activeToasts[position].findIndex(t => t.element === toast);
//     if (index !== -1) {
//         activeToasts[position].splice(index, 1);
//     }

//     // Adjust positions of remaining toasts after animation completes
//     setTimeout(() => {
//         adjustToastPositions(position);
//     }, 300);

//     // Remove from DOM after animation
//     setTimeout(() => {
//         if (toast.parentNode) {
//             toast.parentNode.removeChild(toast);
//         }
//     }, 300);
// }

// export function Toast({ type, title, msg, position = 'top-right', toastTime = 5 } = {}) {
//     showToast(type, title, msg, position, toastTime);
// }