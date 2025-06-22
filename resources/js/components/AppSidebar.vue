<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import NavFooter from '@/components/NavFooter.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, LayoutGrid, Users, Monitor, BadgeHelp } from 'lucide-vue-next';
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
        href: '/manajemen-tim/usermanagement',
        icon: Users,
        guard: ['super_admin','admin'],
        children: [
        {
            title: 'User',
            href: '/manajemen-tim/usermanagement',
        },
        {
            title: 'Perangkat Daerah',
            href: '/manajemen-tim/perangkatdaerah',
        },
        ],
    },


    {
        title: 'Perangkat Daerah',
        href: '/manajemen-tim/perangkatdaerah',
        guard: ['perangkat_daerah'],
        icon: Users,
    },

    {
        title: 'Monitoring',
        href: '/rencana-awal',
        icon: Monitor,
        children: [
        {
            title: 'Manajemen Periode',
            href: '/monitoring/periode',
            guard: ['admin'],
        },
        {
            title: 'Manajemen Anggaran',
            href: '/manajemenanggaran',
        },
        {
            title: 'Rencana Awal',
            href: '/rencana-awal',
        },
        {
            title: 'Triwulan 1',
            href: route('triwulan.index', { tid: 1 }),
        },
        {
            title: 'Triwulan 2',
            href: route('triwulan.index', { tid: 2 }),
        },
        {
            title: 'Triwulan 3',
            href: route('triwulan.index', { tid: 3 }),
        },
        {
            title: 'Triwulan 4',
            href: route('triwulan.index', { tid: 4 }),
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
