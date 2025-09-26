<script setup lang="ts">
import { useTriwulanData } from '@/composables/useTriwulanData';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import ActivityLogger from '@/services/activityLogger';

// Props utama, pastikan tid, triwulanName, dan periode_id dinamis
const props = defineProps<{
    user: {
        id: number;
        nama_skpd: string;
    };
    skpd?: {
        id: number;
        nama_dinas: string;
        nama_operator: string;
        nama_kepala_skpd?: string;
        nip_kepala_skpd?: string;
        nip_operator?: string;
        no_dpa: string;
        kode_organisasi: string;
        nama_skpd?: string;
    };
    tugas: any;
    programTugas: Array<any>;
    kegiatanTugas: Array<any>;
    subkegiatanTugas: Array<any>;
    kepalaSkpd?: string;
    bidangUrusan: any;
    monitoringTargets: Array<any>;
    monitoringRealisasi: Array<any>;
    tid: number;
    tahun: number;
    tahunAktif?: { id: number; tahun: string; status: number };
    allTahun?: Array<{ id: number; tahun: string; status: number }>;
    periode: { id: number; nama: string };
    periodeStatus: { canInput: boolean; reason: string; status: string };
    triwulanName: string;
    breadcrumbUserId?: number | null;
}>();

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

// Breadcrumbs dinamis - Updated to match Rencana Awal style
const breadcrumbs = computed<BreadcrumbItem[]>(() => {
    const showHref = props.breadcrumbUserId ? route('triwulan.show', { tid: props.tid, id: props.breadcrumbUserId }) : '';

    return [
        { title: 'Monitoring', href: route('monitoring.index') },
        { title: `Monitoring ${props.triwulanName}`, href: route('triwulan.index', { tid: props.tid }) },
        { title: `Monitoring Detail ${props.skpd?.nama_dinas || props.user?.nama_skpd || 'SKPD'}`, href: showHref },
    ];
});

// Computed untuk status periode - apakah masih bisa input
const canInputData = computed(() => {
    // Cek apakah periodeStatus ada dan canInput true
    if (props.periodeStatus && !props.periodeStatus.canInput) {
        return false;
    }

    // Fallback check jika periodeStatus tidak ada
    if (!props.periode) {
        return false;
    }

    return true;
});

// Computed untuk pesan status periode
const periodeStatusMessage = computed(() => {
    if (props.periodeStatus) {
        return props.periodeStatus.reason;
    }
    return 'Status periode tidak diketahui';
});

// Tambahkan fungsi-fungsi yang hilang
const getUserPenanggungJawab = () => {
    if (props.tugas?.skpd?.user_penanggung_jawab?.name) {
        const name = props.tugas.skpd.user_penanggung_jawab.name;
        const nip = props.tugas.skpd.user_penanggung_jawab.user_detail?.nip;
        return nip ? `${name} (NIP: ${nip})` : name;
    }
    return null;
};

const getNoDpa = () => {
    return props.tugas?.skpd?.no_dpa || '-';
};
const getCurrentMonitoringTarget = () => {
    return props.monitoringTargets.find((target) => target.periode_id === props.periode?.id);
};

const getCurrentMonitoringRealisasi = () => {
    return props.monitoringRealisasi.find((realisasi) => realisasi.periode_id === props.periode?.id);
};


// Tambahkan fungsi-fungsi yang diperlukan untuk tabel
const editedItems = ref<Record<string, any>>({});
const savingItems = ref<Record<string, boolean>>({});

// Dialog state management
const alertDialog = ref({
    open: false,
    title: '',
    message: '',
    type: 'info' as 'info' | 'success' | 'error',
    onConfirm: () => {}
});

// PDF Modal state - Same as Rencana Awal
const showPdfModal = ref(false);
const isGeneratingPdf = ref(false);

// PDF Form data - Same as Rencana Awal
const pdfForm = ref({
    tahun: props.tahunAktif?.tahun || props.tahun || new Date().getFullYear(),
    penandatangan_1_tempat: 'Banjarmasin',
    penandatangan_1_tanggal: new Date().toISOString().split('T')[0],
    penandatangan_1_nama: props.skpd?.nama_kepala_skpd || 'Kepala SKPD',
    penandatangan_1_jabatan: `Kepala ${props.skpd?.nama_dinas || 'SKPD'}`,
    penandatangan_1_nip: props.skpd?.nip_kepala_skpd || '',
    paper_size: 'A4',
    orientation: 'landscape',
    margin_top: 15,
    margin_right: 15,
    margin_bottom: 15,
    margin_left: 15
});

function showAlert(title: string, message: string, type: 'info' | 'success' | 'error' = 'info') {
    alertDialog.value = {
        open: true,
        title,
        message,
        type,
        onConfirm: () => {
            alertDialog.value.open = false;
        }
    };
}

// PDF Generation function - Same pattern as Rencana Awal
const generatePDF = async () => {
    if (isGeneratingPdf.value) return;
    
    isGeneratingPdf.value = true;
    
    try {
        // Prepare form data
        const formData = new FormData();
        
        // Add all PDF form fields
        Object.entries(pdfForm.value).forEach(([key, value]) => {
            formData.append(key, String(value));
        });
        
        // Add CSRF token
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
        const csrfToken = csrfTokenElement?.content;
        
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        // Make request to generate PDF
        const response = await fetch(route('pdf.triwulan.generate', { tid: props.tid, tugasId: props.tugas.id }), {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            // Get filename from response header or generate default
            const contentDisposition = response.headers.get('Content-Disposition');
            let filename = `Laporan_${props.triwulanName}_${props.skpd?.nama_dinas || 'SKPD'}_${pdfForm.value.tahun}.pdf`;
            
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch) {
                    filename = filenameMatch[1];
                }
            }
            
            // Create blob and download
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
            
            // Close modal and show success
            showPdfModal.value = false;
            showAlert('Berhasil', 'PDF berhasil diunduh!', 'success');
            
        } else {
            const errorData = await response.json().catch(() => ({ error: 'Terjadi kesalahan saat membuat PDF' }));
            showAlert('Error', errorData.error || 'Terjadi kesalahan saat membuat PDF', 'error');
        }
        
    } catch (error: any) {
        console.error('PDF generation error:', error);
        showAlert('Error', error.message || 'Terjadi kesalahan saat membuat PDF', 'error');
    } finally {
        isGeneratingPdf.value = false;
    }
};

// Reactive state for selected year - menggunakan computed agar selalu sinkron dengan props
const selectedTahunId = computed(() => props.tahunAktif?.id || null);

// Handler for year change
const handleTahunChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newTahunId = target.value ? parseInt(target.value) : null;

    if (selectedTahunId.value !== newTahunId) {
        // Reload data with the new year
        const selectedTahun = props.allTahun?.find((t) => t.id === newTahunId);
        if (selectedTahun && props.tugas?.id) {
            router.visit(
                route('triwulan.detail.tahun', {
                    tid: props.tid,
                    id: props.user.id,
                    taskId: props.tugas.id,
                    tahun: selectedTahun.tahun,
                }),
                {
                    preserveState: false,
                },
            );
        }
    }
};

// Gunakan composable untuk data triwulan dengan multiple sumber dana
const { formattedSubKegiatanData } = useTriwulanData(props);

// Debug logging
console.log('=== DEBUG DATA TRIWULAN ===');
console.log('Props bidangUrusan:', props.bidangUrusan);
if (props.bidangUrusan) {
    console.log('- bidangUrusan.kode_nomenklatur:', props.bidangUrusan.kode_nomenklatur);
    console.log('- bidangUrusan.nomor_kode:', props.bidangUrusan.nomor_kode);
    console.log('- bidangUrusan.nama_nomenklatur:', props.bidangUrusan.nama_nomenklatur);
}
console.log('Props programTugas:', props.programTugas);
if (props.programTugas && props.programTugas.length > 0) {
    console.log('- programTugas[0].kode_nomenklatur:', props.programTugas[0].kode_nomenklatur);
    console.log('- programTugas[0].nomor_kode:', props.programTugas[0].nomor_kode);
    console.log('- programTugas[0].nama_nomenklatur:', props.programTugas[0].nama_nomenklatur);
}
console.log('Props kegiatanTugas:', props.kegiatanTugas);
console.log('Props subkegiatanTugas:', props.subkegiatanTugas);
console.log('Props monitoringTargets:', props.monitoringTargets);
console.log('Props monitoringRealisasi:', props.monitoringRealisasi);
console.log('Props periode:', props.periode);
console.log('================================');

// Fungsi untuk menentukan pagu terakhir yang tersedia
// Prioritas: pagu_perubahan > pagu_parsial > pagu_pokok
const getLatestAvailablePagu = (paguPokok: number, paguParsial: number, paguPerubahan: number): number => {
    if (paguPerubahan > 0) return paguPerubahan;
    if (paguParsial > 0) return paguParsial;
    return paguPokok;
};

// Fungsi untuk menghitung persentase keuangan (maksimal 100%)
const calculateKeuanganPersentase = (realisasiKeuangan: number, paguAnggaran: number): number => {
    if (paguAnggaran === 0) return 0;
    const percentage = (realisasiKeuangan / paguAnggaran) * 100;
    return Math.min(percentage, 100); // Batasi maksimal 100%
};

// Fungsi untuk mendapatkan realisasi keuangan yang sudah diedit atau dari data asli
const getRealisasiKeuangan = (id: string, originalValue: number): number => {
    const editedValue = editedItems.value[id]?.realisasiKeuangan;
    if (editedValue !== undefined && editedValue !== '') {
        return parseInt(editedValue.toString().replace(/[^\d]/g, '')) || 0;
    }
    return originalValue || 0;
};

// Computed function untuk mendapatkan persentase keuangan real-time
const getKeuanganPersentase = (id: string): string => {
    // Cari item dari programData untuk mendapatkan pagu
    const item = programData.value.find((item) => item.id === id);
    if (!item || item.type !== 'subkegiatan_sumber_dana') {
        return item?.realisasiKeuanganPersen || '0%';
    }

    // PERBAIKAN: Untuk subkegiatan_sumber_dana, hitung berdasarkan input yang sedang diedit
    // Atau data yang sudah disimpan jika tidak ada yang sedang diedit
    let currentRealisasi = getRealisasiKeuangan(id, 0);
    
    // PERBAIKAN: Jika tidak ada data edited, coba ambil dari data asli item
    if (currentRealisasi === 0 && item.realisasiKeuangan) {
        const realisasiFromItem = parseInt(item.realisasiKeuangan.replace(/[^\d]/g, '')) || 0;
        currentRealisasi = realisasiFromItem;
    }

    // Ekstrak semua jenis pagu dari format "Rp 1,000,000" menjadi number
    const paguPokokStr = item.paguPokok || 'Rp 0';
    const paguParsialStr = item.paguParsial || 'Rp 0';
    const paguPerubahanStr = item.paguPerubahan || 'Rp 0';

    const paguPokok = parseInt(paguPokokStr.replace(/[^\d]/g, '')) || 0;
    const paguParsial = parseInt(paguParsialStr.replace(/[^\d]/g, '')) || 0;
    const paguPerubahan = parseInt(paguPerubahanStr.replace(/[^\d]/g, '')) || 0;

    // Gunakan pagu terakhir yang tersedia (prioritas: perubahan > parsial > pokok)
    const latestPagu = getLatestAvailablePagu(paguPokok, paguParsial, paguPerubahan);
    const persentase = calculateKeuanganPersentase(currentRealisasi, latestPagu);

    console.log(`📊 Calculating percentage for ${id}:`, {
        currentRealisasi,
        realisasiFromItem: item.realisasiKeuangan,
        paguPokok,
        paguParsial,
        paguPerubahan,
        latestPagu,
        persentase: persentase.toFixed(2),
        hasEditedData: !!editedItems.value[id]?.realisasiKeuangan,
    });

    return `${persentase.toFixed(2)}%`;
};

// Computed property untuk data program dengan data real dari RencanaAwal
const programData = computed(() => {
    const result: any[] = [];

    // Hitung agregasi untuk semua level hierarki
    const allSubkegiatanTargets: any[] = [];
    const allSubkegiatanRealisasi: any[] = [];

    if (props.subkegiatanTugas && props.subkegiatanTugas.length > 0) {
        props.subkegiatanTugas.forEach((subkegiatan) => {
            const targets = props.monitoringTargets.filter((target) => target.task_id === subkegiatan.id && target.periode_id === props.periode.id);
            const realisasi = props.monitoringRealisasi.filter((r) => r.task_id === subkegiatan.id && r.periode_id === props.periode.id);
            allSubkegiatanTargets.push(...targets);
            allSubkegiatanRealisasi.push(...realisasi);
        });
    }

    // Hitung agregasi fisik dan keuangan
    const totalTargetFisik = allSubkegiatanTargets.reduce((sum, t) => sum + (t.kinerja_fisik || 0), 0);
    const avgTargetFisik = allSubkegiatanTargets.length > 0 ? totalTargetFisik / allSubkegiatanTargets.length : 0;

    const totalTargetKeuangan = allSubkegiatanTargets.reduce((sum, t) => sum + (t.keuangan || 0), 0);

    const totalRealisasiFisik = allSubkegiatanRealisasi.reduce((sum, r) => sum + (r.kinerja_fisik || 0), 0);
    const avgRealisasiFisik = allSubkegiatanRealisasi.length > 0 ? totalRealisasiFisik / allSubkegiatanRealisasi.length : 0;

    const totalRealisasiKeuangan = allSubkegiatanRealisasi.reduce((sum, r) => sum + (r.keuangan || 0), 0);

    // Hitung agregasi pagu dari semua target
    const totalPaguPokok = allSubkegiatanTargets.reduce((sum, t) => sum + (t.pagu_pokok || 0), 0);
    const totalPaguParsial = allSubkegiatanTargets.reduce((sum, t) => sum + (t.pagu_parsial || 0), 0);
    const totalPaguPerubahan = allSubkegiatanTargets.reduce((sum, t) => sum + (t.pagu_perubahan || 0), 0);

    // Hitung persentase keuangan untuk level agregasi menggunakan pagu terakhir yang tersedia
    const totalLatestPagu = getLatestAvailablePagu(totalPaguPokok, totalPaguParsial, totalPaguPerubahan);
    const totalRealisasiKeuanganPersen = calculateKeuanganPersentase(totalRealisasiKeuangan, totalLatestPagu);

    // Tambahkan data bidang urusan dengan data real
    if (props.bidangUrusan) {
        // Coba berbagai kemungkinan struktur data
        const bidangUrusanKode =
            props.bidangUrusan.kode_nomenklatur?.nomor_kode ||
            props.bidangUrusan.nomor_kode ||
            props.bidangUrusan.kode ||
            props.bidangUrusan.id ||
            '-';

        const bidangUrusanNama =
            props.bidangUrusan.kode_nomenklatur?.nama_nomenklatur ||
            props.bidangUrusan.nama_nomenklatur ||
            props.bidangUrusan.nomenklatur ||
            props.bidangUrusan.nama ||
            props.bidangUrusan.title ||
            props.bidangUrusan.description ||
            'PEMERINTAHAN BIDANG PENDIDIKAN';

        console.log('=== BIDANG URUSAN DEBUG ===');
        console.log('bidangUrusanKode:', bidangUrusanKode);
        console.log('bidangUrusanNama:', bidangUrusanNama);
        console.log('Full bidangUrusan object:', props.bidangUrusan);

        result.push({
            id: `bidang-${props.bidangUrusan.id || 'unknown'}`,
            kode: bidangUrusanKode,
            program: bidangUrusanNama.startsWith('BIDANG URUSAN:') ? bidangUrusanNama : `BIDANG URUSAN: ${bidangUrusanNama}`,
            paguPokok: `Rp ${totalPaguPokok.toLocaleString('id-ID')}`,
            paguParsial: `Rp ${totalPaguParsial.toLocaleString('id-ID')}`,
            paguPerubahan: `Rp ${totalPaguPerubahan.toLocaleString('id-ID')}`,
            sumberDana: '-',
            targetFisik: `${avgTargetFisik.toFixed(1)}%`,
            targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
            realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
            realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
            realisasiKeuanganPersen: `${totalRealisasiKeuanganPersen.toFixed(2)}%`,
            keterangan: '-',
            pptk: '-',
            type: 'bidang_urusan',
        });
    }

    // Tambahkan data program dengan data real
    if (props.programTugas && props.programTugas.length > 0) {
        console.log('=== PROGRAM DEBUG ===');
        console.log('programTugas length:', props.programTugas.length);
        console.log('programTugas[0]:', props.programTugas[0]);

        props.programTugas.forEach((program, index) => {
            const programKode = program.kode_nomenklatur?.nomor_kode || program.nomor_kode || program.kode || program.id || '-';

            const programNama =
                program.kode_nomenklatur?.nama_nomenklatur ||
                program.nama_nomenklatur ||
                program.nomenklatur ||
                program.nama ||
                program.title ||
                program.description ||
                'PENINGKATAN URUSAN PEMERINTAHAN DASAR';

            console.log(`Program ${index}:`, {
                kode: programKode,
                nama: programNama,
                fullObject: program,
            });

            result.push({
                id: `program-${program.id}`,
                kode: programKode,
                program: programNama.startsWith('PROGRAM:') ? programNama : `PROGRAM: ${programNama}`,
                paguPokok: `Rp ${totalPaguPokok.toLocaleString('id-ID')}`,
                paguParsial: `Rp ${totalPaguParsial.toLocaleString('id-ID')}`,
                paguPerubahan: `Rp ${totalPaguPerubahan.toLocaleString('id-ID')}`,
                sumberDana: '-',
                targetFisik: `${avgTargetFisik.toFixed(1)}%`,
                targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
                realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
                realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
                realisasiKeuanganPersen: `${totalRealisasiKeuanganPersen.toFixed(2)}%`,
                keterangan: '-',
                pptk: '-',
                type: 'program',
            });
        });
    } else {
        console.log('=== PROGRAM DEBUG ===');
        console.log('No programTugas data found!');
        console.log('props.programTugas:', props.programTugas);
    }

    // Tambahkan data kegiatan dengan data real
    if (props.kegiatanTugas && props.kegiatanTugas.length > 0) {
        console.log('=== KEGIATAN DEBUG ===');
        console.log('kegiatanTugas length:', props.kegiatanTugas.length);
        console.log('kegiatanTugas[0]:', props.kegiatanTugas[0]);

        props.kegiatanTugas.forEach((kegiatan, index) => {
            const kegiatanKode = kegiatan.kode_nomenklatur?.nomor_kode || kegiatan.nomor_kode || kegiatan.kode || kegiatan.id || '-';

            const kegiatanNama =
                kegiatan.kode_nomenklatur?.nama_nomenklatur ||
                kegiatan.nama_nomenklatur ||
                kegiatan.nomenklatur ||
                kegiatan.nama ||
                kegiatan.title ||
                kegiatan.description ||
                'Perencanaan, Pengangguran dan Evaluasi Kinerja';

            console.log(`Kegiatan ${index}:`, {
                kode: kegiatanKode,
                nama: kegiatanNama,
                fullObject: kegiatan,
            });

            result.push({
                id: `kegiatan-${kegiatan.id}`,
                kode: kegiatanKode,
                program: kegiatanNama.startsWith('KEGIATAN:') ? kegiatanNama : `KEGIATAN: ${kegiatanNama}`,
                paguPokok: `Rp ${totalPaguPokok.toLocaleString('id-ID')}`,
                paguParsial: `Rp ${totalPaguParsial.toLocaleString('id-ID')}`,
                paguPerubahan: `Rp ${totalPaguPerubahan.toLocaleString('id-ID')}`,
                sumberDana: '-',
                targetFisik: `${avgTargetFisik.toFixed(1)}%`,
                targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
                realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
                realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
                realisasiKeuanganPersen: `${totalRealisasiKeuanganPersen.toFixed(2)}%`,
                keterangan: '-',
                pptk: '-',
                type: 'kegiatan',
            });
        });
    } else {
        console.log('=== KEGIATAN DEBUG ===');
        console.log('No kegiatanTugas data found!');
        console.log('props.kegiatanTugas:', props.kegiatanTugas);
    }

    // Tambahkan data subkegiatan dengan data real dari monitoring targets
    if (props.subkegiatanTugas && props.subkegiatanTugas.length > 0) {
        console.log('=== SUBKEGIATAN DEBUG ===');
        console.log('subkegiatanTugas length:', props.subkegiatanTugas.length);
        console.log('subkegiatanTugas[0]:', props.subkegiatanTugas[0]);
        console.log('monitoringTargets:', props.monitoringTargets);
        console.log('monitoringTargets[0] full object:', props.monitoringTargets[0]);

        // Debug semua field yang mungkin berisi nama sumber dana
        if (props.monitoringTargets.length > 0) {
            const firstTarget = props.monitoringTargets[0];
            console.log('=== SUMBER DANA FIELDS DEBUG ===');
            console.log('All keys in target:', Object.keys(firstTarget));
            console.log('sumber_anggaran_nama:', firstTarget.sumber_anggaran_nama);
            console.log('nama_sumber:', firstTarget.nama_sumber);
            console.log('sumber_dana:', firstTarget.sumber_dana);
            console.log('anggaran_nama:', firstTarget.anggaran_nama);
            console.log('nama:', firstTarget.nama);
            console.log('sumber_anggaran:', firstTarget.sumber_anggaran);
            console.log('anggaran:', firstTarget.anggaran);
            console.log('dana:', firstTarget.dana);
        }

        // Debug SKPD data
        console.log('=== SKPD DATA DEBUG ===');
        console.log('props.skpd:', props.skpd);
        console.log('props.tugas.skpd:', props.tugas?.skpd);

        console.log('periode.id:', props.periode.id);

        props.subkegiatanTugas.forEach((subkegiatan, index) => {
            console.log(`Processing subkegiatan ${index}:`, subkegiatan);

            // Cari semua target untuk subkegiatan ini di periode yang sesuai
            const targetsForSubkegiatan = props.monitoringTargets.filter(
                (target) => target.task_id === subkegiatan.id && target.periode_id === props.periode.id,
            );

            console.log(`Targets for subkegiatan ${subkegiatan.id}:`, targetsForSubkegiatan);

            // Cari semua realisasi untuk subkegiatan ini di periode yang sesuai
            const realisasiForSubkegiatan = props.monitoringRealisasi.filter(
                (realisasi) => realisasi.task_id === subkegiatan.id && realisasi.periode_id === props.periode.id,
            );

            if (targetsForSubkegiatan.length > 0) {
                // Hitung data akumulasi untuk subkegiatan utama
                const totalTargetFisik = targetsForSubkegiatan.reduce((sum, t) => sum + (t.kinerja_fisik || 0), 0);
                const avgTargetFisik = totalTargetFisik / targetsForSubkegiatan.length;

                const totalTargetKeuangan = targetsForSubkegiatan.reduce((sum, t) => sum + (t.keuangan || 0), 0);

                const totalRealisasiFisik = realisasiForSubkegiatan.reduce((sum, r) => sum + (r.kinerja_fisik || 0), 0);
                const avgRealisasiFisik = realisasiForSubkegiatan.length > 0 ? totalRealisasiFisik / realisasiForSubkegiatan.length : 0;

                const totalRealisasiKeuangan = realisasiForSubkegiatan.reduce((sum, r) => sum + (r.keuangan || 0), 0);

                // Hitung agregasi pagu untuk subkegiatan ini
                const totalSubkegiatanPaguPokok = targetsForSubkegiatan.reduce((sum, t) => sum + (t.pagu_pokok || 0), 0);
                const totalSubkegiatanPaguParsial = targetsForSubkegiatan.reduce((sum, t) => sum + (t.pagu_parsial || 0), 0);
                const totalSubkegiatanPaguPerubahan = targetsForSubkegiatan.reduce((sum, t) => sum + (t.pagu_perubahan || 0), 0);

                // Hitung persentase keuangan untuk level agregasi menggunakan pagu terakhir yang tersedia
                const totalSubkegiatanLatestPagu = getLatestAvailablePagu(
                    totalSubkegiatanPaguPokok,
                    totalSubkegiatanPaguParsial,
                    totalSubkegiatanPaguPerubahan,
                );
                const totalRealisasiKeuanganPersen = calculateKeuanganPersentase(totalRealisasiKeuangan, totalSubkegiatanLatestPagu);

                // Subkegiatan utama dengan data akumulasi
                const subkegiatanKode =
                    subkegiatan.kode_nomenklatur?.nomor_kode || subkegiatan.nomor_kode || subkegiatan.kode || subkegiatan.id || '-';

                const subkegiatanNama =
                    subkegiatan.kode_nomenklatur?.nama_nomenklatur ||
                    subkegiatan.nama_nomenklatur ||
                    subkegiatan.nomenklatur ||
                    subkegiatan.nama ||
                    subkegiatan.title ||
                    subkegiatan.description ||
                    'Penyusunan Dokumen Perencanaan Perangkat Daerah';

                console.log(`Subkegiatan ${index} - Main:`, {
                    id: subkegiatan.id,
                    kode: subkegiatanKode,
                    nama: subkegiatanNama,
                    fullObject: subkegiatan,
                    targets: targetsForSubkegiatan.length,
                });

                result.push({
                    id: `subkegiatan-${subkegiatan.id}`,
                    kode: subkegiatanKode,
                    program: subkegiatanNama.startsWith('SUB KEGIATAN:') ? subkegiatanNama : `SUB KEGIATAN: ${subkegiatanNama}`,
                    paguPokok: `Rp ${totalSubkegiatanPaguPokok.toLocaleString('id-ID')}`,
                    paguParsial: `Rp ${totalSubkegiatanPaguParsial.toLocaleString('id-ID')}`,
                    paguPerubahan: `Rp ${totalSubkegiatanPaguPerubahan.toLocaleString('id-ID')}`,
                    sumberDana: '', // Kosong untuk baris utama
                    targetFisik: `${avgTargetFisik.toFixed(1)}%`,
                    targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
                    realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
                    realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
                    realisasiKeuanganPersen: `${totalRealisasiKeuanganPersen.toFixed(2)}%`,
                    keterangan: '', // Kosong untuk baris utama
                    pptk: '', // Kosong untuk baris utama
                    type: 'subkegiatan',
                });

                // Tambahkan baris untuk setiap sumber dana
                targetsForSubkegiatan.forEach((target, targetIndex) => {
                    // Cari realisasi yang sesuai dengan sumber dana ini
                    const matchingRealisasi = realisasiForSubkegiatan.find((r) => r.sumber_anggaran_id === target.sumber_anggaran_id);

                    // Debug sumber dana untuk melihat field yang tersedia
                    // Generate unique composite ID
                    const compositeId = `${subkegiatan.id}_${target.sumber_anggaran_id}`;

                    console.log(`Subkegiatan ${index} - Target ${targetIndex}:`, {
                        subkegiatan_id: subkegiatan.id,
                        sumber_anggaran_id: target.sumber_anggaran_id,
                        composite_id: compositeId,
                        sumber_anggaran_nama: target.sumber_anggaran_nama,
                        nama_sumber: target.nama_sumber,
                        sumber_dana: target.sumber_dana,
                        anggaran_nama: target.anggaran_nama,
                        kinerja_fisik: target.kinerja_fisik,
                        keuangan: target.keuangan,
                        pagu_pokok: target.pagu_pokok,
                        pagu_parsial: target.pagu_parsial,
                        pagu_perubahan: target.pagu_perubahan,
                        fullTarget: target,
                        matchingRealisasi: matchingRealisasi,
                    });

                    // Gunakan nama sumber anggaran dari backend
                    const sumberDanaNama = target.sumber_anggaran_nama || `Sumber Dana ${target.sumber_anggaran_id}`;

                    result.push({
                        id: `${subkegiatan.id}_${target.sumber_anggaran_id}`,
                        kode: '',
                        program: `└─ ${sumberDanaNama}`,
                        paguPokok: `Rp ${(target.pagu_pokok || 0).toLocaleString('id-ID')}`,
                        paguParsial: `Rp ${(target.pagu_parsial || 0).toLocaleString('id-ID')}`,
                        paguPerubahan: `Rp ${(target.pagu_perubahan || 0).toLocaleString('id-ID')}`,
                        sumberDana: sumberDanaNama,
                        targetFisik: `${target.kinerja_fisik || 0}%`,
                        targetKeuangan: `Rp ${(target.keuangan || 0).toLocaleString('id-ID')}`,
                        realisasiFisik: `${matchingRealisasi?.kinerja_fisik || 0}%`,
                        realisasiKeuangan: `Rp ${(matchingRealisasi?.keuangan || 0).toLocaleString('id-ID')}`,
                        realisasiKeuanganPersen: `${calculateKeuanganPersentase(
                            matchingRealisasi?.keuangan || 0,
                            getLatestAvailablePagu(target.pagu_pokok || 0, target.pagu_parsial || 0, target.pagu_perubahan || 0),
                        ).toFixed(2)}%`,
                        keterangan: matchingRealisasi?.deskripsi || '',
                        pptk: matchingRealisasi?.nama_pptk || '',
                        type: 'subkegiatan_sumber_dana',
                        indentLevel: 1,
                        _originalSubkegiatanId: subkegiatan.id,
                        _sumberAnggaranId: target.sumber_anggaran_id,
                        _sumberAnggaranNama: target.sumber_anggaran_nama,
                    });
                });
            } else {
                // Jika tidak ada target, tampilkan subkegiatan dengan data kosong
                const subkegiatanKode =
                    subkegiatan.kode_nomenklatur?.nomor_kode || subkegiatan.nomor_kode || subkegiatan.kode || subkegiatan.id || '-';

                const subkegiatanNama =
                    subkegiatan.kode_nomenklatur?.nama_nomenklatur ||
                    subkegiatan.nama_nomenklatur ||
                    subkegiatan.nomenklatur ||
                    subkegiatan.nama ||
                    subkegiatan.title ||
                    subkegiatan.description ||
                    'Penyusunan Dokumen Perencanaan Perangkat Daerah';

                console.log(`Subkegiatan ${index} - No Targets:`, {
                    id: subkegiatan.id,
                    kode: subkegiatanKode,
                    nama: subkegiatanNama,
                    fullObject: subkegiatan,
                });

                result.push({
                    id: `subkegiatan-${subkegiatan.id}`,
                    kode: subkegiatanKode,
                    program: subkegiatanNama.startsWith('SUB KEGIATAN:') ? subkegiatanNama : `SUB KEGIATAN: ${subkegiatanNama}`,
                    paguPokok: 'Rp 0',
                    paguParsial: 'Rp 0',
                    paguPerubahan: 'Rp 0',
                    sumberDana: '', // Kosong untuk baris utama
                    targetFisik: '0%',
                    targetKeuangan: 'Rp 0',
                    realisasiFisik: '0%',
                    realisasiKeuangan: 'Rp 0',
                    realisasiKeuanganPersen: '0%',
                    keterangan: '', // Kosong untuk baris utama
                    pptk: '', // Kosong untuk baris utama
                    type: 'subkegiatan',
                });
            }
        });
    }

    return result;
});

// Fungsi untuk handle input change dengan validasi
const handleInputChange = (id: string, field: string, value: string) => {
    console.log(`🔄 Input changed - ID: ${id}, Field: ${field}, Value: ${value}`);
    console.log(`📊 Current editedItems state:`, JSON.stringify(editedItems.value, null, 2));

    // Auto-convert dan validasi untuk realisasi fisik
    if (field === 'realisasiFisik') {
        let numValue = parseFloat(value);
        if (!isNaN(numValue)) {
            // Auto-convert logic
            if (numValue > 100) {
                const originalValue = numValue;

                if (numValue >= 1000) {
                    // 1000% -> 100%, 1500% -> 100%, 2000% -> 100% (cap at 100%)
                    numValue = 100;
                    console.log(`Auto-convert: ${originalValue}% -> ${numValue}% (capped at 100%)`);
                } else {
                    // 550% -> 55%, 250% -> 25%, 150% -> 15%, 120% -> 12%
                    numValue = numValue / 10;
                    console.log(`Auto-convert: ${originalValue}% -> ${numValue}%`);

                    // Jika hasil pembagian masih > 100, cap di 100
                    if (numValue > 100) {
                        numValue = 100;
                        console.log(`Auto-convert: Further capped to ${numValue}%`);
                    }

                    // Round to 2 decimal places untuk hasil yang lebih bersih
                    numValue = Math.round(numValue * 100) / 100;
                }

                // Update value dengan hasil konversi
                value = numValue.toString();

                // Show notification untuk user awareness
                setTimeout(() => {
                    console.log(`✅ Kinerja fisik auto-converted: ${originalValue}% → ${numValue}%`);
                }, 100);
            }

            // Validasi setelah konversi
            if (numValue < 0) {
                showAlert('Validasi Error', 'Kinerja fisik tidak boleh kurang dari 0%', 'error');
                return;
            }
        }
    }

    // Validasi untuk realisasi keuangan (tidak boleh negatif)
    if (field === 'realisasiKeuangan') {
        const numValue = parseInt(value.replace(/[^\d]/g, ''));
        if (!isNaN(numValue) && numValue < 0) {
            showAlert('Validasi Error', 'Realisasi keuangan tidak boleh kurang dari 0', 'error');
            return;
        }
    }

    if (!editedItems.value[id]) {
        editedItems.value[id] = {};
    }

    editedItems.value[id][field] = value;

    console.log(`✅ Data saved to editedItems[${id}][${field}] = ${value}`);
    console.log(`📊 Updated editedItems state:`, JSON.stringify(editedItems.value, null, 2));

    // Update input field jika ada auto-convert untuk realisasi fisik
    if (field === 'realisasiFisik' && value !== editedItems.value[id][field]) {
        // Cari input field dan update nilainya
        const inputElement = document.querySelector(`[data-field="realisasiFisik-${id}"]`) as HTMLInputElement;
        if (inputElement) {
            inputElement.value = value;
        }
    }
};

// Computed untuk cek apakah ada perubahan pada item tertentu
const hasChanges = (id: string) => {
    const edited = editedItems.value[id];
    if (!edited) return false;

    // Cek apakah ada field yang diubah (realisasiFisik, realisasiKeuangan, keterangan, pptk)
    return Object.keys(edited).some((key) => {
        const value = edited[key];
        return value && value.toString().trim() !== '';
    });
};

// Computed untuk mendapatkan text button yang sesuai
const getButtonText = (id: string) => {
    if (savingItems.value[id]) {
        return 'Menyimpan...';
    }
    return hasChanges(id) ? 'Simpan Perubahan' : 'Simpan';
};

// Computed untuk mendapatkan class button yang sesuai
const getButtonClass = (id: string) => {
    const baseClass = 'px-3 py-1 bg-blue-500 text-white rounded text-sm font-medium hover:bg-blue-600 transition-all duration-200';
    const changedClass = 'px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700 transition-all duration-200';
    const loadingClass = 'px-4 py-2 bg-gray-400 text-white rounded text-sm font-semibold cursor-not-allowed';

    if (savingItems.value[id]) {
        return loadingClass;
    }
    return hasChanges(id) ? changedClass : baseClass;
};

// Fungsi untuk save data dengan support multiple sumber dana
const saveData = async (id: string) => {
    console.log('Saving data for ID:', id);

    // Set loading state
    savingItems.value[id] = true;

    try {
        // Check if this is a composite ID for sumber dana
        if (typeof id === 'string' && id.includes('_')) {
            const parts = id.split('_');
            const originalSubkegiatanId = parts[0];
            const sumberAnggaranId = parts[1];

            console.log('Composite ID detected:');
            console.log('- Original Subkegiatan ID:', originalSubkegiatanId);
            console.log('- Sumber Anggaran ID:', sumberAnggaranId);

            // Get edited data for this composite ID
            const editedData = editedItems.value[id];
            if (editedData) {
                console.log('- Edited Data:', editedData);

                // Prepare data for backend
                const savePayload = {
                    id: parseInt(originalSubkegiatanId), // task_id untuk backend
                    sumber_anggaran_id: parseInt(sumberAnggaranId), // PENTING: Kirim sumber_anggaran_id
                    realisasi_fisik: editedData.realisasiFisik ? parseFloat(editedData.realisasiFisik) : 0,
                    realisasi_keuangan: editedData.realisasiKeuangan ? parseInt(editedData.realisasiKeuangan.replace(/[^\d]/g, '')) : 0,
                    keterangan: editedData.keterangan || '',
                    nama_pptk: editedData.pptk || '',
                };

                console.log('- Save Payload:', savePayload);

                // Send to backend using Inertia
                router.post(`/triwulan/${props.tid}/save-realisasi`, savePayload, {
                    preserveState: false, // PERBAIKAN: Set false untuk get fresh data
                    preserveScroll: true,
                    onSuccess: async (page) => {
                        console.log(`✅ Save successful for ID: ${id}`, page);

                        // 📝 Log aktivitas triwulan
                        await ActivityLogger.logTriwulan('Menyimpan realisasi', props.triwulanName, {
                            subkegiatan_id: originalSubkegiatanId,
                            sumber_anggaran_id: sumberAnggaranId,
                            realisasi_fisik: savePayload.realisasi_fisik,
                            realisasi_keuangan: savePayload.realisasi_keuangan,
                            keterangan: savePayload.keterangan,
                            nama_pptk: savePayload.nama_pptk,
                            periode_id: props.periode?.id,
                            tahun: props.tahun,
                            triwulan_id: props.tid,
                        });

                        // PERBAIKAN: Update editedItems dengan data yang baru disimpan
                        // Ini memastikan persentase tetap tampil dengan data terbaru
                        editedItems.value[id] = {
                            realisasiFisik: savePayload.realisasi_fisik.toString(),
                            realisasiKeuangan: savePayload.realisasi_keuangan.toString(),
                            keterangan: savePayload.keterangan,
                            pptk: savePayload.nama_pptk,
                        };

                        console.log(`📊 Updated editedItems for ID ${id}:`, editedItems.value[id]);

                        // Show success message
                        const flash = (page.props as any).flash;
                        if (flash?.success) {
                            showAlert('Berhasil!', flash.success, 'success');
                        } else {
                            showAlert('Berhasil!', 'Data berhasil disimpan!', 'success');
                        }

                        // PERBAIKAN: Clear editedItems setelah delay untuk mempertahankan persentase
                        setTimeout(() => {
                            // Hanya clear jika tidak ada perubahan baru
                            const currentEdited = editedItems.value[id];
                            if (currentEdited && 
                                currentEdited.realisasiFisik === savePayload.realisasi_fisik.toString() &&
                                currentEdited.realisasiKeuangan === savePayload.realisasi_keuangan.toString()) {
                                // Data belum berubah, aman untuk clear
                                console.log(`🗑️ Safe to clear editedItems for ID: ${id}`);
                                delete editedItems.value[id];
                            }
                        }, 3000); // 3 detik delay untuk memastikan user melihat hasil
                    },
                    onError: (errors) => {
                        console.error('Save failed:', errors);
                        showAlert(
                            'Gagal Menyimpan',
                            'Gagal menyimpan data: ' + Object.values(errors).join(', '),
                            'error'
                        );
                    },
                    onFinish: () => {
                        // Clear loading state
                        savingItems.value[id] = false;
                    },
                });
            } else {
                console.log('No edited data found for ID:', id);
                savingItems.value[id] = false;
            }
        } else {
            // Regular subkegiatan save (shouldn't happen in current design)
            console.log('Regular subkegiatan save for ID:', id);
            savingItems.value[id] = false;
        }
    } catch (error) {
        console.error('Error in saveData:', error);
        showAlert('Terjadi Kesalahan', 'Terjadi kesalahan saat menyimpan data', 'error');
        savingItems.value[id] = false;
    }
};


</script>

<style scoped>
/* PERBAIKAN: Custom styles untuk layout PPTK dan Keterangan yang fleksibel */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Ensure textarea expands properly */
textarea.auto-expand {
    resize: none;
    transition: height 0.2s ease;
    overflow: hidden;
}

/* Better spacing for long text content */
.text-content {
    word-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
    line-height: 1.5;
}

/* Improve table cell alignment for dynamic content */
.table-cell-flexible {
    vertical-align: top;
    word-wrap: break-word;
}

/* Percentage badge styling */
.percentage-badge {
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

/* Animation for successful save */
@keyframes save-success {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.save-success {
    animation: save-success 0.3s ease;
}
</style>

<template>
    <Head :title="`Monitoring ${props.triwulanName}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 bg-gray-100 p-4 dark:bg-gray-800">
            <!-- Header section -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-4 rounded-full bg-blue-100 p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-blue-600"
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
                            <h1 class="text-xl font-bold text-gray-600">Monitoring {{ props.triwulanName }}</h1>
                            <p class="text-sm text-gray-500">Monitoring Detail {{ props.skpd?.nama_dinas || props.user?.nama_skpd || 'Perangkat Daerah' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Dropdown Periode dan Tahun -->
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
                                {{ tahunAktif?.tahun || tahun }}
                            </span>
                        </div>


                    </div>
                </div>

                <!-- Periode Status Alert -->
                <div v-if="periodeStatus && !periodeStatus.canInput" class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Input Data Dinonaktifkan</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ periodeStatus.reason }}. Anda tidak dapat menginput atau mengubah data pada periode ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Triwulan Header -->
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h2 class="mb-2 text-sm font-medium text-blue-600">Detail {{ triwulanName }}</h2>
                    <p class="text-xl font-bold text-blue-800">{{ skpd?.nama_dinas || tugas.skpd?.nama_skpd || 'SKPD' }}</p>
                </div>
            </div>

            <!-- Detail Perangkat Daerah -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <div class="mb-6 flex items-center">
                    <div class="mr-4 rounded-full bg-blue-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
                        <p class="text-sm text-gray-500">Informasi SKPD dan Kode Urusan</p>
                    </div>
                </div>

                <!-- KODE/URUSAN PEMERINTAHAN sebagai header utama -->
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h3 class="mb-2 text-sm font-medium text-blue-600">KODE/URUSAN PEMERINTAHAN:</h3>
                    <p class="text-xl font-bold text-blue-800">{{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Perangkat Daerah</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd?.nama_dinas || tugas.skpd?.nama_skpd || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd?.kode_organisasi || tugas.skpd?.kode_organisasi || 'ORG-001' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd?.nama_kepala_skpd || 'Tidak tersedia' }}</p>
                        <p v-if="skpd?.nip_kepala_skpd && skpd.nip_kepala_skpd !== '-'" class="font-mono text-sm text-gray-500">
                            NIP: {{ skpd.nip_kepala_skpd }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd?.nama_operator || 'Tidak tersedia' }}</p>
                        <p v-if="skpd?.nip_operator && skpd.nip_operator !== '-'" class="font-mono text-sm text-gray-500">
                            NIP: {{ skpd.nip_operator }}
                        </p>
                    </div>
                </div>
            </div>


            <!-- Program table with targets -->
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-medium text-gray-700">Tabel Data Monitoring</h3>
                        <span class="text-sm text-gray-500">Aksi</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse" style="table-layout: auto;">
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
                            <col style="width: 200px" />
                            <col style="width: 200px" />
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
                                    colspan="5"
                                    class="border-r border-gray-200 bg-blue-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    {{ props.triwulanName.toUpperCase() }}
                                </th>
                                <th
                                    rowspan="3"
                                    class="border-r border-gray-200 bg-gray-100 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    KETERANGAN
                                </th>
                                <th
                                    rowspan="3"
                                    class="border-r border-gray-200 bg-gray-100 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    PPTK
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
                                    TARGET
                                </th>
                                <th
                                    colspan="3"
                                    class="border-r border-gray-200 bg-yellow-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    REALISASI
                                </th>
                            </tr>
                            <tr class="border-b-2 border-gray-300">
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KINERJA<br />FISIK (%)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(RP)
                                </th>
                                <th class="bg-blue-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KINERJA<br />FISIK (%)
                                    <div class="mt-1 text-xs font-normal text-blue-600">🔄 Auto Convert</div>
                                    <div class="mt-1 text-xs font-normal text-green-600">550% → 55%</div>
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(%)
                                </th>
                                <th class="bg-green-25 border-r border-gray-200 px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase">
                                    KEUANGAN<br />(RP)
                                    <div class="mt-1 text-xs font-normal text-green-600">🔄 Auto Rp</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="programData.length === 0">
                                <td colspan="12" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            ></path>
                                        </svg>
                                        <div>
                                            <p class="font-medium">Belum ada data tersedia</p>
                                            <p class="mt-1 text-xs text-gray-400">Data akan muncul setelah rencana awal dibuat</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                v-for="item in programData"
                                :key="item.id"
                                :class="[
                                    'border-b border-gray-100 transition-colors hover:bg-blue-50',
                                    item.type === 'bidang_urusan' ? 'border-l-4 border-blue-500 bg-blue-50 font-extrabold' : '',
                                    item.type === 'program' ? 'border-l-4 border-gray-400 bg-gray-50 font-bold' : '',
                                    item.type === 'kegiatan' ? 'bg-orange-25 border-l-4 border-orange-400 font-semibold' : '',
                                    item.type === 'subkegiatan' ? 'border-l-4 border-yellow-500 bg-yellow-50' : '',
                                    item.type === 'subkegiatan_sumber_dana' ? 'bg-gray-25 border-l-4 border-gray-300' : '',
                                ]"
                                :data-item-type="item.type"
                                :data-item-id="item.id"
                            >
                                <!-- Kode Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                    {{ item.kode }}
                                </td>

                                <!-- Program/Kegiatan Column -->
                                <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                    <div class="line-clamp-3">{{ item.program }}</div>
                                </td>

                                <!-- Pagu Pokok Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ item.paguPokok || 'Rp 0' }}
                                </td>

                                <!-- Pagu Parsial Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ item.paguParsial || 'Rp 0' }}
                                </td>

                                <!-- Pagu Perubahan Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ item.paguPerubahan || 'Rp 0' }}
                                </td>

                                <!-- Sumber Dana Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                    <template v-if="item.sumberDana">
                                        <div class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                            {{ item.sumberDana }}
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-gray-400">-</span>
                                    </template>
                                </td>

                                <!-- Target Fisik Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                    {{ item.targetFisik }}
                                </td>

                                <!-- Target Keuangan Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ item.targetKeuangan }}
                                </td>

                                <!-- Realisasi Fisik Column -->
                                <td class="bg-blue-25 border-r border-gray-200 px-2 py-3 text-center align-middle">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <div class="relative">
                                            <input
                                                type="text"
                                                :data-field="`realisasiFisik-${item.id}`"
                                                class="h-8 w-20 rounded border border-blue-300 px-2 py-1 text-center text-xs transition-all duration-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'bg-blue-50 hover:bg-blue-100': true,
                                                    'border-orange-400 bg-orange-50 shadow-md':
                                                        editedItems[item.id]?.realisasiFisik &&
                                                        editedItems[item.id]?.realisasiFisik !== item.realisasiFisik,
                                                    'ring-2 ring-orange-200':
                                                        editedItems[item.id]?.realisasiFisik &&
                                                        editedItems[item.id]?.realisasiFisik !== item.realisasiFisik,
                                                }"
                                                :value="editedItems[item.id]?.realisasiFisik || item.realisasiFisik?.replace('%', '') || ''"
                                                @input="
                                                    (e: Event) => handleInputChange(item.id, 'realisasiFisik', (e.target as HTMLInputElement).value)
                                                "
                                                :disabled="isAdminOrOperator"
                                                placeholder="60"
                                                title="Masukkan angka 0-100. Auto-convert: 550% → 55%, 1000% → 100%"
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiFisik }}</span>
                                    </template>
                                </td>

                                <!-- Realisasi Keuangan Persentase Column -->
                                <td class="border-r border-gray-200 px-2 py-3 text-center align-middle text-sm">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <div class="relative">
                                            <span
                                                class="percentage-badge inline-flex h-8 w-16 items-center justify-center rounded border border-blue-200 bg-blue-50 text-xs font-semibold text-blue-700"
                                                :class="{
                                                    'border-green-200 bg-green-50 text-green-700 save-success': parseFloat(getKeuanganPersentase(item.id)) >= 80,
                                                    'border-yellow-200 bg-yellow-50 text-yellow-700':
                                                        parseFloat(getKeuanganPersentase(item.id)) >= 50 &&
                                                        parseFloat(getKeuanganPersentase(item.id)) < 80,
                                                    'border-red-200 bg-red-50 text-red-700': parseFloat(getKeuanganPersentase(item.id)) < 50,
                                                }"
                                                :title="`Dihitung dari: (Realisasi Keuangan / Pagu Pokok) × 100`"
                                            >
                                                {{ getKeuanganPersentase(item.id) }}
                                            </span>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiKeuanganPersen || '0%' }}</span>
                                    </template>
                                </td>

                                <!-- Realisasi Keuangan Rupiah Column -->
                                <td class="border-r border-gray-200 px-2 py-3 text-right align-middle text-sm">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <div class="relative">
                                            <input
                                                type="text"
                                                :data-field="`realisasiKeuangan-${item.id}`"
                                                class="h-8 w-24 rounded border border-green-300 px-2 py-1 text-right text-xs transition-all duration-200 focus:border-green-500 focus:ring-2 focus:ring-green-500"
                                                :class="{
                                                    'bg-green-50 hover:bg-green-100': true,
                                                    'border-orange-400 bg-orange-50 shadow-md':
                                                        editedItems[item.id]?.realisasiKeuangan &&
                                                        editedItems[item.id]?.realisasiKeuangan !== item.realisasiKeuangan,
                                                    'ring-2 ring-orange-200':
                                                        editedItems[item.id]?.realisasiKeuangan &&
                                                        editedItems[item.id]?.realisasiKeuangan !== item.realisasiKeuangan,
                                                }"
                                                :value="
                                                    editedItems[item.id]?.realisasiKeuangan || item.realisasiKeuangan?.replace(/[^\d]/g, '') || ''
                                                "
                                                @input="
                                                    (e: Event) =>
                                                        handleInputChange(item.id, 'realisasiKeuangan', (e.target as HTMLInputElement).value)
                                                "
                                                placeholder="500000"
                                                title="Masukkan angka tanpa titik/koma, format Rp akan ditambah otomatis. Persentase akan dihitung otomatis."
                                                :disabled="!canInputData || isAdminOrOperator"
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiKeuangan }}</span>
                                    </template>
                                </td>

                                <!-- Keterangan Column -->
                                <td class="border-r border-gray-200 px-3 py-3 align-top text-sm" style="min-width: 200px; max-width: 300px;">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <textarea
                                            :data-field="`keterangan-${item.id}`"
                                            class="w-full resize-none rounded border border-gray-300 px-2 py-1 text-xs leading-relaxed transition-all duration-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{
                                                'border-orange-400 bg-orange-50':
                                                    editedItems[item.id]?.keterangan && editedItems[item.id]?.keterangan !== item.keterangan,
                                            }"
                                            :value="editedItems[item.id]?.keterangan || item.keterangan"
                                            @input="(e: Event) => handleInputChange(item.id, 'keterangan', (e.target as HTMLTextAreaElement).value)"
                                            :disabled="isAdminOrOperator"
                                            placeholder="Masukkan keterangan..."
                                            rows="2"
                                            style="min-height: 40px;"
                                            @focus="(e: Event) => {
                                                const target = e.target as HTMLTextAreaElement;
                                                target.style.height = 'auto';
                                                target.style.height = Math.max(40, target.scrollHeight) + 'px';
                                            }"
                                            @blur="(e: Event) => {
                                                const target = e.target as HTMLTextAreaElement;
                                                if (!target.value.trim()) {
                                                    target.style.height = '40px';
                                                }
                                            }"
                                        />
                                    </template>
                                    <template v-else>
                                        <div class="max-w-[280px] break-words text-gray-400 leading-relaxed">{{ item.keterangan || '-' }}</div>
                                    </template>
                                </td>

                                <!-- PPTK Column -->
                                <td class="border-r border-gray-200 px-3 py-3 align-top text-sm" style="min-width: 180px; max-width: 250px;">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <textarea
                                            :data-field="`pptk-${item.id}`"
                                            class="w-full resize-none rounded border border-gray-300 px-2 py-1 text-xs leading-relaxed transition-all duration-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{
                                                'border-orange-400 bg-orange-50':
                                                    editedItems[item.id]?.pptk && editedItems[item.id]?.pptk !== item.pptk,
                                            }"
                                            :value="editedItems[item.id]?.pptk || item.pptk"
                                            @input="(e: Event) => {
                                                const target = e.target as HTMLTextAreaElement;
                                                // Auto-expand while typing
                                                target.style.height = 'auto';
                                                target.style.height = Math.max(35, target.scrollHeight) + 'px';
                                                // Call the existing input handler
                                                handleInputChange(item.id, 'pptk', target.value);
                                            }"
                                            placeholder="Masukkan nama PPTK..."
                                            :disabled="!canInputData || isAdminOrOperator"
                                            rows="1"
                                            style="min-height: 35px;"
                                            @focus="(e: Event) => {
                                                const target = e.target as HTMLTextAreaElement;
                                                target.style.height = 'auto';
                                                target.style.height = Math.max(35, target.scrollHeight) + 'px';
                                            }"
                                            @blur="(e: Event) => {
                                                const target = e.target as HTMLTextAreaElement;
                                                if (!target.value.trim()) {
                                                    target.style.height = '35px';
                                                }
                                            }"
                                        />
                                    </template>
                                    <template v-else>
                                        <div class="max-w-[230px] break-words text-gray-400 leading-relaxed">{{ item.pptk || '-' }}</div>
                                    </template>
                                </td>

                                <!-- Aksi Column -->
                                <td v-if="!isAdminOrOperator" class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <button
                                            @click="saveData(item.id)"
                                            :class="getButtonClass(item.id)"
                                            :disabled="savingItems[item.id] || !canInputData || isAdminOrOperator"
                                        >
                                            {{ getButtonText(item.id) }}
                                        </button>
                                    </template>
                                    <template v-else>
                                        <span class="text-gray-400">-</span>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Dialog Alert -->
    <Dialog :open="alertDialog.open" @update:open="(value) => alertDialog.open = value">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ alertDialog.title }}</DialogTitle>
                <DialogDescription>{{ alertDialog.message }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button 
                    @click="alertDialog.onConfirm"
                    :class="{
                        'bg-blue-600 text-white hover:bg-blue-700': alertDialog.type === 'info',
                        'bg-green-600 text-white hover:bg-green-700': alertDialog.type === 'success',
                        'bg-red-600 text-white hover:bg-red-700': alertDialog.type === 'error'
                    }"
                >
                    OK
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- PDF Configuration Modal - Same as Rencana Awal -->
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
                        <h2 class="text-2xl font-bold text-gray-800">Konfigurasi PDF {{ props.triwulanName }}</h2>
                    </div>
                    <button @click="showPdfModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-6">
                    {{ props.skpd?.nama_dinas || props.user?.nama_skpd || 'SKPD' }} - {{ props.tugas?.kodeNomenklatur?.nomor_kode || '-' }} {{ props.tugas?.kodeNomenklatur?.nomenklatur || '-' }}
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
                                <option v-for="tahun in props.allTahun" :key="tahun.id" :value="tahun.tahun">
                                    {{ tahun.tahun }} {{ tahun.status === 1 ? '(Aktif)' : '' }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Penandatangan -->
                    <div class="bg-emerald-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Penandatangan</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Informasi pejabat yang menandatangani laporan PDF</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
                                <input type="text" v-model="pdfForm.penandatangan_1_tempat" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" v-model="pdfForm.penandatangan_1_tanggal" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pejabat</label>
                                <input type="text" v-model="pdfForm.penandatangan_1_nama" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <input type="text" v-model="pdfForm.penandatangan_1_jabatan" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                <input type="text" v-model="pdfForm.penandatangan_1_nip" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan PDF -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Pengaturan PDF</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Konfigurasi format dan ukuran dokumen PDF</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kertas</label>
                                <select v-model="pdfForm.paper_size" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none">
                                    <option value="A4">A4</option>
                                    <option value="A3">A3</option>
                                    <option value="Letter">Letter</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Orientasi</label>
                                <select v-model="pdfForm.orientation" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none">
                                    <option value="portrait">Portrait (Tegak)</option>
                                    <option value="landscape">Landscape (Mendatar)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Margin (mm)</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Atas</label>
                                    <input type="number" v-model.number="pdfForm.margin_top" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Kanan</label>
                                    <input type="number" v-model.number="pdfForm.margin_right" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Bawah</label>
                                    <input type="number" v-model.number="pdfForm.margin_bottom" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Kiri</label>
                                    <input type="number" v-model.number="pdfForm.margin_left" min="0" max="50" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none">
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

    
</template>
