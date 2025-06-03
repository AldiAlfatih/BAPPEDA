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

// Tambahkan state untuk edit target subkegiatan
const editingTargets = ref<Record<string, any>>({});
const loadingRow = ref<string | null>(null);
const successRow = ref<string | null>(null);
const errorRow = ref<string | null>(null);

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

// Handler for period change
const handlePeriodeChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  const newPeriodeId = target.value ? parseInt(target.value) : null;
  
  if (selectedPeriodeId.value !== newPeriodeId) {
    selectedPeriodeId.value = newPeriodeId;
    
    // Reload data with the new period
    if (props.tugas?.id) {
      router.visit(`/monitoring/rencanaawal/${props.tugas.id}?periode_id=${newPeriodeId || ''}`, {
        preserveState: true,
        only: ['dataAnggaranTerakhir', 'subkegiatanTugas']
      });
    } else if (props.user?.id) {
      router.visit(`/monitoring/${props.user.id}?periode_id=${newPeriodeId || ''}`, {
        preserveState: true,
        only: ['dataAnggaranTerakhir', 'subkegiatanTugas']
      });
    }
  }
};

// Sample data for the top table

// Add a computed property to transform subkegiatan data to include bidang urusan and multiple rows per sumber dana
const formattedSubKegiatanData = computed(() => {
  const result: any[] = [];
  
  if (!props.subkegiatanTugas) {
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
        { key: 'dak', name: 'DAU' },
        { key: 'dak_peruntukan', name: 'DAU Peruntukan' },
        { key: 'dak_fisik', name: 'DAK Fisik' },
        { key: 'dak_non_fisik', name: 'DAK Non-Fisik' },
        { key: 'blud', name: 'BLUD' }
      ];
      
      // For each active funding source, create a row
      let hasActiveSource = false;
      sources.forEach(source => {
        const sourceKey = source.key as keyof typeof fundingData.sumber_anggaran;
        const valueKey = source.key as keyof typeof fundingData.values;
        
        if (fundingData.sumber_anggaran[sourceKey] && fundingData.values[valueKey] > 0) {
          hasActiveSource = true;
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
      
      // If no active sources are found but funding data exists, create a default row
      if (!hasActiveSource) {
        result.push({
          id: `${subKegiatan.id}-default`,
          subKegiatan: subKegiatan,
          kegiatan: parentKegiatan,
          program: parentProgram,
          bidangUrusan: parentBidangUrusan,
          sumberDana: 'Belum diisi',
          pokok: 0,
          parsial: 0,
          perubahan: 0
        });
      }
    } 
    // If no funding data but has monitoring, create at least one row for this subkegiatan
    else if (subKegiatan.monitoring) {
      result.push({
        id: `${subKegiatan.id}-default`,
        subKegiatan: subKegiatan,
        kegiatan: parentKegiatan,
        program: parentProgram,
        bidangUrusan: parentBidangUrusan,
        sumberDana: subKegiatan.monitoring.sumber_dana || 'Multiple',
        pokok: subKegiatan.monitoring.pagu_pokok || 0,
        parsial: subKegiatan.monitoring.pagu_parsial || 0,
        perubahan: subKegiatan.monitoring.pagu_perubahan || 0
      });
    }
    // If no funding data and no monitoring, still show the row with zero values
    else {
      result.push({
        id: `${subKegiatan.id}-default`,
        subKegiatan: subKegiatan,
        kegiatan: parentKegiatan,
        program: parentProgram,
        bidangUrusan: parentBidangUrusan,
        sumberDana: 'Belum diisi',
        pokok: 0,
        parsial: 0,
        perubahan: 0
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

// Helper untuk inisialisasi data target
function getInitialTargets(subKegiatan: any) {
  // Cek apakah sudah ada monitoring/targets
  const targets = subKegiatan.monitoring?.targets || subKegiatan.targets || [];
  // Deep copy: buat array baru dengan objek baru di dalamnya
  return [0, 1, 2, 3].map(i => ({
    kinerja_fisik: targets[i]?.kinerja_fisik || targets[i]?.kinerjaFisik || '',
    keuangan: targets[i]?.keuangan || '',
  }));
}

// Helper untuk key unik per baris subkegiatan (id + sumberDana)
function getUniqueKey(subKegiatan: any, sumberDana: string) {
  return `${subKegiatan.id}-${sumberDana}`;
}

// Fungsi untuk mulai edit
function startEdit(subKegiatan: any) {
  const uniqueKey = getUniqueKey(subKegiatan, subKegiatan.sumberDana);
  editingTargets.value[uniqueKey] = getInitialTargets(subKegiatan);
}

// Tambahkan method untuk handle input target
function onInputTarget(uniqueKey: string, idx: number, field: 'kinerja_fisik' | 'keuangan', value: any) {
  if (editingTargets.value[uniqueKey] && editingTargets.value[uniqueKey][idx]) {
    editingTargets.value[uniqueKey][idx][field] = value;
    
    // Clear any error status when user makes changes
    if (errorRow.value === uniqueKey.split('-')[0]) {
      errorRow.value = null;
    }
  }
}

// Tambahkan fungsi untuk mendapatkan ID sumber anggaran dari namanya
function getSumberAnggaranId(sumberDanaNama: string): number {
  // Mapping nama sumber dana ke ID
  const sumberDanaMapping: Record<string, number> = {
    'DAU': 1,
    'DAU Peruntukan': 2,
    'DAK Fisik': 3,
    'DAK Non Fisik': 4,
    'BLUD': 5,
    // Tambahkan semua mapping yang ada di database
    'DAK': 1 // Fallback jika ada perbedaan penamaan
  };
  
  return sumberDanaMapping[sumberDanaNama] || 1; // Default ke ID 1 jika tidak ditemukan
}


// Fungsi simpan target
async function saveTargets(subKegiatan: any) {
  loadingRow.value = subKegiatan.id;
  errorRow.value = null;
  successRow.value = null;
  try {
    const uniqueKey = getUniqueKey(subKegiatan, subKegiatan.sumberDana);
    const targets = editingTargets.value[uniqueKey];
    
    // Validate data before sending
    let hasError = false;
    targets.forEach((target: any, idx: number) => {
      if ((target.kinerja_fisik !== '' && (isNaN(target.kinerja_fisik) || target.kinerja_fisik < 0 || target.kinerja_fisik > 100)) || 
          (target.keuangan !== '' && (isNaN(target.keuangan) || target.keuangan < 0))) {
        console.error('Invalid target data', target);
        errorRow.value = subKegiatan.id;
        hasError = true;
      }
    });
    
    if (hasError) {
      console.error('Validation failed');
      loadingRow.value = null;
      return;
    }
    
    // Log data yang akan dikirim
    console.log('Mengirim data target:', {
      tugas_id: subKegiatan.id,
      skpd_id: props.tugas?.skpd_id,
      tahun: props.tahunAktif?.tahun,
      deskripsi: 'Rencana Awal',
      targets,
      sumber_anggaran_id: getSumberAnggaranId(subKegiatan.sumberDana)
    });
    
    // Kirim ke backend
    await router.post(`/rencanaawal/target/save`, {
      tugas_id: subKegiatan.id,
      skpd_id: props.tugas?.skpd_id,
      tahun: props.tahunAktif?.tahun,
      deskripsi: 'Rencana Awal',
      targets,
      sumber_anggaran_id: getSumberAnggaranId(subKegiatan.sumberDana)
    }, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (response) => {
        console.log('Response sukses:', response);
        successRow.value = subKegiatan.id;
        
        // Update monitoring/targets di frontend
        subKegiatan.monitoring = subKegiatan.monitoring || {};
        subKegiatan.monitoring.targets = [...targets]; // Make a copy to ensure reactivity
        
        // Format data for display
        subKegiatan.monitoring.targets.forEach((target: any) => {
          target.kinerja_fisik = parseFloat(target.kinerja_fisik);
          target.keuangan = parseFloat(target.keuangan);
        });
        
        delete editingTargets.value[uniqueKey];
        
        // Reload the page after a slight delay to refresh data
        setTimeout(() => {
          if (props.user?.id) {
            router.reload({ only: ['subkegiatanTugas', 'dataAnggaranTerakhir'] });
          }
        }, 500);
      },
      onError: (err) => {
        console.error('Error saat simpan:', err);
        errorRow.value = subKegiatan.id;
      },
      onFinish: () => {
        loadingRow.value = null;
        // Keep success message visible for 3 seconds
        if (successRow.value) {
          setTimeout(() => {
            if (successRow.value === subKegiatan.id) {
              successRow.value = null;
            }
          }, 3000);
        }
      }
    });
  } catch (e) {
    console.error('Exception:', e);
    errorRow.value = subKegiatan.id;
    loadingRow.value = null;
  }
}

// Fungsi hapus target
async function deleteTargets(subKegiatan: any) {
  loadingRow.value = subKegiatan.id;
  errorRow.value = null;
  successRow.value = null;
  try {
    const uniqueKey = getUniqueKey(subKegiatan, subKegiatan.sumberDana);
    
    // Ask for confirmation
    if (!confirm('Apakah Anda yakin ingin menghapus target untuk sub kegiatan ini?')) {
      loadingRow.value = null;
      return;
    }
    
    console.log('Menghapus data target:', {
      tugas_id: subKegiatan.id,
      skpd_id: props.tugas?.skpd_id,
      tahun: props.tahunAktif?.tahun,
      deskripsi: 'Rencana Awal',
      sumber_anggaran_id: getSumberAnggaranId(subKegiatan.sumberDana)
    });

    await router.post(`/rencanaawal/target/delete`, {
      tugas_id: subKegiatan.id,
      skpd_id: props.tugas?.skpd_id,
      tahun: props.tahunAktif?.tahun,
      deskripsi: 'Rencana Awal',
      sumber_anggaran_id: getSumberAnggaranId(subKegiatan.sumberDana)
    }, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (response) => {
        console.log('Response hapus sukses:', response);
        successRow.value = subKegiatan.id;
        
        // Reset form values to empty
        if (editingTargets.value[uniqueKey]) {
          editingTargets.value[uniqueKey] = [0, 1, 2, 3].map(i => ({
            kinerja_fisik: '',
            keuangan: '',
          }));
        }
        
        // Kosongkan monitoring/targets di frontend
        if (subKegiatan.monitoring) {
          subKegiatan.monitoring.targets = [];
        }
        
        // Show temporary success message
        setTimeout(() => {
          if (successRow.value === subKegiatan.id) {
            successRow.value = null;
          }
        }, 3000);
        
        // Reload the page after a slight delay to refresh data
        setTimeout(() => {
          if (props.user?.id) {
            router.reload({ only: ['subkegiatanTugas', 'dataAnggaranTerakhir'] });
          }
        }, 500);
      },
      onError: (err) => {
        console.error('Error saat hapus:', err);
        errorRow.value = subKegiatan.id;
      },
      onFinish: () => {
        loadingRow.value = null;
      }
    });
  } catch (e) {
    console.error('Exception saat hapus:', e);
    errorRow.value = subKegiatan.id;
    loadingRow.value = null;
  }
}

// Function to handle navigation
function goToMonitoringDetail() {
    router.visit(`/monitoring/${props.user?.id}`);
}

function goToCreate() {
  router.visit('/rencanaawal/create');
}

// Update ensureEditingTargets agar pakai key unik
function ensureEditingTargets() {
  const allRows = new Set<string>();
  if (formattedSubKegiatanData.value) {
    formattedSubKegiatanData.value.forEach((item: any) => {
      const uniqueKey = getUniqueKey(item.subKegiatan, item.sumberDana);
      allRows.add(uniqueKey);
      if (!editingTargets.value[uniqueKey]) {
        editingTargets.value[uniqueKey] = getInitialTargets(item.subKegiatan);
      }
    });
  }
}

onMounted(ensureEditingTargets);

watch([
  () => props.subkegiatanTugas,
  () => formattedSubKegiatanData.value
], ensureEditingTargets, { immediate: true, deep: true });
</script>


<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section (redesigned like Sumberdana) -->
            <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-100 p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-600">Rencana Kinerja</h1>
                            <p class="text-sm text-gray-500">Rencana Awal Perangkat Daerah</p>
                        </div>
                    </div>
                    
                    <!-- Add period selector -->
                    <div class="flex items-center">
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
                        
                        <div class="ml-4 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-medium text-gray-500">Tahun Anggaran</span>
                            <div class="text-lg font-bold text-blue-600 text-center">{{ props.tahunAktif?.tahun || 'Belum ada' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Card -->
            <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
                <div class="flex items-center mb-2">
                    <h2 class="text-lg font-semibold text-gray-600 mb-2">Informasi Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN</h3>
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
                </div>
            </div>

            <!-- Program table with targets -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-600">Detail Rencana Kinerja</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="text-center bg-gray-50 border-b">
                                <th rowspan="3" class="border px-2 py-1">KODE</th>
                                <th rowspan="3" class="border px-2 py-1">BIDANG URUSAN/PROGRAM/KEGIATAN/SUB KEGIATAN</th>
                                <th colspan="3" class="border px-2 py-1 bg-amber-50">PAGU ANGGARAN APBD</th>
                                <th rowspan="3" class="border px-2 py-1 bg-amber-50">SUMBER DANA</th>
                                <th colspan="8" class="border px-2 py-1 bg-blue-50">TARGET</th>
                                <th rowspan="3" class="border px-2 py-1 bg-gray-50">AKSI</th>
                            </tr>
                            <tr class="text-center bg-gray-50">
                                <th rowspan="2" class="border bg-amber-50 px-2 py-1">POKOK (RP)</th>
                                <th rowspan="2" class="border bg-amber-50 px-2 py-1">PARSIAL (RP)</th>
                                <th rowspan="2" class="border bg-amber-50 px-2 py-1">PERUBAHAN (RP)</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 1</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 2</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 3</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 4</th>
                            </tr>
                            <tr class="text-center bg-blue-50">
                                <!-- Triwulan 1 -->
                                <th class="border px-2 py-1 text-xs">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs">KEUANGAN (RP)</th>
                                <!-- Triwulan 2 -->
                                <th class="border px-2 py-1 text-xs">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs">KEUANGAN (RP)</th>
                                <!-- Triwulan 3 -->
                                <th class="border px-2 py-1 text-xs">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs">KEUANGAN (RP)</th>
                                <!-- Triwulan 4 -->
                                <th class="border px-2 py-1 text-xs">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs">KEUANGAN (RP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Display bidang urusan from the selected urusan -->
                            <template v-for="bidangUrusan in props.bidangurusanTugas" :key="bidangUrusan.id">
                                <tr class="bg-blue-50 font-semibold hover:bg-blue-100">
                                    <td class="p-2 border text-left">{{ bidangUrusan.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="p-2 border">{{ bidangUrusan.kode_nomenklatur.nomenklatur }}</td>
                                    <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-2 border text-right">0</td>
                                    <td class="p-2 border text-right">0</td>
                                    <td class="p-2 border text-left">-</td>
                                    <td class="p-2 border text-left">-</td>
                                    <td class="p-2 border text-right">-</td>
                                    <td class="p-2 border text-left">-</td>
                                    <td class="p-2 border text-right">-</td>
                                    <td class="p-2 border text-left">-</td>
                                    <td class="p-2 border text-right">-</td>
                                    <td class="p-2 border text-left">-</td>
                                    <td class="p-2 border text-right">-</td>
                                    <td></td>
                                </tr>

                                <!-- Display programs that belong to this bidang urusan -->
                                <template v-for="program in props.programTugas?.filter(p => p.kode_nomenklatur.details[0]?.id_bidang_urusan === bidangUrusan.kode_nomenklatur.id)" :key="program.id">
                                    <tr class="border bg-gray-50 hover:bg-gray-100 font-medium">
                                        <td class="p-2 border text-left">{{ program.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="p-2 border text-left">{{ program.kode_nomenklatur.nomenklatur }}</td>
                                        <td class="p-2 border text-left">{{ calculateProgram[program.kode_nomenklatur.id]?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-2 border text-right">{{ program.monitoring?.pagu_parsial?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-2 border text-right">{{ program.monitoring?.pagu_perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-2 border text-left">{{ program.monitoring?.sumber_dana || '-' }}</td>
                                        <td class="p-2 border text-left">{{ program.monitoring?.targets?.[0]?.kinerja_fisik || program.targets?.[0]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-2 border text-right">{{ program.monitoring?.targets?.[0]?.keuangan?.toLocaleString('id-ID') || program.targets?.[0]?.keuangan || '-' }}</td>
                                        <td class="p-2 border text-left">{{ program.monitoring?.targets?.[1]?.kinerja_fisik || program.targets?.[1]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-2 border text-right">{{ program.monitoring?.targets?.[1]?.keuangan?.toLocaleString('id-ID') || program.targets?.[1]?.keuangan || '-' }}</td>
                                        <td class="p-2 border text-left">{{ program.monitoring?.targets?.[2]?.kinerja_fisik || program.targets?.[2]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-2 border text-right">{{ program.monitoring?.targets?.[2]?.keuangan?.toLocaleString('id-ID') || program.targets?.[2]?.keuangan || '-' }}</td>
                                        <td class="p-2 border text-left">{{ program.monitoring?.targets?.[3]?.kinerja_fisik || program.targets?.[3]?.kinerjaFisik || '-' }}</td>
                                        <td class="p-2 border text-right">{{ program.monitoring?.targets?.[3]?.keuangan?.toLocaleString('id-ID') || program.targets?.[3]?.keuangan || '-' }}</td>
                                        <td></td>
                                    </tr>

                                    <!-- Display kegiatan for this program -->
                                    <template v-for="kegiatan in props.kegiatanTugas?.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                        <tr class="border hover:bg-gray-50">
                                            <td class="p-2 border text-left">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                                            <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-2 border text-right">{{ kegiatan.monitoring?.pagu_parsial?.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-2 border text-right">{{ kegiatan.monitoring?.pagu_perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.monitoring?.sumber_dana || '-' }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.monitoring?.targets?.[0]?.kinerja_fisik || kegiatan.targets?.[0]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-2 border text-right">{{ kegiatan.monitoring?.targets?.[0]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[0]?.keuangan || '-' }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.monitoring?.targets?.[1]?.kinerja_fisik || kegiatan.targets?.[1]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-2 border text-right">{{ kegiatan.monitoring?.targets?.[1]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[1]?.keuangan || '-' }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.monitoring?.targets?.[2]?.kinerja_fisik || kegiatan.targets?.[2]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-2 border text-right">{{ kegiatan.monitoring?.targets?.[2]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[2]?.keuangan || '-' }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.monitoring?.targets?.[3]?.kinerja_fisik || kegiatan.targets?.[3]?.kinerjaFisik || '-' }}</td>
                                            <td class="p-2 border text-right">{{ kegiatan.monitoring?.targets?.[3]?.keuangan?.toLocaleString('id-ID') || kegiatan.targets?.[3]?.keuangan || '-' }}</td>
                                            <td></td>
                                        </tr>
                                        
                                        <!-- Display subkegiatan data for this kegiatan with funding details -->
                                        <template v-for="item in formattedSubKegiatanData.filter(sk => sk.kegiatan.id === kegiatan.id)" :key="item.id">
                                            <tr class="border hover:bg-blue-50 transition-all" 
                                                :class="{
                                                    'bg-green-50': successRow === item.subKegiatan.id,
                                                    'bg-red-50': errorRow === item.subKegiatan.id,
                                                    'opacity-75': loadingRow === item.subKegiatan.id
                                                }">
                                                <td class="p-2 border text-left">
                                                    <div class="text-xs font-medium text-gray-500 bg-blue-100 px-2 py-1 rounded inline-block">
                                                        {{ item.subKegiatan.kode_nomenklatur.nomor_kode }}
                                                    </div>
                                                </td>
                                                <td class="p-2 border">{{ item.subKegiatan.kode_nomenklatur.nomenklatur }}</td>
                                                <td class="p-2 border text-right font-medium text-green-600">{{ item.pokok.toLocaleString('id-ID') }}</td>
                                                <td class="p-2 border text-right">{{ item.parsial.toLocaleString('id-ID') || '0' }}</td>
                                                <td class="p-2 border text-right">{{ item.perubahan.toLocaleString('id-ID') || '0' }}</td>
                                                <td class="p-2 border text-center">
                                                    <div class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                                        {{ item.sumberDana }}
                                                    </div>
                                                </td>
                                                <!-- Target Triwulan 1-4, Fisik & Keuangan -->
                                                <td v-for="tw in 4" :key="'fisik'+tw" class="border px-1 py-1 text-center">
                                                    <input 
                                                        type="number" 
                                                        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none" 
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[tw-1].kinerja_fisik" 
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), tw-1, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)" 
                                                        :disabled="loadingRow === item.subKegiatan.id" 
                                                        placeholder="0.00"
                                                        min="0" 
                                                        max="100"
                                                        step="0.01"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.subKegiatan.monitoring?.targets?.[tw-1]?.kinerja_fisik">
                                                        <span>Tersimpan: {{ item.subKegiatan.monitoring?.targets?.[tw-1]?.kinerja_fisik }}%</span>
                                                    </div>
                                                </td>
                                                <td v-for="tw in 4" :key="'keu'+tw" class="border px-1 py-1 text-center">
                                                    <input 
                                                        type="number" 
                                                        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none" 
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[tw-1].keuangan" 
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), tw-1, 'keuangan', ($event.target as HTMLInputElement)?.value)" 
                                                        :disabled="loadingRow === item.subKegiatan.id" 
                                                        placeholder="0"
                                                        min="0"
                                                        step="1000"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.subKegiatan.monitoring?.targets?.[tw-1]?.keuangan">
                                                        <span>Tersimpan: {{ Number(item.subKegiatan.monitoring?.targets?.[tw-1]?.keuangan).toLocaleString('id-ID') }}</span>
                                                    </div>
                                                </td>
                                                <!-- Kolom aksi -->
                                                <td class="p-2 border text-center w-40">
                                                    <div class="flex flex-col gap-1 items-center">
                                                        <div class="flex gap-1">
                                                            <button 
                                                                class="px-3 py-1 bg-green-600 text-white rounded text-xs mr-1 flex items-center transition-all hover:bg-green-700"
                                                                :class="{'opacity-50 cursor-not-allowed': loadingRow === item.subKegiatan.id}"
                                                                :disabled="loadingRow === item.subKegiatan.id" 
                                                                @click="saveTargets(item.subKegiatan)"
                                                            >
                                                                <svg v-if="loadingRow === item.subKegiatan.id" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                <span>{{ loadingRow === item.subKegiatan.id ? 'Menyimpan...' : 'Simpan' }}</span>
                                                            </button>
                                                            <button 
                                                                class="px-3 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition-all" 
                                                                :disabled="loadingRow === item.subKegiatan.id" 
                                                                @click="deleteTargets(item.subKegiatan)"
                                                            >
                                                                Hapus
                                                            </button>
                                                        </div>
                                                        <div class="text-xs">
                                                            <span v-if="successRow === item.subKegiatan.id" class="text-green-600 font-medium">
                                                                ✓ Data tersimpan
                                                            </span>
                                                            <span v-if="errorRow === item.subKegiatan.id" class="text-red-600 font-medium">
                                                                ⚠ Gagal menyimpan
                                                            </span>
                                                        </div>
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
        </div>
    </AppLayout>
</template>

<style scoped>
/* Enhanced styling to match Sumberdana.vue */
.hover\:bg-blue-50:hover {
  transition: background-color 0.2s ease-in-out;
}

input[type="number"] {
  -moz-appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Table responsive */
@media (max-width: 768px) {
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }
}
</style>