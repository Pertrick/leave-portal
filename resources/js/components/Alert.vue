<template>
    <!-- No visible alert, only toast notification -->
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ToastService } from '@/services/toast';

interface Alert {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
}

const page = usePage();
const alert = ref<Alert | null>(page.props.alert as Alert | null);

const showToast = async (type: string, message: string) => {
    try {
        await nextTick();
        if (type === 'success') {
            ToastService.success(message);
        } else if (type === 'error') {
            ToastService.error(message);
        } else if (type === 'warning') {
            ToastService.warning(message);
        } else if (type === 'info') {
            ToastService.info(message);
        }
    } catch (error) {
        console.error('Error showing toast:', error);
        console.log(`[${type.toUpperCase()}] ${message}`);
    }
};

watch(() => page.props.alert, async (newAlert) => {
    if (newAlert && typeof newAlert === 'object' && 'type' in newAlert && 'message' in newAlert) {
        const typedAlert = newAlert as { type: string; message: string };
        alert.value = typedAlert as Alert;
        await showToast(typedAlert.type, typedAlert.message);
    }
});

onMounted(async () => {
    if (alert.value) {
        await showToast(alert.value.type, alert.value.message);
    }
});
</script> 