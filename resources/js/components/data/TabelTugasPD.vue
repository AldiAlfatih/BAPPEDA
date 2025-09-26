<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Binoculars, FileText } from 'lucide-vue-next';

const props = defineProps<{
    skpd: {
        id: number;
        nama_dinas: string;
        nama_operator: string;
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
    tid?: number; // Tambah parameter triwulan ID
    tahun?: number; // Tambah parameter tahun
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

function getTriwulanName(triwulanId: number | undefined): string {
    const triwulanNames: { [key: number]: string } = {
        1: 'Triwulan 1',
        2: 'Triwulan 2', 
        3: 'Triwulan 3',
        4: 'Triwulan 4'
    };
    return triwulanNames[triwulanId || 1] || 'Triwulan';
}

function ShowTugas(tugasId: number) {
    const triwulanId = props.tid || 1; // Default ke 1 jika tidak ada
    // Ambil user ID dari URL saat ini dengan cara yang aman
    const currentPath = window.location.pathname;
    const pathParts = currentPath.split('/');
    const userId = pathParts[3] || props.skpd.user?.id || props.skpd.id;
    router.visit(route('triwulan.detail', { tid: triwulanId, id: userId, taskId: tugasId }));
}

// State untuk PDF modal
const showPdfModal = ref(false);
const selectedTugasId = ref<number | null>(null);
const isGeneratingPdf = ref(false);

// Form data untuk PDF
const pdfForm = ref({
    tahun: props.tahun || new Date().getFullYear(),
    penandatangan_1_tempat: 'Banjarmasin',
    penandatangan_1_tanggal: new Date().toISOString().split('T')[0],
    penandatangan_1_nama: '',
    penandatangan_1_jabatan: '',
    penandatangan_1_nip: '',
    paper_size: 'A4',
    orientation: 'landscape',
    margin_top: 20,
    margin_right: 20,
    margin_bottom: 20,
    margin_left: 20,
});

// Available years untuk dropdown
const availableYears = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let i = currentYear - 5; i <= currentYear + 2; i++) {
        years.push(i);
    }
    return years;
});

// Function untuk membuka modal PDF
function openPdfModal(tugasId: number) {
    selectedTugasId.value = tugasId;
    
    // Set default values dari SKPD data
    pdfForm.value.penandatangan_1_nama = props.skpd.user?.name || '';
    pdfForm.value.penandatangan_1_jabatan = `Kepala ${props.skpd.nama_skpd || props.skpd.nama_dinas || 'SKPD'}`;
    pdfForm.value.penandatangan_1_nip = props.skpd.user?.user_detail?.nip || '';
    
    showPdfModal.value = true;
}


// Function untuk generate PDF - MENGIKUTI POLA RENCANA AWAL
async function generatePDF() {
    if (!selectedTugasId.value || !props.tid) {
        alert('Data tidak lengkap untuk generate PDF');
        return;
    }
    
    isGeneratingPdf.value = true;
    
    try {
        // Menggunakan Inertia router seperti PDF rencana awal
        const pdfData = {
            ...pdfForm.value,
            tid: props.tid, // Tambahkan tid ke form data
            tugasId: selectedTugasId.value
        };
        
        console.log('PDF Generation using Inertia router:', {
            tid: props.tid,
            tugasId: selectedTugasId.value,
            pdfData: pdfData
        });
        
        // Use fetch API for file download - MENGIKUTI POLA PDF RENCANA AWAL
        const getCsrfToken = () => {
            const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (metaToken) return metaToken;
            
            const pageProps = (window as any).page?.props;
            if (pageProps?.csrf_token) return pageProps.csrf_token;
            
            if ((window as any).Laravel && (window as any).Laravel.csrfToken) {
                return (window as any).Laravel.csrfToken;
            }
            
            return '';
        };
        
        const csrfToken = getCsrfToken();
        
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to recognize AJAX request
        };

        console.log('PDF generation request headers:', headers);
        console.log('Request data:', pdfData);

        const response = await fetch(route('pdf.triwulan.generate', {
            tid: props.tid,
            tugasId: selectedTugasId.value
        }), {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(pdfData),
            credentials: 'same-origin', // Important for CSRF cookie handling
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('PDF generation failed:', response.status, errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Handle PDF download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        
        // Generate filename
        const triwulanNames = {
            1: 'Triwulan_I',
            2: 'Triwulan_II', 
            3: 'Triwulan_III',
            4: 'Triwulan_IV'
        };
        const triwulanName = triwulanNames[props.tid as keyof typeof triwulanNames] || 'Triwulan';
        const skpdName = (props.skpd.nama_skpd || props.skpd.nama_dinas || 'SKPD').replace(/[^a-zA-Z0-9]/g, '_');
        a.download = `Laporan_${triwulanName}_${skpdName}_${pdfData.tahun}.pdf`;
        
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Close modal and show success message
        showPdfModal.value = false;
        console.log('PDF downloaded successfully');
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Terjadi kesalahan saat membuat PDF: ' + (error as Error).message);
    } finally {
        // Reset loading state in all cases
        isGeneratingPdf.value = false;
    }
}


</script>
<template>
    <div class="flex h-full flex-1 flex-col gap-4 bg-gray-100 p-4 dark:bg-gray-800">
        <!-- Card Monitoring Triwulan -->
        <div v-if="tid && urusanTugas && urusanTugas.length > 0" class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="mr-4 rounded-full bg-green-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-green-700">Monitoring {{ getTriwulanName(tid) }}</h2>
                        <p class="text-base text-gray-600 mt-1">{{ skpd?.nama_dinas || 'SKPD' }}</p>
                        <p class="text-sm text-gray-500">Laporan monitoring triwulan periode {{ new Date().getFullYear() }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- PDF Download Button matching Rencana Awal style -->
                    <button
                        class="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700 shadow-md"
                        @click="openPdfModal(urusanTugas[0]?.id || 0)"
                        :title="`Download PDF ${getTriwulanName(tid)}`"
                    >
                        <FileText class="w-4 h-4" />
                        <span class="text-sm font-medium">Download PDF {{ getTriwulanName(tid) }}</span>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gradient-to-r from-green-50 to-blue-50">
                        <tr>
                            <th class="border-b border-gray-200 px-6 py-4 text-left text-base font-semibold text-gray-700">Tugas Perangkat Daerah</th>
                            <th class="border-b border-gray-200 px-6 py-4 w-40 text-center text-base font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="!urusanTugas || urusanTugas.length === 0">
                            <td colspan="2" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada tugas yang tersedia</td>
                        </tr>
                        <tr v-for="tugas in urusanTugas" :key="tugas.id" class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-gray-700 font-medium">{{ getTaskLabel(tugas) }}</td>
                            <td class="px-6 py-4 w-40">
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="flex items-center justify-center bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-3 py-2 rounded-lg shadow-md transition-colors duration-200"
                                        @click="ShowTugas(tugas.id)"
                                        title="Lihat Monitoring"
                                    >
                                        <Binoculars class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
            
        <!-- PDF Configuration Modal -->
        <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/20 dark:bg-gray-900/20 transition-all duration-300 ease-in-out">
            <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 dark:border-gray-700/20 max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto custom-scrollbar transform transition-all duration-300 ease-in-out scale-100 animate-in fade-in-0 zoom-in-95">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h-4v4"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Konfigurasi PDF {{ getTriwulanName(tid) }}</h2>
                        </div>
                        <button @click="showPdfModal = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30">
                        <p class="text-gray-700 dark:text-gray-300 font-medium">
                            {{ skpd.nama_skpd || skpd.nama_dinas || 'SKPD' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Kode Organisasi: {{ skpd.kode_organisasi || '-' }}
                        </p>
                    </div>

                    <!-- PDF Form -->
                    <div class="space-y-6">
                        <!-- Periode Tahun -->
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800/30 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-blue-100 dark:bg-blue-800/50 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Periode Tahun</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pilih tahun untuk data yang akan ditampilkan dalam laporan PDF</p>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun</label>
                                <select v-model="pdfForm.tahun" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                    <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Penandatangan -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-6 border border-emerald-100 dark:border-emerald-800/30 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-800/50 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Penandatangan</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Informasi pejabat yang akan menandatangani (Kepala SKPD)</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tempat</label>
                                    <input v-model="pdfForm.penandatangan_1_tempat" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md" placeholder="Nama kota/tempat">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal</label>
                                    <input v-model="pdfForm.penandatangan_1_tanggal" type="date" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                                    <input v-model="pdfForm.penandatangan_1_nama" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md" placeholder="Nama lengkap penandatangan">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jabatan</label>
                                    <input v-model="pdfForm.penandatangan_1_jabatan" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md" placeholder="Jabatan penandatangan">
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                                <input v-model="pdfForm.penandatangan_1_nip" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md" placeholder="NIP penandatangan">
                            </div>
                        </div>

                        <!-- Pengaturan PDF -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-purple-100 dark:border-purple-800/30 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-purple-100 dark:bg-purple-800/50 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Pengaturan PDF</h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Konfigurasi format dan ukuran dokumen PDF</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ukuran Kertas</label>
                                    <select v-model="pdfForm.paper_size" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                        <option value="A4">A4</option>
                                        <option value="A3">A3</option>
                                        <option value="Letter">Letter</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Orientasi</label>
                                    <select v-model="pdfForm.orientation" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                        <option value="portrait">Portrait</option>
                                        <option value="landscape">Landscape</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Margin Halaman</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Atas (mm)</label>
                                        <input v-model="pdfForm.margin_top" type="number" min="0" max="50" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Kanan (mm)</label>
                                        <input v-model="pdfForm.margin_right" type="number" min="0" max="50" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Bawah (mm)</label>
                                        <input v-model="pdfForm.margin_bottom" type="number" min="0" max="50" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Kiri (mm)</label>
                                        <input v-model="pdfForm.margin_left" type="number" min="0" max="50" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-200 shadow-sm hover:shadow-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex flex-col sm:flex-row justify-between gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showPdfModal = false" type="button" class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </div>
                        </button>
                        <button @click="generatePDF" :disabled="isGeneratingPdf" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-xl font-medium shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="{ 'animate-spin': isGeneratingPdf }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path v-if="!isGeneratingPdf" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                {{ isGeneratingPdf ? 'Sedang Membuat PDF...' : 'Download PDF' }}
                            </div>
                        </button>
                    </div>
                </div>
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

/* Custom scrollbar for modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.5);
}

/* Animation classes */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-in {
    animation: fadeIn 0.3s ease-out;
}

.fade-in-0 {
    animation-fill-mode: both;
}

.zoom-in-95 {
    animation-name: fadeIn;
}

/* Loading spinner animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
