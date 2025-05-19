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
        <div class="flex h-full flex-1 w-5xl  overflow-x-hidden flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow">
                <h2 class="p-2 text-xl font-semibold bg-emerald-600 text-white rounded-t-xl">
                    Rencana Kinerja
                </h2>

                <!-- Top table -->
                    <div class="p-4">
                        <div class="mb-2">
                            <strong>KODE/URUSAN PEMERINTAHAN:</strong>
                            {{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}
                        </div>

                        <div class="mb-2">
                            <strong>Nama SKPD:</strong>
                            {{ tugas.skpd.nama_dinas }}
                        </div>
                        <div class="mb-2">
                            <strong>Kode Organisasi:</strong>
                            {{ tugas.skpd.kode_organisasi ?? '-' }}
                        </div>

                        <div class="mb-2">
                            <strong>No DPA:</strong>
                            {{ tugas.skpd.no_dpa ?? '-' }}
                        </div>

                        <div class="mb-2">
                            <strong>Kepala SKPD:</strong>
                            {{ kepalaSkpd ?? tugas.skpd.skpd_kepala[0]?.user?.user_detail?.nama ?? '-' }}
                        </div>

                        <div class="mt-4 p-4 border rounded bg-gray-100" v-if="tugas.rencana_awal">
                            <h2 class="font-semibold">Rencana Awal:</h2>
                            <p><strong>Indikator:</strong> {{ tugas.rencana_awal.indikator }}</p>
                            <p><strong>Target:</strong> {{ tugas.rencana_awal.target }}</p>
                        </div>

                        <div v-else class="mt-4 p-4 border rounded bg-yellow-100 text-yellow-800">
                            Belum ada data rencana awal.
                        </div>
                    </div>
            </div>

            <!-- Program table with targets -->
            <div class="bg-white dark:bg-gray-700 rounded-t-xl shadow overflow-x-auto">
                <div class="flex justify-end p-4">
                    <button
                        @click="goToCreate"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors"
                    >
                        Tambahkan
                    </button>
                </div>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="3" class="border border-amber-300 bg-white px-2 py-1">KODE</th>
                            <th rowspan="3" class="border sticky left-0 z-10 bg-white w-[100px]">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
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
                                <td class="border border-amber-300 px-2 py-1 sticky left-0 bg-white">{{ program.kode_nomenklatur.nomenklatur }}</td>
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
                            </tr>
                            
                            <!-- Kegiatan untuk program ini -->
                            <template v-for="kegiatan in kegiatanTugas.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                <tr>
                                    <td class="border border-amber-300 px-2 py-1">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="border border-amber-300 px-2 py-1 sticky left-0 bg-white pl-8">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
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
                                </tr>
                                
                                <!-- Subkegiatan untuk kegiatan ini -->
                                <template v-for="subKegiatan in subkegiatanTugas.filter(sk => sk.kode_nomenklatur.details[0]?.id_kegiatan === kegiatan.kode_nomenklatur.id)" :key="subKegiatan.id">
                                    <tr>
                                        <td class="border border-amber-300 px-2 py-1">{{ subKegiatan.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="border border-amber-300 px-2 py-1 sticky left-0 bg-white pl-12">{{ subKegiatan.kode_nomenklatur.nomenklatur }}</td>
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
