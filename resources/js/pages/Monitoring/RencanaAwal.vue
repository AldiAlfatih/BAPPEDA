<script setup lang="ts">
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

interface Target {
    kinerjaFisik: string;
    keuangan: string;
}

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
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Monitoring', href: '/Monitoring' },
    { title: `Monitoring Detail ${props.user?.nama_skpd ?? '-'}`, href: 'monitoring/show' },
  { title: 'Rencana Awal PD', href: '/rencanaawal' },
]);

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
        pagu_anggaran: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
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

    router.post('/rencanaawal/save-monitoring', {
        skpd_id: currentSkpdId,
        sumber_dana: editedData.value.sumberDana || '-',
        periode_id: null,
        tahun: new Date().getFullYear(),
        deskripsi: 'Rencana Awal',
        pagu_anggaran: parseInt(editedData.value.pokok.replace(/[^0-9]/g, '')) || 0,
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
</script>

<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
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
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.pokok" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.pokok || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.parsial" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.parsial || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.perubahan" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.perubahan || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.sumberDana" 
                                           type="text" 
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.sumberDana || '-' }}</span>
                                </td>

                                <!-- Triwulan 1 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[0].kinerjaFisik" 
                                           type="text" 
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[0]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[0].keuangan" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[0]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Triwulan 2 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[1].kinerjaFisik" 
                                           type="text" 
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[1]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[1].keuangan" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[1]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Triwulan 3 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[2].kinerjaFisik" 
                                           type="text" 
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[2]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[2].keuangan" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[2]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Triwulan 4 -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[3].kinerjaFisik" 
                                           type="text" 
                                           class="w-full text-center bg-transparent">
                                    <span v-else>{{ program.targets?.[3]?.kinerjaFisik || '-' }}</span>
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                    <input v-if="isEditing && editingRow?.id === program.id" 
                                           v-model="editedData.targets[3].keuangan" 
                                           type="text" 
                                           class="w-full text-right bg-transparent">
                                    <span v-else>{{ program.targets?.[3]?.keuangan || '-' }}</span>
                                </td>

                                <!-- Action column -->
                                <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
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
                                </td>
                            </tr>

                            <!-- Kegiatan untuk program ini -->
                            <template v-for="kegiatan in kegiatanTugas?.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                <tr class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="p-3 border sticky left-0 z-10 bg-white w-[100px]">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.pokok" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.pokok || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.parsial" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.parsial || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.perubahan" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.perubahan || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.sumberDana" 
                                               type="text" 
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.sumberDana || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 1 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[0].kinerjaFisik" 
                                               type="text" 
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[0]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[0].keuangan" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[0]?.keuangan || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 2 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[1].kinerjaFisik" 
                                               type="text" 
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[1]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[1].keuangan" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[1]?.keuangan || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 3 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[2].kinerjaFisik" 
                                               type="text" 
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[2]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[2].keuangan" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[2]?.keuangan || '-' }}</span>
                                    </td>

                                    <!-- Triwulan 4 -->
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[3].kinerjaFisik" 
                                               type="text" 
                                               class="w-full text-center bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[3]?.kinerjaFisik || '-' }}</span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                        <input v-if="isEditing && editingRow?.id === kegiatan.id" 
                                               v-model="editedData.targets[3].keuangan" 
                                               type="text" 
                                               class="w-full text-right bg-transparent">
                                        <span v-else>{{ kegiatan.targets?.[3]?.keuangan || '-' }}</span>
                                    </td>

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
                                    </td>
                                </tr>

                                <!-- Subkegiatan untuk kegiatan ini -->
                                <template v-for="subKegiatan in subkegiatanTugas?.filter(sk => sk.kode_nomenklatur.details[0]?.id_kegiatan === kegiatan.kode_nomenklatur.id)" :key="subKegiatan.id">
                                    <tr class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ subKegiatan.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="p-3 border sticky left-0 z-10 bg-white w-[100px]">{{ subKegiatan.kode_nomenklatur.nomenklatur }}</td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.pokok" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.pokok || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.parsial" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.parsial || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.perubahan" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.perubahan || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.sumberDana" 
                                                   type="text" 
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.sumberDana || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 1 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[0].kinerjaFisik" 
                                                   type="text" 
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[0]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[0].keuangan" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[0]?.keuangan || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 2 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[1].kinerjaFisik" 
                                                   type="text" 
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[1]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[1].keuangan" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[1]?.keuangan || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 3 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[2].kinerjaFisik" 
                                                   type="text" 
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[2]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[2].keuangan" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[2]?.keuangan || '-' }}</span>
                                        </td>

                                        <!-- Triwulan 4 -->
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[3].kinerjaFisik" 
                                                   type="text" 
                                                   class="w-full text-center bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[3]?.kinerjaFisik || '-' }}</span>
                                        </td>
                                        <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                            <input v-if="isEditing && editingRow?.id === subKegiatan.id" 
                                                   v-model="editedData.targets[3].keuangan" 
                                                   type="text" 
                                                   class="w-full text-right bg-transparent">
                                            <span v-else>{{ subKegiatan.targets?.[3]?.keuangan || '-' }}</span>
                                        </td>

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
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </tbody>
                </table>

                <!-- Update button at the bottom -->
                <div class="p-4 flex justify-end">
                    <button @click="updateAllData"
                            class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                        Update
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
