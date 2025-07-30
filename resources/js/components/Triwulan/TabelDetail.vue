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
        user?:
            | {
                  id: number;
                  name: string;
                  user_detail?: {
                      nip?: string;
                  } | null;
              }
            | undefined;
    };
    triwulanName?: string;
}

const props = defineProps<Props>();

// Helper function to get kepala SKPD name
function getKepalaName(): string {
    // First check direct property
    if (props.skpd.nama_kepala_skpd && props.skpd.nama_kepala_skpd.trim() !== '') {
        return props.skpd.nama_kepala_skpd;
    }
    
    // Then check user object
    if (props.skpd.user && props.skpd.user.name && props.skpd.user.name.trim() !== '') {
        return props.skpd.user.name;
    }
    
    return 'Tidak tersedia';
}

// Helper function to get kepala SKPD NIP
function getKepalaNip(): string {
    // First check direct property
    if (props.skpd.nip_kepala_skpd && props.skpd.nip_kepala_skpd.trim() !== '') {
        return props.skpd.nip_kepala_skpd;
    }
    
    // Then check user detail
    if (props.skpd.user?.user_detail?.nip && props.skpd.user.user_detail.nip.trim() !== '') {
        return props.skpd.user.user_detail.nip;
    }
    
    return '-';
}

// Helper function to get operator name
function getOperatorName(): string {
    if (props.skpd.nama_operator && props.skpd.nama_operator.trim() !== '') {
        return props.skpd.nama_operator;
    }
    
    return 'Tidak tersedia';
}

// Helper function to get operator NIP
function getOperatorNip(): string {
    if (props.skpd.nip_operator && props.skpd.nip_operator.trim() !== '') {
        return props.skpd.nip_operator;
    }
    
    return '-';
}
</script>

<template>
    <!-- SKPD Info -->
    <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-6 flex items-center">
            <div class="mr-4 rounded-full bg-blue-100 p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Perangkat Daerah</h3>
                <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_dinas || skpd.nama_skpd || 'Tidak tersedia' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <h3 class="mb-2 text-sm font-medium text-gray-500">Kode Organisasi</h3>
                <p class="text-lg font-semibold text-gray-500">{{ skpd.kode_organisasi || 'Tidak tersedia' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <h3 class="mb-2 text-sm font-medium text-gray-500">Kepala SKPD</h3>
                <p class="text-lg font-semibold text-gray-500">{{ getKepalaName() }}</p>
                <p class="font-mono text-sm text-gray-500">NIP: {{ getKepalaNip() }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Penanggung Jawab</h3>
                <p class="text-lg font-semibold text-gray-500">{{ getOperatorName() }}</p>
                <p class="font-mono text-sm text-gray-500">NIP: {{ getOperatorNip() }}</p>
            </div>
        </div>
    </div>
</template>
