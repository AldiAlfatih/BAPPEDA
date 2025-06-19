<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Eye } from 'lucide-vue-next';
import TabelTugasPD from '@/components/data/TabelTugasPD.vue';

const props = defineProps<{
    skpd: {
        id: number;
        nama_dinas: string;
        nama_operator: string;
        nama_kepala_skpd?: string;
        nip_kepala_skpd?: string;
        nip_operator?: string;
        no_dpa: string;
        kode_organisasi: string;
        nama_skpd?: string;
        user?: {
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

const showFlash = ref({
    success: false,
    error: false,
    info: false,
});

// State untuk tabel
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('name');
const sortDirection = ref('asc');
const showDetailId = ref<number | null>(null);
const loadingCreate = ref(false);

function getFieldValue(item: any, field: string) {
  switch(field) {
    case 'nama_dinas':
      return item.nama_dinas || '';
    case 'nama_operator':
      return item.nama_operator || '';
    case 'nama_skpd':
      return item.nama_dinas || '';
    case 'kode_organisasi':
      return item.kode_organisasi || '';
    default:
      return item[field] || '';
  }
}

const filteredData = computed(() => {
  const query = searchQuery.value.toLowerCase();
  const data = [props.skpd];
  
  if (query) {
    return data.filter(item => 
      (item.nama_dinas || '').toLowerCase().includes(query) ||
      (item.nama_operator || '').toLowerCase().includes(query) ||
      (item.nama_skpd || '').toLowerCase().includes(query) ||
      (item.kode_organisasi || '').toLowerCase().includes(query)
    );
  }

  if (sortField.value) {
    data.sort((a, b) => {
      const aVal = getFieldValue(a, sortField.value);
      const bVal = getFieldValue(b, sortField.value);
      
      // Numeric comparison
      if (!isNaN(aVal) && !isNaN(bVal)) {
        return sortDirection.value === 'asc'
          ? Number(aVal) - Number(bVal)
          : Number(bVal) - Number(aVal);
      }
      
      // String comparison
      return sortDirection.value === 'asc'
        ? aVal.localeCompare(bVal)
        : bVal.localeCompare(aVal);
    });
  }
  
  return data;
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

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Monitoring', href: '/Triwulan1' },
    { title: `Monitoring Detail ${props.skpd.nama_dinas || props.skpd.nama_skpd || 'SKPD'}`, href: '' },
];

<<<<<<< HEAD
function getUserNip(user: any): string {
  if (!user) return '-';
  
  if (user.user_detail && typeof user.user_detail.nip === 'string' && user.user_detail.nip.trim() !== '') {
    return user.user_detail.nip;
  }
=======
// function getUserNip(user: { user_detail?: { nip?: string } | null; nip?: string }): string {
//   if (user.user_detail && typeof user.user_detail.nip === 'string' && user.user_detail.nip.trim() !== '') {
//     return user.user_detail.nip;
//   }
>>>>>>> 1653c22a8692dd307d928021242200888c562522

//   if (typeof user.nip === 'string' && user.nip.trim() !== '') {
//     return user.nip;
//   }

//   return '-';
// }

// function getUserNip(user: any): string {
//   return user.user_detail?.nip || user.nip || '';
// }

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
                        <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
<<<<<<< HEAD
                        <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_kepala_skpd || 'Tidak tersedia' }}</p>
                        <p class="text-sm font-mono text-gray-500">NIP: {{ skpd.nip_kepala_skpd || '-' }}</p>
=======
                        <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_skpd || 'Tidak tersedia' }}</p>
                        <p class="text-sm font-mono text-gray-500">{{ skpd.nip_skpd || 'Tidak tersedia' }}</p>
>>>>>>> 1653c22a8692dd307d928021242200888c562522
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd.nama_operator || 'Tidak tersedia' }}</p>
                        <p class="text-sm font-mono text-gray-500">NIP: {{ skpd.nip_operator || '-' }}</p>
                    </div>


                </div>
            </div>

            <!-- Tabel Tugas PD -->
            <TabelTugasPD
                :skpd="props.skpd"
                :skpd-tugas="props.skpdTugas"
                :urusan-list="props.urusanList"
                :bidang-urusan-list="props.bidangUrusanList"
                :program-list="props.programList"
                :kegiatan-list="props.kegiatanList"
                :subkegiatan-list="props.subkegiatanList"
            ></TabelTugasPD>

        </div>
    </AppLayout>
</template>
