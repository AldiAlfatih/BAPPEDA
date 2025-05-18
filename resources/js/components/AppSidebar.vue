<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import NavFooter from '@/components/NavFooter.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, Monitor, Bell, Info, BadgeHelp } from 'lucide-vue-next';
// import AppLogo from './AppLogo.vue';


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },

    {
        title: 'Nomenklatur',
        href: '/kodenomenklatur',
        guard: ['admin'],
        icon: BookOpen,
    },

    {
        title: 'Manajemen Tim',
        href: '/usermanagement',
        icon: Users,
        guard: ['super_admin','admin'],
        children: [
        {
            title: 'User',
            href: '/usermanagement',
        },
        {
            title: 'Perangkat Daerah',
            href: '/perangkatdaerah',
        },
        ],

    

    },

    
    {
        title: 'Monitoring',
        href: '/monitoring',
        icon: Monitor,
    },


    {
        title: 'Monitoring',
        href: '/monitoring',
        icon: Monitor,
        guard: ['admin', 'perangkat_daerah'],
        children: [
        {
            title: 'Manajemen Periode',
            href: '/periode',
            guard: ['admin']
        },
        {
            title: 'Rencana Awal',
            href: '/rencanaawal',
        },
        {
            title: 'Triwulan 1',
            href: 'triwulan1',
        },
        {
            title: 'Triwulan 2',
            href: '#',
        },
        {
            title: 'Triwulan 3',
            href: '#',
        },
        {
            title: 'Triwulan 4',
            href: '#',
        },
        {
            title: 'Laporan Akhir',
            href: '#',
        },
        ],
    },


];


const footerNavItems: NavItem[] = [
{
        title: 'Bantuan',
        href: '/bantuan',
        icon: BadgeHelp,
    },
    {
        title: 'Panduan',
        href: '/panduan',
        icon: BookOpen,
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

        <SidebarContent class="bg-green-900">
            <NavMain :items="mainNavItems" />
        </SidebarContent>


        <SidebarFooter class="bg-green-900">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
