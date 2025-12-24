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
    popover({ type, title, titleColor, content, contentColor = null, options = {}, buttons = [], apiConfig = null, size = 'md', position, buttonPosition = 'center', buttonWidth = 'fit', buttonContainerClass = '', buttonContainerStyles = '', backgroundColor = '#0003' }) {
        switch (type) {
            case 'success':
                return popup.success({ title, titleColor, content: { text: content, color: contentColor }, options: { ...options, position, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor }, buttons, apiConfig, size });
            case 'confirm':
                return popup.confirm({ title, titleColor, content: { text: content, color: contentColor }, buttons, options: { ...options, position, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor }, size });
            case 'info':
                return popup.info({ title, titleColor, content: { text: content, color: contentColor }, options: { ...options, position, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor }, size });
            case 'content':
                return popup.content({ title, titleColor, content, buttons, apiConfig, size, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor, position });
            case 'apiContent':
                return popup.apiContent({ title, titleColor, endpoint: apiConfig?.endpoint, method: apiConfig?.method, data: apiConfig?.data, buttons, size, position, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor });
            case 'error':
                return popup.error({ title, titleColor, content, options: { ...options, position, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor }, size });
            case 'close':
                return popup.destroyAll();
            default:
                return popup.success({ title, titleColor, content: { text: content, color: contentColor }, options: { ...options, position, buttonPosition, buttonWidth, buttonContainerClass, buttonContainerStyles, backgroundColor }, size });
        }
    },
};

window.Toast = Toast;