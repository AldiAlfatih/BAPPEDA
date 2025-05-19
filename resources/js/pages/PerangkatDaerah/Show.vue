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
        <div class="flex flex-col gap-4 p-4">
            <!-- Flash Messages dengan animasi fadeout -->
            <transition name="notification">
                <div
                    v-if="flashMessage.success && showFlash.success"
                    class="notification mb-4 flex items-center justify-between rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700"
                >
                    <span>{{ flashMessage.success }}</span>
                    <button @click="showFlash.success = false" class="text-green-700 hover:text-green-900">×</button>
                </div>
            </transition>

            <transition name="notification">
                <div
                    v-if="flashMessage.error && showFlash.error"
                    class="notification mb-4 flex items-center justify-between rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700"
                >
                    <span>{{ flashMessage.error }}</span>
                    <button @click="showFlash.error = false" class="text-red-700 hover:text-red-900">×</button>
                </div>
            </transition>

            <transition name="notification">
                <div
                    v-if="flashMessage.info && showFlash.info"
                    class="notification mb-4 flex items-center justify-between rounded border border-blue-400 bg-blue-100 px-4 py-3 text-blue-700"
                >
                    <span>{{ flashMessage.info }}</span>
                    <button @click="showFlash.info = false" class="text-blue-700 hover:text-blue-900">×</button>
                </div>
            </transition>

            <!-- Header Section -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-2xl font-semibold">Detail Perangkat Daerah</h2>

                <div class="flex justify-between">
                    <span class="font-bold">Nama SKPD:</span>
                    <span>{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="font-bold">Kode Organisasi :</span>
                    <span>{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="font-bold">No DPA :</span>
                    <span>{{ user.skpd?.no_dpa || 'Tidak tersedia' }}</span>
                </div>

                <!-- SKPD Details -->
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="font-bold">Kepala SKPD :</span>
                        <span>{{ user.skpd?.nama_skpd || 'Tidak tersedia' }}</span>
                    </div>
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="mt-6 rounded-lg bg-white p-6 shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-semibold">Tugas PD</h3>
                    <button class="rounded bg-black px-4 py-2 text-white hover:bg-gray-700" @click="openModal">Tambah Tugas</button>
                </div>

                <!-- Table for Tugas PD and Aksi -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2">Tugas PD</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!props.skpdTugas || props.skpdTugas.length === 0">
                                <td colspan="2" class="border px-4 py-2 text-center">Tidak ada tugas yang tersedia</td>
                            </tr>
                            <tr v-for="tugas in props.skpdTugas" :key="tugas.id">
                                <td class="border px-4 py-2">{{ getTaskLabel(tugas) }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <button class="text-red-500 hover:text-red-700" @click="deleteTugas(tugas.id)">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <Button
                    type="button"
                    variant="outline"
                    class="rounded bg-gray-600 px-6 py-2 text-white hover:bg-gray-700"
                    @click="router.visit('/perangkatdaerah')"
                >
                    Kembali
                </Button>
            </div>

            <!-- Modal for Adding Tasks -->
            <div v-if="isModalOpen" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-gray-600">
                <div class="w-1/3 rounded-lg bg-white p-6 shadow-md">
                    <h4 class="mb-4 text-xl font-semibold">TAMBAHKAN TUGAS</h4>

                    <!-- Error message -->
                    <div v-if="error" class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                        {{ error }}
                    </div>

                    <form @submit.prevent="saveTugas()" class="space-y-4">
                        <!-- Jenis Nomenklatur Dropdown -->
                        <div class="flex flex-col">
                            <Label for="jenis_nomenklatur">Jenis Nomenklatur</Label>
                            <select
                                id="jenis_nomenklatur"
                                v-model="jenisNomenklatur"
                                @change="handleJenisChange(Number(($event.target as HTMLSelectElement).value))"
                                class="rounded border px-3 py-2"
                                required
                            >
                                <option value="" disabled selected>Pilih jenis...</option>
                                <option v-for="option in jenisNomenklaturOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Urusan Dropdown (Always visible if jenisNomenklatur is selected) -->
                        <div v-if="jenisNomenklatur !== null" class="flex flex-col">
                            <Label for="urusan">Urusan</Label>
                            <select id="urusan" v-model="urusan" class="rounded border px-3 py-2" required>
                                <option value="" disabled selected>Pilih Urusan</option>
                                <option v-for="option in urusanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Bidang Urusan Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 1 && urusan !== null" class="flex flex-col">
                            <Label for="bidang_urusan">Bidang Urusan</Label>
                            <select id="bidang_urusan" v-model="bidangUrusan" class="rounded border px-3 py-2" required>
                                <option value="" disabled selected>Pilih Bidang Urusan</option>
                                <option v-for="option in bidangUrusanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Program Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 2 && bidangUrusan !== null" class="flex flex-col">
                            <Label for="program">Program</Label>
                            <select id="program" v-model="program" class="rounded border px-3 py-2" required>
                                <option value="" disabled selected>Pilih Program</option>
                                <option v-for="option in programOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Kegiatan Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 3 && program !== null" class="flex flex-col">
                            <Label for="kegiatan">Kegiatan</Label>
                            <select id="kegiatan" v-model="kegiatan" class="rounded border px-3 py-2" required>
                                <option value="" disabled selected>Pilih Kegiatan</option>
                                <option v-for="option in kegiatanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Sub Kegiatan Dropdown -->
                        <div v-if="jenisNomenklatur !== null && jenisNomenklatur >= 4 && kegiatan !== null" class="flex flex-col">
                            <Label for="subkegiatan">Sub Kegiatan</Label>
                            <select id="subkegiatan" v-model="subkegiatan" class="rounded border px-3 py-2" required>
                                <option value="" disabled selected>Pilih Sub Kegiatan</option>
                                <option v-for="option in subkegiatanOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Form Buttons -->
                        <div class="flex justify-end space-x-2 pt-4">
                            <button type="button" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700" @click="closeModal">Batal</button>
                            <button
                                type="submit"
                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                :disabled="loading || !isFormValid()"
                                :class="{ 'cursor-not-allowed opacity-50': loading || !isFormValid() }"
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
