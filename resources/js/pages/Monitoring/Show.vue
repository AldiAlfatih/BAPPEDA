<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Eye } from 'lucide-vue-next';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        user_detail?: {
            nip?: string;
        } | null;
        skpd?: {
            nama_skpd: string;
            operator_name: string | null;
            kepala_name: string | null;
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
    const pageProps = page.props as any;
    return pageProps.flash || {};
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Monitoring', href: '/rencana-awal' },
    { title: `Monitoring Detail ${props.user.skpd?.nama_skpd}`, href: '' },
];

function getUserNip(user: { user_detail?: { nip?: string } | null; nip?: string }): string {
  if (user.user_detail && typeof user.user_detail.nip === 'string' && user.user_detail.nip.trim() !== '') {
    return user.user_detail.nip;
  }

  if (typeof user.nip === 'string' && user.nip.trim() !== '') {
    return user.nip;
  }

  return '-';
}


const showFlash = ref({
    success: false,
    error: false,
    info: false,
});

onMounted(() => {
    if (flashMessage.value.success) {
        showFlash.value.success = true;
        setTimeout(() => (showFlash.value.success = false), 5000);
    }
    if (flashMessage.value.error) {
        showFlash.value.error = true;
        setTimeout(() => (showFlash.value.error = false), 5000);
    }
    if (flashMessage.value.info) {
        showFlash.value.info = true;
        setTimeout(() => (showFlash.value.info = false), 5000);
    }
});

onMounted(() => {
  console.log('USER DETAIL', props.user.user_detail);
});

const jenisNomenklaturOptions = [
    { value: 0, label: 'Urusan' },
    { value: 1, label: 'Bidang Urusan' },
    { value: 2, label: 'Program' },
    { value: 3, label: 'Kegiatan' },
    { value: 4, label: 'Sub Kegiatan' },
];

// Filter hanya tugas dengan jenis nomenklatur = 0 (Urusan)
const urusanTugas = computed(() => {
    return props.skpdTugas.filter(tugas => tugas.kode_nomenklatur.jenis_nomenklatur === 0);
});

const urusanOptions = computed(() => {
    return props.urusanList
        .filter(item => item.jenis_nomenklatur === 0)
        .map(item => ({
            label: `${item.nomor_kode} - ${item.nomenklatur}`,
            value: item.id,
        }));
});

function ShowTugas(tugasId: number) {
    router.visit(`/rencana-awal/rencanaawal/${tugasId}`);
}

function getTaskLabel(task: { kode_nomenklatur: { nomor_kode: any; nomenklatur: any } }) {
    return `${task.kode_nomenklatur.nomor_kode} - ${task.kode_nomenklatur.nomenklatur}`;
}
</script>

<template>
    <Head title="Detail Perangkat Daerah" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <!-- Flash messages -->
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

            <!-- SKPD Info -->
            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
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
                        <p class="text-sm font-mono text-gray-500">{{ getUserNip(user) || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.operator_name || 'Tidak tersedia' }}</p>
                        <p class="text-sm font-mono text-gray-500">{{ user.skpd?.nip_operator || '-' }}</p>
                    </div>


                </div>
            </div>

            <!-- Tugas PD Table -->
            <div class="mt-6 rounded-lg bg-white p-6 shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2 text-gray-600">Tugas PD</th>
                                <th class="border px-4 py-2 w-32 text-center text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!urusanTugas || urusanTugas.length === 0">
                                <td colspan="2" class="border px-4 py-2 text-center text-gray-600">Tidak ada tugas yang tersedia</td>
                            </tr>
                            <tr v-for="tugas in urusanTugas" :key="tugas.id">
                                <td class="border px-4 py-2 text-gray-500">{{ getTaskLabel(tugas) }}</td>
                                <td class="border px-4 py-2 w-32">
                                    <div class="flex justify-center">
                                        <button
                                            class="flex items-center gap-1 bg-orange-500 hover:bg-orange-700 text-white text-sm font-medium px-3 py-1 rounded"
                                            @click="ShowTugas(tugas.id)"
                                        >
                                            <Eye class="w-4 h-4 mr-1" />
                                            Detail
                                        </button>
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
