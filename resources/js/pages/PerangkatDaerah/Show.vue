<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

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

// Show flash message system
const showFlash = ref({
    success: false,
    error: false,
    info: false,
});

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

    if (selectedIds.length === 0 || !props.user.skpd?.id) {
        error.value = 'Silakan pilih nomenklatur dengan lengkap';
        return;
    }

    loading.value = true;

    router.post(
        '/skpdtugas',
        {
            skpd_id: props.user.skpd.id,
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
        router.delete(`/skpdtugas/${id}`);
    }
}

function getTaskLabel(task: { kode_nomenklatur: { nomor_kode: any; nomenklatur: any } }) {
    return `${task.kode_nomenklatur.nomor_kode} - ${task.kode_nomenklatur.nomenklatur}`;
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
            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Detail Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">No DPA</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.no_dpa || 'Tidak tersedia' }}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ user.skpd?.nama_skpd || 'Tidak tersedia' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tasks Section with improved table -->
            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Tugas</h3>
                    </div>
                    <button 
                        class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md" 
                        @click="openModal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Tugas
                    </button>
                </div>

                <!-- Table for Tugas PD and Aksi -->
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas PD</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="!props.skpdTugas || props.skpdTugas.length === 0">
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-gray-500">Tidak ada tugas yang tersedia</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="tugas in props.skpdTugas" :key="tugas.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ getTaskLabel(tugas) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button 
                                        class="text-red-600 hover:text-red-900 flex items-center justify-end w-full" 
                                        @click="deleteTugas(tugas.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-2 flex justify-end">
                <Button
                    type="button"
                    variant="outline"
                    class="flex items-center px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 shadow-md"
                    @click="router.visit('/perangkatdaerah')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </Button>
            </div>

            <!-- Modal for Adding Tasks with improved styling -->
            <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center">
                <div class="relative bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-xl font-bold text-gray-800">Tambahkan Tugas</h4>
                        <button 
                            @click="closeModal" 
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Error message -->
                    <div v-if="error" class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800">
                        <div class="flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ error }}
                        </div>
                    </div>

                    <form @submit.prevent="saveTugas()" class="space-y-5">
                        <!-- Jenis Nomenklatur Dropdown -->
                        <div class="space-y-2">
                            <Label for="jenis_nomenklatur" class="text-sm font-medium text-gray-700">Jenis Nomenklatur</Label>
                            <select
                                id="jenis_nomenklatur"
                                v-model="jenisNomenklatur"
                                @change="handleJenisChange(Number(($event.target as HTMLSelectElement).value))"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20"
                                required
                            >
                                <option value="" disabled selected>Pilih jenis...</option>
                                <option v-for="option in jenisNomenklaturOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Urusan Dropdown (Always visible if jenisNomenklatur is selected) -->
                        <div v-if="jenisNomenklatur !== null" class="space-y-2">
                            <Label for="urusan" class="text-sm font-medium text-gray-700">Urusan</Label>
                            <select 
                                id="urusan" 
                                v-model="urusan" 
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20" 
                                required
                            >
                                <option value="" disabled selected>Pilih Urusan</option>
                                <option v-for="option in urusanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Bidang Urusan Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 1 && urusan !== null" class="space-y-2">
                            <Label for="bidang_urusan" class="text-sm font-medium text-gray-700">Bidang Urusan</Label>
                            <select 
                                id="bidang_urusan" 
                                v-model="bidangUrusan" 
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20" 
                                required
                            >
                                <option value="" disabled selected>Pilih Bidang Urusan</option>
                                <option v-for="option in bidangUrusanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Program Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 2 && bidangUrusan !== null" class="space-y-2">
                            <Label for="program" class="text-sm font-medium text-gray-700">Program</Label>
                            <select 
                                id="program" 
                                v-model="program" 
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20" 
                                required
                            >
                                <option value="" disabled selected>Pilih Program</option>
                                <option v-for="option in programOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Kegiatan Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 3 && program !== null" class="space-y-2">
                            <Label for="kegiatan" class="text-sm font-medium text-gray-700">Kegiatan</Label>
                            <select 
                                id="kegiatan" 
                                v-model="kegiatan" 
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20" 
                                required
                            >
                                <option value="" disabled selected>Pilih Kegiatan</option>
                                <option v-for="option in kegiatanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Sub Kegiatan Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 4 && kegiatan !== null" class="space-y-2">
                            <Label for="subkegiatan" class="text-sm font-medium text-gray-700">Sub Kegiatan</Label>
                            <select 
                                id="subkegiatan" 
                                v-model="subkegiatan" 
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20" 
                                required
                            >
                                <option value="" disabled selected>Pilih Sub Kegiatan</option>
                                <option v-for="option in subkegiatanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Form Buttons -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <button 
                                type="button" 
                                class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-20 transition-colors duration-200" 
                                @click="closeModal"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors duration-200"
                                :disabled="loading || !isFormValid()"
                                :class="{ 'opacity-50 cursor-not-allowed': loading || !isFormValid() }"
                            >
                                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ loading ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>