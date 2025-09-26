<script setup lang="ts">
import TabelDetail from '@/components/Triwulan/TabelDetail.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Search, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const props = defineProps<{
  user: { id: number; name: string };
  skpd: {
    id: number; nama_skpd: string; nama_dinas: string; no_dpa: string;
    kode_organisasi: string; nama_operator: string; nip_operator: string;
    nama_kepala_skpd: string; nip_kepala_skpd: string;
    kepala_skpd: { id: number; name: string; nip: string } | null;
  };
  urusanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number }[];
  bidangUrusanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; urusan_id: number }[];
  programList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; bidang_urusan_id: number }[];
  kegiatanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; program_id: number }[];
  subkegiatanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; kegiatan_id: number }[];
  skpdTugas: {
    id: number;
    kode_nomenklatur: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number };
  }[];
  errors?: Record<string, string>;
  flash?: { success?: string; error?: string; info?: string };
}>();

// ===== Role & Access =====
const page = usePage();
const flashMessage = computed(() => (page.props as any).flash || {});

const rolesOf = (u: any): string[] => {
  const raw = Array.isArray(u?.roles) ? u.roles : (u?.role ? [u.role] : []);
  return raw.map((r: any) => String(r).toLowerCase().replace(/\s+/g, '_'));
};

// Hanya role "perangkat_daerah" (alias umum: perangkat-daerah / skpd) yang boleh tambah/hapus tugas
const isPerangkatDaerah = computed(() => {
  const u = (page.props as any)?.auth?.user ?? {};
  const roles = rolesOf(u);
  return roles.includes('perangkat_daerah') || roles.includes('perangkat-daerah') || roles.includes('skpd');
});
const isReadOnly = computed(() => !isPerangkatDaerah.value);

// ===== Breadcrumbs =====
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/manajemen-tim/perangkatdaerah' },
  { title: `Detail ${props.skpd.nama_skpd}`, href: '' },
];

// ===== Modal & Form State =====
const isModalOpen = ref(false);
const jenisNomenklatur = ref<number | null>(null);
const urusan = ref<number | null>(null);
const bidangUrusan = ref<number | null>(null);
const program = ref<number | null>(null);
const kegiatan = ref<number | null>(null);
const subkegiatan = ref<number | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

// ===== Flash visibility =====
const showFlash = ref({ success: false, error: false, info: false });

// ===== Table filter/sort/pagination =====
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref<'nomor_kode' | 'nomenklatur' | 'jenis_nomenklatur'>('nomor_kode');
const sortDirection = ref<'asc' | 'desc'>('asc');

// ===== Delete dialog state =====
const confirmDelete = ref<number | null>(null);

// ===== Tabel data computed =====
const filteredTugas = computed(() => {
  let data = [...props.skpdTugas];

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    data = data.filter(t =>
      (t.kode_nomenklatur.nomor_kode || '').toLowerCase().includes(q) ||
      (t.kode_nomenklatur.nomenklatur || '').toLowerCase().includes(q)
    );
  }

  data.sort((a, b) => {
    let aVal: any = getFieldValue(a, sortField.value);
    let bVal: any = getFieldValue(b, sortField.value);
    if (aVal == null) aVal = '';
    if (bVal == null) bVal = '';

    if (typeof aVal === 'string' && typeof bVal === 'string') {
      return sortDirection.value === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    }
    return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
  });

  return data;
});

function getFieldValue(item: any, field: string) {
  switch (field) {
    case 'nomor_kode': return item.kode_nomenklatur.nomor_kode || '';
    case 'nomenklatur': return item.kode_nomenklatur.nomenklatur || '';
    case 'jenis_nomenklatur': return item.kode_nomenklatur.jenis_nomenklatur;
    default: return item[field] || '';
  }
}

function toggleSort(field: 'nomor_kode' | 'nomenklatur' | 'jenis_nomenklatur') {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
}

function handleSearchChange() {
  currentPage.value = 1;
}

const totalPages = computed(() => Math.ceil(filteredTugas.value.length / itemsPerPage.value));
const paginatedTugas = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredTugas.value.slice(start, start + itemsPerPage.value);
});

function getJenisNomenklaturName(jenis: number): string {
  const options: Record<number, string> = {
    0: 'Urusan', 1: 'Bidang Urusan', 2: 'Program', 3: 'Kegiatan', 4: 'Sub Kegiatan'
  };
  return options[jenis] ?? 'Unknown';
}

onMounted(() => {
  if (flashMessage.value.success) { showFlash.value.success = true; setTimeout(() => (showFlash.value.success = false), 5000); }
  if (flashMessage.value.error) { showFlash.value.error = true; setTimeout(() => (showFlash.value.error = false), 5000); }
  if (flashMessage.value.info) { showFlash.value.info = true; setTimeout(() => (showFlash.value.info = false), 5000); }
});

const jenisNomenklaturOptions = [
  { value: 0, label: 'Urusan' },
  { value: 1, label: 'Bidang Urusan' },
  { value: 2, label: 'Program' },
  { value: 3, label: 'Kegiatan' },
  { value: 4, label: 'Sub Kegiatan' },
];

// ===== Cascading options =====
const urusanOptions = computed(() =>
  props.urusanList.filter(i => i.jenis_nomenklatur === 0).map(i => ({ label: `${i.nomor_kode} - ${i.nomenklatur}`, value: i.id }))
);
const bidangUrusanOptions = computed(() =>
  urusan.value
    ? props.bidangUrusanList
        .filter(i => i.jenis_nomenklatur === 1 && i.urusan_id === urusan.value)
        .map(i => ({ label: `${i.nomor_kode} - ${i.nomenklatur}`, value: i.id }))
    : []
);
const programOptions = computed(() =>
  bidangUrusan.value
    ? props.programList
        .filter(i => i.jenis_nomenklatur === 2 && i.bidang_urusan_id === bidangUrusan.value)
        .map(i => ({ label: `${i.nomor_kode} - ${i.nomenklatur}`, value: i.id }))
    : []
);
const kegiatanOptions = computed(() =>
  program.value
    ? props.kegiatanList
        .filter(i => i.jenis_nomenklatur === 3 && i.program_id === program.value)
        .map(i => ({ label: `${i.nomor_kode} - ${i.nomenklatur}`, value: i.id }))
    : []
);
const subkegiatanOptions = computed(() =>
  kegiatan.value
    ? props.subkegiatanList
        .filter(i => i.jenis_nomenklatur === 4 && i.kegiatan_id === kegiatan.value)
        .map(i => ({ label: `${i.nomor_kode} - ${i.nomenklatur}`, value: i.id }))
    : []
);

// ===== Helper: ambil label lengkap berdasarkan value (untuk tooltip & preview)
function getOptionLabelByValue(options: Array<{ label: string; value: number }>, val: number | null) {
  if (val === null) return '';
  const found = options.find(o => o.value === val);
  return found ? found.label : '';
}

// ===== Watchers reset chain =====
watch(jenisNomenklatur, () => { urusan.value = bidangUrusan.value = program.value = kegiatan.value = subkegiatan.value = null; });
watch(urusan, () => { bidangUrusan.value = program.value = kegiatan.value = subkegiatan.value = null; });
watch(bidangUrusan, () => { program.value = kegiatan.value = subkegiatan.value = null; });
watch(program, () => { kegiatan.value = subkegiatan.value = null; });
watch(kegiatan, () => { subkegiatan.value = null; });

function handleJenisChange(value: number) {
  jenisNomenklatur.value = typeof value === 'string' ? parseInt(value as any, 10) : value;
}

// Setter untuk komponen Select (string -> number)
function setJenisModel(v: string) {
  jenisNomenklatur.value = v ? parseInt(v) : null;
}
function setUrusanModel(v: string) {
  urusan.value = v ? parseInt(v) : null;
}
function setBidangUrusanModel(v: string) {
  bidangUrusan.value = v ? parseInt(v) : null;
}
function setProgramModel(v: string) {
  program.value = v ? parseInt(v) : null;
}
function setKegiatanModel(v: string) {
  kegiatan.value = v ? parseInt(v) : null;
}
function setSubkegiatanModel(v: string) {
  subkegiatan.value = v ? parseInt(v) : null;
}

function isFormValid() {
  if (jenisNomenklatur.value === null) return false;
  switch (jenisNomenklatur.value) {
    case 0: return urusan.value !== null;
    case 1: return urusan.value !== null && bidangUrusan.value !== null;
    case 2: return urusan.value !== null && bidangUrusan.value !== null && program.value !== null;
    case 3: return urusan.value !== null && bidangUrusan.value !== null && program.value !== null && kegiatan.value !== null;
    case 4: return urusan.value !== null && bidangUrusan.value !== null && program.value !== null && kegiatan.value !== null && subkegiatan.value !== null;
    default: return false;
  }
}

// ===== Guards: only Perangkat Daerah can modify =====
function openModal() {
  if (isReadOnly.value) return; // block Admin/Operator
  isModalOpen.value = true;
  resetForm();
}
function closeModal() {
  isModalOpen.value = false;
  resetForm();
}
function resetForm() {
  jenisNomenklatur.value = urusan.value = bidangUrusan.value = program.value = kegiatan.value = subkegiatan.value = null;
  error.value = null;
}

// Create
function saveTugas() {
  if (isReadOnly.value) { error.value = 'Akses ditolak: hanya Perangkat Daerah yang dapat menambahkan tugas.'; return; }
  error.value = null;

  const selectedIds: number[] = [];
  if (urusan.value) selectedIds.push(urusan.value);
  if (bidangUrusan.value) selectedIds.push(bidangUrusan.value);
  if (program.value) selectedIds.push(program.value);
  if (kegiatan.value) selectedIds.push(kegiatan.value);
  if (subkegiatan.value) selectedIds.push(subkegiatan.value);

  if (selectedIds.length === 0 || !props.skpd?.id) {
    error.value = 'Silakan pilih nomenklatur dengan lengkap';
    return;
  }

  loading.value = true;
  router.post(
    '/tugas',
    { skpd_id: props.skpd.id, nomenklatur_ids: selectedIds, is_aktif: 1 },
    {
      onSuccess: () => { closeModal(); loading.value = false; },
      onError: (errors: any) => {
        loading.value = false;
        if (errors?.error) error.value = errors.error;
        else if (errors?.message) error.value = errors.message;
        else error.value = 'Terjadi kesalahan saat menyimpan data';
      },
    }
  );
}

// Delete
function deleteTugas(id: number) {
  if (isReadOnly.value) return; // block Admin/Operator
  confirmDelete.value = id;
}
function confirmDeleteAction() {
  if (isReadOnly.value) { confirmDelete.value = null; return; }
  if (confirmDelete.value) {
    router.delete(`/tugas/${confirmDelete.value}`);
    confirmDelete.value = null;
  }
}
function cancelDelete() { confirmDelete.value = null; }

// ===== Pagination controls =====
function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++; }
function prevPage() { if (currentPage.value > 1) currentPage.value--; }
function goToPage(page: number) { if (page >= 1 && page <= totalPages.value) currentPage.value = page; }

const pageNumbers = computed(() => {
  const pages: number[] = [];
  const maxVisible = 5;
  if (totalPages.value <= maxVisible) {
    for (let i = 1; i <= totalPages.value; i++) pages.push(i);
  } else {
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);
    if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1);
    for (let i = start; i <= end; i++) pages.push(i);
  }
  return pages;
});
</script>

<style scoped>
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; text-align: left; border: 1px solid #e2e8f0; }
button { cursor: pointer; }
.notification { transition: opacity 0.5s ease-in-out; }
.notification-enter-active, .notification-leave-active { transition: opacity 0.5s; }
.notification-enter-from, .notification-leave-to { opacity: 0; }
.animate-fadeIn { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.table-row-hover:hover { background-color: #f3f4f6; transition: background-color 0.15s ease-in-out; }
</style>

<template>
  <Head title="Detail Perangkat Daerah" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 bg-gray-50 p-6">
      <!-- Flash Messages -->
      <transition name="notification">
        <div
          v-if="flashMessage.success && showFlash.success"
          class="notification mb-4 flex items-center justify-between rounded-lg border-l-4 border-green-500 bg-green-50 p-4 text-green-800 shadow-md"
        >
          <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ flashMessage.success }}
          </span>
          <button @click="showFlash.success = false" class="text-green-700 hover:text-green-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </transition>

      <transition name="notification">
        <div
          v-if="flashMessage.error && showFlash.error"
          class="notification mb-4 flex items-center justify-between rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800 shadow-md"
        >
          <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ flashMessage.error }}
          </span>
          <button @click="showFlash.error = false" class="text-red-700 hover:text-red-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </transition>

      <transition name="notification">
        <div
          v-if="flashMessage.info && showFlash.info"
          class="notification mb-4 flex items-center justify-between rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 text-blue-800 shadow-md"
        >
          <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ flashMessage.info }}
          </span>
          <button @click="showFlash.info = false" class="text-blue-700 hover:text-blue-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </transition>

      <!-- Header detail -->
      <TabelDetail :skpd="props.skpd" />

      <!-- Tugas Section -->
      <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
          <div class="flex items-center">
            <div class="mr-4 rounded-full bg-green-100 p-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-800">Tugas</h3>
              <p class="text-sm text-gray-500" v-if="isReadOnly">Mode baca saja (Admin & Operator tidak dapat mengubah data)</p>
            </div>
          </div>

          <!-- Tambah Tugas: hanya Perangkat Daerah -->
          <Button
            v-if="isPerangkatDaerah"
            class="flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white shadow-md transition-colors duration-200 hover:bg-blue-700"
            @click="openModal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Tugas
          </Button>
        </div>

        <!-- Filter/Search -->
        <div class="mb-4 flex flex-col items-center justify-between gap-4 sm:flex-row">
          <div class="relative w-full sm:w-96">
            <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
            <Input
              v-model="searchQuery"
              placeholder="Cari kode atau nomenklatur tugas..."
              class="w-full rounded-lg border-gray-300 pr-4 pl-10 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
              @input="handleSearchChange"
            />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Tampilkan:</span>
            <select v-model="itemsPerPage" class="rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
              <th class="w-16 px-6 py-3 text-center text-left text-xs font-medium tracking-wider text-gray-500 uppercase">No</th>
              <th @click="toggleSort('nomor_kode')"
                  class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                <div class="flex items-center gap-1">
                  Kode
                  <ArrowUpDown class="h-4 w-4 opacity-50" :class="{ 'text-blue-600': sortField === 'nomor_kode' }" />
                </div>
              </th>
              <th @click="toggleSort('nomenklatur')"
                  class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                <div class="flex items-center gap-1">
                  Nomenklatur
                  <ArrowUpDown class="h-4 w-4 opacity-50" :class="{ 'text-blue-600': sortField === 'nomenklatur' }" />
                </div>
              </th>
              <th @click="toggleSort('jenis_nomenklatur')"
                  class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                <div class="flex items-center gap-1">
                  Jenis
                  <ArrowUpDown class="h-4 w-4 opacity-50" :class="{ 'text-blue-600': sortField === 'jenis_nomenklatur' }" />
                </div>
              </th>
              <th v-if="isPerangkatDaerah" class="w-24 px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase">Aksi</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-if="paginatedTugas.length === 0">
              <td :colspan="isPerangkatDaerah ? 5 : 4" class="px-6 py-4 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center py-6">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  <p class="text-lg font-medium">Tidak ada data tugas</p>
                  <p class="mt-1 text-sm text-gray-500" v-if="isPerangkatDaerah">Tambahkan tugas baru untuk perangkat daerah ini</p>
                </div>
              </td>
            </tr>

            <tr v-for="(tugas, index) in paginatedTugas" :key="tugas.id" class="table-row-hover">
              <td class="px-6 py-4 text-center text-sm whitespace-nowrap text-gray-500">
                {{ (currentPage - 1) * itemsPerPage + index + 1 }}
              </td>
              <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                {{ tugas.kode_nomenklatur.nomor_kode }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ tugas.kode_nomenklatur.nomenklatur }}
              </td>
              <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                <span
                  class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                  :class="{
                    'bg-blue-100 text-blue-800': tugas.kode_nomenklatur.jenis_nomenklatur === 0,
                    'bg-indigo-100 text-indigo-800': tugas.kode_nomenklatur.jenis_nomenklatur === 1,
                    'bg-purple-100 text-purple-800': tugas.kode_nomenklatur.jenis_nomenklatur === 2,
                    'bg-green-100 text-green-800': tugas.kode_nomenklatur.jenis_nomenklatur === 3,
                    'bg-yellow-100 text-yellow-800': tugas.kode_nomenklatur.jenis_nomenklatur === 4,
                  }"
                >
                  {{ getJenisNomenklaturName(tugas.kode_nomenklatur.jenis_nomenklatur) }}
                </span>
              </td>

              <!-- Aksi: hanya Perangkat Daerah -->
              <td v-if="isPerangkatDaerah" class="px-6 py-4 text-right text-sm whitespace-nowrap">
                <button
                  @click="deleteTugas(tugas.id)"
                  class="font-medium text-red-600 hover:text-red-900 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
                  title="Hapus tugas"
                >
                  <Trash2 class="h-5 w-5" />
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex flex-col items-center justify-between gap-4 text-sm sm:flex-row" v-if="filteredTugas.length > 0">
          <div class="text-gray-500">
            Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} -
            {{ Math.min(currentPage * itemsPerPage, filteredTugas.length) }} dari {{ filteredTugas.length }} tugas
          </div>
          <div class="flex items-center gap-1">
            <button @click="prevPage" :disabled="currentPage === 1" :class="{ 'cursor-not-allowed opacity-50': currentPage === 1 }"
                    class="rounded-md border border-gray-300 bg-white px-3 py-1 text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            <button v-for="page in pageNumbers" :key="page" @click="goToPage(page)"
                    :class="{ 'bg-blue-600 text-white': currentPage === page, 'bg-white text-gray-700': currentPage !== page }"
                    class="rounded-md border border-gray-300 px-3 py-1 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:outline-none">
              {{ page }}
            </button>

            <button @click="nextPage" :disabled="currentPage === totalPages"
                    :class="{ 'cursor-not-allowed opacity-50': currentPage === totalPages }"
                    class="rounded-md border border-gray-300 bg-white px-3 py-1 text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Tugas: hanya render saat isModalOpen (dibuka via guard) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" @keydown.esc="closeModal">
      <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <!-- Overlay (blurred background, no tint color) -->
        <div class="fixed inset-0 bg-transparent backdrop-blur-md transition-opacity" @click="closeModal" aria-hidden="true"></div>

        <!-- Panel (glassmorphism) -->
        <div class="animate-fadeIn w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white/80 backdrop-blur-xl p-8 text-left align-middle shadow-2xl ring-1 ring-black/5 transition-all">
          <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4">
            <h3 class="text-2xl leading-7 font-semibold text-gray-900">Tambah Tugas</h3>
            <button @click="closeModal"
                    class="text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Error -->
          <div v-if="error" class="mb-4 flex items-center rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ error }}
          </div>

          <!-- Form -->
          <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-5">
            <!-- Jenis -->
            <div>
              <Label for="jenis-nomenklatur" class="mb-1 block text-sm font-medium text-gray-700">Jenis Nomenklatur</Label>
              <Select :modelValue="jenisNomenklatur !== null ? String(jenisNomenklatur) : ''" @update:modelValue="setJenisModel">
                <SelectTrigger :title="getOptionLabelByValue(jenisNomenklaturOptions, jenisNomenklatur)" class="w-full h-11 rounded-xl px-4">
                  <SelectValue placeholder="Pilih Jenis Nomenklatur" />
                </SelectTrigger>
                <SelectContent class="max-h-72 w-full">
                  <SelectItem v-for="option in jenisNomenklaturOptions" :key="option.value" :value="String(option.value)">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="jenisNomenklatur !== null" class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-700 break-words">
                {{ getOptionLabelByValue(jenisNomenklaturOptions, jenisNomenklatur) }}
              </p>
            </div>

            <!-- Urusan -->
            <div v-if="jenisNomenklatur !== null">
              <Label for="urusan" class="mb-1 block text-sm font-medium text-gray-700">Urusan</Label>
              <Select :modelValue="urusan !== null ? String(urusan) : ''" @update:modelValue="setUrusanModel">
                <SelectTrigger :title="getOptionLabelByValue(urusanOptions, urusan)" class="w-full h-11 rounded-xl px-4">
                  <SelectValue placeholder="Pilih Urusan" />
                </SelectTrigger>
                <SelectContent class="max-h-80 w-full">
                  <SelectItem v-for="option in urusanOptions" :key="option.value" :value="String(option.value)">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="urusan !== null" class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-700 break-words">
                {{ getOptionLabelByValue(urusanOptions, urusan) }}
              </p>
            </div>

            <!-- Bidang Urusan -->
            <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 1 && urusan !== null">
              <Label for="bidang-urusan" class="mb-1 block text-sm font-medium text-gray-700">Bidang Urusan</Label>
              <Select :modelValue="bidangUrusan !== null ? String(bidangUrusan) : ''" @update:modelValue="setBidangUrusanModel">
                <SelectTrigger :title="getOptionLabelByValue(bidangUrusanOptions, bidangUrusan)" class="w-full h-11 rounded-xl px-4">
                  <SelectValue placeholder="Pilih Bidang Urusan" />
                </SelectTrigger>
                <SelectContent class="max-h-80 w-full">
                  <SelectItem v-for="option in bidangUrusanOptions" :key="option.value" :value="String(option.value)">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="bidangUrusan !== null" class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-700 break-words">
                {{ getOptionLabelByValue(bidangUrusanOptions, bidangUrusan) }}
              </p>
            </div>

            <!-- Program -->
            <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 2 && bidangUrusan !== null">
              <Label for="program" class="mb-1 block text-sm font-medium text-gray-700">Program</Label>
              <Select :modelValue="program !== null ? String(program) : ''" @update:modelValue="setProgramModel">
                <SelectTrigger :title="getOptionLabelByValue(programOptions, program)" class="w-full h-11 rounded-xl px-4">
                  <SelectValue placeholder="Pilih Program" />
                </SelectTrigger>
                <SelectContent class="max-h-80 w-full">
                  <SelectItem v-for="option in programOptions" :key="option.value" :value="String(option.value)">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="program !== null" class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-700 break-words">
                {{ getOptionLabelByValue(programOptions, program) }}
              </p>
            </div>

            <!-- Kegiatan -->
            <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 3 && program !== null">
              <Label for="kegiatan" class="mb-1 block text-sm font-medium text-gray-700">Kegiatan</Label>
              <Select :modelValue="kegiatan !== null ? String(kegiatan) : ''" @update:modelValue="setKegiatanModel">
                <SelectTrigger :title="getOptionLabelByValue(kegiatanOptions, kegiatan)" class="w-full h-11 rounded-xl px-4">
                  <SelectValue placeholder="Pilih Kegiatan" />
                </SelectTrigger>
                <SelectContent class="max-h-80 w-full">
                  <SelectItem v-for="option in kegiatanOptions" :key="option.value" :value="String(option.value)">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="kegiatan !== null" class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-700 break-words">
                {{ getOptionLabelByValue(kegiatanOptions, kegiatan) }}
              </p>
            </div>

            <!-- Sub Kegiatan -->
            <div v-if="jenisNomenklatur === 4 && kegiatan !== null">
              <Label for="subkegiatan" class="mb-1 block text-sm font-medium text-gray-700">Sub Kegiatan</Label>
              <Select :modelValue="subkegiatan !== null ? String(subkegiatan) : ''" @update:modelValue="setSubkegiatanModel">
                <SelectTrigger :title="getOptionLabelByValue(subkegiatanOptions, subkegiatan)" class="w-full h-11 rounded-xl px-4">
                  <SelectValue placeholder="Pilih Sub Kegiatan" />
                </SelectTrigger>
                <SelectContent class="max-h-80 w-full">
                  <SelectItem v-for="option in subkegiatanOptions" :key="option.value" :value="String(option.value)">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="subkegiatan !== null" class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-700 break-words">
                {{ getOptionLabelByValue(subkegiatanOptions, subkegiatan) }}
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-8 flex justify-end gap-3">
            <button @click="closeModal"
                    class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none">
              Batal
            </button>
            <button
              @click="saveTugas"
              :disabled="!isFormValid() || loading"
              :class="{ 'cursor-not-allowed opacity-50': !isFormValid() || loading }"
              class="inline-flex justify-center rounded-lg border border-transparent bg-blue-600 px-5 py-2.5 text-base font-semibold text-white shadow-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
            >
              <svg v-if="loading" class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Dialog Konfirmasi Hapus -->
  <Dialog :open="confirmDelete !== null" @update:open="cancelDelete">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Konfirmasi Hapus</DialogTitle>
        <DialogDescription>Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.</DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex justify-end gap-2 pt-4">
        <Button variant="outline" @click="cancelDelete">Batal</Button>
        <Button class="bg-red-600 text-white hover:bg-red-700" @click="confirmDeleteAction">
          <Trash2 class="mr-2 h-4 w-4" />
          Hapus
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
