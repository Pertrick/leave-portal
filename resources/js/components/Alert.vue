<template>
    <div v-if="alert" class="fixed top-4 right-4 z-50">
        <div
            :class="[
                'p-4 rounded-lg shadow-lg max-w-md transform transition-all duration-300 ease-in-out',
                alertClasses[alert.type]
            ]"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <component :is="alertIcons[alert.type]" class="h-5 w-5" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" :class="textClasses[alert.type]">
                        {{ alert.message }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <button
                        @click="closeAlert"
                        class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, getCurrentInstance } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon, ExclamationCircleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';

interface Alert {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
}

const page = usePage();
const alert = ref<Alert | null>(page.props.alert as Alert | null);
const instance = getCurrentInstance();

const alertClasses = {
    success: 'bg-green-50 border border-green-200',
    error: 'bg-red-50 border border-red-200',
    warning: 'bg-yellow-50 border border-yellow-200',
    info: 'bg-blue-50 border border-blue-200',
};

const textClasses = {
    success: 'text-green-800',
    error: 'text-red-800',
    warning: 'text-yellow-800',
    info: 'text-blue-800',
};

const alertIcons = {
    success: CheckCircleIcon,
    error: XCircleIcon,
    warning: ExclamationCircleIcon,
    info: InformationCircleIcon,
};

const closeAlert = () => {
    alert.value = null;
};

const showToast = (type: string, message: string) => {
    if (instance?.proxy?.$toast) {
        (instance.proxy.$toast as any)[type](message);
    }
};

watch(() => page.props.alert, (newAlert) => {
    console.log('Alert watch triggered:', newAlert);
    if (newAlert && typeof newAlert === 'object' && 'type' in newAlert && 'message' in newAlert) {
        const typedAlert = newAlert as { type: string; message: string };
        alert.value = typedAlert as Alert;
        showToast(typedAlert.type, typedAlert.message);
    }
});

onMounted(() => {
    console.log('Alert component mounted, alert value:', alert.value);
    console.log('Page props:', page.props);
    if (alert.value) {
        showToast(alert.value.type, alert.value.message);
    }
});
</script> 