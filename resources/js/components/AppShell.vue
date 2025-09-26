<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import AppFooter from '@/components/AppFooter.vue';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage<SharedData>().props.sidebarOpen;
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <div class="flex-1">
            <slot />
        </div>
        <AppFooter />
    </div>
    <SidebarProvider v-else :default-open="isOpen" class="flex min-h-screen w-full flex-col">
        <div class="flex-1 flex">
            <slot />
        </div>
        <AppFooter />
    </SidebarProvider>
</template>
