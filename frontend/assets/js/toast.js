import { toast } from "./toast/fire.js";
import { toast2 } from "./toast/fire2.js";
import { popup } from "./toast/popover.js";


export const Toast = {
    fire({ type, title, msg, position = 'top-right', duration = 5 }) {
        toast(type, title, msg, position, duration);
    },
    fire2({ type, title, msg, position = 'top-right', duration = 5 }) {
        toast2(type, title, msg, position, duration);
    },
    popover({ type, title, content, contentColor = null, options = {}, buttons = [], apiConfig = null, size = 'md', buttonPosition = 'center' }) {
        switch (type) {
            case 'success':
                return popup.success({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition }, buttons, apiConfig, size });
            case 'confirm':
                return popup.confirm({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition }, size });
            case 'info':
                return popup.info({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition }, size });
            case 'content':
                return popup.content({ title, content, buttons, apiConfig, size, buttonPosition });
            case 'apiContent':
                return popup.apiContent({ title, endpoint: apiConfig?.endpoint, method: apiConfig?.method, data: apiConfig?.data, buttons, size, buttonPosition });
            case 'error':
                return popup.error({ title, content, options: { ...options, buttonPosition }, size });
            default:
                return popup.success({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition }, size });
        }
    },
};

window.Toast = Toast;