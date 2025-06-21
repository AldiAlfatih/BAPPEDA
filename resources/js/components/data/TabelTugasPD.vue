<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Eye } from 'lucide-vue-next';

const props = defineProps<{
    skpd: {
        id: number;
        nama_dinas: string;
        nama_operator: string;
        no_dpa: string;
        kode_organisasi: string;
        nama_skpd?: string;
        user: {
            id: number;
            name: string;
            user_detail?: {
                nip?: string;
            } | null;
        };
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
    tid?: number; // Tambah parameter triwulan ID
    errors?: Record<string, string>;
    flash?: {
        success?: string;
        error?: string;
        info?: string;
    };
}>();

const showFlash = ref({
    success: false,
    error: false,
    info: false,
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

function getTaskLabel(task: { kode_nomenklatur: { nomor_kode: any; nomenklatur: any } }) {
    return `${task.kode_nomenklatur.nomor_kode} - ${task.kode_nomenklatur.nomenklatur}`;
}

function ShowTugas(tugasId: number) {
    const triwulanId = props.tid || 1; // Default ke 1 jika tidak ada
    // Ambil user ID dari URL saat ini dengan cara yang aman
    const currentPath = window.location.pathname;
    const pathParts = currentPath.split('/');
    const userId = pathParts[3] || props.skpd.user?.id || props.skpd.id;
    router.visit(route('triwulan.detail', { tid: triwulanId, id: userId, taskId: tugasId }));
}

</script>
<template>
    
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