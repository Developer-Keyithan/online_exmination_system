// PHP API Content Loader
const PHPAPILoader = {
    config: {
        baseUrl: 'model/',
        timeout: 10000,
        retryAttempts: 3,
        defaultHeaders: {
            'Content-Type': 'application/json',
        }
    },

    configure(newConfig) {
        this.config = { ...this.config, ...newConfig };
    },

    async fetchContent(endpoint, data = null, method = null) {
        if (!endpoint || typeof endpoint !== 'string') {
            throw new Error('Endpoint must be a valid string');
        }

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.timeout);

        let attempts = 0;

        while (attempts < this.config.retryAttempts) {
            try {
                const url = this.config.baseUrl + endpoint;
                const requestMethod = method || (data ? 'POST' : 'GET');
                const options = {
                    method: requestMethod,
                    headers: this.config.defaultHeaders,
                    signal: controller.signal
                };

                if (data && (requestMethod === 'POST' || requestMethod === 'PUT' || requestMethod === 'PATCH')) {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(url, options);
                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();

            } catch (error) {
                attempts++;
                console.warn(`Attempt ${attempts} failed:`, error.message);

                if (attempts >= this.config.retryAttempts) {
                    clearTimeout(timeoutId);
                    throw error;
                }
            }
        }
    },

    renderError(message, error = null) {
        return `
            <div class="text-center p-6">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-3"></i>
                <h4 class="font-semibold text-lg text-red-700 mb-2">API Error</h4>
                <p class="text-red-600 mb-2">${message}</p>
                ${error ? `<p class="text-sm text-gray-600">${error.message}</p>` : ''}
                <button class="mt-4 popover-button popover-button-primary" onclick="location.reload()">
                    <i class="fas fa-redo mr-1"></i>
                    Retry
                </button>
            </div>
        `;
    }
};

// Simplified Popover system with automatic API fetching for any content type
export const popup = {
    async show(options) {
        const overlay = document.createElement('div');
        overlay.className = 'popover-overlay';
        document.body.appendChild(overlay);

        const popover = document.createElement('div');
        const positionClass = options.type === 'content' ? 'top-center' : 'center';

        // Fixed responsive width classes - removed my-4 from all sizes
        const sizeClass = options.size === 'xs' ? 'max-w-xs w-[calc(100%-2rem)] md:w-full' :
            options.size === 'sm' ? 'max-w-sm w-[calc(100%-2rem)] md:w-full' :
                options.size === 'md' ? 'max-w-md w-[calc(100%-2rem)] md:w-full' :
                    options.size === 'lg' ? 'max-w-lg w-[calc(100%-2rem)] md:w-full' :
                        options.size === 'xl' ? 'max-w-xl w-[calc(100%-2rem)] md:w-full' :
                            options.size === 'xxl' ? 'max-w-2xl w-[calc(100%-2rem)] md:w-full' :
                                options.size === 'full' ? 'max-w-full w-[calc(100%-2rem)] md:w-[calc(100%-3rem)]' :
                                    'max-w-md w-[calc(100%-2rem)] md:w-full';

        popover.className = `popover-content ${positionClass} ${sizeClass}`;

        let contentHtml = options.content || '';

        // Handle automatic API content loading
        if (options.apiConfig) {
            contentHtml = `
                <div class="popover-loading">
                    <div class="loading-spinner"></div>
                    <p class="mt-3 text-gray-600">Loading content...</p>
                </div>
            `;
        } else if (typeof options.content === 'function') {
            contentHtml = `
                <div class="popover-loading">
                    <div class="loading-spinner"></div>
                    <p class="mt-3 text-gray-600">Loading content...</p>
                </div>
            `;
        }

        let footerHtml = '';

        switch (options.type) {
            case 'info':
                footerHtml = `
                    <button 
                        class="popover-button"
                        style="background:${options.confirmBg || '#3498db'};color:${options.confirmColor || '#fff'}"
                        data-action="ok">
                        ${options.confirmText || 'OK'}
                    </button>
                `;
                break;

            case 'confirm':
                footerHtml = `
                    <button 
                        class="popover-button"
                        style="background:${options.confirmBg || '#4CAF50'};color:${options.confirmColor || '#fff'}"
                        data-action="confirm">
                        ${options.confirmText || 'Confirm'}
                    </button>
                    <button 
                        class="popover-button"
                        style="background:${options.cancelBg || '#f44336'};color:${options.cancelColor || '#fff'}"
                        data-action="cancel">
                        ${options.cancelText || 'Cancel'}
                    </button>
                `;
                break;

            case 'content':
                if (Array.isArray(options.buttons)) {
                    footerHtml = options.buttons.map((btn, index) => `
                        <button 
                            class="popover-button ${btn.class || ''}"
                            style="background:${btn.background || '#3498db'};color:${btn.color || '#fff'}"
                            data-index="${index}">
                            ${btn.text}
                        </button>
                    `).join('');
                } else {
                    footerHtml = options.buttons || '';
                }
                break;

            case 'success':
                footerHtml = `
                    <button 
                        class="popover-button"
                        style="background:${options.confirmBg || '#3498db'};color:${options.confirmColor || '#fff'}"
                        data-action="ok">
                        ${options.confirmText || 'OK'}
                    </button>
                `;
                break;
        }


        // Fixed button position - use proper Tailwind classes
        const buttonPositionClass = options.btnPosition === 'start' ? 'flex-start' :
            options.btnPosition === 'end' ? 'flex-end' :
                options.btnPosition === 'between' ? 'space-between' :
                    'center';

        popover.innerHTML = `
            <div class="popover-header">
                <h3 class="popover-title" style="color:${options.titleColor || '#000'}">${options.title}</h3>
                <button class="popover-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="popover-body" style="color:${options.contentColor || '#000'}">
                ${contentHtml}
            </div>
            <div class="popover-footer" style="justify-content:${buttonPositionClass}">
                ${footerHtml}
            </div>
        `;


        document.body.appendChild(popover);

        setTimeout(() => {
            overlay.classList.add('show');
            popover.classList.add('show');
        }, 10);

        // Handle automatic API content loading
        if (options.apiConfig) {
            try {
                const loadedContent = await this.fetchAPIContent(options.apiConfig);
                const body = popover.querySelector('.popover-body');
                body.innerHTML = loadedContent;
            } catch (error) {
                const body = popover.querySelector('.popover-body');
                body.innerHTML = this.renderError(error.message);
            }
        } else if (typeof options.content === 'function') {
            try {
                const loadedContent = await options.content();
                const body = popover.querySelector('.popover-body');
                body.innerHTML = loadedContent;
            } catch (error) {
                const body = popover.querySelector('.popover-body');
                body.innerHTML = this.renderError(error.message);
            }
        }

        const closePopover = () => {
            overlay.classList.remove('show');
            popover.classList.remove('show');

            setTimeout(() => {
                if (document.body.contains(overlay)) document.body.removeChild(overlay);
                if (document.body.contains(popover)) document.body.removeChild(popover);
            }, 300);
        };

        popover.querySelector('.popover-close').addEventListener('click', closePopover);

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closePopover();
                if (options.onCancel) options.onCancel();
            }
        });

        popover.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', () => {
                const action = button.getAttribute('data-action');

                if (action === 'ok' || action === 'confirm') {
                    if (options.onConfirm) options.onConfirm();
                } else if (action === 'cancel') {
                    if (options.onCancel) options.onCancel();
                }

                closePopover();
            });
        });

        popover.querySelectorAll('.popover-footer button').forEach((btn, idx) => {
            btn.addEventListener('click', () => {
                const button = options.buttons && options.buttons[idx];
                if (button && button.onClick) button.onClick(popover);
                else closePopover();
            });
        });

        const handleKeydown = (e) => {
            if (e.key === 'Escape') {
                if (options.onCancel) options.onCancel();
                closePopover();
                document.removeEventListener('keydown', handleKeydown);
            } else if (e.key === 'Enter') {
                const primaryButton = popover.querySelector('[data-action="ok"], [data-action="confirm"]');
                if (primaryButton) {
                    primaryButton.click();
                    document.removeEventListener('keydown', handleKeydown);
                }
            }
        };

        document.addEventListener('keydown', handleKeydown);

        let isDragging = false;
        let dragOffset = { x: 0, y: 0 };

        const header = popover.querySelector('.popover-header');

        header.addEventListener('mousedown', (e) => {
            isDragging = true;
            popover.classList.add('dragging');
            popover.style.transition = 'none'; // Disable transition during dragging
            const rect = popover.getBoundingClientRect();
            dragOffset.x = e.clientX - rect.left;
            dragOffset.y = e.clientY - rect.top;
            e.preventDefault();
        });

        const handleMouseMove = (e) => {
            if (!isDragging) return;
            popover.style.left = `${e.clientX - dragOffset.x}px`;
            popover.style.top = `${e.clientY - dragOffset.y}px`;
            popover.style.transform = 'none';
        };

        const handleMouseUp = () => {
            isDragging = false;
            popover.classList.remove('dragging');
            popover.style.transition = ''; // Re-enable transition after dragging
        };

        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);

        const originalClose = closePopover;
        const enhancedClose = () => {
            document.removeEventListener('keydown', handleKeydown);
            document.removeEventListener('mousemove', handleMouseMove);
            document.removeEventListener('mouseup', handleMouseUp);
            originalClose();
        };

        return {
            close: enhancedClose,
            element: popover,
            updateContent: (newContent) => {
                const body = popover.querySelector('.popover-body');
                body.innerHTML = newContent;
            },
            reloadContent: async () => {
                if (options.apiConfig) {
                    try {
                        const body = popover.querySelector('.popover-body');
                        body.innerHTML = `
                            <div class="popover-loading">
                                <div class="loading-spinner"></div>
                                <p class="mt-3 text-gray-600">Reloading...</p>
                            </div>
                        `;
                        const loadedContent = await this.fetchAPIContent(options.apiConfig);
                        body.innerHTML = loadedContent;
                    } catch (error) {
                        const body = popover.querySelector('.popover-body');
                        body.innerHTML = this.renderError(error.message);
                    }
                }
            }
        };
    },

    async fetchAPIContent(apiConfig) {
        try {
            const { endpoint, method = 'GET', data = null } = apiConfig;
            const result = await PHPAPILoader.fetchContent(endpoint, data, method);

            // Handle different response formats
            if (result.html) return result.html;
            if (result.content) return result.content;
            if (result.data) {
                // If it's structured data, create a simple display
                if (typeof result.data === 'object') {
                    return this.renderObjectAsHTML(result.data);
                }
                return String(result.data);
            }

            return PHPAPILoader.renderError('No displayable content received from API');
        } catch (error) {
            return this.renderError(`Failed to load content: ${error.message}`);
        }
    },

    renderObjectAsHTML(data) {
        if (Array.isArray(data)) {
            // Render as table for arrays
            if (data.length === 0) return '<p class="text-gray-500 text-center p-4">No data available</p>';

            const headers = Object.keys(data[0]);
            return `
                <div class="overflow-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                ${headers.map(header =>
                `<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">${header}</th>`
            ).join('')}
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.map(row => `
                                <tr>
                                    ${headers.map(header =>
                `<td class="px-4 py-2 text-sm text-gray-900">${row[header]}</td>`
            ).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            // Render as key-value pairs for objects
            return `
                <div class="space-y-2">
                    ${Object.entries(data).map(([key, value]) => `
                        <div class="flex justify-between border-b pb-1">
                            <span class="font-medium text-gray-700">${key}:</span>
                            <span class="text-gray-900">${value}</span>
                        </div>
                    `).join('')}
                </div>
            `;
        }
    },

    error({ title, content, options = { confirm: { text: 'OK', background: '#3498db', color: '#fff', onConfirm: null }, size: 'md', buttonPosition: 'center' } }) {
        return this.show({
            type: 'info',
            title,
            titleColor: '#f44336',
            content: `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-3"></i>
                    <p class="text-red-600">${content}</p>
                </div>
            `,
            confirmText: options.confirm?.text || 'OK',
            onConfirm: options.confirm?.onConfirm || null,
            confirmBg: options.confirm?.background || '#3498db',
            confirmColor: options.confirm?.color || '#fff',
            size: options.size,
            btnPosition: options.buttonPosition
        });
    },

    renderError(message) {
        return `
            <div class="text-center p-6">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-3"></i>
                <h4 class="font-semibold text-lg text-red-700 mb-2">Error</h4>
                <p class="text-red-600">${message}</p>
            </div>
        `;
    },

    info({ title, content = { text, color: '#3498db' }, titleColor = '#3498db', size = 'md', options = {
        confirm: { text: 'OK', background: '#3498db', color: '#fff', onConfirm: null },
        buttonPosition: 'center'
    } }) {
        return this.show({
            type: 'info',
            title: title || 'Information',
            titleColor: titleColor || '#3498db',
            content: content.text || 'This is an info message',
            confirmText: options.confirm?.text || 'OK',
            onConfirm: options.confirm?.onConfirm || null,
            confirmBg: options.confirm?.background || '#3498db',
            confirmColor: options.confirm?.color || '#fff',
            btnPosition: options.buttonPosition || 'center',
            size,
            btnPosition: options.buttonPosition
        });
    },

    confirm({ title, content = { text, color: '#f59e0b' }, size = 'md', options = {
        confirm: { text: 'Ok, confirm', background: '#4CAF50', color: '#fff', onConfirm: null },
        cancel: { text: 'No, Cancel', background: '#f44336', color: '#fff', onCancel: null },
        buttonPosition: 'center'
    } }) {
        return this.show({
            type: 'confirm',
            title: title || 'Confirmation',
            titleColor: '#f59e0b',
            content: content.text || 'This is a confirm message',
            contentColor: content.color || '#000',
            confirmText: options.confirm?.text || 'Ok, Confirm',
            cancelText: options.cancel?.text || 'No, Cancel',
            onConfirm: options.confirm?.onConfirm || null,
            onCancel: options.cancel?.onCancel || null,
            size,
            btnPosition: options.buttonPosition
        });
    },

    content({ title, content, buttons = [], apiConfig = null, size = 'md', buttonPosition = 'center' }) {
        return this.show({
            type: 'content',
            title: title || 'Content Popover',
            content,
            buttons,
            apiConfig,
            size,
            btnPosition: buttonPosition
        });
    },

    success({ title, content = { text, color: '#4CAF50' }, options = { confirmText: 'OK', onConfirm: null, buttonPosition: 'center' }, size = 'md' }) {
        return this.show({
            type: 'success',
            title: title || 'Success!',
            titleColor: '#4CAF50',
            content: content.text || 'This is a success message',
            contentColor: content.color || '#004c02' || '#4CAF50',
            confirmText: options.confirm?.text || 'OK',
            onConfirm: options.confirm?.onConfirm || null,
            size,
            btnPosition: options.buttonPosition
        });
    },

    // Universal API content loader
    apiContent({ title, endpoint, method = 'GET', data = null, buttons = [], size = 'lg', buttonPosition = 'center' }) {
        return this.content({
            title,
            apiConfig: { endpoint, method, data },
            buttons,
            size,
            buttonPosition
        });
    },

    // Cleanup method to remove all popovers
    destroyAll() {
        document.querySelectorAll('.popover-overlay, .popover-content').forEach(el => {
            if (document.body.contains(el)) {
                document.body.removeChild(el);
            }
        });
    }
};