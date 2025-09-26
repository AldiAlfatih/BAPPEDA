<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Binoculars } from 'lucide-vue-next';
import ActivityLogger from '@/services/activityLogger';

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
    { title: 'Monitoring', href: route('monitoring.index') },
    { title: `Monitoring Rencana Awal`, href: '' },
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

function ShowTugas(tugas: { id: number; kode_nomenklatur: { id: number; jenis_nomenklatur: number } }) {
    // Gunakan clean URL: /rencana-awal/rencanaawal/{tugasId}/urusan/{urusanId}
    const urusanId = tugas.kode_nomenklatur.id; // Karena yang ditampilkan adalah daftar Urusan (jenis 0)
    const url = route('monitoring.rencanaawal.urusan', {
        id: tugas.id,
        urusanId,
    });
    router.visit(url);
}

function getTaskLabel(task: { kode_nomenklatur: { nomor_kode: any; nomenklatur: any } }) {
    return `${task.kode_nomenklatur.nomor_kode} - ${task.kode_nomenklatur.nomenklatur}`;
}

// PDF Modal state
const showPdfModal = ref(false);
const isGeneratingPdf = ref(false);

// PDF form data
const pdfForm = ref({
    tahun: new Date().getFullYear(),
    penandatangan_1_tempat: 'Parepare',
    penandatangan_1_tanggal: new Date().toISOString().split('T')[0],
    penandatangan_1_nama: props.user.skpd?.kepala_name || '',
    penandatangan_1_jabatan: 'Kepala SKPD',
    penandatangan_1_nip: getUserNip(props.user),
    paper_size: 'A4',
    orientation: 'portrait',
    margin_top: 20,
    margin_right: 20,
    margin_bottom: 20,
    margin_left: 20,
});

// Available years for PDF
const availableYears = computed(() => {
    const years = [];
    const currentYear = new Date().getFullYear();
    for (let i = currentYear; i >= currentYear - 5; i--) {
        years.push(i);
    }
    return years.sort((a, b) => b - a);
});

// Handle PDF modal open
const handlePDFDownload = async () => {
    if (!props.user?.id) return;

    try {
        // 📝 Log aktivitas download PDF
        await ActivityLogger.logPDFDownload('Rencana Awal', {
            user_id: props.user.id,
            skpd_name: props.user.skpd?.nama_skpd,
            tahun: pdfForm.value.tahun,
        });

        // Open PDF modal
        showPdfModal.value = true;
    } catch (error) {
        console.error('Error opening PDF form:', error);
        alert('Terjadi kesalahan saat membuka form PDF. Silakan coba lagi.');
    }
};

// Alternative PDF generation using Inertia (more reliable for CSRF)
const generatePDFWithInertia = () => {
    // Get first tugas ID for PDF generation
    const firstTugas = urusanTugas.value[0];
    if (!firstTugas?.id) {
        alert('Tidak ada tugas yang tersedia untuk generate PDF');
        return;
    }

    isGeneratingPdf.value = true;

    // Use Inertia visit with POST method for better CSRF handling
    router.post(route('pdf.rencana-awal.generate', firstTugas.id), pdfForm.value, {
        onSuccess: (page) => {
            // Handle success - this won't be called for file downloads
            console.log('PDF generation initiated successfully');
        },
        onError: (errors) => {
            console.error('PDF generation failed:', errors);
            if (errors.message && errors.message.includes('CSRF')) {
                alert('Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.');
                if (confirm('Apakah Anda ingin me-refresh halaman sekarang?')) {
                    window.location.reload();
                }
            } else {
                alert('Terjadi kesalahan saat membuat PDF: ' + (errors.message || 'Unknown error'));
            }
        },
        onFinish: () => {
            isGeneratingPdf.value = false;
            showPdfModal.value = false;
        },
        preserveState: true,
        preserveScroll: true,
        forceFormData: false,
    });
};

// Handle PDF generation with fetch (original method with enhanced error handling)
const generatePDF = async () => {
    // Get first tugas ID for PDF generation
    const firstTugas = urusanTugas.value[0];
    if (!firstTugas?.id) {
        alert('Tidak ada tugas yang tersedia untuk generate PDF');
        return;
    }

    isGeneratingPdf.value = true;

    try {
        console.log('Starting PDF generation with tugas ID:', firstTugas.id);
        console.log('PDF form data:', pdfForm.value);

        // Get CSRF token with multiple fallback methods
        const getCsrfToken = () => {
            // Method 1: From meta tag
            const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (metaToken) {
                console.log('CSRF token found in meta tag');
                return metaToken;
            }

            // Method 2: From page props (Inertia)
            const pageProps = page.props as any;
            if (pageProps.csrf_token) {
                console.log('CSRF token found in page props');
                return pageProps.csrf_token;
            }

            // Method 3: From window object (if available)
            if ((window as any).Laravel && (window as any).Laravel.csrfToken) {
                console.log('CSRF token found in window.Laravel');
                return (window as any).Laravel.csrfToken;
            }

            console.warn('No CSRF token found!');
            return '';
        };

        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            throw new Error('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.');
        }

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minutes timeout

        // Enhanced headers with additional security headers
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to recognize AJAX request
        };

        console.log('Request headers:', headers);
        console.log('CSRF token being sent:', csrfToken.substring(0, 10) + '...');

        const response = await fetch(route('pdf.rencana-awal.generate', firstTugas.id), {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(pdfForm.value),
            signal: controller.signal,
            credentials: 'same-origin', // Important for CSRF cookie handling
        });

        clearTimeout(timeoutId);

        console.log('PDF generation response status:', response.status);
        console.log('PDF generation response headers:', response.headers);

        if (response.ok) {
            const contentType = response.headers.get('Content-Type');
            if (contentType && contentType.includes('application/pdf')) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Rencana_Awal_${props.user.skpd?.nama_skpd || 'SKPD'}_${new Date().toISOString().split('T')[0]}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                // Close modal
                showPdfModal.value = false;
            } else {
                const errorData = await response.json();
                console.error('PDF generation error response:', errorData);
                throw new Error(errorData.error || 'Response bukan file PDF');
            }
        } else {
            try {
                const errorData = await response.json();
                console.error('HTTP error response:', errorData);
                
                // Handle specific CSRF token mismatch error
                if (response.status === 419 || errorData.message?.includes('CSRF token mismatch')) {
                    console.error('CSRF token mismatch detected');
                    alert('Sesi Anda telah berakhir atau token keamanan tidak valid. Silakan refresh halaman dan coba lagi.');
                    // Optionally reload the page to get fresh CSRF token
                    if (confirm('Apakah Anda ingin me-refresh halaman untuk mendapatkan token keamanan yang baru?')) {
                        window.location.reload();
                    }
                    return;
                }
                
                // Show debug information if available
                if (errorData.debug) {
                    throw new Error(`${errorData.error}\nDebug: ${errorData.debug.message} (${errorData.debug.file}:${errorData.debug.line})`);
                } else {
                    throw new Error(errorData.error || `HTTP Error ${response.status}: ${response.statusText}`);
                }
            } catch (jsonError) {
                console.error('Failed to parse error response:', jsonError);
                
                // Handle CSRF error even if JSON parsing fails
                if (response.status === 419) {
                    alert('Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.');
                    return;
                }
                
                throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
            }
        }
    } catch (error) {
        console.error('Error generating PDF:', error);
        
        if (error.name === 'AbortError') {
            alert('Timeout: Proses pembuatan PDF memakan waktu terlalu lama. Silakan coba lagi atau hubungi administrator.');
        } else if (error.message.includes('Failed to fetch')) {
            alert('Koneksi gagal. Pastikan koneksi internet Anda stabil dan coba lagi.');
        } else {
            alert(`Terjadi kesalahan saat membuat PDF: ${error.message}`);
        }
    } finally {
        isGeneratingPdf.value = false;
    }
};
</script>

<template>
    <Head title="Monitoring Rencana Awal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 bg-gray-100 p-4 dark:bg-gray-800">
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

            <!-- Header section - Matching Triwulan Layout -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-4 rounded-full bg-blue-100 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-600">Monitoring Rencana Awal</h1>
                            <p class="text-sm text-gray-500">Monitoring Detail {{ user.skpd?.nama_skpd || 'Perangkat Daerah' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-blue-600">Data Rencana Awal</span>
                            <span class="rounded-md bg-blue-100 px-3 py-1 text-sm font-bold text-blue-800">
                                {{ new Date().getFullYear() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detail Rencana Awal Header -->
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h2 class="mb-2 text-sm font-medium text-blue-600">Detail Rencana Awal</h2>
                    <p class="text-xl font-bold text-blue-800">{{ user.skpd?.nama_skpd || 'SKPD' }}</p>
                </div>
            </div>

            <!-- Detail Perangkat Daerah -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <div class="mb-6 flex items-center">
                    <div class="mr-4 rounded-full bg-blue-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
                        <p class="text-sm text-gray-500">Informasi SKPD dan Kode Urusan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Perangkat Daerah</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.nama_skpd || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.kepala_name || 'Tidak tersedia' }}</p>
                        <p class="font-mono text-sm text-gray-500">NIP: {{ getUserNip(user) || '-' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.operator_name || 'Tidak tersedia' }}</p>
                        <p class="font-mono text-sm text-gray-500">NIP: {{ user.skpd?.operator_nip || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Rencana Awal Section with PDF Button -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-4 rounded-full bg-blue-100 p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h-4v4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-600">Rencana Awal</h2>
                            <p class="text-sm text-gray-500">Laporan rencana awal {{ user.skpd?.nama_skpd || 'SKPD' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- PDF Download Button matching Triwulan style -->
                        <button
                            class="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700 shadow-md"
                            @click="handlePDFDownload"
                            title="Download PDF Rencana Awal"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h-4v4"/>
                            </svg>
                            <span class="text-sm font-medium">Download PDF Rencana Awal</span>
                        </button>
                    </div>
                </div>

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
                                            @click="ShowTugas(tugas)"
                                        >
                                            <Binoculars class="w-4 h-4 mr-2 ml-2" />
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
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Konfigurasi PDF Rencana Awal</h2>
                        </div>
                        <button @click="showPdfModal = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30">
                        <p class="text-gray-700 dark:text-gray-300 font-medium">
                            {{ user.skpd?.nama_skpd || 'SKPD' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Kode Organisasi: {{ user.skpd?.kode_organisasi || '-' }}
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
    </AppLayout>
</template>

<style scoped>
/* Table Styles */
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

/* Animation Styles */
.notification {
    transition: opacity 0.5s ease-in-out;
}

/* Modal Animation */
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

/* Custom backdrop blur for better browser support */
.backdrop-blur-custom {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Smooth focus transitions */
input:focus,
select:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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
