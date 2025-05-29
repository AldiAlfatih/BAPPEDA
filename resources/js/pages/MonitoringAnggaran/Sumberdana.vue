<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { computed, ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Manajemen Anggaran', href: '/manajemenanggaran' },
  { title: 'Kelola Anggaran', href: '/manajemenanggaran/sumberdana' },
]


interface AnggaranItem {
  id: number
  kode: string
  jenis_nomenklatur: string
  sumber_anggaran: {
    dak: boolean
    dak_peruntukan: boolean
    dak_fisik: boolean
    dak_non_fisik: boolean
    blud: boolean
  }
  dak: number
  dak_peruntukan: number
  dak_fisik: number
  dak_non_fisik: number
  blud: number
}

const props = defineProps<{
    user: {
        skpd: {
            id: number;
            nama_skpd: string;
            nama_dinas: string;
            no_dpa: string;
            kode_organisasi: string;
        } | null;
    };
    skpdTugas?: Array<{
        id: number;
        kode_nomenklatur: {
            id: number;
            nomor_kode: string;
            nomenklatur: string;
            jenis_nomenklatur: number;
        };
    }>;
    urusanList?: Array<any>;
    bidangUrusanList?: Array<any>;
    programList?: Array<any>;
    kegiatanList?: Array<any>;
    subkegiatanList?: Array<any>;
    periodeAktif?: Array<{
        id: number;
        tahap: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    }>;
    tahunAktif?: {
        id: number;
        tahun: string;
    } | null;
    semuaPeriodeAktif?: Array<{
        id: number;
        tahap: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    }>;
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
}>();

// Create a reactive array for anggaran items
const anggaranItems = ref<AnggaranItem[]>([]);

// Ref untuk status periode dan pesan error
const errorMessage = ref('');

// Computed untuk cek apakah periode aktif
const isPeriodeAktif = computed(() => props.periodeAktif && props.periodeAktif.length > 0);

// Computed untuk pesan status periode
const periodeMessage = computed(() => {
  if (isPeriodeAktif.value) {
    return `Periode "Rencana" sedang dibuka untuk tahun ${props.periodeAktif?.[0]?.tahun?.tahun || ''}. Anda dapat mengisi sumber dana.`;
  } else {
    if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
      const periodeList = props.semuaPeriodeAktif.map(p => p.tahap.tahap).join(", ");
      return `Periode yang sedang aktif: ${periodeList}. Sumber dana hanya dapat diisi pada periode Rencana.`;
    } else {
      return 'Tidak ada periode yang aktif saat ini. Sumber dana tidak dapat diisi sampai periode Rencana dibuka.';
    }
  }
});

// Initialize data from SKPD tasks when component is mounted
onMounted(() => {
  if (props.skpdTugas?.length) {
    // Filter only sub-kegiatan items (jenis_nomenklatur = 4)
    const subKegiatanTasks = props.skpdTugas.filter(task => 
      task.kode_nomenklatur.jenis_nomenklatur === 4
    );
    
    anggaranItems.value = subKegiatanTasks.map(task => {
      // Cek apakah ada data terakhir untuk tugas ini
      const lastData = props.dataAnggaranTerakhir?.[task.id];
      
      if (lastData) {
        // Gunakan data terakhir yang sudah pernah disimpan
        return {
          id: task.id,
          kode: task.kode_nomenklatur.nomor_kode,
          jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
          sumber_anggaran: {
            dak: lastData.sumber_anggaran.dak,
            dak_peruntukan: lastData.sumber_anggaran.dak_peruntukan,
            dak_fisik: lastData.sumber_anggaran.dak_fisik,
            dak_non_fisik: lastData.sumber_anggaran.dak_non_fisik,
            blud: lastData.sumber_anggaran.blud,
          },
          dak: lastData.values.dak,
          dak_peruntukan: lastData.values.dak_peruntukan,
          dak_fisik: lastData.values.dak_fisik,
          dak_non_fisik: lastData.values.dak_non_fisik,
          blud: lastData.values.blud,
        };
      } else {
        // Gunakan data kosong jika belum ada data yang disimpan
        return {
          id: task.id,
          kode: task.kode_nomenklatur.nomor_kode,
          jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
          sumber_anggaran: {
            dak: false,
            dak_peruntukan: false,
            dak_fisik: false,
            dak_non_fisik: false,
            blud: false,
          },
          dak: 0,
          dak_peruntukan: 0,
          dak_fisik: 0,
          dak_non_fisik: 0,
          blud: 0,
        };
      }
    });
  }
});

// Use the reactive array for the table
const anggaranData = computed<AnggaranItem[]>(() => anggaranItems.value);

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const editItem = (item: AnggaranItem) => {
  // jika perlu fungsi edit
}

type SumberAnggaranKey = keyof AnggaranItem['sumber_anggaran'];

const countSelectedSources = (sumberAnggaran: AnggaranItem['sumber_anggaran']) => {
  return Object.values(sumberAnggaran).filter(value => value === true).length;
};

// Handler for checkbox changes
const handleSumberAnggaranChange = (item: AnggaranItem, key: SumberAnggaranKey, event: Event) => {
  // Periksa apakah periode sedang aktif
  if (!isPeriodeAktif.value) {
    alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
    if (event.target instanceof HTMLInputElement) {
      event.target.checked = false;
    }
    return;
  }

  const target = event.target as HTMLInputElement;
  if (target) {
    const checked = target.checked;
    
    // If trying to check a third source
    if (checked && countSelectedSources(item.sumber_anggaran) >= 2) {
      target.checked = false;
      alert('Maksimal hanya 2 sumber anggaran yang dapat dipilih per sub kegiatan!');
      return;
    }
    
    item.sumber_anggaran[key] = checked;
    
    // Reset the corresponding value if unchecked
    if (!checked) {
      item[key] = 0;
    }
  }
};

const calculateTotal = (item: AnggaranItem): number => {
  return (
    item.dak +
    item.dak_peruntukan +
    item.dak_fisik +
    item.dak_non_fisik +
    item.blud
  );
};

const handleInputChange = (item: AnggaranItem, field: keyof AnggaranItem, event: Event) => {
  // Periksa apakah periode sedang aktif
  if (!isPeriodeAktif.value) {
    alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
    return;
  }

  const target = event.target;
  if (!(target instanceof HTMLInputElement)) return;
  
  const value = parseInt(target.value) || 0;
  if (field in item) {
    const numericField = field as keyof Pick<AnggaranItem, 'dak' | 'dak_peruntukan' | 'dak_fisik' | 'dak_non_fisik' | 'blud'>;
    item[numericField] = value;
  }
};

const saveItem = (item: AnggaranItem) => {
  // Periksa apakah periode sedang aktif
  if (!isPeriodeAktif.value) {
    alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
    return;
  }

  // Validate that at least one funding source is selected
  const selectedCount = countSelectedSources(item.sumber_anggaran);
  
  if (selectedCount === 0) {
    alert('Pilih minimal satu sumber anggaran terlebih dahulu!');
    return;
  }
  
  if (selectedCount > 2) {
    alert('Maksimal hanya 2 sumber anggaran yang dapat dipilih per sub kegiatan!');
    return;
  }

  const total = calculateTotal(item);
  
  // Siapkan data untuk disimpan ke database
  const dataToSave = {
    skpd_tugas_id: item.id,
    sumber_anggaran: item.sumber_anggaran,
    values: {
      dak: item.dak,
      dak_peruntukan: item.dak_peruntukan,
      dak_fisik: item.dak_fisik,
      dak_non_fisik: item.dak_non_fisik,
      blud: item.blud
    }
  };

  // Gunakan Inertia router untuk mengirim data ke server
  router.post('/monitoring-anggaran-save', dataToSave, {
    onSuccess: () => {
      alert(`Data untuk kode ${item.kode} dengan total Rp ${formatCurrency(total)} berhasil disimpan!`);
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors).join('\n');
      alert('Terjadi kesalahan saat menyimpan data: ' + errorMessage.value);
      console.error(errors);
    }
  });
}

// Computed untuk mendapatkan total anggaran dari semua sub kegiatan
const totalSeluruhAnggaran = computed(() => {
  return anggaranItems.value.reduce((total, item) => {
    return total + calculateTotal(item);
  }, 0);
});

// Computed untuk mendapatkan jumlah sub kegiatan yang sudah diisi
const jumlahSubKegiatanDiisi = computed(() => {
  return anggaranItems.value.filter(item => calculateTotal(item) > 0).length;
});

// Computed untuk mendapatkan jumlah total sub kegiatan
const totalSubKegiatan = computed(() => {
  return anggaranItems.value.length;
});

</script>

<template>
  <Head title="Manajemen Anggaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div>
                <h1 class="text-3xl font-bold text-gray-600">Manajemen Anggaran</h1>
                <p class="text-gray-600 mt-1">Kelola dan monitor anggaran perangkat daerah</p>
              </div>
            </div>
            <div class="bg-white px-6 py-3 rounded-xl shadow-md border border-gray-200">
              <span class="text-sm font-medium text-gray-500">Tahun Anggaran</span>
              <div class="text-2xl font-bold text-indigo-600 text-center">{{ props.tahunAktif?.tahun || 'Belum ada' }}</div>
            </div>
          </div>
        </div>

        <!-- Status Period Card -->
        <div class="mb-4">
          <div class="rounded-xl shadow-lg overflow-hidden" 
               :class="isPeriodeAktif ? 'bg-gradient-to-r from-green-900 to-emerald-600' : 'bg-gradient-to-r from-red-500 to-rose-600'">
            <div class="p-3 text-white">
              <div class="flex items-center">
                <div class="mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="isPeriodeAktif">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-semibold">Status Periode</h3>
                  <p class="text-white/90">{{ periodeMessage }}</p>
                </div>
              </div>
              
              <!-- Active Periods Display -->
              <div v-if="props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0 && !isPeriodeAktif" class="mt-6 pt-4 border-t border-white/20">
                <div class="text-sm font-medium text-white/80 mb-3">Periode yang sedang aktif:</div>
                <div class="flex flex-wrap gap-2">
                  <div 
                    v-for="periode in props.semuaPeriodeAktif" 
                    :key="periode.id"
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm"
                  >
                    {{ periode.tahap.tahap }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SKPD Information Card -->
        <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
          <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-600">Informasi Perangkat Daerah</h2>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-lg border border-grey-200">
                <h3 class="text-sm font-medium text-blue-700 mb-2">Nama SKPD</h3>
                <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</p>
              </div>

              <div class="bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-lg border border-grey-200">
                <h3 class="text-sm font-medium text-purple-700 mb-2">Kode Organisasi</h3>
                <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
              </div>

              <div class="bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-lg border border-grey-200">
                <h3 class="text-sm font-medium text-amber-700 mb-2">No DPA</h3>
                <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.no_dpa || 'Tidak tersedia' }}</p>
              </div>

              <div class="bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-lg border border-grey-200">
                <h3 class="text-sm font-medium text-emerald-700 mb-2">Total Anggaran</h3>
                <p class="text-xl font-bold text-green-600">Rp {{ formatCurrency(totalSeluruhAnggaran) }}</p>
                <p class="text-sm text-gray-600 mt-1">
                  {{ jumlahSubKegiatanDiisi }} dari {{ totalSubKegiatan }} sub kegiatan
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Budget Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-600">Detail Anggaran Sub Kegiatan</h2>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub Kegiatan</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber Anggaran</th>
                  <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DAK</th>
                  <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DAK Peruntukan</th>
                  <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DAK Fisik</th>
                  <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DAK Non Fisik</th>
                  <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">BLUD</th>
                  <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item, index) in anggaranData" :key="item.id" 
                    :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                    class="hover:bg-blue-50 transition-colors duration-200">
                  
                  <!-- Kode -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 bg-blue-100 px-3 py-1 rounded-full inline-block">
                      {{ item.kode }}
                    </div>
                  </td>
                  
                  <!-- Sub Kegiatan -->
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 font-medium">{{ item.jenis_nomenklatur }}</div>
                  </td>
                  
                  <!-- Sumber Anggaran -->
                  <td class="px-6 py-4">
                    <div class="space-y-3">
                      <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <input 
                          type="checkbox" 
                          :checked="item.sumber_anggaran.dak"
                          @change="(e) => handleSumberAnggaranChange(item, 'dak', e)"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          :disabled="!isPeriodeAktif"
                        >
                        <span class="ml-3 text-sm text-gray-700">DAK</span>
                        <span v-if="item.sumber_anggaran.dak" class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✓</span>
                      </label>
                      
                      <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <input 
                          type="checkbox" 
                          :checked="item.sumber_anggaran.dak_peruntukan"
                          @change="(e) => handleSumberAnggaranChange(item, 'dak_peruntukan', e)"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          :disabled="!isPeriodeAktif"
                        >
                        <span class="ml-3 text-sm text-gray-700">DAK Peruntukan</span>
                        <span v-if="item.sumber_anggaran.dak_peruntukan" class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✓</span>
                      </label>
                      
                      <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <input 
                          type="checkbox" 
                          :checked="item.sumber_anggaran.dak_fisik"
                          @change="(e) => handleSumberAnggaranChange(item, 'dak_fisik', e)"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          :disabled="!isPeriodeAktif"
                        >
                        <span class="ml-3 text-sm text-gray-700">DAK Fisik</span>
                        <span v-if="item.sumber_anggaran.dak_fisik" class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✓</span>
                      </label>
                      
                      <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <input 
                          type="checkbox" 
                          :checked="item.sumber_anggaran.dak_non_fisik"
                          @change="(e) => handleSumberAnggaranChange(item, 'dak_non_fisik', e)"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          :disabled="!isPeriodeAktif"
                        >
                        <span class="ml-3 text-sm text-gray-700">DAK Non Fisik</span>
                        <span v-if="item.sumber_anggaran.dak_non_fisik" class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✓</span>
                      </label>
                      
                      <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <input 
                          type="checkbox" 
                          :checked="item.sumber_anggaran.blud"
                          @change="(e) => handleSumberAnggaranChange(item, 'blud', e)"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                          :disabled="!isPeriodeAktif"
                        >
                        <span class="ml-3 text-sm text-gray-700">BLUD</span>
                        <span v-if="item.sumber_anggaran.blud" class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✓</span>
                      </label>
                    </div>
                  </td>
                  
                  <!-- DAK Amount -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center space-y-2">
                      <input
                        type="number"
                        :value="item.dak"
                        @input="(e) => handleInputChange(item, 'dak', e)"
                        :disabled="!item.sumber_anggaran.dak || !isPeriodeAktif"
                        min="0"
                        class="w-32 h-10 border-2 border-gray-300 rounded-lg px-3 py-2 text-right focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                        :class="{ 'bg-gray-100 cursor-not-allowed border-gray-200': !item.sumber_anggaran.dak || !isPeriodeAktif }"
                      />
                      <div v-if="item.dak > 0" class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded">
                        {{ formatCurrency(item.dak) }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- DAK Peruntukan Amount -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center space-y-2">
                      <input
                        type="number"
                        :value="item.dak_peruntukan"
                        @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                        :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif"
                        min="0"
                        class="w-32 h-10 border-2 border-gray-300 rounded-lg px-3 py-2 text-right focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                        :class="{ 'bg-gray-100 cursor-not-allowed border-gray-200': !item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif }"
                      />
                      <div v-if="item.dak_peruntukan > 0" class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded">
                        {{ formatCurrency(item.dak_peruntukan) }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- DAK Fisik Amount -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center space-y-2">
                      <input
                        type="number"
                        :value="item.dak_fisik"
                        @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                        :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeAktif"
                        min="0"
                        class="w-32 h-10 border-2 border-gray-300 rounded-lg px-3 py-2 text-right focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                        :class="{ 'bg-gray-100 cursor-not-allowed border-gray-200': !item.sumber_anggaran.dak_fisik || !isPeriodeAktif }"
                      />
                      <div v-if="item.dak_fisik > 0" class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded">
                        {{ formatCurrency(item.dak_fisik) }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- DAK Non Fisik Amount -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center space-y-2">
                      <input
                        type="number"
                        :value="item.dak_non_fisik"
                        @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                        :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif"
                        min="0"
                        class="w-32 h-10 border-2 border-gray-300 rounded-lg px-3 py-2 text-right focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                        :class="{ 'bg-gray-100 cursor-not-allowed border-gray-200': !item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif }"
                      />
                      <div v-if="item.dak_non_fisik > 0" class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded">
                        {{ formatCurrency(item.dak_non_fisik) }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- BLUD Amount -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center space-y-2">
                      <input
                        type="number"
                        :value="item.blud"
                        @input="(e) => handleInputChange(item, 'blud', e)"
                        :disabled="!item.sumber_anggaran.blud || !isPeriodeAktif"
                        min="0"
                        class="w-32 h-10 border-2 border-gray-300 rounded-lg px-3 py-2 text-right focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                        :class="{ 'bg-gray-100 cursor-not-allowed border-gray-200': !item.sumber_anggaran.blud || !isPeriodeAktif }"
                      />
                      <div v-if="item.blud > 0" class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded">
                        {{ formatCurrency(item.blud) }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- Action Button -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center space-y-3">
                      <button
                        @click="saveItem(item)"
                        :disabled="!isPeriodeAktif"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:from-indigo-500 disabled:hover:to-purple-600"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Simpan
                      </button>
                      
                      <!-- Total per row -->
                      <div class="bg-gradient-to-r from-blue-50 to-indigo-100 px-3 py-2 rounded-lg border border-blue-200">
                        <div class="text-xs text-blue-700 font-medium">Total</div>
                        <div class="text-sm font-bold text-blue-900">
                          Rp {{ formatCurrency(calculateTotal(item)) }}
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            
            <!-- Empty State -->
            <div v-if="anggaranData.length === 0" class="text-center py-16">
              <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data sub kegiatan</h3>
              <p class="text-gray-500 mb-4">Belum ada sub kegiatan yang tersedia untuk SKPD ini.</p>
            </div>
          </div>
        </div>


            

        <!-- Footer Note -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
          <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
              <ul class="text-yellow-700 space-y-1 text-sm">
                <li>• Setiap sub kegiatan dapat memiliki maksimal 2 sumber anggaran</li>
                <li>• Input anggaran hanya dapat dilakukan pada periode "Rencana"</li>
                <li>• Pastikan untuk menyimpan setiap perubahan data yang telah diinput</li>
                <li>• Data yang telah disimpan akan tetap tersimpan meskipun periode berubah</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom scrollbar untuk table */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Smooth transitions untuk input fields */
input[type="number"]:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
}

/* Animation untuk checkbox */
input[type="checkbox"]:checked {
  animation: checkboxPulse 0.3s ease-in-out;
}

@keyframes checkboxPulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

/* Hover effects untuk table rows */
tbody tr:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Button hover animations */
button:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(79, 70, 229, 0.25);
}

button:not(:disabled):active {
  transform: translateY(0);
}
</style>