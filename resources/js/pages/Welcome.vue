<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import Alert from '@/components/Alert.vue';
import { Button } from '@/components/ui/button';

const page = usePage();

defineProps({
    showRegBtn: {
        type: Boolean,
        default: false
    },
    companyName: {
        type: String,
        default: 'Leave Portal'
    },
    logoUrl : {
        type : String,
        default : false
    }
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <Alert />
    
    <div class="min-h-screen bg-gradient-to-br from-background via-background to-muted/20">
        <!-- Header -->
        <header class="border-b bg-background/80 backdrop-blur-sm sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img :src="logoUrl" alt="Logo" class="h-10 w-40">
                </div>
                
                <nav class="flex items-center space-x-6">
                    <Link
                        v-if="page.props.auth.user"
                        :href="route('dashboard')"
                        class="text-muted-foreground hover:text-foreground transition-colors font-medium"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-muted-foreground hover:text-foreground transition-colors font-medium"
                        >
                            Sign In
                        </Link>
                        <Link v-if="showRegBtn"
                            :href="route('register')"
                            class="bg-primary text-primary-foreground px-5 py-2 rounded-lg hover:bg-primary/90 transition-all duration-200 font-medium"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="py-16 px-6">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Title and Description -->
                <div class="mb-12">
                    <h1 class="text-4xl md:text-5xl font-bold text-foreground mb-6 leading-tight">
                       Leave Portal System
                    </h1>
                    <p class="text-xl text-muted-foreground max-w-2xl mx-auto leading-relaxed">
                        Streamline your leave management with our comprehensive employee portal. 
                        Apply for leave, track balances, and stay connected with your team.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    <Link
                        v-if="page.props.auth.user"
                        :href="route('dashboard')"
                        class="bg-primary text-primary-foreground px-8 py-4 rounded-xl font-semibold hover:bg-primary/90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 min-w-[200px]"
                    >
                        Go to Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="bg-primary text-primary-foreground px-8 py-4 rounded-xl font-semibold hover:bg-primary/90 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 min-w-[200px]"
                        >
                            Sign In
                        </Link>
                        <Link v-if="showRegBtn"
                            :href="route('register')"
                            class="border-2 border-primary text-primary px-8 py-4 rounded-xl font-semibold hover:bg-primary hover:text-primary-foreground transition-all duration-200 min-w-[200px]"
                        >
                            Create Account
                        </Link>
                    </template>
                </div>

                <!-- Feature Cards -->
                <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="bg-card border rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Easy Leave Applications</h3>
                        <p class="text-muted-foreground text-sm">Submit and track your leave requests with just a few clicks.</p>
                    </div>

                    <div class="bg-card border rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Balance Tracking</h3>
                        <p class="text-muted-foreground text-sm">Monitor your leave balances and entitlements in real-time.</p>
                    </div>

                    <div class="bg-card border rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Team Communication</h3>
                        <p class="text-muted-foreground text-sm">Stay connected with notifications and approval workflows.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.bg-grid-white {
    background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
}
</style>
