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
    periodeStatus: boolean; // Status periode aktif/tidak
    periodeName: string; // Nama periode
    monitoringData?: Record<number, {
        sumber_anggaran: {
            dak: boolean;
            dak_peruntukan: boolean;
            dak_fisik: boolean;
            dak_non_fisik: boolean;
            blud: boolean;
        };
        nilai: {
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

// Initialize data from SKPD tasks when component is mounted
onMounted(() => {
  if (props.skpdTugas?.length) {
    // Filter only sub-kegiatan items (jenis_nomenklatur = 4)
    const subKegiatanTasks = props.skpdTugas.filter(task =>
      task.kode_nomenklatur.jenis_nomenklatur === 4
    );

    anggaranItems.value = subKegiatanTasks.map(task => {
      // Cek apakah ada data monitoring untuk task ini
      const savedData = props.monitoringData?.[task.id];
      
      return {
        id: task.id,
        kode: task.kode_nomenklatur.nomor_kode,
        jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
        sumber_anggaran: savedData ? {
          dak: savedData.sumber_anggaran.dak,
          dak_peruntukan: savedData.sumber_anggaran.dak_peruntukan,
          dak_fisik: savedData.sumber_anggaran.dak_fisik,
          dak_non_fisik: savedData.sumber_anggaran.dak_non_fisik,
          blud: savedData.sumber_anggaran.blud,
        } : {
          dak: false,
          dak_peruntukan: false,
          dak_fisik: false,
          dak_non_fisik: false,
          blud: false,
        },
        dak: savedData ? savedData.nilai.dak : 0,
        dak_peruntukan: savedData ? savedData.nilai.dak_peruntukan : 0,
        dak_fisik: savedData ? savedData.nilai.dak_fisik : 0,
        dak_non_fisik: savedData ? savedData.nilai.dak_non_fisik : 0,
        blud: savedData ? savedData.nilai.blud : 0,
      };
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
  const target = event.target;
  if (!(target instanceof HTMLInputElement)) return;

  const value = parseInt(target.value) || 0;
  if (field in item) {
    const numericField = field as keyof Pick<AnggaranItem, 'dak' | 'dak_peruntukan' | 'dak_fisik' | 'dak_non_fisik' | 'blud'>;
    item[numericField] = value;
  }
};

const saveItem = (item: AnggaranItem) => {
  // Check if period is active
  if (!props.periodeStatus) {
    alert('Tidak dapat menyimpan data. Periode tidak aktif.');
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
      alert('Terjadi kesalahan saat menyimpan data: ' + Object.values(errors).join('\n'));
      console.error(errors);
    }
  });
}

</script>

<template>
  <Head title="Manajemen Anggaran" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
              <div class="flex items-center mb-6">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
                  <div class="mt-1 flex items-center">
                    <span class="text-sm font-medium">Status Periode:</span>
                    <span v-if="periodeStatus" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Periode Aktif - {{ periodeName }}
                    </span>
                    <span v-else class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      Periode Tidak Aktif
                    </span>
                  </div>
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
                <table class="min-w-full border-collapse border border-gray-400">
                  <thead>
                    <tr>
                      <th class="border border-gray-400 px-4 py-2">Kode</th>
                      <th class="border border-gray-400 px-4 py-2">Sub Kegiatan</th>
                      <th class="border border-gray-400 px-4 py-2">Sumber Anggaran</th>
                      <th class="border border-gray-400 px-4 py-2">DAU</th>
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
                              :disabled="!periodeStatus"
                            >
                            <span class="ml-2">DAU</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input
                              type="checkbox"
                              :checked="item.sumber_anggaran.dak_peruntukan"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak_peruntukan', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!periodeStatus"
                            >
                            <span class="ml-2">DAK Peruntukan</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input
                              type="checkbox"
                              :checked="item.sumber_anggaran.dak_fisik"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak_fisik', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!periodeStatus"
                            >
                            <span class="ml-2">DAK Fisik</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input
                              type="checkbox"
                              :checked="item.sumber_anggaran.dak_non_fisik"
                              @change="(e) => handleSumberAnggaranChange(item, 'dak_non_fisik', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!periodeStatus"
                            >
                            <span class="ml-2">DAK Non Fisik</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input
                              type="checkbox"
                              :checked="item.sumber_anggaran.blud"
                              @change="(e) => handleSumberAnggaranChange(item, 'blud', e)"
                              class="form-checkbox h-4 w-4 text-blue-600"
                              :disabled="!periodeStatus"
                            >
                            <span class="ml-2">BLUD</span>
                          </label>
                        </div>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <input
                          type="number"
                          :value="item.dak"
                          @input="(e) => handleInputChange(item, 'dak', e)"
                          :disabled="!periodeStatus || !item.sumber_anggaran.dak"
                          min="0"
                          :class="[
                            'w-20 h-10 border rounded-md px-2 py-1 text-right focus:ring-1',
                            item.sumber_anggaran.dak 
                              ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' 
                              : 'border-gray-200 bg-gray-50 text-gray-500'
                          ]"
                        />
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <input
                          type="number"
                          :value="item.dak_peruntukan"
                          @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                          :disabled="!periodeStatus || !item.sumber_anggaran.dak_peruntukan"
                          min="0"
                          :class="[
                            'w-20 h-10 border rounded-md px-2 py-1 text-right focus:ring-1',
                            item.sumber_anggaran.dak_peruntukan 
                              ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' 
                              : 'border-gray-200 bg-gray-50 text-gray-500'
                          ]"
                        />
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <input
                          type="number"
                          :value="item.dak_fisik"
                          @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                          :disabled="!periodeStatus || !item.sumber_anggaran.dak_fisik"
                          min="0"
                          :class="[
                            'w-20 h-10 border rounded-md px-2 py-1 text-right focus:ring-1',
                            item.sumber_anggaran.dak_fisik 
                              ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' 
                              : 'border-gray-200 bg-gray-50 text-gray-500'
                          ]"
                        />
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <input
                          type="number"
                          :value="item.dak_non_fisik"
                          @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                          :disabled="!periodeStatus || !item.sumber_anggaran.dak_non_fisik"
                          min="0"
                          :class="[
                            'w-20 h-10 border rounded-md px-2 py-1 text-right focus:ring-1',
                            item.sumber_anggaran.dak_non_fisik 
                              ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' 
                              : 'border-gray-200 bg-gray-50 text-gray-500'
                          ]"
                        />
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <input
                          type="number"
                          :value="item.blud"
                          @input="(e) => handleInputChange(item, 'blud', e)"
                          :disabled="!periodeStatus || !item.sumber_anggaran.blud"
                          min="0"
                          :class="[
                            'w-20 h-10 border rounded-md px-2 py-1 text-right focus:ring-1',
                            item.sumber_anggaran.blud 
                              ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' 
                              : 'border-gray-200 bg-gray-50 text-gray-500'
                          ]"
                        />
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        <button
                          @click="saveItem(item)"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200"
                          :disabled="!periodeStatus"
                          :class="{ 'opacity-50 cursor-not-allowed': !periodeStatus }"
                        >
                          Simpan
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
