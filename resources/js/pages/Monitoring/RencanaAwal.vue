<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue';
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

interface ItemWithKodeNomenklatur {
  id: number;
  kode_nomenklatur: {
    id: number;
    nomor_kode: string;
    nomenklatur: string;
    jenis_nomenklatur: number;
    details?: any[];
  };
  monitoring?: {
    targets?: Array<{
      kinerja_fisik: number;
      keuangan: number;
    }>;
  };
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
    periodeAktif?: Array<{ id: number; tahap: { id: number; tahap: string }; tahun: { id: number; tahun: string } }>;
    semuaPeriodeAktif?: Array<{ id: number; tahap: { id: number; tahap: string }; tahun: { id: number; tahun: string } }>;
    tahunAktif?: { id: number; tahun: string } | null;
    bidangurusanTugas?: any[];
}

const props = defineProps<Props>();

// Add reactive state for selected period
const selectedPeriodeId = ref<number | null>(null);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Monitoring', href: '/monitoring' },
    { title: `Monitoring Detail ${props.user?.nama_skpd ?? '-'}`, href: `/monitoring/${props.user?.id}` },
    { title: 'Rencana Awal PD', href: '/rencanaawal' },
]);

// Initialize with the active period if available
onMounted(() => {
  if (props.periodeAktif && props.periodeAktif.length > 0) {
    selectedPeriodeId.value = props.periodeAktif[0].id;
  } else if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
    selectedPeriodeId.value = props.semuaPeriodeAktif[0].id;
  }
});

// Modal and target editing states
const showModal = ref(false);
const currentItem = ref<ItemWithKodeNomenklatur | null>(null);
const targetData = ref({
  tw1: { kinerja_fisik: 0, keuangan: 0 },
  tw2: { kinerja_fisik: 0, keuangan: 0 },
  tw3: { kinerja_fisik: 0, keuangan: 0 },
  tw4: { kinerja_fisik: 0, keuangan: 0 }
});

// Fill targets action
const fillTargets = (item: ItemWithKodeNomenklatur) => {
  currentItem.value = item;
  // Populate target data from existing values if available
  if (item.monitoring?.targets) {
    targetData.value = {
      tw1: { 
        kinerja_fisik: item.monitoring.targets[0]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[0]?.keuangan || 0 
      },
      tw2: { 
        kinerja_fisik: item.monitoring.targets[1]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[1]?.keuangan || 0 
      },
      tw3: { 
        kinerja_fisik: item.monitoring.targets[2]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[2]?.keuangan || 0 
      },
      tw4: { 
        kinerja_fisik: item.monitoring.targets[3]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[3]?.keuangan || 0 
      }
    };
  } else {
    // Reset to defaults if no data exists
    targetData.value = {
      tw1: { kinerja_fisik: 0, keuangan: 0 },
      tw2: { kinerja_fisik: 0, keuangan: 0 },
      tw3: { kinerja_fisik: 0, keuangan: 0 },
      tw4: { kinerja_fisik: 0, keuangan: 0 }
    };
  }
  showModal.value = true;
};

// Save targets action
const saveTargets = (item: ItemWithKodeNomenklatur) => {
  if (!currentItem.value || !showModal.value) {
    fillTargets(item); // If not already in edit mode, open the modal first
    return;
  }
  
  // Prepare data for saving
  const targets = [
    { kinerja_fisik: targetData.value.tw1.kinerja_fisik, keuangan: targetData.value.tw1.keuangan },
    { kinerja_fisik: targetData.value.tw2.kinerja_fisik, keuangan: targetData.value.tw2.keuangan },
    { kinerja_fisik: targetData.value.tw3.kinerja_fisik, keuangan: targetData.value.tw3.keuangan },
    { kinerja_fisik: targetData.value.tw4.kinerja_fisik, keuangan: targetData.value.tw4.keuangan }
  ];
  
  // Determine what kind of item we are saving (bidang urusan, program, kegiatan, or subkegiatan)
  const itemType = item.kode_nomenklatur.jenis_nomenklatur;
  const route = getRouteBasedOnItemType(itemType);
  
  // Send to server
  router.post(route, {
    tugas_id: item.id,
    skpd_id: props.user?.skpd_id || props.tugas?.skpd_id,
    sumber_dana: 'APBD', // Default sumber dana
    deskripsi: 'Rencana Awal',
    tahun: props.tahunAktif?.tahun || new Date().getFullYear(),
    periode_id: selectedPeriodeId.value,
    pagu_pokok: calculateItemTotal(item), // Get appropriate total based on item type
    pagu_parsial: 0,
    pagu_perubahan: 0,
    targets: targets
  }, {
    onSuccess: () => {
      alert('Target berhasil disimpan');
      showModal.value = false;
      currentItem.value = null;
    },
    onError: (errors) => {
      alert('Gagal menyimpan target: ' + Object.values(errors).join('\n'));
    }
  });
};

// Helper to calculate total for an item based on its type
const calculateItemTotal = (item: ItemWithKodeNomenklatur) => {
  const itemType = item.kode_nomenklatur.jenis_nomenklatur;
  
  if (itemType === 1) { // Bidang urusan
    return calculateBidangUrusan.value[item.kode_nomenklatur.id] || 0;
  } else if (itemType === 2) { // Program
    return calculateProgram.value[item.kode_nomenklatur.id] || 0;
  } else if (itemType === 3) { // Kegiatan
    return calculateKegiatan.value[item.id] || 0;
  } else if (itemType === 4) { // Subkegiatan
    // For subkegiatan with specific sumber dana, return the specific amount
    const fundingData = props.dataAnggaranTerakhir?.[item.id];
    if (fundingData) {
      return Object.values(fundingData.values).reduce((sum, val) => sum + val, 0);
    }
    return 0;
  }
  return 0;
};

// Helper to determine the API endpoint based on item type
const getRouteBasedOnItemType = (itemType: number) => {
  // Using the same endpoint for all types for simplicity
  return '/rencana-awal/save-monitoring-data';
};

// Delete item action
const deleteItem = (item: ItemWithKodeNomenklatur) => {
  if (confirm(`Apakah Anda yakin ingin menghapus item ini?\n${item.kode_nomenklatur.nomenklatur}`)) {
    router.delete(`/rencana-awal/delete/${item.id}`, {
      onSuccess: () => {
        alert('Item berhasil dihapus');
      },
      onError: (errors) => {
        alert('Gagal menghapus item: ' + Object.values(errors).join('\n'));
      }
    });
  }
};

// Close modal
const closeModal = () => {
  showModal.value = false;
  currentItem.value = null;
};

// Handler for period change
const handlePeriodeChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  const newPeriodeId = target.value ? parseInt(target.value) : null;
  
  if (selectedPeriodeId.value !== newPeriodeId) {
    selectedPeriodeId.value = newPeriodeId;
    
    // Reload data with the new period
    if (props.user?.id) {
      router.visit(`/monitoring/${props.user.id}?periode_id=${newPeriodeId || ''}`, {
        preserveState: true,
        only: ['dataAnggaranTerakhir']
      });
    }
  }
};

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
    const parentBidangUrusan = props.bidangurusanTugas?.find(bu => 
      bu.kode_nomenklatur.id === parentProgram.kode_nomenklatur.details[0]?.id_bidang_urusan
    );

    if (!parentBidangUrusan) return;

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
      // Find the bidang urusan with this ID
      const bidangUrusan = props.bidangurusanTugas?.find(bu => 
        bu.kode_nomenklatur.id === parentBidangUrusanId
      );
      
      if (bidangUrusan) {
        const bidangUrusanNomenklaturId = bidangUrusan.kode_nomenklatur.id;
        if (!bidangUrusanSums[bidangUrusanNomenklaturId]) {
          bidangUrusanSums[bidangUrusanNomenklaturId] = 0;
        }
        bidangUrusanSums[bidangUrusanNomenklaturId] += calculateProgram.value[program.kode_nomenklatur.id] || 0;
      }
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
                    
                    <!-- Add period selector -->
                    <div class="ml-auto flex items-center">
                        <label for="periode-selector" class="mr-2 text-gray-700 font-medium">Pilih Periode:</label>
                        <select 
                            id="periode-selector" 
                            class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="handlePeriodeChange"
                            :value="selectedPeriodeId"
                        >
                            <option value="">Semua Periode</option>
                            <option 
                                v-for="periode in props.semuaPeriodeAktif" 
                                :key="periode.id" 
                                :value="periode.id"
                                :selected="periode.id === selectedPeriodeId"
                            >
                                {{ periode.tahap.tahap }} - {{ periode.tahun.tahun }}
                            </option>
                        </select>
                        
                        <div class="ml-4 bg-blue-50 px-4 py-2 rounded-lg">
                            <span class="font-semibold text-blue-700">Tahun Anggaran: {{ props.tahunAktif?.tahun || 'Belum ada' }}</span>
                        </div>
                    </div>
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
                            <th rowspan="3" class="border border-amber-300 px-2 py-1 bg-blue-100">AKSI</th>
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
                        <!-- Display bidang urusan from the selected urusan -->
                        <template v-for="bidangUrusan in props.bidangurusanTugas" :key="bidangUrusan.id">
                            <tr class="bg-blue-100 font-semibold">
                                <td class="p-3 border border-gray-200 text-center">{{ bidangUrusan.kode_nomenklatur.nomor_kode }}</td>
                                <td class="p-3 border border-gray-200">{{ bidangUrusan.kode_nomenklatur.nomenklatur }}</td>
                                <td class="p-3 border border-gray-200 text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.toLocaleString('id-ID') || '0' }}</td>
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
                                <td class="p-3 border border-gray-200 text-center">
                                  <div class="flex flex-col space-y-1">
                                    <button @click="fillTargets(bidangUrusan)" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                      Isi Target
                                    </button>
                                    <button @click="saveTargets(bidangUrusan)" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                      Simpan
                                    </button>
                                    <button @click="deleteItem(bidangUrusan)" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                      Hapus
                                    </button>
                                  </div>
                                </td>
                            </tr>

                            <!-- Display programs that belong to this bidang urusan -->
                            <template v-for="program in props.programTugas?.filter(p => p.kode_nomenklatur.details[0]?.id_bidang_urusan === bidangUrusan.kode_nomenklatur.id)" :key="program.id">
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
                                    <td class="p-3 border border-gray-200 text-center">
                                      <div class="flex flex-col space-y-1">
                                        <button @click="fillTargets(program)" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                          Isi Target
                                        </button>
                                        <button @click="saveTargets(program)" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                          Simpan
                                        </button>
                                        <button @click="deleteItem(program)" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                          Hapus
                                        </button>
                                      </div>
                                    </td>
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
                                        <td class="p-3 border border-gray-200 text-center">
                                          <div class="flex flex-col space-y-1">
                                            <button @click="fillTargets(kegiatan)" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                              Isi Target
                                            </button>
                                            <button @click="saveTargets(kegiatan)" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                              Simpan
                                            </button>
                                            <button @click="deleteItem(kegiatan)" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                              Hapus
                                            </button>
                                          </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Display subkegiatan data for this kegiatan with funding details -->
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
                                            <td class="p-3 border border-gray-200 text-center">
                                              <div class="flex flex-col space-y-1">
                                                <button @click="fillTargets(item.subKegiatan)" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                                  Isi Target
                                                </button>
                                                <button @click="saveTargets(item.subKegiatan)" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                                  Simpan
                                                </button>
                                                <button @click="deleteItem(item.subKegiatan)" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                                  Hapus
                                                </button>
                                              </div>
                                            </td>
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

    <!-- Modal for Target Editing -->
    <teleport to="body">
      <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-[800px] max-w-[90%] max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
              Edit Target: {{ currentItem?.kode_nomenklatur.nomenklatur }}
            </h3>
            <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <div class="mb-4">
            <p class="text-sm text-gray-600">Kode: {{ currentItem?.kode_nomenklatur.nomor_kode }}</p>
            <p class="font-medium">{{ currentItem?.kode_nomenklatur.nomenklatur }}</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Triwulan 1 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h4 class="font-bold text-blue-600 mb-2">Triwulan 1</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Kinerja Fisik (%)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw1.kinerja_fisik" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                    max="100"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Keuangan (Rp)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw1.keuangan" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                  />
                </div>
              </div>
            </div>
            
            <!-- Triwulan 2 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h4 class="font-bold text-blue-600 mb-2">Triwulan 2</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Kinerja Fisik (%)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw2.kinerja_fisik" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                    max="100"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Keuangan (Rp)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw2.keuangan" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                  />
                </div>
              </div>
            </div>
            
            <!-- Triwulan 3 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h4 class="font-bold text-blue-600 mb-2">Triwulan 3</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Kinerja Fisik (%)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw3.kinerja_fisik" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                    max="100"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Keuangan (Rp)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw3.keuangan" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                  />
                </div>
              </div>
            </div>
            
            <!-- Triwulan 4 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h4 class="font-bold text-blue-600 mb-2">Triwulan 4</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Kinerja Fisik (%)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw4.kinerja_fisik" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                    max="100"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Keuangan (Rp)</label>
                  <input 
                    type="number" 
                    v-model.number="targetData.tw4.keuangan" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="0"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button 
              @click="closeModal" 
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
            >
              Batal
            </button>
            <button 
              @click="saveTargets(currentItem as ItemWithKodeNomenklatur)" 
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              :disabled="!currentItem"
            >
              Simpan Target
            </button>
          </div>
        </div>
      </div>
    </teleport>
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
