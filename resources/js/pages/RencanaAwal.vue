<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rencana Awal',
        href: '/rencanaawal',
    },
];

// Sample data for the top table
const headerData = ref([
    { label: 'KODE/URUSAN PEMERINTAHAN', value: '5 UNSUR PENUNJANG PEMERINTAHAN ANGGARAN APBD' },
    { label: 'KODE ORGANISASI', value: '5.01.5.05.0.00.25.0000' },
    { label: 'NO.DPA SKPD', value: 'DPA/A.1/5.01.5.05.0.00.25.0000/001.2024' },
    { label: 'NAMA KEPALA SKPD', value: 'ZULKARNAEN, ST., M.Si' },
]);

// Sample data for the program table
const programData = ref([
    {
        kode: '1',
        program: 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR',
        pokok: '216.551.782.886,00',
        parsial: '211.129.110.516,00',
        perubahan: '',
        sumberDana: 'DAU'
    },
    {
        kode: '1.01',
        program: 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN',
        pokok: '216.551.782.886,00',
        parsial: '211.129.110.516,00',
        perubahan: '0,0',
        sumberDana: 'DAU'
    },
    {
        kode: '1.01.01',
        program: 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA',
        pokok: '170.351.678.486,00',
        parsial: '164.871.072.942,00',
        perubahan: '',
        sumberDana: ''
    },
    {
        kode: '1.01.01.2.01',
        program: 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah',
        pokok: '',
        parsial: '',
        perubahan: '',
        sumberDana: ''
    },
]);
</script>

<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow">
                <h2 class="p-2 text-xl font-semibold bg-emerald-600 text-white rounded-t-xl">
                    Rencana Kinerja
                </h2>
                
                <!-- Top table -->
                <div class="overflow-hidden">
                    <table class="w-full border-collapse">
                        <tbody>
                            <tr v-for="(item, index) in headerData" :key="index" class="border border-gray-200 dark:border-gray-600">
                                <td class="p-3 border-r border-gray-200 dark:border-gray-600 font-medium w-1/3">{{ item.label }}</td>
                                <td class="p-3 font-medium">{{ item.value }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Add button -->
            </div>
            
            <!-- Program table -->
            <div class="bg-white dark:bg-gray-700 rounded-t-xl shadow overflow-x-auto">
                <div class="flex justify-end p-4">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                        Tambahkan
                    </button>
                </div>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class=" text-center">
                            <th rowspan="3" class="border border-amber-300 px-2 py-1">kode</th>
                            <th rowspan="3" class="border border-amber-300 px-2 py-1">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                            <th colspan="3" class="border border-amber-300 px-2 py-1">PAGU ANGGARAN APBD</th>
                            <th rowspan="3" class="border border-amber-300 px-2 py-1">SUMBER DANA</th>
                        </tr>
                        <tr class=" text-center">
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">POKOK (RP)</th>
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">PARSIAL (RP)</th>
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">PERUBAHAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in programData" :key="item.kode" class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-3 border border-gray-200 dark:border-gray-600">{{ item.kode }}</td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 font-medium">{{ item.program }}</td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">{{ item.pokok }}</td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">{{ item.parsial }}</td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ item.perubahan }}</td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ item.sumberDana }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Additional styling */
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #000000;
    padding: 4px 8px;
}
</style>