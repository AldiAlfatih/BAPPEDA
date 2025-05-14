<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Triwulan 1',
        href: '/triwulan1',
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
        targetFisik: '',
        targetKeuangan: '216.551.782.886,00',
        realisasiFisik: '',
        realisasiKeuangan: '211.129.110.516,00',
        capaianFisik: '',
        capaianKeuangan: '',
        capaianTahunanFisik: '',
        capaianTahunanKeuangan: ''
    },
    {
        kode: '1.01',
        program: 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN',
        targetFisik: '',
        targetKeuangan: '216.551.782.886,00',
        realisasiFisik: '',
        realisasiKeuangan: '211.129.110.516,00',
        capaianFisik: '',
        capaianKeuangan: '',
        capaianTahunanFisik: '',
        capaianTahunanKeuangan: ''
    },
    {
        kode: '1.01.01',
        program: 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA',
        targetFisik: '',
        targetKeuangan: '170.351.678.486,00',
        realisasiFisik: '',
        realisasiKeuangan: '164.871.072.942,00',
        capaianFisik: '',
        capaianKeuangan: '',
        capaianTahunanFisik: '',
        capaianTahunanKeuangan: ''
    },
    {
        kode: '1.01.01.2.01',
        program: 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah',
        targetFisik: '',
        targetKeuangan: '',
        realisasiFisik: '',
        realisasiKeuangan: '',
        capaianFisik: '',
        capaianKeuangan: '',
        capaianTahunanFisik: '',
        capaianTahunanKeuangan: ''
    },
]);
</script>

<template>
    <Head title="Triwulan 1" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow">
                <h2 class="p-2 text-xl font-semibold bg-emerald-600 text-white rounded-t-xl">
                    Triwulan 1
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
                        <tr>
                            <th rowspan="3" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center align-middle">kode</th>
                            <th rowspan="3" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center align-middle">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                            <th colspan="8" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">Triwulan 1</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">TARGET</th>
                            <th colspan="2" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">REALISASI</th>
                            <th colspan="2" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">CAPAIAN PERTRIWULAN</th>
                            <th colspan="2" class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">CAPAIAN TAHUNAN</th>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KINERJA FISIK (%)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KEUANGAN (RP)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KINERJA FISIK (%)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KEUANGAN (RP)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KINERJA FISIK (%)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KEUANGAN (RP)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KINERJA FISIK (%)</th>
                            <th class="border border-gray-300 bg-gray-50 px-2 py-1 text-center">KEUANGAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in programData" :key="item.kode" class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="border border-gray-300 px-2 py-1">{{ item.kode }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.program }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-center">{{ item.targetFisik }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ item.targetKeuangan }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-center">{{ item.realisasiFisik }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ item.realisasiKeuangan }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-center">{{ item.capaianFisik }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ item.capaianKeuangan }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-center">{{ item.capaianTahunanFisik }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-right">{{ item.capaianTahunanKeuangan }}</td>
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
    border: 1px solid #070000;
    padding: 4px 8px;
    vertical-align: middle;
}

th {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Align monetary values to the right */
td:nth-child(4),
td:nth-child(6),
td:nth-child(8),
td:nth-child(10) {
    text-align: right;
}

/* Center percentage values */
td:nth-child(3),
td:nth-child(5),
td:nth-child(7),
td:nth-child(9) {
    text-align: center;
}

.dark th {
    background-color: #374151;
    color: #f3f4f6;
}

.dark td {
    color: #e5e7eb;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .overflow-x-auto {
        max-width: 100%;
    }
}
</style>