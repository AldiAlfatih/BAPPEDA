<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 px-3 py-2 ${$props.class || ''}`">
        <SidebarGroupContent>
            <SidebarMenu class="space-y-1">
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton 
                        class="text-emerald-100 hover:text-white hover:bg-white/10 
                               transition-all duration-300 rounded-lg group
                               hover:transform hover:translate-x-1" 
                        as-child
                    >
                        <Link :href="item.href" class="flex items-center gap-3 px-3 py-2">
                            <div class="p-1 rounded bg-white/10 group-hover:bg-white/20 transition-colors duration-300">
                                <component :is="item.icon" class="w-4 h-4" />
                            </div>
                            <span class="font-medium">{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
