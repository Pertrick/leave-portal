import type { ToastOptions } from 'vue-toastification';

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $toast: {
            success(message: string, options?: ToastOptions): void;
            error(message: string, options?: ToastOptions): void;
            warning(message: string, options?: ToastOptions): void;
            info(message: string, options?: ToastOptions): void;
        };
    }
} 