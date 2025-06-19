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

// Add reactive state for selected period
const selectedPeriodeId = ref<number | null>(null);

// Computed untuk cek apakah periode aktif
const isPeriodeAktif = computed(() => props.periodeAktif && props.periodeAktif.length > 0);

// Initialize with the active period if available
onMounted(() => {
  if (props.periodeAktif && props.periodeAktif.length > 0) {
    selectedPeriodeId.value = props.periodeAktif[0].id;
  } else if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
    selectedPeriodeId.value = props.semuaPeriodeAktif[0].id;
  }

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
  if (!isPeriodeAktif.value) {
    alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
    return;
  }

  const target = event.target;
  if (!(target instanceof HTMLInputElement)) return;

  const value = parseInt(target.value) || 0;

  if (field in item) {
    const numericField = field as keyof Pick<
      AnggaranItem,
      'dak' | 'dak_peruntukan' | 'dak_fisik' | 'dak_non_fisik' | 'blud'
    >;
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

// Handler for period change
const handlePeriodeChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  const newPeriodeId = target.value ? parseInt(target.value) : null;

  if (selectedPeriodeId.value !== newPeriodeId) {
    selectedPeriodeId.value = newPeriodeId;

    // Reload data with the new period
    const skpdId = props.user?.skpd?.id;
    if (skpdId) {
      router.visit(`/manajemenanggaran/${skpdId}?periode_id=${newPeriodeId || ''}`, {
        preserveState: true,
        only: ['dataAnggaranTerakhir']
      });
    }
  }
};

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

</script>

<template>
  <Head title="Manajemen Anggaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">

      <!-- Header Section - Diperkecil -->
      <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="rounded-full bg-blue-100 p-2 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-600">Manajemen Anggaran</h1>
              <p class="text-sm text-gray-500">Kelola dan monitor anggaran perangkat daerah</p>
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

      <!-- Status Period Card - Diperkecil -->
      <div class="rounded-lg shadow-md overflow-hidden"
           :class="isPeriodeAktif ? 'bg-green-600' : 'border-gray-200 bg-gray-100'">
        <div class="p-3 text-white">
          <div class="flex items-center">
            <div class="mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                  :class="isPeriodeAktif ? 'text-white' : 'text-red-600'"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="isPeriodeAktif">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                  :class="isPeriodeAktif ? 'text-white' : 'text-red-600'"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="flex-1">
                      <h3 :class="isPeriodeAktif ? 'text-white font-semibold' : 'text-red-700 font-semibold'">
                        Status Periode
                      </h3>
              <p :class="isPeriodeAktif ? 'text-white/90' : 'text-red-600'">{{ periodeMessage }}</p>
            </div>
          </div>
        </div>
      </div>
              <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
                <div class="flex items-center mb-2">
                    <h2 class="text-lg font-semibold text-gray-600 mb-2">Informasi Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Perangkat Daerah</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.nama_skpd || 'Tidak tersedia' }}</p>
                    </div>
<!--
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">No NIP</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ getUserNip(user) || 'Tidak tersedia' }}</p>
                    </div> -->

                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                      <h3 class="text-xs font-medium text-emerald-700 mb-1">Total Anggaran</h3>
                      <p class="text-lg font-bold text-green-600">Rp {{ formatCurrency(totalSeluruhAnggaran) }}</p>
                      <p class="text-xs text-gray-600 mt-1">
                        {{ jumlahSubKegiatanDiisi }} dari {{ totalSubKegiatan }} sub kegiatan
                      </p>
                    </div>

                </div>
            </div>

      <!-- Budget Table - Diperkecil -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-600">Detail Anggaran Sub Kegiatan</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Kegiatan</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber Anggaran</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAU</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Peruntukan</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Fisik</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Non Fisik</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">BLUD</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(item, index) in anggaranData" :key="item.id"
                  :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                  class="hover:bg-blue-50">

                <!-- Kode -->
                <td class="px-4 py-3">
                  <div class="text-xs font-medium text-gray-500 bg-blue-100 px-2 py-1 rounded inline-block">
                    {{ item.kode }}
                  </div>
                </td>

                <!-- Sub Kegiatan -->
                <td class="px-4 py-3">
                  <div class="text-sm text-gray-500 font-medium">{{ item.jenis_nomenklatur }}</div>
                </td>

                <!-- Sumber Anggaran -->
                <td class="px-4 py-3">
                  <div class="space-y-1">
                    <label class="flex items-center text-xs">
                      <input
                        type="checkbox"
                        :checked="item.sumber_anggaran.dak"
                        @change="(e) => handleSumberAnggaranChange(item, 'dak', e)"
                        class="h-3 w-3 text-indigo-600 border-gray-300 rounded mr-2"
                        :disabled="!isPeriodeAktif"
                      >
                      DAU
                      <span v-if="item.sumber_anggaran.dak" class="ml-1 text-green-600">✓</span>
                    </label>

                    <label class="flex items-center text-xs">
                      <input
                        type="checkbox"
                        :checked="item.sumber_anggaran.dak_peruntukan"
                        @change="(e) => handleSumberAnggaranChange(item, 'dak_peruntukan', e)"
                        class="h-3 w-3 text-indigo-600 border-gray-300 rounded mr-2"
                        :disabled="!isPeriodeAktif"
                      >
                      DAK Peruntukan
                      <span v-if="item.sumber_anggaran.dak_peruntukan" class="ml-1 text-green-600">✓</span>
                    </label>

                    <label class="flex items-center text-xs">
                      <input
                        type="checkbox"
                        :checked="item.sumber_anggaran.dak_fisik"
                        @change="(e) => handleSumberAnggaranChange(item, 'dak_fisik', e)"
                        class="h-3 w-3 text-indigo-600 border-gray-300 rounded mr-2"
                        :disabled="!isPeriodeAktif"
                      >
                      DAK Fisik
                      <span v-if="item.sumber_anggaran.dak_fisik" class="ml-1 text-green-600">✓</span>
                    </label>

                    <label class="flex items-center text-xs">
                      <input
                        type="checkbox"
                        :checked="item.sumber_anggaran.dak_non_fisik"
                        @change="(e) => handleSumberAnggaranChange(item, 'dak_non_fisik', e)"
                        class="h-3 w-3 text-indigo-600 border-gray-300 rounded mr-2"
                        :disabled="!isPeriodeAktif"
                      >
                      DAK Non Fisik
                      <span v-if="item.sumber_anggaran.dak_non_fisik" class="ml-1 text-green-600">✓</span>
                    </label>

                    <label class="flex items-center text-xs">
                      <input
                        type="checkbox"
                        :checked="item.sumber_anggaran.blud"
                        @change="(e) => handleSumberAnggaranChange(item, 'blud', e)"
                        class="h-3 w-3 text-indigo-600 border-gray-300 rounded mr-2"
                        :disabled="!isPeriodeAktif"
                      >
                      BLUD
                      <span v-if="item.sumber_anggaran.blud" class="ml-1 text-green-600">✓</span>
                    </label>
                  </div>
                </td>

                <!-- DAK Amount -->
                <td class="px-4 py-3 text-center">
                  <input
                    type="number"
                    :value="item.dak"
                    @input="(e) => handleInputChange(item, 'dak', e)"
                    :disabled="!item.sumber_anggaran.dak || !isPeriodeAktif"
                    min="0"
                    class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                    :class="{ 'bg-gray-100 cursor-not-allowed': !item.sumber_anggaran.dak || !isPeriodeAktif }"
                  />
                  <div v-if="item.dak > 0" class="text-xs text-green-600 mt-1">
                    {{ formatCurrency(item.dak) }}
                  </div>
                </td>

                <!-- DAK Peruntukan Amount -->
                <td class="px-4 py-3 text-center">
                  <input
                    type="number"
                    :value="item.dak_peruntukan"
                    @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                    :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif"
                    min="0"
                    class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                    :class="{ 'bg-gray-100 cursor-not-allowed': !item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif }"
                  />
                  <div v-if="item.dak_peruntukan > 0" class="text-xs text-green-600 mt-1">
                    {{ formatCurrency(item.dak_peruntukan) }}
                  </div>
                </td>

                <!-- DAK Fisik Amount -->
                <td class="px-4 py-3 text-center">
                  <input
                    type="number"
                    :value="item.dak_fisik"
                    @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                    :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeAktif"
                    min="0"
                    class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                    :class="{ 'bg-gray-100 cursor-not-allowed': !item.sumber_anggaran.dak_fisik || !isPeriodeAktif }"
                  />
                  <div v-if="item.dak_fisik > 0" class="text-xs text-green-600 mt-1">
                    {{ formatCurrency(item.dak_fisik) }}
                  </div>
                </td>

                <!-- DAK Non Fisik Amount -->
                <td class="px-4 py-3 text-center">
                  <input
                    type="number"
                    :value="item.dak_non_fisik"
                    @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                    :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif"
                    min="0"
                    class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                    :class="{ 'bg-gray-100 cursor-not-allowed': !item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif }"
                  />
                  <div v-if="item.dak_non_fisik > 0" class="text-xs text-green-600 mt-1">
                    {{ formatCurrency(item.dak_non_fisik) }}
                  </div>
                </td>

                <!-- BLUD Amount -->
                <td class="px-4 py-3 text-center">
                  <input
                    type="number"
                    :value="item.blud"
                    @input="(e) => handleInputChange(item, 'blud', e)"
                    :disabled="!item.sumber_anggaran.blud || !isPeriodeAktif"
                    min="0"
                    class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                    :class="{ 'bg-gray-100 cursor-not-allowed': !item.sumber_anggaran.blud || !isPeriodeAktif }"
                  />
                  <div v-if="item.blud > 0" class="text-xs text-green-600 mt-1">
                    {{ formatCurrency(item.blud) }}
                  </div>
                </td>

                <!-- Action Button -->
                <td class="px-4 py-3 text-center">
                  <div class="flex flex-col items-center space-y-2">
                    <button
                      @click="saveItem(item)"
                      :disabled="!isPeriodeAktif"
                      class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                    >
                      Simpan
                    </button>

                    <div class="text-xs text-gray-600">
                      Total: <span class="font-bold text-green-600">{{ formatCurrency(calculateTotal(item)) }}</span>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>


    </div>
  </AppLayout>
</template>

<style scoped>
/* Additional custom styles if needed */
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

/* Checkbox styling */
input[type="checkbox"]:checked {
  background-color: #4f46e5;
  border-color: #4f46e5;
}

input[type="checkbox"]:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Button disabled state */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Table responsive */
@media (max-width: 768px) {
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }
}
</style>
