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
    popover({ type, title, content, contentColor = null, options = {}, buttons = [], apiConfig = null, size = 'md', buttonPosition = 'center', buttonWidth = 'fit', buttonContainerClass = '', buttonContainerStyles = '' }) {
        switch (type) {
            case 'success':
                return popup.success({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles }, buttons, apiConfig, size });
            case 'confirm':
                return popup.confirm({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles }, size });
            case 'info':
                return popup.info({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles }, size });
            case 'content':
                return popup.content({ title, content, buttons, apiConfig, size, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles });
            case 'apiContent':
                return popup.apiContent({ title, endpoint: apiConfig?.endpoint, method: apiConfig?.method, data: apiConfig?.data, buttons, size, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles });
            case 'error':
                return popup.error({ title, content, options: { ...options, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles }, size });
            case 'close':
                return popup.destroyAll();
            default:
                return popup.success({ title, content: { text: content, color: contentColor }, options: { ...options, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles }, size });
        }
    },
};

window.Toast = Toast;