<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';

interface Target {
    kinerjaFisik: string;
    keuangan: string;
}

interface ProgramData {
    kode: string;
    program: string;
    pokok: string;
    parsial: string;
    perubahan: string;
    sumberDana: string;
    targets: Target[];
    monitoring?: {
        pagu_pokok: number;
        pagu_parsial: number;
        pagu_perubahan: number;
        sumber_dana: string;
        targets: Array<{
            kinerja_fisik: number;
            keuangan: number;
        }>;
    };
}

interface User {
    id: number;
    name: string;
    nama_skpd: string;
    skpd_id?: number;
    nip: string;
}

interface Props {
    user?: User;
    programTugas?: any[];
    kegiatanTugas?: any[];
    subkegiatanTugas?: any[];
    bidangUrusanTugas?: any[];
    kepalaSkpd?: string;
    isFinalized?: boolean | number;
    flash?: {
        success?: string;
        error?: string;
    };
    skpd?: {
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
    urusan?: number[]; 
    bidangUrusan?: number[];
    bidangUrusanList?: any[];
    monitoring?: {
      sumber_dana: string;
      pagu_pokok: number;
      pagu_parsial: number;
      pagu_perubahan: number;
      targets: Array<any>;
      realisasi: Array<any>;
    };
    tugas?: any;
    dataAnggaranTerakhir?: Record<number, {
      sumber_anggaran: {
        dak: boolean;
        dak_peruntukan: boolean;
        dak_fisik: boolean;
        dak_non_fisik: boolean;
        blud: boolean;
      };
      values: {
        dak: number;
        dak_peruntukan: number;
        dak_fisik: number;
        dak_non_fisik: number;
        blud: number;
      };
    }>;
}

const props = defineProps<Props>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Monitoring', href: '/monitoring' },
    { title: `Monitoring Detail ${props.user?.nama_skpd ?? '-'}`, href: `/monitoring/${props.user?.id}` },
    { title: 'Rencana Awal PD', href: '/rencanaawal' },
]);

// Sample data for the top table
const headerData = ref([
    { label: 'KODE/URUSAN PEMERINTAHAN', value: '5 UNSUR PENUNJANG PEMERINTAHAN ANGGARAN APBD' },
    { label: 'NAMA SKPD', value: props.user?.nama_skpd || 'DINAS PENDIDIDKAN' },
    { label: 'KODE ORGANISASI', value: '5.01.5.05.0.00.25.0000' },
    { label: 'NO.DPA SKPD', value: 'DPA/A.1/5.01.5.05.0.00.25.0000/001.2024' },
    { label: 'NAMA KEPALA SKPD', value: props.kepalaSkpd || 'ZULKARNAEN, ST., M.Si' },
]);

// Add a computed property to transform subkegiatan data to include bidang urusan and multiple rows per sumber dana
const formattedSubKegiatanData = computed(() => {
  const result: any[] = [];
  
  if (!props.subkegiatanTugas || !props.dataAnggaranTerakhir) {
    return result;
  }

  // For each subkegiatan
  props.subkegiatanTugas.forEach(subKegiatan => {
    // Find the parent kegiatan
    const parentKegiatan = props.kegiatanTugas?.find(k => 
      k.kode_nomenklatur.id === subKegiatan.kode_nomenklatur.details[0]?.id_kegiatan
    );
    
    if (!parentKegiatan) return;
    
    // Find the parent program
    const parentProgram = props.programTugas?.find(p => 
      p.kode_nomenklatur.id === parentKegiatan.kode_nomenklatur.details[0]?.id_program
    );
    
    if (!parentProgram) return;
    
    // Find the parent bidang urusan
    const parentBidangUrusan = props.bidangUrusanList?.find(bu => 
      bu.id === parentProgram.kode_nomenklatur.details[0]?.id_bidang_urusan
    );
    
    // Get the funding data for this subkegiatan
    const fundingData = props.dataAnggaranTerakhir?.[subKegiatan.id];
    
    if (fundingData) {
      // Check each funding source
      const sources = [
        { key: 'dak', name: 'DAK' },
        { key: 'dak_peruntukan', name: 'DAK Peruntukan' },
        { key: 'dak_fisik', name: 'DAK Fisik' },
        { key: 'dak_non_fisik', name: 'DAK Non-Fisik' },
        { key: 'blud', name: 'BLUD' }
      ];
      
      // For each active funding source, create a row
      sources.forEach(source => {
        const sourceKey = source.key as keyof typeof fundingData.sumber_anggaran;
        const valueKey = source.key as keyof typeof fundingData.values;
        
        if (fundingData.sumber_anggaran[sourceKey] && fundingData.values[valueKey] > 0) {
          result.push({
            id: `${subKegiatan.id}-${source.key}`,
            subKegiatan: subKegiatan,
            kegiatan: parentKegiatan,
            program: parentProgram,
            bidangUrusan: parentBidangUrusan,
            sumberDana: source.name,
            pokok: fundingData.values[valueKey],
            parsial: 0,
            perubahan: 0
          });
        }
      });
    }
  });
  
  return result;
});

// Add computed properties to calculate the sums
const calculateKegiatan = computed<Record<number, number>>(() => {
  const kegiatanSums: Record<number, number> = {};
  
  // First, calculate sums for each kegiatan based on its subkegiatans
  formattedSubKegiatanData.value.forEach(item => {
    const kegiatanId = item.kegiatan.id;
    if (!kegiatanSums[kegiatanId]) {
      kegiatanSums[kegiatanId] = 0;
    }
    kegiatanSums[kegiatanId] += item.pokok;
  });
  
  return kegiatanSums;
});

const calculateProgram = computed<Record<number, number>>(() => {
  const programSums: Record<number, number> = {};
  
  // Calculate sums for each program based on its kegiatans
  props.kegiatanTugas?.forEach(kegiatan => {
    const parentProgramId = kegiatan.kode_nomenklatur.details[0]?.id_program;
    if (parentProgramId) {
      if (!programSums[parentProgramId]) {
        programSums[parentProgramId] = 0;
      }
      programSums[parentProgramId] += calculateKegiatan.value[kegiatan.id] || 0;
    }
  });
  
  return programSums;
});

const calculateBidangUrusan = computed<Record<number, number>>(() => {
  const bidangUrusanSums: Record<number, number> = {};
  
  // Calculate sums for each bidang urusan based on its programs
  props.programTugas?.forEach(program => {
    const parentBidangUrusanId = program.kode_nomenklatur.details[0]?.id_bidang_urusan;
    if (parentBidangUrusanId) {
      if (!bidangUrusanSums[parentBidangUrusanId]) {
        bidangUrusanSums[parentBidangUrusanId] = 0;
      }
      bidangUrusanSums[parentBidangUrusanId] += calculateProgram.value[program.kode_nomenklatur.id] || 0;
    }
  });
  
  return bidangUrusanSums;
});

// Function to handle navigation
function goToMonitoringDetail() {
  router.visit(`/monitoring/${props.user?.id}`);
}

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
                    <h2 class="text-2xl font-bold text-gray-600">Rencana Kinerja</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN:</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.tugas?.kode_nomenklatur.nomor_kode }} - {{ props.tugas?.kode_nomenklatur.nomenklatur }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.tugas?.skpd.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>


                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.tugas?.skpd.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.kepalaSkpd ?? props.tugas?.skpd.skpd_kepala[0]?.user?.user_detail?.nama ?? '-' }}</p>
                    </div>

                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">No NIP</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.user?.nip || 'Tidak tersedia' }}</p>
                    </div>
                </div>
            </div>


            <!-- Program table with targets -->
            <div class="bg-white dark:bg-gray-700 rounded-t-xl shadow overflow-x-auto">
                <table class="w-full border-collapse text-sm mt-6">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="3" class="border border-amber-300 bg-white px-2 py-1">KODE</th>
                            <th rowspan="3" class="border sticky left-0 z-10 bg-white w-[400px]">BIDANG URUSAN/PROGRAM/KEGIATAN/SUB KEGIATAN</th>
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
                            <!-- Triwulan 1 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <!-- Triwulan 2 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <!-- Triwulan 3 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <!-- Triwulan 4 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Display bidang urusan -->
                        <template v-for="bidangUrusan in props.bidangUrusanList" :key="bidangUrusan.id">
                            <tr class="bg-blue-100 font-semibold">
                                <td class="p-3 border border-gray-200 text-center">{{ bidangUrusan.nomor_kode }}</td>
                                <td class="p-3 border border-gray-200">{{ bidangUrusan.nomenklatur }}</td>
                                <td class="p-3 border border-gray-200 text-right">{{ calculateBidangUrusan[bidangUrusan.id]?.toLocaleString('id-ID') || '0' }}</td>
                                <td class="p-3 border border-gray-200 text-right">0</td>
                                <td class="p-3 border border-gray-200 text-right">0</td>
                                <td class="p-3 border border-gray-200 text-center">-</td>
                                <td class="p-3 border border-gray-200 text-center">-</td>
                                <td class="p-3 border border-gray-200 text-right">-</td>
                                <td class="p-3 border border-gray-200 text-center">-</td>
                                <td class="p-3 border border-gray-200 text-right">-</td>
                                <td class="p-3 border border-gray-200 text-center">-</td>
                                <td class="p-3 border border-gray-200 text-right">-</td>
                                <td class="p-3 border border-gray-200 text-center">-</td>
                                <td class="p-3 border border-gray-200 text-right">-</td>
                            </tr>
                            
                            <!-- Display programs for this bidang urusan -->
                            <template v-for="program in props.programTugas?.filter(p => p.kode_nomenklatur.details[0]?.id_bidang_urusan === bidangUrusan.id)" :key="program.id">
                                <tr class="border border-gray-200 bg-gray-50 font-medium">
                                    <td class="p-3 border border-gray-200 text-center">{{ program.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="p-3 border border-gray-200 pl-6">{{ program.kode_nomenklatur.nomenklatur }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ program.monitoring?.pagu_parsial?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ program.monitoring?.pagu_perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-3 border border-gray-200 text-center">{{ program.monitoring?.sumber_dana || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-center">{{ program.monitoring?.targets?.[0]?.kinerja_fisik || program.targets?.[0]?.kinerjaFisik || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ program.monitoring?.targets?.[0]?.keuangan?.toLocaleString('id-ID') || program.targets?.[0]?.keuangan || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-center">{{ program.monitoring?.targets?.[1]?.kinerja_fisik || program.targets?.[1]?.kinerjaFisik || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ program.monitoring?.targets?.[1]?.keuangan?.toLocaleString('id-ID') || program.targets?.[1]?.keuangan || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-center">{{ program.monitoring?.targets?.[2]?.kinerja_fisik || program.targets?.[2]?.kinerjaFisik || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ program.monitoring?.targets?.[2]?.keuangan?.toLocaleString('id-ID') || program.targets?.[2]?.keuangan || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-center">{{ program.monitoring?.targets?.[3]?.kinerja_fisik || program.targets?.[3]?.kinerjaFisik || '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-right">{{ program.monitoring?.targets?.[3]?.keuangan?.toLocaleString('id-ID') || program.targets?.[3]?.keuangan || '-' }}</td>
                                </tr>
                                
                                <!-- Display kegiatan for this program -->
                                <template v-for="kegiatan in props.kegiatanTugas?.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                    <tr class="border border-gray-200">
                                        <td class="p-3 border border-gray-200 text-center">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="p-3 border border-gray-200 pl-12">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ calculateKegiatan[kegiatan.id]?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ kegiatan.monitoring?.pagu_parsial?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ kegiatan.monitoring?.pagu_perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-3 border border-gray-200 text-center">{{ kegiatan.monitoring?.sumber_dana || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-center">{{ kegiatan.monitoring?.targets?.[0]?.kinerja_fisik || kegiatan.targets?.[0]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ kegiatan.monitoring?.targets?.[0]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[0]?.keuangan || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-center">{{ kegiatan.monitoring?.targets?.[1]?.kinerja_fisik || kegiatan.targets?.[1]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ kegiatan.monitoring?.targets?.[1]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[1]?.keuangan || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-center">{{ kegiatan.monitoring?.targets?.[2]?.kinerja_fisik || kegiatan.targets?.[2]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ kegiatan.monitoring?.targets?.[2]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[2]?.keuangan || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-center">{{ kegiatan.monitoring?.targets?.[3]?.kinerja_fisik || kegiatan.targets?.[3]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-3 border border-gray-200 text-right">{{ kegiatan.monitoring?.targets?.[3]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[3]?.keuangan || '-' }}</td>
                                    </tr>
                                    
                                    <!-- Display formatted subkegiatan data for this kegiatan -->
                                    <template v-for="item in formattedSubKegiatanData.filter(sk => sk.kegiatan.id === kegiatan.id)" :key="item.id">
                                        <tr class="border border-gray-200 hover:bg-gray-50">
                                            <td class="p-3 border border-gray-200 text-center">{{ item.subKegiatan.kode_nomenklatur.nomor_kode }}</td>
                                            <td class="p-3 border border-gray-200 pl-16">{{ item.subKegiatan.kode_nomenklatur.nomenklatur }}</td>
                                            <td class="p-3 border border-gray-200 text-right font-medium text-green-600">{{ item.pokok.toLocaleString('id-ID') }}</td>
                                            <td class="p-3 border border-gray-200 text-right">{{ item.parsial.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-3 border border-gray-200 text-right">{{ item.perubahan.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-3 border border-gray-200 text-center font-medium text-blue-600">{{ item.sumberDana }}</td>
                                            <td class="p-3 border border-gray-200 text-center">{{ item.subKegiatan.monitoring?.targets?.[0]?.kinerja_fisik || item.subKegiatan.targets?.[0]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-right">{{ item.subKegiatan.monitoring?.targets?.[0]?.keuangan?.toLocaleString('id-ID') || item.subKegiatan.targets?.[0]?.keuangan || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-center">{{ item.subKegiatan.monitoring?.targets?.[1]?.kinerja_fisik || item.subKegiatan.targets?.[1]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-right">{{ item.subKegiatan.monitoring?.targets?.[1]?.keuangan?.toLocaleString('id-ID') || item.subKegiatan.targets?.[1]?.keuangan || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-center">{{ item.subKegiatan.monitoring?.targets?.[2]?.kinerja_fisik || item.subKegiatan.targets?.[2]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-right">{{ item.subKegiatan.monitoring?.targets?.[2]?.keuangan?.toLocaleString('id-ID') || item.subKegiatan.targets?.[2]?.keuangan || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-center">{{ item.subKegiatan.monitoring?.targets?.[3]?.kinerja_fisik || item.subKegiatan.targets?.[3]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-3 border border-gray-200 text-right">{{ item.subKegiatan.monitoring?.targets?.[3]?.keuangan?.toLocaleString('id-ID') || item.subKegiatan.targets?.[3]?.keuangan || '-' }}</td>
                                        </tr>
                                    </template>
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

input {
    border: 1px solid #ddd;
    padding: 2px 4px;
    border-radius: 4px;
}

input:focus {
    outline: none;
    border-color: #4CAF50;
}
</style>
