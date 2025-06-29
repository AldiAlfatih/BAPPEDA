<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { ArrowUpDown, Search } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import TabelDetail from '@/components/Triwulan/TabelDetail.vue';

const props = defineProps<{
    user: {
        id: number;
        name: string;
    };
    skpd: {
        id: number;
        nama_skpd: string;
        nama_dinas: string;
        no_dpa: string;
        kode_organisasi: string;
        nama_operator: string;
        nip_operator: string;
        nama_kepala_skpd: string;
        nip_kepala_skpd: string;
        kepala_skpd: {
            id: number;
            name: string;
            nip: string;
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
        };
    }[];
    errors?: Record<string, string>;
    flash?: {
        success?: string;
        error?: string;
        info?: string;
    };
}>();

const page = usePage();
const flashMessage = computed(() => {
    // Parse from Inertia shared props
    const pageProps = page.props as any;
    return pageProps.flash || {};
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Perangkat Daerah', href: '/manajemen-tim/perangkatdaerah' },
    { title: `Detail ${props.skpd.nama_skpd}`, href: '' },
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
let selectedIds: number[] = [];

// Show flash message system
const showFlash = ref({
    success: false,
    error: false,
    info: false,
});

// Table filtering and pagination state
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('nomor_kode');
const sortDirection = ref('asc');

// Computed properties for table
const filteredTugas = computed(() => {
    let data = [...props.skpdTugas];
    
    // Filter based on search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter(tugas => 
            (tugas.kode_nomenklatur.nomor_kode || '').toLowerCase().includes(query) || 
            (tugas.kode_nomenklatur.nomenklatur || '').toLowerCase().includes(query)
        );
    }
    
    // Apply sorting
    data.sort((a, b) => {
        let aVal = getFieldValue(a, sortField.value);
        let bVal = getFieldValue(b, sortField.value);
        
        // Handle null values
        if (aVal === null || aVal === undefined) aVal = '';
        if (bVal === null || bVal === undefined) bVal = '';
        
        // String comparison
        if (typeof aVal === 'string' && typeof bVal === 'string') {
            return sortDirection.value === 'asc' 
                ? aVal.localeCompare(bVal) 
                : bVal.localeCompare(aVal);
        }
        
        // Number comparison for jenis_nomenklatur
        return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
    });
    
    return data;
});

// Helper to get field value for sorting
function getFieldValue(item: any, field: string) {
    switch(field) {
        case 'nomor_kode':
            return item.kode_nomenklatur.nomor_kode || '';
        case 'nomenklatur':
            return item.kode_nomenklatur.nomenklatur || '';
        case 'jenis_nomenklatur':
            return item.kode_nomenklatur.jenis_nomenklatur;
        default:
            return item[field] || '';
    }
}

// Toggle sorting direction
function toggleSort(field: string) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
}

// Reset pagination when search changes
function handleSearchChange() {
    currentPage.value = 1;
}

// Pagination computed properties
const totalPages = computed(() => Math.ceil(filteredTugas.value.length / itemsPerPage.value));
const paginatedTugas = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredTugas.value.slice(start, end);
});

// Get jenis nomenklatur name
function getJenisNomenklaturName(jenis: number): string {
    const options = {
        0: 'Urusan',
        1: 'Bidang Urusan',
        2: 'Program',
        3: 'Kegiatan',
        4: 'Sub Kegiatan'
    };
    return options[jenis as keyof typeof options] || 'Unknown';
}

onMounted(() => {
    // Check if there are flash messages to show
    if (flashMessage.value.success) {
        showFlash.value.success = true;
        setTimeout(() => {
            showFlash.value.success = false;
        }, 5000);
    }
    if (flashMessage.value.error) {
        showFlash.value.error = true;
        setTimeout(() => {
            showFlash.value.error = false;
        }, 5000);
    }
    if (flashMessage.value.info) {
        showFlash.value.info = true;
        setTimeout(() => {
            showFlash.value.info = false;
        }, 5000);
    }
});

const jenisNomenklaturOptions = [
    { value: 0, label: 'Urusan' },
    { value: 1, label: 'Bidang Urusan' },
    { value: 2, label: 'Program' },
    { value: 3, label: 'Kegiatan' },
    { value: 4, label: 'Sub Kegiatan' },
];

// Filtered options for each dropdown
const urusanOptions = computed(() => {
    return props.urusanList
        .filter((item) => item.jenis_nomenklatur === 0)
        .map((item) => ({
            label: `${item.nomor_kode} - ${item.nomenklatur}`,
            value: item.id,
        }));
});

const bidangUrusanOptions = computed(() => {
    if (!urusan.value) return [];

    return props.bidangUrusanList
        .filter((item) => item.jenis_nomenklatur === 1 && item.urusan_id === urusan.value)
        .map((item) => ({
            label: `${item.nomor_kode} - ${item.nomenklatur}`,
            value: item.id,
        }));
});

const programOptions = computed(() => {
    if (!bidangUrusan.value) return [];

    return props.programList
        .filter((item) => item.jenis_nomenklatur === 2 && item.bidang_urusan_id === bidangUrusan.value)
        .map((item) => ({
            label: `${item.nomor_kode} - ${item.nomenklatur}`,
            value: item.id,
        }));
});

const kegiatanOptions = computed(() => {
    if (!program.value) return [];

    return props.kegiatanList
        .filter((item) => item.jenis_nomenklatur === 3 && item.program_id === program.value)
        .map((item) => ({
            label: `${item.nomor_kode} - ${item.nomenklatur}`,
            value: item.id,
        }));
});

const subkegiatanOptions = computed(() => {
    if (!kegiatan.value) return [];

    return props.subkegiatanList
        .filter((item) => item.jenis_nomenklatur === 4 && item.kegiatan_id === kegiatan.value)
        .map((item) => ({
            label: `${item.nomor_kode} - ${item.nomenklatur}`,
            value: item.id,
        }));
});

watch(jenisNomenklatur, () => {
    urusan.value = null;
    bidangUrusan.value = null;
    program.value = null;
    kegiatan.value = null;
    subkegiatan.value = null;
});

watch(urusan, () => {
    bidangUrusan.value = null;
    program.value = null;
    kegiatan.value = null;
    subkegiatan.value = null;
});

watch(bidangUrusan, () => {
    program.value = null;
    kegiatan.value = null;
    subkegiatan.value = null;
});

watch(program, () => {
    kegiatan.value = null;
    subkegiatan.value = null;
});

watch(kegiatan, () => {
    subkegiatan.value = null;
});

function handleJenisChange(value: number) {
    jenisNomenklatur.value = typeof value === 'string' ? parseInt(value, 10) : value;
}

function isFormValid() {
    if (jenisNomenklatur.value === null) return false;

    switch (jenisNomenklatur.value) {
        case 0: // Urusan
            return urusan.value !== null;
        case 1: // Bidang Urusan
            return urusan.value !== null && bidangUrusan.value !== null;
        case 2: // Program
            return urusan.value !== null && bidangUrusan.value !== null && program.value !== null;
        case 3: // Kegiatan
            return urusan.value !== null && bidangUrusan.value !== null && program.value !== null && kegiatan.value !== null;
        case 4: // Sub Kegiatan
            return (
                urusan.value !== null &&
                bidangUrusan.value !== null &&
                program.value !== null &&
                kegiatan.value !== null &&
                subkegiatan.value !== null
            );
        default:
            return false;
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
    error.value = null;
}

function saveTugas() {
    error.value = null;

    // Dapatkan semua ID nomenklatur yang dipilih sesuai hierarki
    const selectedIds = [];

    // Selalu tambahkan ID nomenklatur urusan jika dipilih
    if (urusan.value) {
        selectedIds.push(urusan.value);
    }

    // Tambahkan ID nomenklatur bidang urusan jika dipilih
    if (bidangUrusan.value) {
        selectedIds.push(bidangUrusan.value);
    }

    // Tambahkan ID nomenklatur program jika dipilih
    if (program.value) {
        selectedIds.push(program.value);
    }

    // Tambahkan ID nomenklatur kegiatan jika dipilih
    if (kegiatan.value) {
        selectedIds.push(kegiatan.value);
    }

    // Tambahkan ID nomenklatur subkegiatan jika dipilih
    if (subkegiatan.value) {
        selectedIds.push(subkegiatan.value);
    }

    if (selectedIds.length === 0 || !props.skpd?.id) {
        error.value = 'Silakan pilih nomenklatur dengan lengkap';
        return;
    }

    loading.value = true;

    router.post(
        '/tugas',
        {
            skpd_id: props.skpd.id,
            nomenklatur_ids: selectedIds,
            is_aktif: 1,
        },
        {
            onSuccess: () => {
                closeModal();
                loading.value = false;
            },
            onError: (errors) => {
                loading.value = false;
                if (errors.error) {
                    error.value = errors.error;
                } else if (errors.message) {
                    error.value = errors.message;
                } else {
                    error.value = 'Terjadi kesalahan saat menyimpan data';
                }
            },
        },
    );
}

function deleteTugas(id: number) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        router.delete(`/tugas/${id}`);
    }
}

// Pagination controls
function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
}

function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
}

function goToPage(page: number) {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
}

// Generate page numbers for pagination
const pageNumbers = computed(() => {
    const pages = [];
    const maxVisiblePages = 5;
    
    if (totalPages.value <= maxVisiblePages) {
        // Show all pages if total pages are less than or equal to maxVisiblePages
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i);
        }
    } else {
        // Logic for showing current page and adjacent pages
        let startPage = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);
        
        // Adjust startPage if we're near the end
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }
    }
    
    return pages;
});

// Function in the methods section
async function handleSubmit() {
    if (selectedIds.length === 0 || !props.skpd.id) {
        error.value = 'Pilih minimal satu item';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        await router.post('/manajemen-tim/perangkatdaerah/tugas', {
            skpd_id: props.skpd.id,
            tugas_ids: selectedIds,
            jenis_nomenklatur: jenisNomenklatur.value,
        });
        
        isModalOpen.value = false;
        selectedIds = [];
    } catch (e) {
        error.value = 'Terjadi kesalahan. Silakan coba lagi.';
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
}
th,
td {
    padding: 12px;
    text-align: left;
    border: 1px solid #e2e8f0;
}
button {
    cursor: pointer;
}
.notification {
    transition: opacity 0.5s ease-in-out;
}
.notification-enter-active,
.notification-leave-active {
    transition: opacity 0.5s;
}
.notification-enter-from,
.notification-leave-to {
    opacity: 0;
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.table-row-hover:hover {
    background-color: #f3f4f6;
    transition: background-color 0.15s ease-in-out;
}
</style>

<template>
    <Head title="Detail Perangkat Daerah" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 bg-gray-50">
            <!-- Flash Messages dengan animasi fadeout -->
            <transition name="notification">
                <div
                    v-if="flashMessage.success && showFlash.success"
                    class="notification mb-4 flex items-center justify-between rounded-lg border-l-4 border-green-500 bg-green-50 p-4 shadow-md text-green-800"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    class="notification mb-4 flex items-center justify-between rounded-lg border-l-4 border-red-500 bg-red-50 p-4 shadow-md text-red-800"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                    class="notification mb-4 flex items-center justify-between rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 shadow-md text-blue-800"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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

            <!-- Header Section with better card design -->
            <TabelDetail
                :skpd="props.skpd"
            />

            <!-- Tasks Section with improved table -->
            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                    <div class="flex items-center">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Tugas</h3>
                    </div>
                    <Button
                        class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md"
                        @click="openModal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Tugas
                    </Button>
                </div>

                <!-- Filter and Search for Tasks -->
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-between mb-4">
                    <div class="relative w-full sm:w-96">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                        <Input 
                            v-model="searchQuery" 
                            placeholder="Cari kode atau nomenklatur tugas..." 
                            class="pl-10 pr-4 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                            @input="handleSearchChange"
                        />
                    </div>
                    <div class="flex gap-2 items-center">
                        <span class="text-sm text-gray-500">Tampilkan:</span>
                        <select 
                            v-model="itemsPerPage" 
                            class="rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Table for Tugas PD and Aksi -->
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16 text-center">No</th>
                                <th 
                                    @click="toggleSort('nomor_kode')" 
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                >
                                    <div class="flex items-center gap-1">
                                        Kode
                                        <ArrowUpDown class="w-4 h-4 opacity-50" :class="{'text-blue-600': sortField === 'nomor_kode'}" />
                                    </div>
                                </th>
                                <th 
                                    @click="toggleSort('nomenklatur')" 
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                >
                                    <div class="flex items-center gap-1">
                                        Nomenklatur
                                        <ArrowUpDown class="w-4 h-4 opacity-50" :class="{'text-blue-600': sortField === 'nomenklatur'}" />
                                    </div>
                                </th>
                                <th 
                                    @click="toggleSort('jenis_nomenklatur')" 
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                >
                                    <div class="flex items-center gap-1">
                                        Jenis
                                        <ArrowUpDown class="w-4 h-4 opacity-50" :class="{'text-blue-600': sortField === 'jenis_nomenklatur'}" />
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="paginatedTugas.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-lg font-medium">Tidak ada data tugas</p>
                                        <p class="text-sm text-gray-500 mt-1">Tambahkan tugas baru untuk perangkat daerah ini</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(tugas, index) in paginatedTugas" :key="tugas.id" class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ tugas.kode_nomenklatur.nomor_kode }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ tugas.kode_nomenklatur.nomenklatur }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          :class="{
                                            'bg-blue-100 text-blue-800': tugas.kode_nomenklatur.jenis_nomenklatur === 0,
                                            'bg-indigo-100 text-indigo-800': tugas.kode_nomenklatur.jenis_nomenklatur === 1,
                                            'bg-purple-100 text-purple-800': tugas.kode_nomenklatur.jenis_nomenklatur === 2,
                                            'bg-green-100 text-green-800': tugas.kode_nomenklatur.jenis_nomenklatur === 3,
                                            'bg-yellow-100 text-yellow-800': tugas.kode_nomenklatur.jenis_nomenklatur === 4,
                                          }">
                                        {{ getJenisNomenklaturName(tugas.kode_nomenklatur.jenis_nomenklatur) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button
                                        @click="deleteTugas(tugas.id)"
                                        class="text-red-600 hover:text-red-900 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-4 text-sm" v-if="filteredTugas.length > 0">
                    <div class="text-gray-500">
                        Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredTugas.length) }} dari {{ filteredTugas.length }} tugas
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1"
                            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                            class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        
                        <button
                            v-for="page in pageNumbers"
                            :key="page"
                            @click="goToPage(page)"
                            :class="{ 'bg-blue-600 text-white': currentPage === page, 'bg-white text-gray-700': currentPage !== page }"
                            class="px-3 py-1 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                        >
                            {{ page }}
                        </button>
                        
                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                            :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                            class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for adding a task -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-30 transition-opacity" @click="closeModal"></div>

                <!-- Modal panel -->
                <div class="animate-fadeIn w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tambah Tugas</h3>
                        <button 
                            @click="closeModal" 
                            class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Error message -->
                    <div 
                        v-if="error" 
                        class="mb-4 flex items-center rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ error }}
                    </div>

                    <!-- Form fields -->
                    <div class="space-y-4">
                        <!-- Jenis Nomenklatur -->
                        <div>
                            <Label for="jenis-nomenklatur" class="mb-1 block text-sm font-medium text-gray-700">Jenis Nomenklatur</Label>
                            <select
                                id="jenis-nomenklatur"
                                v-model="jenisNomenklatur"
                                @change="(e) => handleJenisChange(Number((e.target as HTMLSelectElement).value))"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="" disabled selected>Pilih Jenis Nomenklatur</option>
                                <option v-for="option in jenisNomenklaturOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Urusan (selalu muncul) -->
                        <div v-if="jenisNomenklatur !== null">
                            <Label for="urusan" class="mb-1 block text-sm font-medium text-gray-700">Urusan</Label>
                            <select
                                id="urusan"
                                v-model="urusan"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="" disabled selected>Pilih Urusan</option>
                                <option v-for="option in urusanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Bidang Urusan (muncul jika jenis >= 1) -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 1 && urusan !== null">
                            <Label for="bidang-urusan" class="mb-1 block text-sm font-medium text-gray-700">Bidang Urusan</Label>
                            <select
                                id="bidang-urusan"
                                v-model="bidangUrusan"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="" disabled selected>Pilih Bidang Urusan</option>
                                <option v-for="option in bidangUrusanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Program (muncul jika jenis >= 2) -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 2 && bidangUrusan !== null">
                            <Label for="program" class="mb-1 block text-sm font-medium text-gray-700">Program</Label>
                            <select
                                id="program"
                                v-model="program"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="" disabled selected>Pilih Program</option>
                                <option v-for="option in programOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Kegiatan (muncul jika jenis >= 3) -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 3 && program !== null">
                            <Label for="kegiatan" class="mb-1 block text-sm font-medium text-gray-700">Kegiatan</Label>
                            <select
                                id="kegiatan"
                                v-model="kegiatan"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="" disabled selected>Pilih Kegiatan</option>
                                <option v-for="option in kegiatanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Sub Kegiatan (muncul jika jenis = 4) -->
                        <div v-if="jenisNomenklatur === 4 && kegiatan !== null">
                            <Label for="subkegiatan" class="mb-1 block text-sm font-medium text-gray-700">Sub Kegiatan</Label>
                            <select
                                id="subkegiatan"
                                v-model="subkegiatan"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="" disabled selected>Pilih Sub Kegiatan</option>
                                <option v-for="option in subkegiatanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Form actions -->
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            @click="closeModal"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Batal
                        </button>
                        <button
                            @click="saveTugas"
                            :disabled="!isFormValid() || loading"
                            :class="{ 'opacity-50 cursor-not-allowed': !isFormValid() || loading }"
                            class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <svg v-if="loading" class="h-4 w-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>