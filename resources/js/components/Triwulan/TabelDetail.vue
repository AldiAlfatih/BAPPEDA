<script setup lang="ts">
interface Props {
    skpd: {
        id: number;
        nama_dinas?: string;
        nama_skpd?: string;
        nama_operator: string;
        nama_kepala_skpd?: string;
        nip_kepala_skpd?: string;
        nip_operator?: string;
        no_dpa: string;
        kode_organisasi: string;
        user?: {
            id: number;
            name: string;
            user_detail?: {
                nip?: string;
            } | null;
        } | undefined;
    };
    triwulanName?: string;
}

const props = defineProps<Props>();
</script>

<template>
    <!-- SKPD Info -->
    <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
        <div class="flex items-center mb-6">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Perangkat Daerah</h3>
                <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_dinas || skpd.nama_skpd || 'Tidak tersedia' }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                <p class="text-lg font-semibold text-gray-500">{{ skpd.kode_organisasi || 'Tidak tersedia' }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_kepala_skpd || 'Tidak tersedia' }}</p>
                <p class="text-sm font-mono text-gray-500">NIP: {{ skpd.nip_kepala_skpd || '-' }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Penanggung Jawab</h3>
                <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_operator || 'Tidak tersedia' }}</p>
                <p class="text-sm font-mono text-gray-500">NIP: {{ skpd.nip_operator || '-' }}</p>
            </div>
        </div>
    </div>
</template>
