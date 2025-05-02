<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, Monitor, Bell, Info } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    
    {
        title: 'Kode Nomenklatur',
        href: '/kodenomenklatur',
        guard: ['admin'],
        icon: BookOpen,
    },

    {
        title: 'Manjemen Tim Kerja',
        href: '/usermanagement',
        icon: Users,
        guard: ['super_admin','admin'],
    },
    {
        title: 'Monitoring',
        href: '/monitoring',
        icon: Monitor,
        guard: ['admin'],
        children: [
        {
            title: 'History',
            href: '#',
        },
        {
            title: 'Starred',
            href: '#',
        },
        {
            title: 'Settings',
            href: '#',
        },
        ],
    },

    {
        title: 'Bantuan',
        href: 'bantuan',
        icon: Bell,
    },

    {
        title: 'BantuanFaq',
        href: 'bantuanfaq',
        icon: Bell,
    },

    {
        title: 'Informasi',
        href: '/bantuan',
        icon: Info,
        children: [
        {
            title: 'Pemberitahuan',
            href: '/pemberitahuan',
        },
        {
            title: 'Starred',
            href: '#',
        },
        {
            title: 'Settings',
            href: '#',
        },
        ],
    },

];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>