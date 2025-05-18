<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, } from 'vue';

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
    { title: 'Monitoring', href: '/Monitoring' },
    { title: `Monitoring Detail ${props.user.skpd?.nama_skpd}`, href: '' },
];

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

function ShowTugas(tugasId: number) {
    router.visit(` /monitoring/rencanaawal/${tugasId}`);
}

// function ShowTugas(tugasId: number) {
//   router.visit(route('monitoring.rencanaawal', { tugasId }));
// }
//  monitoring/rencanaawal/{id}

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
                <!-- <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-semibold">Tugas PD</h3>
                    <button class="rounded bg-black px-4 py-2 text-white hover:bg-gray-700" @click="openModal">Tambah Tugas</button>
                </div> -->

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
                                    <button class="text-red-500 hover:text-red-700" @click="ShowTugas(tugas.id)">Show</button>
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
                    @click="router.visit('/monitoring')"
                >
                    Kembali
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
