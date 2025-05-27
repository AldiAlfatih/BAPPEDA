<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

// Ambil props dari Inertia
const page = usePage();
const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';

// Ambil role user (dari backend)
const role = page.props.auth?.user?.role ?? ''; // sesuaikan nama key jika perlu

// Daftar menu sesuai role
let sidebarNavItems: NavItem[] = [];

if (role === 'admin' || role === 'operator') {
    sidebarNavItems = [
        { title: 'Profile', href: '/settings/profile' },
        { title: 'Password', href: '/settings/password' },
    ];
} else if (role === 'perangkat_daerah') {
    sidebarNavItems = [
        { title: 'Profil SKPD', href: '/settings/profiledetail' },
        { title: 'Password', href: '/settings/password' },
    ];
}
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Pengaturan" description="Manage your profile and account settings" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-x-0 space-y-1">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
