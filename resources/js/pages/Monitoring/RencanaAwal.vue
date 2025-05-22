<script setup lang="ts">
import { computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
<<<<<<< HEAD
=======
// import axios from 'axios';
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114

interface Target {
    kinerjaFisik: string;
    keuangan: string;
}
<<<<<<< HEAD

interface Program {
    id: number;
    kode_nomenklatur: {
        id: number;
        nomor_kode: string;
        nomenklatur: string;
        details: Array<{
            id_urusan: number;
            id_bidang_urusan: number;
            id_program: number;
            id_kegiatan: number;
        }>;
    };
    pokok?: string;
    parsial?: string;
    perubahan?: string;
    sumberDana?: string;
    targets?: Target[];
}
interface User {
    id: number;
    name: string;
    nama_skpd: string;
}
=======
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114

interface ProgramData {
    kode: string;
    program: string;
    pokok: string;
    parsial: string;
    perubahan: string;
    sumberDana: string;
    targets: Target[];
}

interface User {
    id: number;
    name: string;
    nama_skpd: string;
}

const props = defineProps<{
    user?: User;
    programTugas?: any[];
    kegiatanTugas?: any[];
    subkegiatanTugas?: any[];
    kepalaSkpd?: string;
    isFinalized?: boolean;
    flash?: {
        success?: string;
        error?: string;
    };
<<<<<<< HEAD
  };
  tugas: {
    id: number;
    kode_nomenklatur: { 
      nomor_kode: string; 
      nomenklatur: string;
      details: Array<{
        id_urusan: number;
        id_bidang_urusan: number;
        id_program: number;
        id_kegiatan: number;
      }>[];
    };
    skpd: {
      nama_dinas: string;
      kode_organisasi?: string;
      no_dpa?: string;
      skpd_kepala: Array<{
        user: {
          user_detail: {
            nama: string;
          };
        };
      }>[];
    };
    rencana_awal?: {
      indikator: string;
      target: string;
    } | null;
  };
  programTugas: Array<any>;
  kegiatanTugas: Array<any>;
  subkegiatanTugas: Array<any>;
  kepalaSkpd?: string;
  urusan: number[]; 
  bidangUrusan: number[];
  monitoring: {
    sumber_dana: string;
    pagu_pokok: number;
    pagu_parsial: number;
    pagu_perubahan: number;
    targets: Array<any>;
    realisasi: Array<any>;
  };
=======
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
}>();


const breadcrumbs = computed(() => [
  { title: 'Monitoring', href: '/Monitoring' },
    { title: `Monitoring Detail ${props.user?.nama_skpd ?? '-'}`, href: 'monitoring/show' },
  { title: 'Rencana Awal PD', href: '/rencanaawal' },
]) as unknown as BreadcrumbItem[];

// Sample data for the top table
const headerData = ref([
    { label: 'KODE/URUSAN PEMERINTAHAN', value: '5 UNSUR PENUNJANG PEMERINTAHAN ANGGARAN APBD' },
    { label: 'NAMA SKPD', value: props.user?.nama_skpd || 'DINAS PENDIDIDKAN' },
    { label: 'KODE ORGANISASI', value: '5.01.5.05.0.00.25.0000' },
    { label: 'NO.DPA SKPD', value: 'DPA/A.1/5.01.5.05.0.00.25.0000/001.2024' },
    { label: 'NAMA KEPALA SKPD', value: props.kepalaSkpd || 'ZULKARNAEN, ST., M.Si' },
]);

// Add new refs for editing functionality
const isEditing = ref(false);
const editingRow = ref<any | null>(null);
const editedData = ref({
    pokok: '',
    parsial: '',
    perubahan: '',
    sumberDana: '',
    targets: [
        { kinerjaFisik: '', keuangan: '' },
        { kinerjaFisik: '', keuangan: '' },
        { kinerjaFisik: '', keuangan: '' },
        { kinerjaFisik: '', keuangan: '' },
    ]
});

// Add new ref for finalization state
const isFinalized = ref(props.isFinalized || false);

// Add new refs for tracking finalized rows
const finalizedRows = ref(new Set());

// Add function to check if all rows are finalized
const isAllRowsFinalized = computed(() => {
    const allRows = [
        ...(props.programTugas || []),
        ...(props.kegiatanTugas || []),
        ...(props.subkegiatanTugas || [])
    ];
    return allRows.length > 0 && allRows.every(row => finalizedRows.value.has(row.id));
});

// Add flash message handling
const showFlash = ref(false);
const flashMessage = ref('');
const flashType = ref('');

watch(() => props.flash, (newFlash) => {
    if (newFlash?.success) {
        flashMessage.value = newFlash.success;
        flashType.value = 'success';
        showFlash.value = true;
    } else if (newFlash?.error) {
        flashMessage.value = newFlash.error;
        flashType.value = 'error';
        showFlash.value = true;
    }
}, { immediate: true });

// Auto hide flash message after 3 seconds
watch(showFlash, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            showFlash.value = false;
        }, 3000);
    }
});

// Methods for handling edit functionality
const startEditing = (row: any) => {
    isEditing.value = true;
    editingRow.value = row;
    editedData.value = {
        pokok: row.pokok ? row.pokok.toString() : '',
        parsial: row.parsial ? row.parsial.toString() : '',
        perubahan: row.perubahan ? row.perubahan.toString() : '',
        sumberDana: row.sumber_dana || '',
        targets: [
            { kinerjaFisik: row.targets?.[0]?.kinerja_fisik?.toString() || '', keuangan: row.targets?.[0]?.keuangan?.toString() || '' },
            { kinerjaFisik: row.targets?.[1]?.kinerja_fisik?.toString() || '', keuangan: row.targets?.[1]?.keuangan?.toString() || '' },
            { kinerjaFisik: row.targets?.[2]?.kinerja_fisik?.toString() || '', keuangan: row.targets?.[2]?.keuangan?.toString() || '' },
            { kinerjaFisik: row.targets?.[3]?.kinerja_fisik?.toString() || '', keuangan: row.targets?.[3]?.keuangan?.toString() || '' },
        ]
    };
};

const saveChanges = () => {
    if (editingRow.value) {
        // Get skpd_id from props.user
        const currentSkpdId = props.user?.id;

        if (!currentSkpdId) {
            flashMessage.value = 'SKPD ID tidak ditemukan. Silakan refresh halaman dan coba lagi.';
            flashType.value = 'error';
            showFlash.value = true;
            return;
        }

        const dataToSend = {
            skpd_id: currentSkpdId,
            sumber_dana: editedData.value.sumberDana || '-',
            periode_id: null,
            tahun: new Date().getFullYear(),
            deskripsi: 'Rencana Awal',
            pagu_pokok: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
            pagu_parsial: parseInt(editedData.value.parsial.replace(/[^0-9]/g, '')) || 0,
            pagu_perubahan: parseInt(editedData.value.perubahan.replace(/[^0-9]/g, '')) || 0,
            targets: editedData.value.targets.map(target => ({
                kinerja_fisik: parseFloat(target.kinerjaFisik) || 0,
                keuangan: parseInt(target.keuangan.replace(/[^0-9]/g, '')) || 0
            })),
            tugas_id: editingRow.value.id
        };

        router.post('/rencanaawal/save-monitoring', dataToSend, {
            onSuccess: () => {
                isEditing.value = false;
                editingRow.value = null;
                editedData.value = {
                pokok: '',
                parsial: '',
                perubahan: '',
                sumberDana: '',
                targets: [
                    { kinerjaFisik: '', keuangan: '' },
                    { kinerjaFisik: '', keuangan: '' },
                    { kinerjaFisik: '', keuangan: '' },
                    { kinerjaFisik: '', keuangan: '' },
                ],
                };
                // Reload page or visit current url to fetch latest data
                router.reload(); // ini akan memanggil controller dan me-render ulang dengan data terbaru
            },
            onError: (errors) => {
                // handle error
            },
            });

    }
};

const updateAllData = () => {
    // Debug: Log the data we're about to send
    console.log('Sending data:', {
        skpd_id: props.user?.id,
        sumber_dana: editedData.value.sumberDana,
        periode_id: null,
        tahun: new Date().getFullYear(),
        deskripsi: 'Rencana Awal',
        pagu_pokok: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pagu_parsial: parseInt(editedData.value.parsial.replace(/[^0-9]/g, '')) || 0,
        pagu_perubahan: parseInt(editedData.value.perubahan.replace(/[^0-9]/g, '')) || 0,
        targets: editedData.value.targets.map(target => ({
            kinerja_fisik: parseFloat(target.kinerjaFisik) || 0,
            keuangan: parseInt(target.keuangan.replace(/[^0-9]/g, '')) || 0
        }))
    });

    // Get skpd_id from props.user
    const currentSkpdId = props.user?.id;

    if (!currentSkpdId) {
        alert('SKPD ID tidak ditemukan. Silakan refresh halaman dan coba lagi.');
        return;
    }

    router.post('/rencanaawal/save-monitoring', {
        skpd_id: currentSkpdId,
        sumber_dana: editedData.value.sumberDana || '-',
        periode_id: null,
        tahun: new Date().getFullYear(),
        deskripsi: 'Rencana Awal',
        pagu_pokok: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pagu_parsial: parseInt(editedData.value.parsial.replace(/[^0-9]/g, '')) || 0,
        pagu_perubahan: parseInt(editedData.value.perubahan.replace(/[^0-9]/g, '')) || 0,
        targets: editedData.value.targets.map(target => ({
            kinerja_fisik: parseFloat(target.kinerjaFisik) || 0,
            keuangan: parseInt(target.keuangan.replace(/[^0-9]/g, '')) || 0
        }))
    }, {
        onSuccess: () => {
            alert('Data berhasil disimpan');
            isEditing.value = false;
            editingRow.value = null;
        },
        onError: (errors) => {
            console.error('Error saving data:', errors);
            alert('Terjadi kesalahan saat menyimpan data: ' + Object.values(errors).join(', '));
        }
    });
};

// Add function to finalize single row
const finalizeRow = (row: any) => {
    if (!props.user?.id) {
        flashMessage.value = 'SKPD ID tidak ditemukan. Silakan refresh halaman dan coba lagi.';
        flashType.value = 'error';
        showFlash.value = true;
        return;
    }

    router.post('/rencanaawal/finalize-row', {
        skpd_id: props.user.id,
        tahun: new Date().getFullYear(),
        tugas_id: row.id
    }, {
        onSuccess: () => {
            finalizedRows.value.add(row.id);
        }
    });
};

// Modify finalizeData function
const finalizeData = () => {
    if (!props.user?.id) {
        flashMessage.value = 'SKPD ID tidak ditemukan. Silakan refresh halaman dan coba lagi.';
        flashType.value = 'error';
        showFlash.value = true;
        return;
    }

    if (!isAllRowsFinalized.value) {
        flashMessage.value = 'Semua baris harus difinalisasi terlebih dahulu';
        flashType.value = 'error';
        showFlash.value = true;
        return;
    }

    router.post('/rencanaawal/finalize', {
        skpd_id: props.user.id,
        tahun: new Date().getFullYear()
    }, {
        onSuccess: () => {
            isFinalized.value = true;
        }
    });
};

<<<<<<< HEAD

// Add new refs for editing functionality
const isEditing = ref(false);
const editingRow = ref<any | null>(null);
const editedData = ref({
    pokok: '',
    parsial: '',
    perubahan: '',
    sumberDana: '',
    targets: [
        { kinerjaFisik: '', keuangan: '' },
        { kinerjaFisik: '', keuangan: '' },
        { kinerjaFisik: '', keuangan: '' },
        { kinerjaFisik: '', keuangan: '' },
    ]
});

// Methods for handling edit functionality
const startEditing = (row: any) => {
    isEditing.value = true;
    editingRow.value = row;
    editedData.value = {
        pokok: row.pokok || '',
        parsial: row.parsial || '',
        perubahan: row.perubahan || '',
        sumberDana: row.sumberDana || '',
        targets: [
            { kinerjaFisik: row.targets?.[0]?.kinerjaFisik || '', keuangan: row.targets?.[0]?.keuangan || '' },
            { kinerjaFisik: row.targets?.[1]?.kinerjaFisik || '', keuangan: row.targets?.[1]?.keuangan || '' },
            { kinerjaFisik: row.targets?.[2]?.kinerjaFisik || '', keuangan: row.targets?.[2]?.keuangan || '' },
            { kinerjaFisik: row.targets?.[3]?.kinerjaFisik || '', keuangan: row.targets?.[3]?.keuangan || '' },
        ]
    };
};

const saveChanges = () => {
    if (editingRow.value) {
        // Update the data in the appropriate array based on the type
        if (props.programTugas?.some(p => p.id === editingRow.value.id)) {
            const index = props.programTugas.findIndex(p => p.id === editingRow.value.id);
            if (index !== -1) {
                props.programTugas[index] = {
                    ...props.programTugas[index],
                    ...editedData.value
                };
            }
        } else if (props.kegiatanTugas?.some(k => k.id === editingRow.value.id)) {
            const index = props.kegiatanTugas.findIndex(k => k.id === editingRow.value.id);
            if (index !== -1) {
                props.kegiatanTugas[index] = {
                    ...props.kegiatanTugas[index],
                    ...editedData.value
                };
            }
        } else if (props.subkegiatanTugas?.some(sk => sk.id === editingRow.value.id)) {
            const index = props.subkegiatanTugas.findIndex(sk => sk.id === editingRow.value.id);
            if (index !== -1) {
                props.subkegiatanTugas[index] = {
                    ...props.subkegiatanTugas[index],
                    ...editedData.value
                };
            }
        }
    }
    isEditing.value = false;
    editingRow.value = null;
};

const updateAllData = () => {
    // Debug: Log the data we're about to send
    console.log('Sending data:', {
        skpd_id: props.user?.id,
        sumber_dana: editedData.value.sumberDana,
        periode_id: null,
        tahun: new Date().getFullYear(),
        deskripsi: 'Rencana Awal',
        pagu_pokok: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pagu_parsial: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pagu_perubahan: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pokok: editedData.value.pokok || '0',
        parsial: editedData.value.parsial || '0',
        perubahan: editedData.value.perubahan || '0',
        targets: editedData.value.targets.map(target => ({
            kinerja_fisik: parseFloat(target.kinerjaFisik) || 0,
            keuangan: parseInt(target.keuangan.replace(/[^0-9]/g, '')) || 0
        }))
    });

    // Get skpd_id from props.user
    const currentSkpdId = props.user?.id;

    if (!currentSkpdId) {
        alert('SKPD ID tidak ditemukan. Silakan refresh halaman dan coba lagi.');
        return;
    }

    router.post('/rencanaawal/', {
        skpd_id: currentSkpdId,
        sumber_dana: editedData.value.sumberDana || '-',
        periode_id: null,
        tahun: new Date().getFullYear(),
        deskripsi: 'Rencana Awal',
        pagu_pokok: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pagu_parsial: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pagu_perubahan: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
        pokok: editedData.value.pokok || '0',
        parsial: editedData.value.parsial || '0',
        perubahan: editedData.value.perubahan || '0',
        targets: editedData.value.targets.map(target => ({
            kinerja_fisik: parseFloat(target.kinerjaFisik) || 0,
            keuangan: parseInt(target.keuangan.replace(/[^0-9]/g, '')) || 0
        }))
    }, {
        onSuccess: () => {
            alert('Data berhasil disimpan');
            isEditing.value = false;
            editingRow.value = null;

            
        },
        onError: (errors) => {
            console.error('Error saving data:', errors);
            alert('Terjadi kesalahan saat menyimpan data: ' + Object.values(errors).join(', '));
        }
    });
};
=======
function goToCreate() {
  router.visit('/rencanaawal/create');
}
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
</script>

<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
<<<<<<< HEAD
 <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->
            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-600">Rencana Kinerja</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN:</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.skpd.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>


                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.skpd.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">No DPA</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.skpd?.no_dpa || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ kepalaSkpd ?? tugas.skpd.skpd_kepala[0]?.user?.user_detail?.nama ?? '-' }}</p>
                    </div>
=======
        <!-- Flash Message -->
        <div v-if="showFlash"
             :class="[
                 'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50',
                 flashType === 'success' ? 'bg-green-500' : 'bg-red-500',
                 'text-white'
             ]">
            {{ flashMessage }}
        </div>

        <div class="flex h-full flex-1 w-5xl overflow-x-hidden flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow">
                <h2 class="p-2 text-xl font-semibold bg-emerald-600 text-white rounded-t-xl">
                    Rencana Kinerja
                </h2>

                <!-- Top table -->
                <div class="overflow-hidden">
                    <table class="w-full border-collapse">
                        <tbody>
                            <tr v-for="(item, index) in headerData" :key="index" class="border border-gray-200 dark:border-gray-600">
                                <td class="p-3 border-r border-gray-200 dark:border-gray-600 font-medium w-1/3">{{ item.label }}</td>
                                <td class="p-3 font-medium">{{ item.value }}</td>
                            </tr>
                        </tbody>
                    </table>
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                </div>
            </div>


            <!-- Program table with targets -->
            <div class="bg-white dark:bg-gray-700 rounded-t-xl shadow overflow-x-auto">
                <table class="w-full border-collapse text-sm mt-6">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="3" class="border border-amber-300 bg-white px-2 py-1">KODE</th>
                            <th rowspan="3" class="border sticky left-0 z-10 bg-white w-[100px]">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                            <th colspan="3" class="border border-amber-300 px-2 py-1 bg-amber-100">PAGU ANGGARAN APBD</th>
                            <th rowspan="3" class="border border-amber-300 px-2 py-1 bg-amber-100">SUMBER DANA</th>
                            <th colspan="8" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TARGET</th>
                            <th rowspan="3" class="border border-amber-300 px-2 py-1 bg-white">AKSI</th>
                        </tr>
                        <tr class="text-center bg-amber-100">
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">POKOK (RP)</th>
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">PARSIAL (RP)</th>
                            <th rowspan="2" class="border border-amber-300 px-2 py-1">PERUBAHAN (RP)</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 1</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 2</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 3</th>
                            <th colspan="2" class="border border-amber-300 px-2 py-1 bg-[#fbe9db]">TRIWULAN 4</th>
                        </tr>
                        <tr class="text-center bg-[#fbe9db]">
                            <!-- Triwulan 1 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <!-- Triwulan 2 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <!-- Triwulan 3 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                            <!-- Triwulan 4 -->
                            <th class="border border-amber-300 px-2 py-1">KINERJA FISIK (%)</th>
                            <th class="border border-amber-300 px-2 py-1">KEUANGAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Programs -->
                        <template v-for="program in programTugas" :key="program.id">
                            <tr class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ program.kode_nomenklatur.nomor_kode }}</td>
                                <td class="p-3 border sticky left-0 z-10 bg-white w-[100px]">{{ program.kode_nomenklatur.nomenklatur }}</td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.pokok" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.pokok"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.pokok || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.parsial" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.parsial"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.parsial || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.perubahan" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.perubahan"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.perubahan || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.sumberDana" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.sumberDana"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.sumberDana || '-' }}</span>
                                </td>

                                <!-- Triwulan 1 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[0].kinerjaFisik" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[0].kinerjaFisik"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[0]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[0].keuangan" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[0].keuangan"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[0]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Triwulan 2 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[1].kinerjaFisik" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[1].kinerjaFisik"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[1]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[1].keuangan" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[1].keuangan"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[1]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Triwulan 3 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[2].kinerjaFisik" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[2].kinerjaFisik"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[2]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[2].keuangan" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[2].keuangan"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[2]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Triwulan 4 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[3].kinerjaFisik" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[3].kinerjaFisik"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[3]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[3].keuangan" 
                                           type="text" 
=======
                                    <input v-if="isEditing && editingRow?.id === program.id"
                                           v-model="editedData.targets[3].keuangan"
                                           type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[3]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Action column -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                    <button v-if="!isEditing || editingRow?.id !== program.id"
                                            @click="startEditing(program)"
                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Isi
                                    </button>
                                    <button v-else
                                            @click="saveChanges"
                                            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                        Simpan
                                    </button>
=======
                                    <div class="flex gap-2 justify-center">
                                        <button v-if="!isFinalized && !finalizedRows.has(program.id) && (!isEditing || editingRow?.id !== program.id)"
                                                @click="startEditing(program)"
                                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Isi
                                        </button>
                                        <button v-if="!isFinalized && !finalizedRows.has(program.id) && (!isEditing || editingRow?.id !== program.id)"
                                                @click="finalizeRow(program)"
                                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                            Selesai
                                        </button>
                                        <button v-if="!isFinalized && !finalizedRows.has(program.id) && isEditing && editingRow?.id === program.id"
                                                @click="saveChanges"
                                                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            Simpan
                                        </button>
                                        <button v-if="!isFinalized && finalizedRows.has(program.id)"
                                                disabled
                                                class="px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed">
                                            Selesai
                                        </button>
                                    </div>
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                </td>
                            </tr>

                            <!-- Kegiatan untuk program ini -->
                            <template v-for="kegiatan in kegiatanTugas?.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                <tr class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="p-3 border sticky left-0 z-10 bg-white w-[100px]">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.pokok" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.pokok"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.pokok || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.parsial" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.parsial"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.parsial || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.perubahan" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.perubahan"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.perubahan || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.sumberDana" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.sumberDana"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.sumberDana || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 1 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[0].kinerjaFisik" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[0].kinerjaFisik"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[0]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[0].keuangan" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[0].keuangan"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[0]?.keuangan || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 2 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[1].kinerjaFisik" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[1].kinerjaFisik"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[1]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[1].keuangan" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[1].keuangan"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[1]?.keuangan || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 3 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[2].kinerjaFisik" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[2].kinerjaFisik"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[2]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[2].keuangan" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[2].keuangan"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[2]?.keuangan || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 4 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[3].kinerjaFisik" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[3].kinerjaFisik"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[3]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[3].keuangan" 
                                               type="text" 
=======
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id"
                                               v-model="editedData.targets[3].keuangan"
                                               type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[3]?.keuangan || '-' }}</span>
                                    </td>

<<<<<<< HEAD
                                    <!-- Action column -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <button v-if="!isEditing || editingRow?.id !== kegiatan.id"
                                                @click="startEditing(kegiatan)"
                                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            Isi
                                        </button>
                                        <button v-else
                                                @click="saveChanges"
                                                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            Simpan
                                        </button>
=======
                                    <!-- Action column for kegiatan -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button v-if="!isFinalized && !finalizedRows.has(kegiatan.id) && (!isEditing || editingRow?.id !== kegiatan.id)"
                                                    @click="startEditing(kegiatan)"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Isi
                                            </button>
                                            <button v-if="!isFinalized && !finalizedRows.has(kegiatan.id) && (!isEditing || editingRow?.id !== kegiatan.id)"
                                                    @click="finalizeRow(kegiatan)"
                                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                Selesai
                                            </button>
                                            <button v-if="!isFinalized && !finalizedRows.has(kegiatan.id) && isEditing && editingRow?.id === kegiatan.id"
                                                    @click="saveChanges"
                                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                                Simpan
                                            </button>
                                            <button v-if="!isFinalized && finalizedRows.has(kegiatan.id)"
                                                    disabled
                                                    class="px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed">
                                                Selesai
                                            </button>
                                        </div>
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                    </td>
                                </tr>

                                <!-- Subkegiatan untuk kegiatan ini -->
                                <template v-for="subKegiatan in subkegiatanTugas?.filter(sk => sk.kode_nomenklatur.details[0]?.id_kegiatan === kegiatan.kode_nomenklatur.id)" :key="subKegiatan.id">
                                    <tr class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ subKegiatan.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="p-3 border sticky left-0 z-10 bg-white w-[100px]">{{ subKegiatan.kode_nomenklatur.nomenklatur }}</td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.pokok" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.pokok"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.pokok || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.parsial" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.parsial"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.parsial || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.perubahan" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.perubahan"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.perubahan || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.sumberDana" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.sumberDana"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.sumberDana || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 1 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[0].kinerjaFisik" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[0].kinerjaFisik"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[0]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[0].keuangan" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[0].keuangan"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[0]?.keuangan || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 2 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[1].kinerjaFisik" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[1].kinerjaFisik"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[1]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[1].keuangan" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[1].keuangan"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[1]?.keuangan || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 3 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[2].kinerjaFisik" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[2].kinerjaFisik"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[2]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[2].keuangan" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[2].keuangan"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[2]?.keuangan || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 4 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[3].kinerjaFisik" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[3].kinerjaFisik"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[3]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
<<<<<<< HEAD
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[3].keuangan" 
                                                   type="text" 
=======
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id"
                                                   v-model="editedData.targets[3].keuangan"
                                                   type="text"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[3]?.keuangan || '-' }}</span>
                                        </td>

<<<<<<< HEAD
                                        <!-- Action column -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <button v-if="!isEditing || editingRow?.id !== subKegiatan.id"
                                                    @click="startEditing(subKegiatan)"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Isi
                                            </button>
                                            <button v-else
                                                    @click="saveChanges"
                                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                                Simpan
                                            </button>
=======
                                        <!-- Action column for subkegiatan -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <div class="flex gap-2 justify-center">
                                                <button v-if="!isFinalized && !finalizedRows.has(subKegiatan.id) && (!isEditing || editingRow?.id !== subKegiatan.id)"
                                                        @click="startEditing(subKegiatan)"
                                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                    Isi
                                                </button>
                                                <button v-if="!isFinalized && !finalizedRows.has(subKegiatan.id) && (!isEditing || editingRow?.id !== subKegiatan.id)"
                                                        @click="finalizeRow(subKegiatan)"
                                                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                    Selesai
                                                </button>
                                                <button v-if="!isFinalized && !finalizedRows.has(subKegiatan.id) && isEditing && editingRow?.id === subKegiatan.id"
                                                        @click="saveChanges"
                                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                                    Simpan
                                                </button>
                                                <button v-if="!isFinalized && finalizedRows.has(subKegiatan.id)"
                                                        disabled
                                                        class="px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed">
                                                    Selesai
                                                </button>
                                            </div>
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </tbody>
                </table>

<<<<<<< HEAD
                <!-- Update button at the bottom -->
                <div class="p-4 flex justify-end">
                    <button @click="updateAllData"
                            class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                        Update
=======
                <!-- Modify the finalize button at the bottom -->
                <div class="p-4 flex justify-end">
                    <button v-if="!isFinalized"
                            @click="finalizeData"
                            :disabled="!isAllRowsFinalized"
                            :class="[
                                'px-4 py-2 rounded',
                                isAllRowsFinalized
                                    ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                    : 'bg-gray-400 text-white cursor-not-allowed'
                            ]">
                        Selesaikan
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Enhanced styling to match the reference image */
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #000000;
    padding: 4px 8px;
}

thead {
    background-color: #FFF2CC;
}

thead th {
    font-weight: bold;
    vertical-align: middle;
}

tbody tr:nth-child(odd) {
    background-color: #FFFCF3;
}

input {
    border: 1px solid #ddd;
    padding: 2px 4px;
    border-radius: 4px;
}

input:focus {
    outline: none;
    border-color: #4CAF50;
}
</style>
