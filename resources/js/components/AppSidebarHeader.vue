<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { watch, computed } from 'vue';

const props = defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

// Debug: log breadcrumbs to verify actual items rendered
// watch(
//     () => props.breadcrumbs,
//     (val) => {
//         console.log('Breadcrumbs debug:', val);
//     },
//     { immediate: true },
// );

// Normalize breadcrumbs: collapse 3-step pattern into 2 steps
const displayBreadcrumbs = computed(() => {
    const bc = props.breadcrumbs ?? [];
    if (
        bc.length === 3 &&
        bc[0]?.title === 'Arsip Monitoring' &&
        typeof bc[1]?.title === 'string' && bc[1].title.startsWith('Arsip Monitoring') &&
        typeof bc[2]?.title === 'string' && bc[2].title.toLowerCase().startsWith('detail arsip')
    ) {
        const skpdName = bc[1].title.replace(/^Arsip Monitoring\s*/i, '').trim();
        return [
            bc[0],
            { title: `Detail Arsip ${skpdName}`, href: '' },
        ];
    }
    return bc;
});

// Debug normalized breadcrumbs as well
// watch(
//     () => displayBreadcrumbs.value,
//     (val) => {
//         console.log('Breadcrumbs normalized:', val);
//     },
//     { immediate: true },
// );
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="displayBreadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="displayBreadcrumbs" />
            </template>
        </div>
    </header>
</template>
