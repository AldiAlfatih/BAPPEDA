<script setup lang="ts">
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';



const props = defineProps<{
  user?: {
    skpd?: {
      nama_skpd?: string;
    };
  };
  tugas: {
    id: number;
    kode_nomenklatur: { 
      nomor_kode: string; 
      nomenklatur: string;
      details: Array<{
        id_urusan: number;
        id_bidang_urusan: number;
        id_program: number;
        id_kegiatan: number;
      }>;
    };
    skpd: {
      nama_dinas: string;
      kode_organisasi?: string;
      no_dpa?: string;
      skpd_kepala: Array<{
        user: {
          user_detail: {
            nama: string;
          };
        };
      }>;
    };
    rencana_awal?: {
      indikator: string;
      target: string;
    } | null;
  };
  programTugas: Array<any>;
  kegiatanTugas: Array<any>;
  subkegiatanTugas: Array<any>;
  kepalaSkpd?: string;
  urusan: number[]; 
  bidangUrusan: number[];
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Monitoring', href: '/Monitoring' },
  { title: `Monitoring Detail ${props.user?.skpd?.nama_skpd ?? '-'}`, href: 'monitoring/show' },
  { title: 'Rencana Awal PD', href: '/rencanaawal' },
]);


function goToCreate() {
  router.visit('/rencanaawal/create');
}

</script>

<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->

            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-600">Rencana Kinerja</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN:</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.skpd.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>


                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.skpd.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">No DPA</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.skpd?.no_dpa || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ kepalaSkpd ?? tugas.skpd.skpd_kepala[0]?.user?.user_detail?.nama ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Program table with targets -->
            <div class="bg-white dark:bg-gray-700 rounded-t-xl shadow overflow-x-auto">
                <div class="flex justify-end p-4">
                </div>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="3" class="border border-amber-300 bg-white px-2 py-1">KODE</th>
                            <th rowspan="3" class="border border-amber-300 bg-white px-2 py-1">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                            <th colspan="3" class="border border-amber-300 px-2 py-1 bg-amber-100">PAGU ANGGARAN APBD</th>
                            <th rowspan="3" class="border border-amber-300 px-2 py-1 bg-amber-100">SUMBER DANA</th>
                            <th colspan="8" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TARGET</th>
                        </tr>
                        <tr class="text-center bg-amber-100">
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">POKOK (RP)</th>
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">PARSIAL (RP)</th>
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">PERUBAHAN (RP)</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 1</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 2</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 3</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 4</th>
                        </tr>
                        <tr class="text-center bg-[#fbe9db]">
                            <th class="border border-amber-300 px-2 py-1">FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <th class="border border-amber-300 px-2 py-1">FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <th class="border border-amber-300 px-2 py-1">FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <th class="border border-amber-300 px-2 py-1">FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Programs -->
                        <template v-for="program in programTugas" :key="program.id">
                            <tr>
                                <td class="border border-amber-300 px-2 py-1">{{ program.kode_nomenklatur.nomor_kode }}</td>
                                <td class="border border-amber-300 px-2 py-1">{{ program.kode_nomenklatur.nomenklatur }}</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                            </tr>
                            
                            <!-- Kegiatan untuk program ini -->
                            <template v-for="kegiatan in kegiatanTugas.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                <tr>
                                    <td class="border border-amber-300 px-2 py-1">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="border border-amber-300 px-2 py-1">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                </tr>
                                
                                <!-- Subkegiatan untuk kegiatan ini -->
                                <template v-for="subKegiatan in subkegiatanTugas.filter(sk => sk.kode_nomenklatur.details[0]?.id_kegiatan === kegiatan.kode_nomenklatur.id)" :key="subKegiatan.id">
                                    <tr>
                                        <td class="border border-amber-300 px-2 py-1">{{ subKegiatan.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="border border-amber-300 px-2 py-1">{{ subKegiatan.kode_nomenklatur.nomenklatur }}</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                        <td class="border border-amber-300 px-2 py-1 text-right">-</td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Enhanced styling to match the reference image */
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #000000;
    padding: 4px 8px;
}

thead {
    background-color: #FFF2CC;
}

thead th {
    font-weight: bold;
    vertical-align: middle;
}

tbody tr:nth-child(odd) {
    background-color: #FFFCF3;
}
</style>