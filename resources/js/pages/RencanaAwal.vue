<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rencana Awal',
        href: '/rencanaawal',
    },
];

// Sample data for the top table
const headerData = ref([
    { label: 'KODE/URUSAN PEMERINTAHAN', value: '5 UNSUR PENUNJANG PEMERINTAHAN ANGGARAN APBD' },
    { label: 'NAMA SKPD', value: 'DINAS PENDIDIDKAN' },
    { label: 'KODE ORGANISASI', value: '5.01.5.05.0.00.25.0000' },
    { label: 'NO.DPA SKPD', value: 'DPA/A.1/5.01.5.05.0.00.25.0000/001.2024' },
    { label: 'NAMA KEPALA SKPD', value: 'ZULKARNAEN, ST., M.Si' },
]);

// Sample data for the program table with target data
const programData = ref<ProgramData[]>([
    {
        kode: '1',
        program: 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR',
        pokok: '216.551.782.886,00',
        parsial: '211.129.110.516,00',
        perubahan: '',
        sumberDana: 'DAU',
        targets: [
            { kinerjaFisik: '', keuangan: '180.512.844.036,00' }, // Triwulan 1
            { kinerjaFisik: '66,22', keuangan: '198.036.609.736,00' }, // Triwulan 2
            { kinerjaFisik: '', keuangan: '214.222.088.060,00' }, // Triwulan 3
            { kinerjaFisik: '', keuangan: '' }, // Triwulan 4
        ]
    },
    {
        kode: '1.01',
        program: 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN',
        pokok: '216.551.782.886,00',
        parsial: '211.129.110.516,00',
        perubahan: '0,0',
        sumberDana: 'DAU',
        targets: [
            { kinerjaFisik: '', keuangan: '180.512.844.036,00' }, // Triwulan 1
            { kinerjaFisik: '66,22', keuangan: '198.036.609.736,00' }, // Triwulan 2
            { kinerjaFisik: '', keuangan: '214.222.088.060,00' }, // Triwulan 3
            { kinerjaFisik: '', keuangan: '' }, // Triwulan 4
        ]
    },
    {
        kode: '1.01.01',
        program: 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA',
        pokok: '170.351.678.486,00',
        parsial: '164.871.072.942,00',
        perubahan: '',
        sumberDana: '',
        targets: [
            { kinerjaFisik: '35,71', keuangan: '168.645.954.036,00' }, // Triwulan 1
            { kinerjaFisik: '64,29', keuangan: '169.226.716.336,00' }, // Triwulan 2
            { kinerjaFisik: '78,57', keuangan: '169.837.391.186,00' }, // Triwulan 3
            { kinerjaFisik: '100,00', keuangan: '' }, // Triwulan 4
        ]
    },
    {
        kode: '1.01.01.2.01',
        program: 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah',
        pokok: '6.300.000,00',
        parsial: '6.300.000,00',
        perubahan: '',
        sumberDana: '',
        targets: [
            { kinerjaFisik: '100,00', keuangan: '2.700.000,00' }, // Triwulan 1
            { kinerjaFisik: '100,00', keuangan: '3.600.000' }, // Triwulan 2
            { kinerjaFisik: '75,00', keuangan: '6.300.000' }, // Triwulan 3
            { kinerjaFisik: '100,00', keuangan: '6.300.000' }, // Triwulan 4
        ]
    },
]);

// Add new refs for editing functionality
const isEditing = ref(false);
const editingRow = ref<ProgramData | null>(null);
const editedData = ref<Omit<ProgramData, 'kode' | 'program'>>({
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
const startEditing = (row: ProgramData) => {
    isEditing.value = true;
    editingRow.value = row;
    editedData.value = {
        pokok: row.pokok,
        parsial: row.parsial,
        perubahan: row.perubahan,
        sumberDana: row.sumberDana,
        targets: [...row.targets]
    };
};

const saveChanges = () => {
    if (editingRow.value) {
        const index = programData.value.findIndex(item => item.kode === editingRow.value?.kode);
        if (index !== -1) {
            programData.value[index] = {
                ...programData.value[index],
                ...editedData.value
            };
        }
    }
    isEditing.value = false;
    editingRow.value = null;
};

const updateAllData = () => {
    // Here you would typically make an API call to save all the data
    console.log('Updating all data:', programData.value);
    // Add your API call here
};
</script>

<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 w-5xl  overflow-x-hidden flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
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
                        <tr v-for="item in programData" :key="item.kode" class="border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">{{ item.kode }}</td>
                            <td class="p-3 border sticky left-0 z-10 bg-white w-[100px]">{{ item.program }}</td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.pokok" 
                                       type="text" 
                                       class="w-full text-right bg-transparent">
                                <span v-else>{{ item.pokok }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.parsial" 
                                       type="text" 
                                       class="w-full text-right bg-transparent">
                                <span v-else>{{ item.parsial }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.perubahan" 
                                       type="text" 
                                       class="w-full text-center bg-transparent">
                                <span v-else>{{ item.perubahan }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.sumberDana" 
                                       type="text" 
                                       class="w-full text-center bg-transparent">
                                <span v-else>{{ item.sumberDana }}</span>
                            </td>

                            <!-- Triwulan 1 -->
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[0].kinerjaFisik" 
                                       type="text" 
                                       class="w-full text-center bg-transparent">
                                <span v-else>{{ item.targets[0].kinerjaFisik }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[0].keuangan" 
                                       type="text" 
                                       class="w-full text-right bg-transparent">
                                <span v-else>{{ item.targets[0].keuangan }}</span>
                            </td>

                            <!-- Triwulan 2 -->
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[1].kinerjaFisik" 
                                       type="text" 
                                       class="w-full text-center bg-transparent">
                                <span v-else>{{ item.targets[1].kinerjaFisik }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[1].keuangan" 
                                       type="text" 
                                       class="w-full text-right bg-transparent">
                                <span v-else>{{ item.targets[1].keuangan }}</span>
                            </td>

                            <!-- Triwulan 3 -->
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[2].kinerjaFisik" 
                                       type="text" 
                                       class="w-full text-center bg-transparent">
                                <span v-else>{{ item.targets[2].kinerjaFisik }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[2].keuangan" 
                                       type="text" 
                                       class="w-full text-right bg-transparent">
                                <span v-else>{{ item.targets[2].keuangan }}</span>
                            </td>

                            <!-- Triwulan 4 -->
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[3].kinerjaFisik" 
                                       type="text" 
                                       class="w-full text-center bg-transparent">
                                <span v-else>{{ item.targets[3].kinerjaFisik }}</span>
                            </td>
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-right">
                                <input v-if="isEditing && editingRow?.kode === item.kode" 
                                       v-model="editedData.targets[3].keuangan" 
                                       type="text" 
                                       class="w-full text-right bg-transparent">
                                <span v-else>{{ item.targets[3].keuangan }}</span>
                            </td>

                            <!-- Action column -->
                            <td class="p-3 border border-gray-200 dark:border-gray-600 text-center">
                                <button v-if="!isEditing || editingRow?.kode !== item.kode"
                                        @click="startEditing(item)"
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
