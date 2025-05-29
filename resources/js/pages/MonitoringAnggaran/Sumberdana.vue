<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { computed, ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'


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

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Menampilkan status periode -->
          <div class="p-4 mb-4" :class="isPeriodeAktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
            <div class="flex items-center">
              <div class="mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="isPeriodeAktif">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <span class="font-medium">{{ periodeMessage }}</span>
            </div>
            
            <!-- Tampilkan daftar periode aktif jika ada -->
            <div v-if="props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0 && !isPeriodeAktif" class="mt-3">
              <div class="text-sm font-semibold">Periode yang sedang aktif:</div>
              <div class="flex flex-wrap gap-2 mt-1">
                <div 
                  v-for="periode in props.semuaPeriodeAktif" 
                  :key="periode.id"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                  :class="periode.tahap.tahap === 'Rencana' ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800'"
                >
                  {{ periode.tahap.tahap }}
                </div>
              </div>
            </div>
          </div>

          <div class="p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
              <div class="flex items-center mb-6">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
                
                <div class="ml-auto bg-blue-50 px-4 py-2 rounded-lg">
                  <span class="font-semibold text-blue-700">Tahun Anggaran: {{ props.tahunAktif?.tahun || 'Belum ada' }}</span>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                  <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                  <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                  <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                  <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                  <h3 class="text-sm font-medium text-gray-500 mb-2">No DPA</h3>
                  <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.no_dpa || 'Tidak tersedia' }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                  <h3 class="text-sm font-medium text-gray-500 mb-2">Total Anggaran</h3>
                  <p class="text-lg font-semibold text-green-600">Rp {{ formatCurrency(totalSeluruhAnggaran) }}</p>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ jumlahSubKegiatanDiisi }} dari {{ totalSubKegiatan }} sub kegiatan telah diisi
                  </p>
                </div>
              </div>
            </div>
          </div>
                          <table class="min-w-full border-collapse border border-gray-400">
                  <thead>
                    <tr>
                      <th class="border border-gray-400 px-4 py-2">Kode</th>
                      <th class="border border-gray-400 px-4 py-2">Sub Kegiatan</th>
                      <th class="border border-gray-400 px-4 py-2">Sumber Anggaran</th>
                      <th class="border border-gray-400 px-4 py-2">DAK</th>
                      <th class="border border-gray-400 px-4 py-2">DAK Peruntukan</th>
                      <th class="border border-gray-400 px-4 py-2">DAK Fisik</th>
                      <th class="border border-gray-400 px-4 py-2">DAK Non Fisik</th>
                      <th class="border border-gray-400 px-4 py-2">BLUD</th>
                      <th class="border border-gray-400 px-4 py-2">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in anggaranData" :key="item.id">
                      <td class="border border-gray-400 px-4 py-2">{{ item.kode }}</td>
                      <td class="border border-gray-400 px-4 py-2">{{ item.jenis_nomenklatur }}</td>
                      <td class="border border-gray-400 px-4 py-2">
                        <div class="flex flex-col space-y-2">
                          <label class="inline-flex items-center">
                            <input 
                              type="checkbox" 
                              :checked="item.sumber_anggaran.dak"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!isPeriodeAktif"
                            >
                            <span class="ml-2">DAK</span>
                            <span v-if="item.sumber_anggaran.dak" class="ml-1 text-xs text-green-600">(Terpilih)</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input 
                              type="checkbox" 
                              :checked="item.sumber_anggaran.dak_peruntukan"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak_peruntukan', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!isPeriodeAktif"
                            >
                            <span class="ml-2">DAK Peruntukan</span>
                            <span v-if="item.sumber_anggaran.dak_peruntukan" class="ml-1 text-xs text-green-600">(Terpilih)</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input 
                              type="checkbox" 
                              :checked="item.sumber_anggaran.dak_fisik"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak_fisik', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!isPeriodeAktif"
                            >
                            <span class="ml-2">DAK Fisik</span>
                            <span v-if="item.sumber_anggaran.dak_fisik" class="ml-1 text-xs text-green-600">(Terpilih)</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input 
                              type="checkbox" 
                              :checked="item.sumber_anggaran.dak_non_fisik"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak_non_fisik', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!isPeriodeAktif"
                            >
                            <span class="ml-2">DAK Non Fisik</span>
                            <span v-if="item.sumber_anggaran.dak_non_fisik" class="ml-1 text-xs text-green-600">(Terpilih)</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input 
                              type="checkbox" 
                              :checked="item.sumber_anggaran.blud"
                              @change="(e) => handleSumberAnggaranChange(item, 'blud', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!isPeriodeAktif"
                            >
                            <span class="ml-2">BLUD</span>
                            <span v-if="item.sumber_anggaran.blud" class="ml-1 text-xs text-green-600">(Terpilih)</span>
                          </label>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <div>
                          <input
                            type="number"
                            :value="item.dak"
                            @input="(e) => handleInputChange(item, 'dak', e)"
                            :disabled="!item.sumber_anggaran.dak || !isPeriodeAktif"
                            min="0"
                            class="w-20 h-10 border border-gray-300 rounded-md px-2 py-1 text-right focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'bg-gray-100 cursor-not-allowed': !isPeriodeAktif }"
                          />
                          <div v-if="item.dak > 0" class="text-xs text-green-600 mt-1">
                            Nilai saat ini: {{ formatCurrency(item.dak) }}
                          </div>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <div>
                          <input
                            type="number"
                            :value="item.dak_peruntukan"
                            @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                            :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif"
                            min="0"
                            class="w-20 h-10 border border-gray-300 rounded-md px-2 py-1 text-right focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'bg-gray-100 cursor-not-allowed': !isPeriodeAktif }"
                          />
                          <div v-if="item.dak_peruntukan > 0" class="text-xs text-green-600 mt-1">
                            Nilai saat ini: {{ formatCurrency(item.dak_peruntukan) }}
                          </div>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <div>
                          <input
                            type="number"
                            :value="item.dak_fisik"
                            @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                            :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeAktif"
                            min="0"
                            class="w-20 h-10 border border-gray-300 rounded-md px-2 py-1 text-right focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'bg-gray-100 cursor-not-allowed': !isPeriodeAktif }"
                          />
                          <div v-if="item.dak_fisik > 0" class="text-xs text-green-600 mt-1">
                            Nilai saat ini: {{ formatCurrency(item.dak_fisik) }}
                          </div>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <div>
                          <input
                            type="number"
                            :value="item.dak_non_fisik"
                            @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                            :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif"
                            min="0"
                            class="w-20 h-10 border border-gray-300 rounded-md px-2 py-1 text-right focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'bg-gray-100 cursor-not-allowed': !isPeriodeAktif }"
                          />
                          <div v-if="item.dak_non_fisik > 0" class="text-xs text-green-600 mt-1">
                            Nilai saat ini: {{ formatCurrency(item.dak_non_fisik) }}
                          </div>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <div>
                          <input
                            type="number"
                            :value="item.blud"
                            @input="(e) => handleInputChange(item, 'blud', e)"
                            :disabled="!item.sumber_anggaran.blud || !isPeriodeAktif"
                            min="0"
                            class="w-20 h-10 border border-gray-300 rounded-md px-2 py-1 text-right focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'bg-gray-100 cursor-not-allowed': !isPeriodeAktif }"
                          />
                          <div v-if="item.blud > 0" class="text-xs text-green-600 mt-1">
                            Nilai saat ini: {{ formatCurrency(item.blud) }}
                          </div>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <div class="flex flex-col gap-2">
                          <button
                            @click="saveItem(item)"
                            :disabled="!isPeriodeAktif"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                            :class="{ 'opacity-50 cursor-not-allowed': !isPeriodeAktif }"
                          >
                            Simpan
                          </button>
                          
                          <div v-if="calculateTotal(item) > 0" class="text-xs bg-blue-50 p-2 rounded">
                            <div class="font-semibold text-blue-700">Total Anggaran:</div>
                            <div class="text-blue-800">{{ formatCurrency(calculateTotal(item)) }}</div>
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
