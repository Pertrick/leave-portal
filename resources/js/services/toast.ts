import { useToast } from 'vue-toastification';
import type { ToastOptions } from 'vue-toastification';

let toastInstance: any = null;

// Initialize toast instance
try {
    toastInstance = useToast();
} catch (error) {
    console.warn('Toast not available:', error);
}

const defaultOptions: ToastOptions = {
    position: 'top-right',
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false,
};

export const ToastService = {
    success(message: string, options: ToastOptions = {}) {
        try {
            if (toastInstance && typeof toastInstance.success === 'function') {
                toastInstance.success(message, {
                    ...defaultOptions,
                    timeout: 3000,
                    ...options,
                });
            } else {
                console.log(`[SUCCESS] ${message}`);
            }
        } catch (error) {
            console.error('Error showing success toast:', error);
            console.log(`[SUCCESS] ${message}`);
        }
    },

    error(message: string, options: ToastOptions = {}) {
        try {
            if (toastInstance && typeof toastInstance.error === 'function') {
                toastInstance.error(message, {
                    ...defaultOptions,
                    timeout: 5000,
                    ...options,
                });
            } else {
                console.error(`[ERROR] ${message}`);
            }
        } catch (error) {
            console.error('Error showing error toast:', error);
            console.error(`[ERROR] ${message}`);
        }
    },

    warning(message: string, options: ToastOptions = {}) {
        try {
            if (toastInstance && typeof toastInstance.warning === 'function') {
                toastInstance.warning(message, {
                    ...defaultOptions,
                    timeout: 4000,
                    ...options,
                });
            } else {
                console.warn(`[WARNING] ${message}`);
            }
        } catch (error) {
            console.error('Error showing warning toast:', error);
            console.warn(`[WARNING] ${message}`);
        }
    },

    info(message: string, options: ToastOptions = {}) {
        try {
            if (toastInstance && typeof toastInstance.info === 'function') {
                toastInstance.info(message, {
                    ...defaultOptions,
                    timeout: 3000,
                    ...options,
                });
            } else {
                console.info(`[INFO] ${message}`);
            }
        } catch (error) {
            console.error('Error showing info toast:', error);
            console.info(`[INFO] ${message}`);
        }
    },

    // Method to reinitialize toast instance
    initialize() {
        try {
            toastInstance = useToast();
        } catch (error) {
            console.warn('Failed to initialize toast:', error);
        }
    },
}; 