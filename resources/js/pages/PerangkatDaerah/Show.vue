<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';

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
  urusanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number }[];
  bidangUrusanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; urusan_id: number }[];
  programList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; bidang_urusan_id: number }[];
  kegiatanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; program_id: number }[];
  subkegiatanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; kegiatan_id: number }[];
  skpdTugas: { 
    id: number; 
    kode_nomenklatur: { 
      id: number;
      nomor_kode: string;
      nomenklatur: string;
      jenis_nomenklatur: number;
    }
  }[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash || {});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/perangkatdaerah' },
  { title: `Detail ${props.user.skpd?.nama_skpd}`, href: '' },
];

// Modal state
const isModalOpen = ref(false);
const jenisNomenklatur = ref<number | null>(null);
const urusan = ref<number | null>(null);
const bidangUrusan = ref<number | null>(null);
const program = ref<number | null>(null);
const kegiatan = ref<number | null>(null);
const subkegiatan = ref<number | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);
const selectedNomenklaturId = ref<number | null>(null);

// Fixed the missing comma here
const jenisNomenklaturOptions = [
  { value: 0, label: "Urusan" },
  { value: 1, label: "Bidang Urusan" },
  { value: 2, label: "Program" },
  { value: 3, label: "Kegiatan" },
  { value: 4, label: "Sub Kegiatan" }
];

// Make sure we're only showing urusan items in the urusan dropdown
const urusanOptions = computed(() => {
  // Filter to ensure we're only getting items where jenis is 0 (Urusan)
  const filteredUrusanList = props.urusanList.filter(item => 
    item.jenis_nomenklatur === 0
  );
  
  console.log('Available urusan items:', filteredUrusanList);
  
  return filteredUrusanList.map((item) => ({
    label: `${item.nomor_kode} - ${item.nomenklatur}`,
    value: item.id
  }));
});

// Make sure bidang urusan is properly filtered based on the selected urusan
const bidangUrusanOptions = computed(() => {
  if (!urusan.value) return [];
  
  const filteredBidangList = props.bidangUrusanList.filter((item) => 
    // Ensure we only get bidang urusan items related to the selected urusan
    item.urusan_id === urusan.value && 
    // And double check that these are actually bidang urusan items
    item.jenis_nomenklatur === 1
  );
  
  console.log('Filtered bidang urusan for urusan ID', urusan.value, ':', filteredBidangList);
  
  return filteredBidangList.map((item) => ({
    label: `${item.nomor_kode} - ${item.nomenklatur}`,
    value: item.id
  }));
});

const programOptions = computed(() => {
  if (!bidangUrusan.value) return [];
  
  return props.programList
    .filter((item) => 
      item.bidang_urusan_id === bidangUrusan.value && 
      item.jenis_nomenklatur === 2
    )
    .map((item) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

const kegiatanOptions = computed(() => {
  if (!program.value) return [];
  
  return props.kegiatanList
    .filter((item) => 
      item.program_id === program.value &&
      item.jenis_nomenklatur === 3
    )
    .map((item) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

const subkegiatanOptions = computed(() => {
  if (!kegiatan.value) return [];
  
  return props.subkegiatanList
    .filter((item) => 
      item.kegiatan_id === kegiatan.value &&
      item.jenis_nomenklatur === 4
    )
    .map((item) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

// Watch for changes and update the selectedNomenklatur
watch([jenisNomenklatur, urusan, bidangUrusan, program, kegiatan, subkegiatan], () => {
  updateSelectedNomenklaturId();
});

function handleJenisChange(value: number) {
  jenisNomenklatur.value = typeof value === 'string' ? parseInt(value, 10) : value;
  urusan.value = null;
  // Reset all dependent fields
  bidangUrusan.value = null;
  program.value = null;
  kegiatan.value = null;
  subkegiatan.value = null;
  // Reset the selected nomenklatur ID
  updateSelectedNomenklaturId();
}

function updateSelectedNomenklaturId() {
  switch (jenisNomenklatur.value) {
    case 0: 
      selectedNomenklaturId.value = urusan.value;
      break;
    case 1: 
      selectedNomenklaturId.value = bidangUrusan.value;
      break;
    case 2: 
      selectedNomenklaturId.value = program.value;
      break;
    case 3: 
      selectedNomenklaturId.value = kegiatan.value;
      break;
    case 4: 
      selectedNomenklaturId.value = subkegiatan.value;
      break;
    default:
      selectedNomenklaturId.value = null;
  }
}

function isFormValid() {
  switch (jenisNomenklatur.value) {
    case 0: return !!urusan.value;
    case 1: return !!bidangUrusan.value && !!urusan.value;
    case 2: return !!program.value && !!bidangUrusan.value && !!urusan.value;
    case 3: return !!kegiatan.value && !!program.value && !!bidangUrusan.value && !!urusan.value;
    case 4: return !!subkegiatan.value && !!kegiatan.value && !!program.value && !!bidangUrusan.value && !!urusan.value;
    default: return false;
  }
}

function openModal() {
  isModalOpen.value = true;
  resetForm();
}

function closeModal() {
  isModalOpen.value = false;
  resetForm();
}

function resetForm() {
  jenisNomenklatur.value = null;
  urusan.value = null;
  bidangUrusan.value = null;
  program.value = null;
  kegiatan.value = null;
  subkegiatan.value = null;
  selectedNomenklaturId.value = null;
  error.value = null;
}

function saveTugas() {
  error.value = null;
  
  // Determine which ID to use based on the current selection type
  let kodeNomenklaturId = selectedNomenklaturId.value;
  
  if (!kodeNomenklaturId || !props.user.skpd?.id) {
    error.value = 'Silakan pilih nomenklatur dengan lengkap';
    return;
  }
  
  loading.value = true;
  
  // Debug what we're submitting
  console.log('Submitting data:', {
    skpd_id: props.user.skpd.id,
    kode_nomenklatur_id: kodeNomenklaturId,
    jenis_nomenklatur: jenisNomenklatur.value,
    is_aktif: 1
  });

  router.post('/skpdtugas', {
    skpd_id: props.user.skpd.id,
    kode_nomenklatur_id: kodeNomenklaturId,
    is_aktif: 1
  }, {
    onSuccess: (page) => {
      console.log('Success response:', page);
      closeModal();
      loading.value = false;
    },
    onError: (errors) => {
      console.error('Error response:', errors);
      loading.value = false;
      if (errors.error) {
        error.value = errors.error;
      } else if (errors.message) {
        error.value = errors.message;
      } else {
        error.value = 'Terjadi kesalahan saat menyimpan data';
      }
    }
  });
}

function deleteTugas(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
    router.delete(`/skpdtugas/${id}`);
  }
}

function getTaskLabel(task) {
  return `${task.kode_nomenklatur.nomor_kode} - ${task.kode_nomenklatur.nomenklatur}`;
}
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 12px;
  text-align: left;
  border: 1px solid #e2e8f0;
}
button {
  cursor: pointer;
}
</style>

<template>
  <Head title="Detail Perangkat Daerah" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <!-- Flash Messages -->
      <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ flash.error }}
      </div>

      <!-- Header Section -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Detail Perangkat Daerah</h2>

        <!-- SKPD Details -->
        <div class="space-y-4">
          <div class="flex justify-between">
            <span class="font-bold">Nama SKPD :</span>
            <span>{{ user.skpd?.nama_skpd || 'Tidak tersedia' }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-bold">Nama Dinas :</span>
            <span>{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-bold">No DPA :</span>
            <span>{{ user.skpd?.no_dpa || 'Tidak tersedia' }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-bold">Kode Organisasi :</span>
            <span>{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</span>
          </div>
        </div>
      </div>

      <!-- Tasks Section -->
      <div class="bg-white p-6 rounded-lg shadow-md mt-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Tugas PD</h3>
          <button class="px-4 py-2 bg-black text-white rounded hover:bg-gray-700" @click="openModal">
            + Tambah
          </button>
        </div>

        <!-- Table for Tugas PD and Aksi -->
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto">
            <thead>
              <tr>
                <th class="px-4 py-2 border">Tugas PD</th>
                <th class="px-4 py-2 border">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!props.skpdTugas || props.skpdTugas.length === 0">
                <td colspan="2" class="px-4 py-2 border text-center">Tidak ada tugas yang tersedia</td>
              </tr>
              <tr v-for="tugas in props.skpdTugas" :key="tugas.id">
                <td class="px-4 py-2 border">{{ getTaskLabel(tugas) }}</td>
                <td class="px-4 py-2 border text-center">
                  <button class="text-red-500 hover:text-red-700" @click="deleteTugas(tugas.id)">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <Button type="button" variant="outline" class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
        @click="$inertia.visit('/perangkatdaerah')">
            Kembali
        </Button>
      </div>

      <!-- Modal for Adding Tasks -->
      <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
          <h4 class="text-xl font-semibold mb-4">TAMBAHKAN TUGAS</h4>

          <!-- Error message -->
          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ error }}
          </div>

          <form @submit.prevent="saveTugas()" class="space-y-4">
            <!-- Jenis Nomenklatur Dropdown -->
            <div class="flex flex-col">
              <Label for="jenis_nomenklatur">Jenis Nomenklatur</Label>
              <select
                id="jenis_nomenklatur"
                v-model="jenisNomenklatur"
                @change="handleJenisChange($event.target.value)"
                class="border rounded px-3 py-2"
                required
              >
                <option value="" disabled selected>Pilih jenis...</option>
                <option
                  v-for="option in jenisNomenklaturOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div v-if="jenisNomenklatur != null" class="flex flex-col">
              <Label for="urusan">Urusan</Label>
              <select
                id="urusan"
                v-model="urusan"
                class="border rounded px-3 py-2"
                required
              >
                <option value="" disabled selected>Pilih Urusan</option>
                <option
                  v-for="option in urusanOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div v-if="jenisNomenklatur >= 1 && urusan" class="flex flex-col">
              <Label for="bidang_urusan">Bidang Urusan</Label>
              <select
                id="bidang_urusan"
                v-model="bidangUrusan"
                class="border rounded px-3 py-2"
                :required="jenisNomenklatur >= 1"
              >
                <option value="" disabled selected>Pilih Bidang Urusan</option>
                <option
                  v-for="option in bidangUrusanOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div v-if="jenisNomenklatur >= 2 && bidangUrusan" class="flex flex-col">
              <Label for="program">Program</Label>
              <select
                id="program"
                v-model="program"
                class="border rounded px-3 py-2"
                :required="jenisNomenklatur >= 2"
              >
                <option value="" disabled selected>Pilih Program</option>
                <option
                  v-for="option in programOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div v-if="jenisNomenklatur >= 3 && program" class="flex flex-col">
              <Label for="kegiatan">Kegiatan</Label>
              <select
                id="kegiatan"
                v-model="kegiatan"
                class="border rounded px-3 py-2"
                :required="jenisNomenklatur >= 3"
              >
                <option value="" disabled selected>Pilih Kegiatan</option>
                <option
                  v-for="option in kegiatanOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div v-if="jenisNomenklatur >= 4 && kegiatan" class="flex flex-col">
              <Label for="subkegiatan">Subkegiatan</Label>
              <select
                id="subkegiatan"
                v-model="subkegiatan"
                class="border rounded px-3 py-2"
                :required="jenisNomenklatur >= 4"
              >
                <option value="" disabled selected>Pilih Subkegiatan</option>
                <option
                  v-for="option in subkegiatanOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div class="flex justify-end space-x-2 pt-4">
              <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" @click="closeModal">
                Batal
              </button>
              <button 
                type="submit" 
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                :disabled="loading || !isFormValid()"
              >
                {{ loading ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>