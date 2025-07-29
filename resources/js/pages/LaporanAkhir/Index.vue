<script setup lang="ts">
import TabelListSKPD from '@/components/data/TabelListSKPD.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            user_detail?: {
                nip?: string;
            } | null;
            skpd: {
                nama_dinas: string;
                nama_operator: string;
                kode_organisasi: string;
            } | null;
        }>;
    };
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Arsip Monitoring', href: '/arsip-monitoring' }];
</script>

<template>
    <Head title="Arsip Monitoring" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header dengan judul -->
            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Arsip Monitoring Perangkat Daerah</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola arsip dokumen monitoring dari rencana awal hingga triwulan 4</p>
                </div>
            </div>
            <TabelListSKPD url_detail="arsip-monitoring.show" :users="users"></TabelListSKPD>
        </div>
    </AppLayout>
</template>
