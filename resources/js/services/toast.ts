import { useToast } from 'vue-toastification';
import type { ToastOptions } from 'vue-toastification';

const toast = useToast();

export const ToastService = {
    success(message: string, options: ToastOptions = {}) {
        toast.success(message, {
            position: 'top-right',
            timeout: 3000,
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
            ...options,
        });
    },

    error(message: string, options: ToastOptions = {}) {
        toast.error(message, {
            position: 'top-right',
            timeout: 5000,
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
            ...options,
        });
    },

    warning(message: string, options: ToastOptions = {}) {
        toast.warning(message, {
            position: 'top-right',
            timeout: 4000,
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
            ...options,
        });
    },

    info(message: string, options: ToastOptions = {}) {
        toast.info(message, {
            position: 'top-right',
            timeout: 3000,
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
            ...options,
        });
    },
}; 