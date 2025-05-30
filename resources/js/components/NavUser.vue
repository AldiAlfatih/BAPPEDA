<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type SharedData, type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { isMobile, state } = useSidebar();
</script>

<template>
    <SidebarMenu class="px-2">
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton 
                        size="lg" 
                        class="data-[state=open]:bg-white/20 hover:bg-white/10 
                               transition-all duration-300 group rounded-xl
                               border border-white/10 backdrop-blur-sm
                               hover:border-white/20 hover:shadow-lg"
                    >
                        <UserInfo :user="user" class="text-white" />
                        <ChevronsUpDown class="ml-auto size-4 text-emerald-200 group-hover:text-white transition-colors duration-300" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent 
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-xl 
                           bg-white/95 backdrop-blur-md border-white/20 shadow-xl"
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end" 
                    :side-offset="8"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>