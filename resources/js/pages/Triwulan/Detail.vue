<script setup lang="ts">
import { useTriwulanData } from '@/composables/useTriwulanData';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
    periode: { id: number; nama: string };
    triwulanName: string;
}>();

// Breadcrumbs dinamis
const breadcrumbs = computed<BreadcrumbItem[]>(() => {
    const showHref = props.user && props.user.id ? route('triwulan.show', { tid: props.tid, id: props.user.id }) : '#';

    return [
        { title: `Monitoring ${props.triwulanName}`, href: route('triwulan.index', { tid: props.tid }) },
        { title: `Monitoring Detail ${props.user?.nama_skpd || 'SKPD'}`, href: showHref },
        { title: 'Rencana Awal PD', href: '' },
    ];
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

const goToDetail = (taskId: number) => {
    if (!props.user?.id) return;

    router.visit(
        route('triwulan.detail', {
            tid: props.tid,
            id: props.user.id,
            taskId: taskId,
        }),
    );
};

// Tambahkan fungsi-fungsi yang diperlukan untuk tabel
const editedItems = ref<Record<string, any>>({});
const savingItems = ref<Record<string, boolean>>({});

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

// Helper function untuk menghitung agregasi data
const calculateAggregatedData = (items: any[], type: 'fisik' | 'keuangan') => {
    if (!items || items.length === 0) return type === 'fisik' ? '0%' : 'Rp 0';

    if (type === 'fisik') {
        // Untuk fisik, gunakan rata-rata
        const total = items.reduce((sum, item) => sum + (parseFloat(item.targetFisik) || 0), 0);
        const avg = total / items.length;
        return `${avg.toFixed(1)}%`;
    } else {
        // Untuk keuangan, gunakan total
        const total = items.reduce((sum, item) => {
            const value = item.targetKeuangan?.replace(/[^\d]/g, '') || '0';
            return sum + parseInt(value);
        }, 0);
        return `Rp ${total.toLocaleString('id-ID')}`;
    }
};

// Computed property untuk data program dengan data real dari RencanaAwal
const programData = computed(() => {
    const result: any[] = [];

    // Hitung agregasi untuk semua level hierarki
    const allSubkegiatanTargets = [];
    const allSubkegiatanRealisasi = [];

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
            targetFisik: `${avgTargetFisik.toFixed(1)}%`,
            targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
            realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
            realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
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
                targetFisik: `${avgTargetFisik.toFixed(1)}%`,
                targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
                realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
                realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
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
                targetFisik: `${avgTargetFisik.toFixed(1)}%`,
                targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
                realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
                realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
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
                    targetFisik: `${avgTargetFisik.toFixed(1)}%`,
                    targetKeuangan: `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`,
                    realisasiFisik: `${avgRealisasiFisik.toFixed(2)}%`,
                    realisasiKeuangan: `Rp ${totalRealisasiKeuangan.toLocaleString('id-ID')}`,
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
                        fullTarget: target,
                        matchingRealisasi: matchingRealisasi,
                    });

                    // Gunakan nama sumber anggaran dari backend
                    const sumberDanaNama = target.sumber_anggaran_nama || `Sumber Dana ${target.sumber_anggaran_id}`;

                    result.push({
                        id: `${subkegiatan.id}_${target.sumber_anggaran_id}`,
                        kode: '',
                        program: `└─ ${sumberDanaNama}`,
                        targetFisik: `${target.kinerja_fisik || 0}%`,
                        targetKeuangan: `Rp ${(target.keuangan || 0).toLocaleString('id-ID')}`,
                        realisasiFisik: `${matchingRealisasi?.kinerja_fisik || 0}%`,
                        realisasiKeuangan: `Rp ${(matchingRealisasi?.keuangan || 0).toLocaleString('id-ID')}`,
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
                    targetFisik: '0%',
                    targetKeuangan: 'Rp 0',
                    realisasiFisik: '0%',
                    realisasiKeuangan: 'Rp 0',
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
                alert('Kinerja fisik tidak boleh kurang dari 0%');
                return;
            }
        }
    }

    // Validasi untuk realisasi keuangan (tidak boleh negatif)
    if (field === 'realisasiKeuangan') {
        const numValue = parseInt(value.replace(/[^\d]/g, ''));
        if (!isNaN(numValue) && numValue < 0) {
            alert('Realisasi keuangan tidak boleh kurang dari 0');
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
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        console.log(`✅ Save successful for ID: ${id}`, page);

                        // PENTING: Hanya clear data untuk ID yang spesifik ini
                        console.log(`🗑️ Clearing editedItems for ID: ${id}`);
                        console.log(`📊 Before clear:`, JSON.stringify(editedItems.value, null, 2));

                        delete editedItems.value[id];

                        console.log(`📊 After clear:`, JSON.stringify(editedItems.value, null, 2));

                        // Show success message from backend
                        const flash = (page.props as any).flash;
                        if (flash?.success) {
                            alert(flash.success);
                        } else {
                            alert('Data berhasil disimpan!');
                        }
                    },
                    onError: (errors) => {
                        console.error('Save failed:', errors);
                        alert('Gagal menyimpan data: ' + Object.values(errors).join(', '));
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
        alert('Terjadi kesalahan saat menyimpan data');
        savingItems.value[id] = false;
    }
};

// ...lanjutkan dengan logic utama, computed, dan fungsi yang digunakan di template...
// (Bagian ini akan diisi bertahap jika perlu, agar tidak overload token)
</script>

<template>
    <Head :title="`Monitoring ${triwulanName}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 bg-gray-100 p-4 dark:bg-gray-800">
            <!-- Header section -->
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
                    <h2 class="text-2xl font-bold text-gray-600">{{ triwulanName }}</h2>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="col-span-1 rounded-lg border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">KODE/URUSAN PEMERINTAHAN:</h3>
                        <p class="text-lg font-semibold text-gray-500">
                            {{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama SKPD</h3>
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
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ skpd?.nama_operator || 'Tidak tersedia' }}</p>
                        <p v-if="skpd?.nip_operator && skpd.nip_operator !== '-'" class="font-mono text-sm text-gray-500">
                            NIP: {{ skpd.nip_operator }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Program table with targets -->
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-600">Data Monitoring {{ triwulanName }}</h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed border-collapse">
                        <colgroup>
                            <col style="width: 120px" />
                            <col style="width: 250px" />
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
                                    colspan="4"
                                    class="border-r border-gray-200 bg-blue-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    {{ triwulanName.toUpperCase() }}
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
                                <th rowspan="3" class="bg-gray-100 px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">AKSI</th>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <th
                                    colspan="2"
                                    class="border-r border-gray-200 bg-green-50 px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase"
                                >
                                    TARGET
                                </th>
                                <th
                                    colspan="2"
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
                                    KEUANGAN<br />(RP)
                                    <div class="mt-1 text-xs font-normal text-green-600">🔄 Auto Rp</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="programData.length === 0">
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
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
                                                placeholder="60"
                                                title="Masukkan angka 0-100. Auto-convert: 550% → 55%, 1000% → 100%"
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiFisik }}</span>
                                    </template>
                                </td>

                                <!-- Realisasi Keuangan Column -->
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
                                                title="Masukkan angka tanpa titik/koma, format Rp akan ditambah otomatis"
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiKeuangan }}</span>
                                    </template>
                                </td>

                                <!-- Keterangan Column -->
                                <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <input
                                            type="text"
                                            :data-field="`keterangan-${item.id}`"
                                            class="w-full rounded border border-gray-300 px-2 py-1 text-xs transition-all duration-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{
                                                'border-orange-400 bg-orange-50':
                                                    editedItems[item.id]?.keterangan && editedItems[item.id]?.keterangan !== item.keterangan,
                                            }"
                                            :value="editedItems[item.id]?.keterangan || item.keterangan"
                                            @input="(e: Event) => handleInputChange(item.id, 'keterangan', (e.target as HTMLInputElement).value)"
                                            placeholder="Masukkan keterangan..."
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-gray-400">{{ item.keterangan || '-' }}</span>
                                    </template>
                                </td>

                                <!-- PPTK Column -->
                                <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <input
                                            type="text"
                                            :data-field="`pptk-${item.id}`"
                                            class="w-full rounded border border-gray-300 px-2 py-1 text-xs transition-all duration-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{
                                                'border-orange-400 bg-orange-50':
                                                    editedItems[item.id]?.pptk && editedItems[item.id]?.pptk !== item.pptk,
                                            }"
                                            :value="editedItems[item.id]?.pptk || item.pptk"
                                            @input="(e: Event) => handleInputChange(item.id, 'pptk', (e.target as HTMLInputElement).value)"
                                            placeholder="Masukkan nama PPTK..."
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-gray-400">{{ item.pptk || '-' }}</span>
                                    </template>
                                </td>

                                <!-- Aksi Column -->
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                    <template v-if="item.type === 'subkegiatan_sumber_dana'">
                                        <button @click="saveData(item.id)" :class="getButtonClass(item.id)" :disabled="savingItems[item.id]">
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
</template>
