<script setup lang="ts">
import { useRencanaAwalData } from '@/composables/useRencanaAwalData';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import ActivityLogger from '@/services/activityLogger';

interface User {
    id: number;
    name: string;
    nama_skpd: string;
    skpd_id?: number;
    nip: string;
}

interface Props {
    user?: User;
    programTugas?: any[];
    kegiatanTugas?: any[];
    subkegiatanTugas?: any[];
    bidangUrusanTugas?: any[];
    kepalaSkpd?: string;
    isFinalized?: boolean | number;
    flash?: {
        success?: string;
        error?: string;
    };
    skpd?: {
        nama_dinas: string;
        kode_organisasi?: string;
        no_dpa?: string;
        skpd_kepala: Array<{
            user: {
                name: string;
                user_detail: {
                    nama: string;
                    nip: string;
                };
            };
        }>;
    };
    rencana_awal?: {
        indikator: string;
        target: string;
    } | null;
    urusan?: number[];
    bidangUrusan?: number[];
    bidangUrusanList?: any[];
    monitoring?: {
        sumber_dana: string;
        pagu_pokok: number;
        pagu_parsial: number;
        pagu_perubahan: number;
        targets: Array<any>;
        realisasi: Array<any>;
    };
    tugas?: any;
    dataAnggaranTerakhir?: Record<
        number,
        {
            sumber_anggaran: {
                dak: boolean;
                dak_peruntukan: boolean;
                dak_fisik: boolean;
                dak_non_fisik: boolean;
                blud: boolean;
            };
            values: {
                rencana_awal: {
                    dak?: number;
                    dak_peruntukan?: number;
                    dak_fisik?: number;
                    dak_non_fisik?: number;
                    blud?: number;
                };
                parsial: {
                    dak?: number;
                    dak_peruntukan?: number;
                    dak_fisik?: number;
                    dak_non_fisik?: number;
                    blud?: number;
                };
                budget_change: {
                    dak?: number;
                    dak_peruntukan?: number;
                    dak_fisik?: number;
                    dak_non_fisik?: number;
                    blud?: number;
                };
            };
            is_parsial_enabled?: boolean;
            is_budget_change_enabled?: boolean;
        }
    >;
    periodeAktif?: Array<{ id: number; tahap: { id: number; tahap: string }; tahun: { id: number; tahun: string }; status: number }>;
    semuaPeriodeAktif?: Array<{ id: number; tahap: { id: number; tahap: string }; tahun: { id: number; tahun: string }; status: number }>;
    tahunAktif?: { id: number; tahun: string; status: number } | null;
    allTahun?: Array<{ id: number; tahun: string; status: number }>;
    bidangurusanTugas?: any[];
    availableUrusans?: Array<{
        id: number;
        nomor_kode: string;
        nomenklatur: string;
        jenis_nomenklatur: number;
    }>;
    selectedUrusanId?: number | null;
    breadcrumbUserId?: number | null;
}

const props = defineProps<Props>();

// Get current user role from global auth
const page = usePage();
const auth = page.props.auth as {
    user: {
        role?: string;
    };
};

// Check if current user is Admin or Operator (hide actions for these roles)
const isAdminOrOperator = computed(() => {
    const userRole = auth?.user?.role?.toLowerCase();
    return userRole === 'admin' || userRole === 'operator';
});

// Add reactive state for selected period
const selectedPeriodeId = ref<number | null>(null);

// Reactive state for selected year - menggunakan computed agar selalu sinkron dengan props
const selectedTahunId = computed(() => props.tahunAktif?.id || null);

// Debounced update untuk meningkatkan responsivitas UI
let updateTimeout: number | null = null;
const debouncedUIUpdate = (callback: () => void, delay: number = 100) => {
    if (updateTimeout) {
        clearTimeout(updateTimeout);
    }
    updateTimeout = setTimeout(callback, delay);
};

// Handler for year change
const handleTahunChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newTahunId = target.value ? parseInt(target.value) : null;

    console.log('Year change triggered:', {
        oldTahunId: selectedTahunId.value,
        newTahunId,
        currentEditingTargets: Object.keys(editingTargets.value).length,
    });

    if (selectedTahunId.value !== newTahunId) {
        // PERBAIKAN: Simpan state editingTargets sebelum navigasi
        const currentEditingState = saveEditingState();
        console.log('Saved editing state before navigation:', currentEditingState);

        // Reload data with the new year
        const selectedTahun = props.allTahun?.find((t) => t.id === newTahunId);
        if (selectedTahun && props.tugas?.id) {
            console.log('Navigating to year:', selectedTahun.tahun);
            const baseUrl = props.selectedUrusanId
                ? route('monitoring.rencanaawal.urusan.tahun', {
                    id: props.tugas.id,
                    urusanId: props.selectedUrusanId,
                    tahun: selectedTahun.tahun,
                })
                : route('monitoring.rencanaawal.tahun', {
                    id: props.tugas.id,
                    tahun: selectedTahun.tahun,
                });

            router.visit(baseUrl, {
                preserveState: false,
                onSuccess: () => {
                    console.log('Navigation successful, restoring state...');
                    // PERBAIKAN: Restore editing state setelah navigasi berhasil
                    nextTick(() => {
                        restoreEditingState(currentEditingState);
                        ensureEditingTargets();
                        console.log('State restoration complete');
                    });
                },
            });
        }
    }
};

// Tambahkan state untuk edit target subkegiatan
const editingTargets = ref<Record<string, any>>({});
const loadingRow = ref<string | null>(null); // Unique key: subkegiatan.id + sumberDana
const successRow = ref<string | null>(null); // Unique key: subkegiatan.id + sumberDana
const errorRow = ref<string | null>(null); // Unique key: subkegiatan.id + sumberDana
const updatingRow = ref<string | null>(null); // Untuk tracking row yang sedang diupdate

// State untuk PDF modal - Matching Triwulan implementation
const showPdfModal = ref(false);
const isGeneratingPdf = ref(false);

// Form data untuk PDF - Matching Triwulan structure
const pdfForm = ref({
    tahun: new Date().getFullYear(),
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

// Function untuk membuka modal PDF - Matching Triwulan implementation
function openPdfModal() {
    // Set default values dari SKPD data
    pdfForm.value.penandatangan_1_nama = props.skpd?.skpd_kepala?.[0]?.user?.name || props.user?.name || '';
    pdfForm.value.penandatangan_1_jabatan = `Kepala ${props.skpd?.nama_dinas || props.user?.nama_skpd || 'SKPD'}`;
    pdfForm.value.penandatangan_1_nip = props.skpd?.skpd_kepala?.[0]?.user?.user_detail?.nip || props.user?.nip || '';
    
    showPdfModal.value = true;
}

// Function untuk generate PDF - Matching existing implementation
async function generatePDF() {
    if (!props.tugas?.id) {
        alert('Data tidak lengkap untuk generate PDF');
        return;
    }
    
    isGeneratingPdf.value = true;
    
    try {
        // Use existing PDF generation route for rencana awal
        const response = await fetch(route('pdf.rencanaawal.generate', props.tugas.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(pdfForm.value),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Handle PDF download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        
        // Generate filename
        const skpdName = (props.skpd?.nama_dinas || props.user?.nama_skpd || 'SKPD').replace(/[^a-zA-Z0-9]/g, '_');
        a.download = `Laporan_Rencana_Awal_${skpdName}_${pdfForm.value.tahun}.pdf`;
        
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
        isGeneratingPdf.value = false;
    }
}

// Computed property untuk menghitung agregasi real-time dari editingTargets
const aggregatedTargets = computed(() => {
    const result: Record<string, any[]> = {};

    // Group editingTargets by subKegiatan ID
    Object.keys(editingTargets.value).forEach((uniqueKey) => {
        const subKegiatanId = uniqueKey.split('-')[0]; // Extract subKegiatan ID from unique key

        if (!result[subKegiatanId]) {
            result[subKegiatanId] = [
                { kinerja_fisik: 0, keuangan: 0 },
                { kinerja_fisik: 0, keuangan: 0 },
                { kinerja_fisik: 0, keuangan: 0 },
                { kinerja_fisik: 0, keuangan: 0 },
            ];
        }

        const targets = editingTargets.value[uniqueKey];
        if (targets && Array.isArray(targets)) {
            targets.forEach((target: any, index: number) => {
                if (index < 4 && target) {
                    // Sum keuangan values
                    result[subKegiatanId][index].keuangan += parseFloat(target.keuangan?.toString().replace(/[^\d]/g, '') || '0');
                    // Sum kinerja_fisik values (will be averaged later)
                    result[subKegiatanId][index].kinerja_fisik += parseFloat(target.kinerja_fisik || '0');
                }
            });
        }
    });

    // Calculate average for kinerja_fisik
    Object.keys(result).forEach((subKegiatanId) => {
        // Count how many sources have data for this subKegiatan
        const sourceCount = Object.keys(editingTargets.value).filter((key) => key.startsWith(subKegiatanId + '-')).length;

        if (sourceCount > 1) {
            result[subKegiatanId].forEach((target) => {
                target.kinerja_fisik = target.kinerja_fisik / sourceCount;
            });
        }
    });

    return result;
});

// Computed property untuk mengecek status periode
const currentPeriodeStatus = computed(() => {
    if (!selectedPeriodeId.value || !props.semuaPeriodeAktif) {
        return { canInput: false, reason: 'Periode tidak ditemukan', status: 'unknown' };
    }
    
    const selectedPeriode = props.semuaPeriodeAktif.find(p => p.id === selectedPeriodeId.value);
    
    if (!selectedPeriode) {
        return { canInput: false, reason: 'Periode tidak ditemukan', status: 'not_found' };
    }
    
    // Validasi khusus untuk halaman Rencana Awal
    const isRencanaAwalPage = window.location.pathname.includes('rencana-awal');
    const isRencanaAwalPeriode = selectedPeriode.tahap?.tahap?.toLowerCase().includes('rencana');
    
    // Jika di halaman Rencana Awal, harus validasi periode Rencana Awal
    if (isRencanaAwalPage && !isRencanaAwalPeriode) {
        return {
            canInput: false,
            reason: 'Anda berada di halaman Rencana Awal, namun periode yang dipilih bukan periode Rencana Awal',
            status: 'wrong_type'
        };
    }
    
    // Status periode: 0 = Tutup, 1 = Buka, 2 = Selesai
    if (selectedPeriode.status === 0) {
        return { 
            canInput: false, 
            reason: `Periode ${selectedPeriode.tahap?.tahap || 'ini'} telah ditutup oleh admin`, 
            status: 'closed' 
        };
    } else if (selectedPeriode.status === 2) {
        return { 
            canInput: false, 
            reason: `Periode ${selectedPeriode.tahap?.tahap || 'ini'} telah selesai dan tidak dapat diubah lagi`, 
            status: 'finished' 
        };
    } else {
        return { canInput: true, reason: '', status: 'open' };
    }
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Monitoring', href: route('monitoring.index') },
    { 
        title: `Monitoring Detail ${props.user?.nama_skpd ?? '-'}`, 
        href: props.breadcrumbUserId ? route('monitoring.show', props.breadcrumbUserId) : '' 
    },
    { title: 'Rencana Awal PD', href: '' },
]);

// Initialize with the active period if available
onMounted(() => {
    // Cari periode "Rencana Awal" secara spesifik
    if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
        // PRIORITAS 1: Cari periode dengan tahap "Rencana" atau "Rencana Awal"
        const rencanaAwalPeriode = props.semuaPeriodeAktif.find(p => 
            p.tahap?.tahap?.toLowerCase().includes('rencana') || 
            p.tahap?.tahap?.toLowerCase() === 'rencana awal'
        );
        
        if (rencanaAwalPeriode) {
            selectedPeriodeId.value = rencanaAwalPeriode.id;
        } else {
            // FALLBACK: Jika tidak ada periode rencana awal, cari yang aktif
            const activePeriode = props.semuaPeriodeAktif.find(p => p.status === 1);
            if (activePeriode) {
                selectedPeriodeId.value = activePeriode.id;
            } else {
                selectedPeriodeId.value = props.semuaPeriodeAktif[0].id;
            }
        }
    } else if (props.periodeAktif && props.periodeAktif.length > 0) {
        selectedPeriodeId.value = props.periodeAktif[0].id;
    }
});


// Format angka ke dalam format rupiah
const formatRupiah = (value: number | string | null | undefined): string => {
    // PERBAIKAN: Handle null, undefined, atau empty values
    if (value === null || value === undefined || value === '') {
        return '0';
    }

    const numberString = value.toString().replace(/[^,\d]/g, '');
    const split = numberString.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        rupiah += (sisa ? '.' : '') + ribuan.join('.');
    }

    return 'Rp ' + rupiah + (split[1] ? ',' + split[1] : '');
};

// Handler for period change
const handlePeriodeChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newPeriodeId = target.value ? parseInt(target.value) : null;

    if (selectedPeriodeId.value !== newPeriodeId) {
        selectedPeriodeId.value = newPeriodeId;

        // PERBAIKAN: Gunakan route yang konsisten untuk mencegah path ganda
        if (props.tugas?.id) {
            const query = newPeriodeId ? `?periode_id=${newPeriodeId}` : '';
            if (props.selectedUrusanId) {
                router.visit(`/rencana-awal/rencanaawal/${props.tugas.id}/urusan/${props.selectedUrusanId}${query}`, {
                    preserveState: true,
                    only: ['dataAnggaranTerakhir', 'subkegiatanTugas'],
                });
            } else {
                router.visit(`/rencana-awal/rencanaawal/${props.tugas.id}${query}`, {
                    preserveState: true,
                    only: ['dataAnggaranTerakhir', 'subkegiatanTugas'],
                });
            }
        } else if (props.breadcrumbUserId) {
            // PERBAIKAN: Gunakan breadcrumbUserId untuk konsistensi navigasi
            router.visit(`/rencana-awal/${props.breadcrumbUserId}?periode_id=${newPeriodeId || ''}`, {
                preserveState: true,
                only: ['dataAnggaranTerakhir', 'subkegiatanTugas'],
            });
        }
    }
};

// Add a function to update calculations when values change
const recalculateAllTargets = () => {
    // This is just a dummy function to trigger recalculation
    // The actual work is done in the computed properties
    console.log('Recalculating all targets');
    // We don't need to do anything here as the computed properties will recalculate automatically
};

// Watch for changes in the editing targets and trigger recalculation
watch(
    editingTargets,
    () => {
        recalculateAllTargets();
    },
    { deep: true },
);

// Also watch for changes in the subkegiatan data
watch(
    () => props.subkegiatanTugas,
    () => {
        recalculateAllTargets();
    },
    { deep: true },
);

// Also watch for changes in the dataAnggaranTerakhir
watch(
    () => props.dataAnggaranTerakhir,
    () => {
        recalculateAllTargets();
    },
    { deep: true },
);

// Computed property untuk mencari urusan yang tepat
const urusanPemerintahan = computed(() => {
    console.log('Debug urusan data:', {
        availableUrusans: props.availableUrusans,
        availableUrusansLength: props.availableUrusans?.length,
        selectedUrusanId: props.selectedUrusanId,
        tugas: props.tugas?.kode_nomenklatur,
        bidangurusanTugas: props.bidangurusanTugas?.[0]?.kode_nomenklatur,
    });

    // Jika tersedia availableUrusans, gunakan data urusan yang benar
    if (props.availableUrusans && props.availableUrusans.length > 0) {
        // Jika ada selectedUrusanId, cari urusan yang sesuai
        if (props.selectedUrusanId) {
            const selectedUrusan = props.availableUrusans.find((u) => u.id === props.selectedUrusanId);
            if (selectedUrusan) {
                return {
                    nomor_kode: selectedUrusan.nomor_kode,
                    nomenklatur: selectedUrusan.nomenklatur,
                };
            }
        }

        // Jika tidak ada selectedUrusanId, gunakan urusan pertama
        const firstUrusan = props.availableUrusans[0];
        return {
            nomor_kode: firstUrusan.nomor_kode,
            nomenklatur: firstUrusan.nomenklatur,
        };
    }

    // Fallback 1: Jika tugas adalah urusan (jenis_nomenklatur = 0), gunakan itu
    if (props.tugas?.kode_nomenklatur?.jenis_nomenklatur === 0) {
        return {
            nomor_kode: props.tugas.kode_nomenklatur.nomor_kode,
            nomenklatur: props.tugas.kode_nomenklatur.nomenklatur,
        };
    }

    // Fallback 2: Coba ekstrak urusan dari kode tugas saat ini
    if (props.tugas?.kode_nomenklatur?.nomor_kode) {
        const kodeArray = props.tugas.kode_nomenklatur.nomor_kode.split('.');
        if (kodeArray.length > 0) {
            const urusanKode = kodeArray[0];
            // Hardcode beberapa urusan umum sebagai fallback
            const commonUrusan: Record<string, string> = {
                '1': 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR',
                '2': 'URUSAN PEMERINTAHAN WAJIB YANG TIDAK BERKAITAN DENGAN PELAYANAN DASAR',
                '3': 'URUSAN PEMERINTAHAN PILIHAN',
                '4': 'URUSAN PEMERINTAHAN LAINNYA',
            };

            if (commonUrusan[urusanKode]) {
                return {
                    nomor_kode: urusanKode,
                    nomenklatur: commonUrusan[urusanKode],
                };
            }
        }
    }

    // Default fallback
    return {
        nomor_kode: 'N/A',
        nomenklatur: 'Urusan tidak tersedia',
    };
});

// Use the hierarchical data composable
const {
    formattedSubKegiatanData,
    calculateKegiatan: calculateKegiatanFromComposable,
    calculateProgram: calculateProgramFromComposable,
    calculateBidangUrusan: calculateBidangUrusanFromComposable,
    getUniqueKey,
} = useRencanaAwalData(props as any, editingTargets);

// Use calculate functions from composable
const calculateKegiatan = calculateKegiatanFromComposable;

const calculateProgram = calculateProgramFromComposable;

const calculateBidangUrusan = calculateBidangUrusanFromComposable;

// Helper untuk inisialisasi data target berdasarkan sumber anggaran
function getInitialTargets(subKegiatan: any, sumberDana?: string) {
    console.log('getInitialTargets called for:', {
        subKegiatanId: subKegiatan.id,
        sumberDana,
        hasMonitoring: !!subKegiatan.monitoring,
        monitoringType: Array.isArray(subKegiatan.monitoring) ? 'array' : typeof subKegiatan.monitoring,
        monitoringLength: Array.isArray(subKegiatan.monitoring) ? subKegiatan.monitoring.length : 'not array',
    });

    // PERBAIKAN: Akses monitoring yang benar - monitoring adalah collection/array
    let monitoring = null;
    if (Array.isArray(subKegiatan.monitoring) && subKegiatan.monitoring.length > 0) {
        monitoring = subKegiatan.monitoring[0]; // Ambil monitoring pertama dari collection
    } else if (subKegiatan.monitoring && !Array.isArray(subKegiatan.monitoring)) {
        monitoring = subKegiatan.monitoring; // Jika bukan array, gunakan langsung
    }

    console.log('Monitoring object:', {
        monitoring,
        hasTargetsBySumber: !!monitoring?.targets_by_sumber_anggaran,
        targetsBySumberKeys: monitoring?.targets_by_sumber_anggaran ? Object.keys(monitoring.targets_by_sumber_anggaran) : [],
    });

    // Jika ada sumberDana, cari target spesifik untuk sumber anggaran tersebut
    if (sumberDana && monitoring?.targets_by_sumber_anggaran) {
        const sumberAnggaranId = getSumberAnggaranId(sumberDana);
        const targetsBySumber = monitoring.targets_by_sumber_anggaran[sumberAnggaranId];

        console.log('Found targets by sumber:', {
            sumberAnggaranId,
            targetsBySumber,
            hasTargets: !!targetsBySumber?.targets,
        });

        if (targetsBySumber?.targets) {
            const result = [0, 1, 2, 3].map((i) => ({
                kinerja_fisik: targetsBySumber.targets[i]?.kinerja_fisik || '',
                keuangan: targetsBySumber.targets[i]?.keuangan || '',
            }));
            console.log('Returning specific targets:', result);
            return result;
        }
    }

    // Fallback ke targets umum atau kosong
    const targets = monitoring?.targets || subKegiatan.targets || [];
    const result = [0, 1, 2, 3].map((i) => ({
        kinerja_fisik: targets[i]?.kinerja_fisik || targets[i]?.kinerjaFisik || '',
        keuangan: targets[i]?.keuangan || '',
    }));
    console.log('Returning fallback targets:', result);
    return result;
}

// Tambahkan method untuk handle input target
function onInputTarget(uniqueKey: string, idx: number, field: 'kinerja_fisik' | 'keuangan', value: any) {
    if (editingTargets.value[uniqueKey] && editingTargets.value[uniqueKey][idx]) {
        if (field === 'kinerja_fisik') {
            // Hapus karakter non-numerik kecuali titik dan koma
            let rawValue = value.replace(/[^\d.,]/g, '');
            rawValue = rawValue.replace(',', '.'); // Ubah koma menjadi titik untuk decimal

            // Pastikan nilai tidak melebihi 100%
            const numericValue = parseFloat(rawValue);
            if (isNaN(numericValue)) {
                editingTargets.value[uniqueKey][idx][field] = '';
            } else {
                editingTargets.value[uniqueKey][idx][field] = Math.min(100, numericValue);
            }
        } else if (field === 'keuangan') {
            // Hapus karakter non-numerik
            const rawValue = value.replace(/[^\d]/g, '');
            const numericValue = parseInt(rawValue) || 0;
            editingTargets.value[uniqueKey][idx][field] = numericValue;
        }

        // Clear any error status when user makes changes
        if (errorRow.value === uniqueKey.split('-')[0]) {
            errorRow.value = null;
        }

        // PERBAIKAN: Tambahkan visual feedback untuk perubahan
        updatingRow.value = uniqueKey;

        // PERBAIKAN: Immediate UI update - langsung update tampilan tanpa delay
        // Ini memberikan feedback instant ke user
        editingTargets.value = { ...editingTargets.value };

        // PERBAIKAN: Debounced update untuk optimasi performa
        debouncedUIUpdate(() => {
            // Force Vue reactivity untuk memastikan semua computed properties ter-update
            editingTargets.value = { ...editingTargets.value };

            // Clear updating indicator setelah update
            setTimeout(() => {
                if (updatingRow.value === uniqueKey) {
                    updatingRow.value = null;
                }
            }, 200); // Kurangi delay untuk response yang lebih cepat
        }, 50); // Delay lebih pendek untuk responsivitas yang lebih baik
    }
}

// Tambahkan fungsi untuk mendapatkan ID sumber anggaran dari namanya
function getSumberAnggaranId(sumberDanaNama: string): number {
    // Lowercase dan normalisasi nama sumber dana
    const normalizedName = sumberDanaNama?.toLowerCase().trim() || 'dau';

    // Mapping nama sumber dana ke ID dengan lebih banyak kemungkinan
    const sumberDanaMapping: Record<string, number> = {
        // PERBAIKAN: Mapping yang benar sesuai database
        dau: 1, // ✅ "DAU" = id 1
        'dau peruntukan': 5, // ✅ "DAU Peruntukan" = id 5
        'dak fisik': 2, // ✅ "DAK Fisik" = id 2
        'dak non fisik': 3, // ✅ "DAK Non Fisik" = id 3
        'dak non-fisik': 3, // ✅ "DAK Non Fisik" = id 3 (variasi dengan tanda hubung)
        blud: 4, // ✅ "BLUD" = id 4
        apbd: 6,

        // Tambahan alternatif untuk kecocokan lebih baik
        dak: 3,
        multiple: 1,
        'belum diisi': 1,
    };

    const result = sumberDanaMapping[normalizedName] || 1;
    return result; // Default ke ID 1 jika tidak ditemukan
}

// PERBAIKAN: Fungsi simpan target dengan parameter sumberDana eksplisit
async function saveTargets(subKegiatan: any, sumberDana: string) {
    // PERBAIKAN: Gunakan sumberDana yang diterima sebagai parameter
    // Tidak perlu mencari dari formattedSubKegiatanData lagi
    if (!sumberDana) {
        console.error('sumberDana tidak ditemukan!');
        alert('Error: Sumber dana tidak ditemukan');
        return;
    }

    // Buat key unik untuk subkegiatan dan sumber dana ini
    const uniqueKey = getUniqueKey(subKegiatan, sumberDana);

    // PERBAIKAN: Gunakan unique key untuk loading state
    loadingRow.value = uniqueKey;
    errorRow.value = null;
    successRow.value = null;

    try {
        // Simpan ID subkegiatan yang sedang disimpan
        const subkegiatanId = subKegiatan.id;

        // Pastikan data editing untuk key ini sudah ada
        if (!editingTargets.value[uniqueKey]) {
            editingTargets.value[uniqueKey] = getInitialTargets(subKegiatan, sumberDana);
        }

        // Ambil data target yang sedang diedit
        const rawTargets = editingTargets.value[uniqueKey];

        // Validasi dan format data target
        const processedTargets = rawTargets.map((target: any, index: number) => {
            const kinerjaFisik = parseFloat(String(target.kinerja_fisik).replace(',', '.'));
            const keuangan = typeof target.keuangan === 'string' ? parseInt(target.keuangan.replace(/[^\d]/g, '')) : target.keuangan || 0;

            if (isNaN(kinerjaFisik) || kinerjaFisik < 0 || kinerjaFisik > 100) {
                throw new Error(`Kinerja fisik triwulan ${index + 1} harus berupa angka antara 0-100`);
            }

            if (isNaN(keuangan) || keuangan < 0) {
                throw new Error(`Keuangan triwulan ${index + 1} harus berupa angka positif`);
            }

            return {
                kinerja_fisik: kinerjaFisik,
                keuangan: keuangan,
                triwulan: index + 1,
            };
        });

        // Dapatkan ID sumber anggaran dari nama sumber dana
        const sumberAnggaranId = getSumberAnggaranId(sumberDana);

        // PERBAIKAN: Hapus kode pengambilan pagu karena tidak diperlukan di RencanaAwal
        // Pagu sudah diset di ManajemenAnggaranController dan tidak boleh diubah di sini

        // Siapkan payload untuk API
        const payload = {
            skpd_tugas_id: subKegiatan.id,
            tahun: props.tahunAktif?.tahun || new Date().getFullYear(),
            // PERBAIKAN: Hapus deskripsi karena tidak diperlukan di RencanaAwal
            // deskripsi: 'Rencana Awal', // Field ini hanya untuk TriwulanController
            targets: processedTargets,
            sumber_anggaran_id: sumberAnggaranId,
            periode_id: selectedPeriodeId.value,
            // PERBAIKAN: Hapus pagu karena tidak diperlukan di RencanaAwal
            // pagu: { ... }, // Pagu hanya diset di ManajemenAnggaranController
            // PENTING: Tambahkan flag untuk menandai ini adalah penyimpanan individual
            is_single_source_save: true,
        };

        console.log('Mengirim data:', payload);

        // PENTING: Cadangkan semua state editing sebelum API call (untuk debugging jika diperlukan)
        // const allEditingTargetsBackup = JSON.parse(JSON.stringify(editingTargets.value));

        // Simpan data semua baris subkegiatan yang ada sebelum menyimpan
        const allRowsForThisSubkegiatan = formattedSubKegiatanData.value.filter((item) => item.subKegiatan.id === subkegiatanId);

        // Simpan data normalisasi target untuk semua baris
        const allNormalizedTargetsBackup: Record<string, any> = {};
        allRowsForThisSubkegiatan.forEach((item) => {
            if (item.normalizedTargets) {
                allNormalizedTargetsBackup[item.sumberDana] = JSON.parse(JSON.stringify(item.normalizedTargets));
            }
        });

        // Simpan data semua subkegiatan sebelum API call
        const allSubkegiatanData: Record<number, any> = {};
        const uniqueSubkegiatanIds = [...new Set(formattedSubKegiatanData.value.map((item) => item.subKegiatan.id))];
        uniqueSubkegiatanIds.forEach((id) => {
            allSubkegiatanData[id] = preserveAllFundingSources(id);
        });

        router.post('/rencanaawal/save-target', payload, {
            preserveScroll: true,
            preserveState: false,
            onSuccess: async () => {
                // PERBAIKAN: Gunakan unique key untuk success state
                successRow.value = uniqueKey;

                // 📝 Log aktivitas rencana awal
                const totalKinerjaFisik = processedTargets.reduce((sum: number, target: any) => sum + target.kinerja_fisik, 0) / processedTargets.length;
                const totalKeuangan = processedTargets.reduce((sum: number, target: any) => sum + target.keuangan, 0);

                await ActivityLogger.logRencanaAwal('Menyimpan target rencana awal', {
                    subkegiatan_id: subKegiatan.id,
                    subkegiatan_name: subKegiatan.kode_nomenklatur?.nomenklatur || 'Sub Kegiatan',
                    sumber_dana: sumberDana,
                    sumber_anggaran_id: sumberAnggaranId,
                    targets: processedTargets,
                    avg_kinerja_fisik: totalKinerjaFisik,
                    total_keuangan: totalKeuangan,
                    periode_id: selectedPeriodeId.value,
                    tahun: props.tahunAktif?.tahun,
                });

                // PERBAIKAN: Immediate update - langsung update editingTargets dengan data yang baru disimpan
                // Ini memberikan feedback langsung ke user tanpa menunggu reload
                const savedTargets = processedTargets.map((target: any) => ({
                    kinerja_fisik: target.kinerja_fisik,
                    keuangan: target.keuangan,
                }));
                editingTargets.value[uniqueKey] = savedTargets;

                // Set timeout untuk menghilangkan success indicator
                setTimeout(() => {
                    if (successRow.value === uniqueKey) {
                        successRow.value = null;
                    }
                }, 3000);

                // PERBAIKAN: Refresh data dari server untuk sinkronisasi
                // Delay lebih pendek untuk response yang lebih cepat
                setTimeout(() => {
                    if (props.tugas?.id) {
                        router.reload({
                            only: ['subkegiatanTugas', 'dataAnggaranTerakhir'],
                            onSuccess: () => {
                                // 2. Setelah reload berhasil, pastikan editingTargets ter-update dengan data server
                                nextTick(() => {
                                    // PERBAIKAN: Simpan data yang baru disimpan sebelum ensureEditingTargets
                                    const currentSavedData = editingTargets.value[uniqueKey];

                                    ensureEditingTargets();

                                    // PERBAIKAN: Jika ada data yang baru disimpan, pertahankan data tersebut
                                    // Jangan timpa dengan data lama dari getInitialTargets
                                    if (currentSavedData && currentSavedData.length > 0) {
                                        const hasRecentData = currentSavedData.some(
                                            (target: any) => target.kinerja_fisik !== '' || target.keuangan !== '',
                                        );
                                        if (hasRecentData) {
                                            editingTargets.value[uniqueKey] = currentSavedData;
                                        }
                                    }

                                    console.log('Data after reload and preserve:', editingTargets.value[uniqueKey]);
                                });
                            },
                        });
                    }
                }, 50); // Kurangi delay menjadi 50ms untuk response yang lebih cepat
            },
            onError: (err: any) => {
                console.error('Error saat menyimpan target:', err);
                // PERBAIKAN: Gunakan unique key untuk error state
                errorRow.value = uniqueKey;
                if (err.message && err.message.includes('Tidak ada periode yang aktif')) {
                    alert(
                        'Tidak ada periode yang aktif saat ini. Data Rencana Awal tidak dapat disimpan. Silakan tunggu hingga periode dibuka oleh admin.',
                    );
                } else {
                    alert(err.message || 'Terjadi kesalahan saat menyimpan target');
                }
            },
            onFinish: () => {
                loadingRow.value = null;
                if (successRow.value === uniqueKey) {
                    setTimeout(() => {
                        if (successRow.value === uniqueKey) {
                            successRow.value = null;
                        }
                    }, 3000);
                }
            },
        });
    } catch (e: any) {
        console.error('Exception:', e);
        // PERBAIKAN: Gunakan unique key untuk error state
        errorRow.value = uniqueKey;
        loadingRow.value = null;
        alert(e.message || 'Terjadi kesalahan saat memproses data');
    }
}

// Function untuk menyimpan state editing saat navigasi
function saveEditingState() {
    // PERBAIKAN: Simpan SEMUA data editingTargets, bukan hanya yang dimodifikasi
    // Ini memastikan data dari database juga tersimpan
    const savedEditingTargets: Record<string, any> = {};

    Object.keys(editingTargets.value).forEach((key) => {
        const targets = editingTargets.value[key];
        if (targets && Array.isArray(targets)) {
            savedEditingTargets[key] = JSON.parse(JSON.stringify(targets)); // Deep copy
        }
    });

    // PERBAIKAN: Simpan juga ke localStorage sebagai backup
    const backupData = {
        editingTargets: savedEditingTargets,
        timestamp: Date.now(),
        tugasId: props.tugas?.id,
        tahunId: selectedTahunId.value, // Simpan juga tahun yang sedang aktif
    };
    localStorage.setItem('rencana_awal_editing_backup', JSON.stringify(backupData));

    console.log('Saving editing state:', savedEditingTargets);

    // Kembalikan semua data editing yang disimpan
    return {
        editingTargets: savedEditingTargets,
    };
}

// Function untuk restore state editing setelah navigasi
function restoreEditingState(savedState: any) {
    if (savedState && savedState.editingTargets) {
        console.log('Restoring editing state:', savedState.editingTargets);

        // PERBAIKAN: Restore semua data yang tersimpan
        // Ini memastikan baik data dari database maupun input user ter-restore
        Object.keys(savedState.editingTargets).forEach((key) => {
            const savedData = savedState.editingTargets[key];
            if (savedData && Array.isArray(savedData)) {
                editingTargets.value[key] = JSON.parse(JSON.stringify(savedData));
            }
        });

        // Force reactivity update
        editingTargets.value = { ...editingTargets.value };

        console.log('Editing targets after restore:', editingTargets.value);
    }
}

// Fungsi untuk mempertahankan data semua sumber dana
function preserveAllFundingSources(subkegiatanId: number) {
    // Dapatkan semua baris untuk subkegiatan ini
    const allRowsForThisSubkegiatan = formattedSubKegiatanData.value.filter((item) => item.subKegiatan.id === subkegiatanId);

    // Hanya simpan editing targets
    const savedEditingTargets: Record<string, any> = {};

    // Simpan data untuk setiap sumber dana
    allRowsForThisSubkegiatan.forEach((item) => {
        const uniqueKey = getUniqueKey(item.subKegiatan, item.sumberDana);

        // Simpan editing targets jika ada
        if (editingTargets.value[uniqueKey]) {
            savedEditingTargets[uniqueKey] = JSON.parse(JSON.stringify(editingTargets.value[uniqueKey]));
        }
    });

    // Kembalikan hanya data editing yang disimpan
    return {
        editingTargets: savedEditingTargets,
    };
}

// Update ensureEditingTargets agar pakai key unik
function ensureEditingTargets() {
    console.log('ensureEditingTargets called');
    const allRows = new Set<string>();
    if (formattedSubKegiatanData.value) {
        console.log('formattedSubKegiatanData.value exists, processing items:', formattedSubKegiatanData.value.length);
        formattedSubKegiatanData.value.forEach((item: any) => {
            const uniqueKey = getUniqueKey(item.subKegiatan, item.sumberDana);
            allRows.add(uniqueKey);

            console.log('Processing item:', {
                uniqueKey,
                subKegiatanId: item.subKegiatan.id,
                sumberDana: item.sumberDana,
                hasExistingData: !!editingTargets.value[uniqueKey],
            });

            // PERBAIKAN: Hanya update jika belum ada data atau data kosong
            // Ini memastikan data yang sudah diinput user tidak tertimpa
            if (!editingTargets.value[uniqueKey]) {
                const initialTargets = getInitialTargets(item.subKegiatan, item.sumberDana);
                editingTargets.value[uniqueKey] = initialTargets;
                console.log('Set initial targets for', uniqueKey, ':', initialTargets);
            } else {
                // Jika sudah ada data, cek apakah perlu update dengan data server yang lebih baru
                const currentData = editingTargets.value[uniqueKey];
                const serverData = getInitialTargets(item.subKegiatan, item.sumberDana);

                // Hanya update jika data server memiliki nilai yang tidak ada di current data
                const hasUserInput = currentData.some((target: any) => target.kinerja_fisik !== '' || target.keuangan !== '');

                console.log('Existing data check:', {
                    uniqueKey,
                    hasUserInput,
                    currentData,
                    serverData,
                });

                if (!hasUserInput) {
                    // Jika tidak ada input user, gunakan data server
                    editingTargets.value[uniqueKey] = serverData;
                    console.log('Updated with server data for', uniqueKey, ':', serverData);
                }
            }
        });
    } else {
        console.log('formattedSubKegiatanData.value is empty or undefined');
    }

    // PERBAIKAN: Hapus editingTargets yang tidak lagi ada di data
    Object.keys(editingTargets.value).forEach((key) => {
        if (!allRows.has(key)) {
            delete editingTargets.value[key];
        }
    });
}

// Function untuk restore data dari localStorage
function restoreFromLocalStorage() {
    try {
        const backupData = localStorage.getItem('rencana_awal_editing_backup');
        if (backupData) {
            const parsed = JSON.parse(backupData);
            // Cek apakah backup masih valid (tidak lebih dari 1 jam dan untuk tugas yang sama)
            const isValid =
                parsed.timestamp &&
                Date.now() - parsed.timestamp < 3600000 && // 1 jam
                parsed.tugasId === props.tugas?.id;

            if (isValid && parsed.editingTargets) {
                // Restore data yang ada di localStorage
                Object.keys(parsed.editingTargets).forEach((key) => {
                    const savedData = parsed.editingTargets[key];
                    const hasUserInput = savedData.some((target: any) => target.kinerja_fisik !== '' || target.keuangan !== '');

                    if (hasUserInput) {
                        editingTargets.value[key] = savedData;
                    }
                });

                // Hapus backup setelah digunakan
                localStorage.removeItem('rencana_awal_editing_backup');
            }
        }
    } catch (error) {
        console.error('Error restoring from localStorage:', error);
        // Hapus backup yang corrupt
        localStorage.removeItem('rencana_awal_editing_backup');
    }
}

onMounted(() => {
    ensureEditingTargets();
    // PERBAIKAN: Restore data dari localStorage jika ada
    restoreFromLocalStorage();
});

// PERBAIKAN: Watch untuk memastikan editingTargets selalu ter-update saat data berubah
watch(
    [() => props.subkegiatanTugas, () => formattedSubKegiatanData.value],
    () => {
        nextTick(() => {
            ensureEditingTargets();
        });
    },
    { immediate: true, deep: true },
);

// PERBAIKAN: Watch khusus untuk tahun aktif - reload editingTargets saat tahun berubah
watch(
    () => props.tahunAktif,
    () => {
        nextTick(() => {
            ensureEditingTargets();
        });
    },
    { immediate: true, deep: true },
);

</script>

<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 bg-gray-100 p-4 dark:bg-gray-800">
            <!-- Header section (redesigned like Sumberdana) -->
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-3 rounded-full bg-blue-100 p-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-600">Rencana Kinerja</h1>
                            <p class="text-sm text-gray-500">Rencana Awal Perangkat Daerah</p>
                        </div>
                    </div>

                    <!-- Add year selector and PDF button -->
                    <div class="flex items-center gap-4">
                        <!-- Dropdown Tahun -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Pilih Periode:</label>
                            <select
                                :value="selectedTahunId"
                                @change="handleTahunChange"
                                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            >
                                <option value="">Pilih Tahun</option>
                                <option v-for="tahun in allTahun" :key="tahun.id" :value="tahun.id">
                                    {{ tahun.tahun }} {{ tahun.status === 1 ? '(Aktif)' : '' }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-blue-600">Tahun Anggaran</span>
                            <span class="rounded-md bg-blue-100 px-3 py-1 text-sm font-bold text-blue-800">
                                {{ tahunAktif?.tahun || 'Belum ada' }}
                            </span>
                        </div>

                        <div class="flex items-center">
                            <select
                                id="periode-selector"
                                class="rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                @change="handlePeriodeChange"
                                :value="selectedPeriodeId"
                                style="display: none"
                            >
                                <option value="">Semua Periode</option>
                                <option
                                    v-for="periode in props.semuaPeriodeAktif"
                                    :key="periode.id"
                                    :value="periode.id"
                                    :selected="periode.id === selectedPeriodeId"
                                >
                                    {{ periode.tahap.tahap }} - {{ periode.tahun.tahun }}
                                </option>
                            </select>
                        </div>



                        <!-- <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                            <span class="text-xs font-medium text-gray-500">Tahun Anggaran</span>
                            <div class="text-center text-lg font-bold text-blue-600">{{ props.tahunAktif?.tahun || 'Belum ada' }}</div>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Periode Status Alert -->
            <div v-if="!currentPeriodeStatus.canInput" class="mb-6 rounded-lg border-l-4 border-red-400 bg-red-50 p-4 shadow-md">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-base font-semibold text-red-800">
                            Input Data Tidak Diizinkan
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p class="font-medium">{{ currentPeriodeStatus.reason }}</p>
                            <p class="mt-1 text-red-600">Semua field input dan tombol telah dinonaktifkan untuk periode ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Perangkat Daerah -->
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
                        <p class="text-sm text-gray-500">Informasi SKPD dan Kode Urusan</p>
                    </div>
                </div>

                <!-- KODE/URUSAN PEMERINTAHAN sebagai header -->
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h3 class="mb-2 text-sm font-medium text-blue-600">KODE/URUSAN PEMERINTAHAN:</h3>
                    <p class="text-xl font-bold text-blue-800">{{ urusanPemerintahan.nomor_kode }} - {{ urusanPemerintahan.nomenklatur }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">
                            {{ props.skpd?.nama_dinas || props.tugas?.skpd?.nama_dinas || props.user?.nama_skpd || 'Tidak tersedia' }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">
                            {{ props.skpd?.kode_organisasi || props.tugas?.skpd?.kode_organisasi || 'Tidak tersedia' }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">
                            {{ props.kepalaSkpd || props.skpd?.skpd_kepala?.[0]?.user?.name || 'Tidak tersedia' }}
                        </p>
                        <p class="font-mono text-sm text-gray-500">NIP: {{ props.skpd?.skpd_kepala?.[0]?.user?.user_detail?.nip || '-' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">
                            {{ props.user?.name || 'Tidak tersedia' }}
                        </p>
                        <p class="font-mono text-sm text-gray-500">NIP: {{ props.user?.nip || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Program table with targets -->
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-600">Detail Rencana Kinerja</h2>
                        
                        <!-- Removed PDF button from header -->
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed border-collapse">
                        <colgroup>
                            <col style="width: 120px" />
                            <col style="width: 250px" />
                            <col style="width: 120px" />
                            <col style="width: 120px" />
                            <col style="width: 120px" />
                            <col style="width: 120px" />
                            <col style="width: 100px" />
                            <col style="width: 130px" />
                            <col style="width: 100px" />
                            <col style="width: 130px" />
                            <col style="width: 100px" />
                            <col style="width: 130px" />
                            <col style="width: 100px" />
                            <col style="width: 130px" />
                            <col style="width: 120px" />
                        </colgroup>
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    rowspan="3"
                                    class="border-r border-gray-200 bg-gray-100 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    KODE
                                </th>
                                <th
                                    rowspan="3"
                                    class="border-r border-gray-200 bg-gray-100 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    BIDANG URUSAN & PROGRAM/<br />KEGIATAN/SUB KEGIATAN
                                </th>
                                <th
                                    colspan="3"
                                    class="border-r border-gray-200 bg-amber-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    PAGU ANGGARAN APBD
                                </th>
                                <th
                                    rowspan="3"
                                    class="border-r border-gray-200 bg-amber-50 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    SUMBER DANA
                                </th>
                                <th
                                    colspan="8"
                                    class="border-r border-gray-200 bg-blue-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    TARGET
                                </th>
                                <th v-if="!isAdminOrOperator" rowspan="3" class="bg-gray-100 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">AKSI</th>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <th
                                    rowspan="2"
                                    class="border-r border-gray-200 bg-amber-50 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase"
                                >
                                    POKOK<br />(RP)
                                </th>
                                <th
                                    rowspan="2"
                                    class="border-r border-gray-200 bg-amber-50 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase"
                                >
                                    PARSIAL<br />(RP)
                                </th>
                                <th
                                    rowspan="2"
                                    class="border-r border-gray-200 bg-amber-50 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase"
                                >
                                    PERUBAHAN<br />(RP)
                                </th>
                                <th
                                    colspan="2"
                                    class="border-r border-gray-200 bg-green-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    TRIWULAN 1
                                </th>
                                <th
                                    colspan="2"
                                    class="border-r border-gray-200 bg-green-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    TRIWULAN 2
                                </th>
                                <th
                                    colspan="2"
                                    class="border-r border-gray-200 bg-green-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    TRIWULAN 3
                                </th>
                                <th
                                    colspan="2"
                                    class="border-r border-gray-200 bg-green-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    TRIWULAN 4
                                </th>
                            </tr>
                            <tr class="border-b-2 border-gray-300">
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KINERJA<br />FISIK (%)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(RP)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KINERJA<br />FISIK (%)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(RP)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KINERJA<br />FISIK (%)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(RP)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KINERJA<br />FISIK (%)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(RP)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <!-- Display bidang urusan from the selected urusan -->
                            <template v-for="bidangUrusan in props.bidangurusanTugas" :key="bidangUrusan.id">
                                <tr
                                    class="border-b border-l-4 border-blue-500 border-gray-100 bg-blue-50 font-extrabold transition-colors hover:bg-blue-50"
                                >
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                        {{ bidangUrusan.kode_nomenklatur.nomor_kode }}
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                        <div class="line-clamp-3">BIDANG URUSAN: {{ bidangUrusan.kode_nomenklatur.nomenklatur }}</div>
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{ `Rp ${calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.pokok?.toLocaleString('id-ID') || '0'}` }}
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{ `Rp ${calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.parsial?.toLocaleString('id-ID') || '0'}` }}
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{
                                            `Rp ${calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.perubahan?.toLocaleString('id-ID') || '0'}`
                                        }}
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        <span class="text-gray-400">-</span>
                                    </td>
                                    <!-- Triwulan 1 -->
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        {{
                                            calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) ||
                                            '0.00'
                                        }}%
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{
                                            `Rp ${(calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID')}`
                                        }}
                                    </td>
                                    <!-- Triwulan 2 -->
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        {{
                                            calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) ||
                                            '0.00'
                                        }}%
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{
                                            `Rp ${(calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID')}`
                                        }}
                                    </td>
                                    <!-- Triwulan 3 -->
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        {{
                                            calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) ||
                                            '0.00'
                                        }}%
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{
                                            `Rp ${(calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID')}`
                                        }}
                                    </td>
                                    <!-- Triwulan 4 -->
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        {{
                                            calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) ||
                                            '0.00'
                                        }}%
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{
                                            `Rp ${(calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID')}`
                                        }}
                                    </td>
                                    <td v-if="!isAdminOrOperator" class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        <span class="text-gray-400">-</span>
                                    </td>
                                </tr>

                                <!-- Display programs that belong to this bidang urusan -->
                                <template
                                    v-for="program in props.programTugas?.filter(
                                        (p) => p.kode_nomenklatur.details[0]?.id_bidang_urusan === bidangUrusan.kode_nomenklatur.id,
                                    )"
                                    :key="program.id"
                                >
                                    <tr
                                        class="border-b border-l-4 border-gray-100 border-gray-400 bg-gray-50 font-bold transition-colors hover:bg-gray-50"
                                    >
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                            {{ program.kode_nomenklatur.nomor_kode }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                            <div class="line-clamp-3">{{ program.kode_nomenklatur.nomenklatur }}</div>
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{ `Rp ${calculateProgram[program.kode_nomenklatur.id]?.pokok?.toLocaleString('id-ID') || '0'}` }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{ `Rp ${calculateProgram[program.kode_nomenklatur.id]?.parsial?.toLocaleString('id-ID') || '0'}` }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{ `Rp ${calculateProgram[program.kode_nomenklatur.id]?.perubahan?.toLocaleString('id-ID') || '0'}` }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            <div
                                                v-if="program.monitoring?.sumber_dana"
                                                class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700"
                                            >
                                                {{ program.monitoring.sumber_dana }}
                                            </div>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <!-- Triwulan 1 -->
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            {{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{
                                                `Rp ${(calculateProgram[program.kode_nomenklatur.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID')}`
                                            }}
                                        </td>
                                        <!-- Triwulan 2 -->
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            {{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{
                                                `Rp ${(calculateProgram[program.kode_nomenklatur.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID')}`
                                            }}
                                        </td>
                                        <!-- Triwulan 3 -->
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            {{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{
                                                `Rp ${(calculateProgram[program.kode_nomenklatur.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID')}`
                                            }}
                                        </td>
                                        <!-- Triwulan 4 -->
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            {{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{
                                                `Rp ${(calculateProgram[program.kode_nomenklatur.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID')}`
                                            }}
                                        </td>
                                        <td v-if="!isAdminOrOperator" class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            <span class="text-gray-400">-</span>
                                        </td>
                                    </tr>

                                    <!-- Display kegiatan for this program -->
                                    <template
                                        v-for="kegiatan in props.kegiatanTugas?.filter(
                                            (k) => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id,
                                        )"
                                        :key="kegiatan.id"
                                    >
                                        <tr
                                            class="bg-orange-25 border-b border-l-4 border-gray-100 border-orange-400 font-semibold transition-colors hover:bg-orange-50"
                                        >
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                                {{ kegiatan.kode_nomenklatur.nomor_kode }}
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                                <div class="line-clamp-3">{{ kegiatan.kode_nomenklatur.nomenklatur }}</div>
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${calculateKegiatan[kegiatan.id]?.pokok?.toLocaleString('id-ID') || '0'}` }}
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${calculateKegiatan[kegiatan.id]?.parsial?.toLocaleString('id-ID') || '0'}` }}
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${calculateKegiatan[kegiatan.id]?.perubahan?.toLocaleString('id-ID') || '0'}` }}
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                <div
                                                    v-if="kegiatan.monitoring?.sumber_dana"
                                                    class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700"
                                                >
                                                    {{ kegiatan.monitoring.sumber_dana }}
                                                </div>
                                                <span v-else class="text-gray-400">-</span>
                                            </td>
                                            <!-- Triwulan 1 -->
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                {{ calculateKegiatan[kegiatan.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${(calculateKegiatan[kegiatan.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID')}` }}
                                            </td>
                                            <!-- Triwulan 2 -->
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                {{ calculateKegiatan[kegiatan.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${(calculateKegiatan[kegiatan.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID')}` }}
                                            </td>
                                            <!-- Triwulan 3 -->
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                {{ calculateKegiatan[kegiatan.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${(calculateKegiatan[kegiatan.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID')}` }}
                                            </td>
                                            <!-- Triwulan 4 -->
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                {{ calculateKegiatan[kegiatan.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ `Rp ${(calculateKegiatan[kegiatan.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID')}` }}
                                            </td>
                                            <td v-if="!isAdminOrOperator" class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                <span class="text-gray-400">-</span>
                                            </td>
                                        </tr>

                                        <!-- Display hierarchical subkegiatan data for this kegiatan -->
                                        <template
                                            v-for="item in formattedSubKegiatanData.filter((sk) => sk.kegiatan.id === kegiatan.id)"
                                            :key="item.id"
                                        >
                                            <!-- Main row for sub-activity (if it's a main row) -->
                                            <tr
                                                v-if="item.isMain"
                                                class="border-b border-l-4 border-gray-100 border-yellow-500 bg-yellow-50 transition-colors hover:bg-yellow-50"
                                                :class="{
                                                    'bg-green-50': successRow === getUniqueKey(item.subKegiatan, item.sumberDana),
                                                    'bg-red-50': errorRow === getUniqueKey(item.subKegiatan, item.sumberDana),
                                                    'opacity-75': loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana),
                                                }"
                                            >
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <span>{{ item.subKegiatan.kode_nomenklatur.nomor_kode }}</span>
                                                        <!-- Static hierarchy icon (always expanded like triwulan) -->
                                                        <svg
                                                            v-if="item.children && item.children.length > 0"
                                                            class="h-4 w-4 text-gray-400"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 9l-7 7-7-7"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                                    <div class="line-clamp-3 font-medium">{{ item.subKegiatan.kode_nomenklatur.nomenklatur }}</div>
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm font-medium">
                                                    {{ `Rp ${item.pokok.toLocaleString('id-ID')}` }}
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm font-medium">
                                                    {{ `Rp ${item.parsial.toLocaleString('id-ID') || '0'}` }}
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm font-medium">
                                                    {{ `Rp ${item.perubahan.toLocaleString('id-ID') || '0'}` }}
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <div class="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                        {{ item.children?.length || 0 }} Sumber Dana
                                                    </div>
                                                </td>
                                                <!-- Aggregated target columns for main row -->
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >{{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[0]?.kinerja_fisik ||
                                                                item.normalizedTargets?.[0]?.kinerja_fisik ||
                                                                0
                                                            ).toFixed(1)
                                                        }}%</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >Rp
                                                        {{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[0]?.keuangan ||
                                                                item.normalizedTargets?.[0]?.keuangan ||
                                                                0
                                                            ).toLocaleString('id-ID')
                                                        }}</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >{{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[1]?.kinerja_fisik ||
                                                                item.normalizedTargets?.[1]?.kinerja_fisik ||
                                                                0
                                                            ).toFixed(1)
                                                        }}%</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >Rp
                                                        {{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[1]?.keuangan ||
                                                                item.normalizedTargets?.[1]?.keuangan ||
                                                                0
                                                            ).toLocaleString('id-ID')
                                                        }}</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >{{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[2]?.kinerja_fisik ||
                                                                item.normalizedTargets?.[2]?.kinerja_fisik ||
                                                                0
                                                            ).toFixed(1)
                                                        }}%</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >Rp
                                                        {{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[2]?.keuangan ||
                                                                item.normalizedTargets?.[2]?.keuangan ||
                                                                0
                                                            ).toLocaleString('id-ID')
                                                        }}</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >{{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[3]?.kinerja_fisik ||
                                                                item.normalizedTargets?.[3]?.kinerja_fisik ||
                                                                0
                                                            ).toFixed(1)
                                                        }}%</span
                                                    >
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="font-medium text-gray-600"
                                                        >Rp
                                                        {{
                                                            (
                                                                aggregatedTargets[item.subKegiatan.id]?.[3]?.keuangan ||
                                                                item.normalizedTargets?.[3]?.keuangan ||
                                                                0
                                                            ).toLocaleString('id-ID')
                                                        }}</span
                                                    >
                                                </td>
                                                <td v-if="!isAdminOrOperator" class="px-3 py-3 text-center align-middle">
                                                    <span class="text-gray-500">-</span>
                                                </td>
                                            </tr>

                                            <!-- Child rows for funding sources (always show like in triwulan) -->
                                            <template v-if="item.isMain && item.children">
                                                <tr
                                                    v-for="child in item.children"
                                                    :key="child.id"
                                                    class="bg-blue-25 border-b border-gray-100 transition-colors hover:bg-blue-50"
                                                    :class="{
                                                        'bg-green-50': successRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                        'bg-red-50': errorRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                        'opacity-75': loadingRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                    }"
                                                >
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                                        <!-- Empty for child rows -->
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                                        <div class="flex items-center gap-2 pl-4">
                                                            <span class="text-gray-400">└─</span>
                                                            <span class="text-sm font-medium text-gray-700">{{ child.sumberDana }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ `Rp ${child.pokok.toLocaleString('id-ID')}` }}
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ `Rp ${child.parsial.toLocaleString('id-ID') || '0'}` }}
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ `Rp ${child.perubahan.toLocaleString('id-ID') || '0'}` }}
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                        <div class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                                            {{ child.sumberDana }}
                                                        </div>
                                                    </td>
                                                    <!-- Target columns for child rows -->
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :class="{
                                                                'border-red-300 bg-red-50':
                                                                    errorRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                'border-green-300 bg-green-50':
                                                                    successRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                'border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed': !currentPeriodeStatus.canInput,
                                                            }"
                                                            :value="
                                                                editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[0]
                                                                    ?.kinerja_fisik || ''
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    0,
                                                                    'kinerja_fisik',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="0"
                                                        />
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :class="{
                                                                'border-red-300 bg-red-50':
                                                                    errorRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                'border-green-300 bg-green-50':
                                                                    successRow === getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                'border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed': !currentPeriodeStatus.canInput,
                                                            }"
                                                            :value="
                                                                formatRupiah(
                                                                    editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[0]
                                                                        ?.keuangan || '',
                                                                )
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    0,
                                                                    'keuangan',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="Rp 0"
                                                        />
                                                    </td>
                                                    <!-- Continue with other triwulan columns... -->
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :value="
                                                                editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[1]
                                                                    ?.kinerja_fisik || ''
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    1,
                                                                    'kinerja_fisik',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="0"
                                                        />
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :value="
                                                                formatRupiah(
                                                                    editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[1]
                                                                        ?.keuangan || '',
                                                                )
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    1,
                                                                    'keuangan',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="Rp 0"
                                                        />
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :value="
                                                                editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[2]
                                                                    ?.kinerja_fisik || ''
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    2,
                                                                    'kinerja_fisik',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="0"
                                                        />
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :value="
                                                                formatRupiah(
                                                                    editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[2]
                                                                        ?.keuangan || '',
                                                                )
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    2,
                                                                    'keuangan',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="Rp 0"
                                                        />
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :value="
                                                                editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[3]
                                                                    ?.kinerja_fisik || ''
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    3,
                                                                    'kinerja_fisik',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="0"
                                                        />
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle">
                                                        <input
                                                            type="text"
                                                            class="h-8 w-20 rounded border border-blue-300 bg-blue-50 px-2 py-1 text-center text-xs transition-all duration-200 hover:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                            :value="
                                                                formatRupiah(
                                                                    editingTargets[getUniqueKey(child.subKegiatan, child.sumberDana)]?.[3]
                                                                        ?.keuangan || '',
                                                                )
                                                            "
                                                            @input="
                                                                onInputTarget(
                                                                    getUniqueKey(child.subKegiatan, child.sumberDana),
                                                                    3,
                                                                    'keuangan',
                                                                    ($event.target as HTMLInputElement)?.value,
                                                                )
                                                            "
                                                            :disabled="isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            placeholder="Rp 0"
                                                        />
                                                    </td>
                                                    <td v-if="!isAdminOrOperator" class="px-3 py-3 text-center align-middle">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <button
                                                                @click="saveTargets(child.subKegiatan, child.sumberDana)"
                                                                class="rounded px-3 py-1 text-xs font-medium transition-colors focus:outline-none"
                                                                :class="{
                                                                    'bg-blue-500 text-white hover:bg-blue-600 focus:ring-2 focus:ring-blue-500': currentPeriodeStatus.canInput && !isAdminOrOperator,
                                                                    'bg-gray-300 text-gray-500 cursor-not-allowed': !currentPeriodeStatus.canInput || isAdminOrOperator,
                                                                    'opacity-50': loadingRow === getUniqueKey(child.subKegiatan, child.sumberDana)
                                                                }"
                                                                :disabled="loadingRow === getUniqueKey(child.subKegiatan, child.sumberDana) || isAdminOrOperator || !currentPeriodeStatus.canInput"
                                                            >
                                                                <span v-if="loadingRow === getUniqueKey(child.subKegiatan, child.sumberDana)"
                                                                    >...</span
                                                                >
                                                                <span v-else>Simpan</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PDF Configuration Modal -->
        <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h-4v4"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-800">Konfigurasi PDF Rencana Awal</h2>
                        </div>
                        <button @click="showPdfModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-gray-600 mb-6">
                        {{ skpd?.nama_dinas || user?.nama_skpd || 'SKPD' }} - {{ tugas?.kodeNomenklatur?.nomor_kode || '-' }} {{ tugas?.kodeNomenklatur?.nomenklatur || '-' }}
                    </p>

                    <!-- PDF Form -->
                    <div class="space-y-6">
                        <!-- Periode Tahun -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Periode Tahun</h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Pilih tahun untuk data yang akan ditampilkan dalam laporan PDF</p>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                                <select v-model="pdfForm.tahun" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Penandatangan -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Penandatangan</h3>
                            <p class="text-sm text-gray-600 mb-4">Informasi pejabat yang akan menandatangani (Kepala SKPD)</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
                                    <input v-model="pdfForm.penandatangan_1_tempat" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none" placeholder="Nama kota/tempat">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                    <input v-model="pdfForm.penandatangan_1_tanggal" type="date" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input v-model="pdfForm.penandatangan_1_nama" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none" placeholder="Nama lengkap penandatangan">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                    <input v-model="pdfForm.penandatangan_1_jabatan" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none" placeholder="Jabatan penandatangan">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                <input v-model="pdfForm.penandatangan_1_nip" type="text" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none" placeholder="NIP penandatangan">
                            </div>
                        </div>

                        <!-- Pengaturan PDF -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Pengaturan PDF</h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Konfigurasi format dan ukuran dokumen PDF</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kertas</label>
                                    <select v-model="pdfForm.paper_size" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                        <option value="A4">A4</option>
                                        <option value="A3">A3</option>
                                        <option value="Letter">Letter</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Orientasi</label>
                                    <select v-model="pdfForm.orientation" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                        <option value="portrait">Portrait</option>
                                        <option value="landscape">Landscape</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 mt-4 pt-4">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Margin Atas (mm)</label>
                                        <input v-model="pdfForm.margin_top" type="number" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Margin Kanan (mm)</label>
                                        <input v-model="pdfForm.margin_right" type="number" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Margin Bawah (mm)</label>
                                        <input v-model="pdfForm.margin_bottom" type="number" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Margin Kiri (mm)</label>
                                        <input v-model="pdfForm.margin_left" type="number" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex flex-col sm:flex-row justify-between gap-4 mt-6 pt-6 border-t border-gray-200">
                        <button @click="showPdfModal = false" type="button" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                            Kembali
                        </button>
                        <button @click="generatePDF" :disabled="isGeneratingPdf" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ isGeneratingPdf ? 'Sedang Membuat PDF...' : 'Download PDF' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
