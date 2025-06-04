<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';

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
  user: {
    id: number;
    nama_skpd: string;
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
      }>;
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
      }>;
    };
    rencana_awal?: {
      indikator: string;
      target: string;
    } | null;
    monitoring?: Array<{
      id: number;
      monitoring_anggaran: Array<{
        monitoring_target: Array<{
          id: number;
          kinerja_fisik: number;
          keuangan: number;
          periode: {
            nama: string;
          };
        }>;
      }>;
    }>;
  };
  programTugas: Array<any>;
  kegiatanTugas: Array<any>;
  subkegiatanTugas: Array<any>;
  kepalaSkpd?: string;
  bidangUrusan: {
    id: number;
    nomor_kode: string;
    nomenklatur: string;
    deskripsi: string;
  } | null;
  monitoringTargets: Array<{
    id: number;
    kinerja_fisik: number;
    keuangan: number;
    periode: string;
    monitoring_id: number;
    task_id: number;
    deskripsi: string;
    nama_pptk?: string;
  }>;
  monitoringRealisasi: Array<{
    id: number;
    kinerja_fisik: number;
    keuangan: number;
    periode: string;
    monitoring_id: number;
    task_id: number;
    monitoring_anggaran_id: number;
    deskripsi?: string;
    nama_pptk?: string;
  }>;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Monitoring Triwulan 1', href: '/triwulan1' },
  { title: `Monitoring Detail ${props.user.nama_skpd}`, href: '/triwulan1/show' },
  { title: 'Rencana Awal PD', href: '/triwulan1/detail' },
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
        skpd_id: props.user.id,
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
    const currentSkpdId = props.user.id;

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

// Add computed property for monitoring targets
const monitoringTargetsByPeriode = computed(() => {
  const targets = props.monitoringTargets || [];
  const grouped = targets.reduce((acc, target) => {
    const periode = target.periode || 'Tidak ada periode';
    if (!acc[periode]) {
      acc[periode] = [];
    }
    acc[periode].push(target);
    return acc;
  }, {} as Record<string, typeof targets>);
  
  return grouped;
});

// Add onMounted hook to log data
onMounted(() => {
  console.log('Props data:', {
    programTugas: props.programTugas,
    kegiatanTugas: props.kegiatanTugas,
    subkegiatanTugas: props.subkegiatanTugas,
    monitoringTargets: props.monitoringTargets
  });
  
  // Log semua targets untuk melihat data asli dari backend
  console.log('ALL MONITORING TARGETS FROM BACKEND:', props.monitoringTargets);
  
  // =================================================================
  // KHUSUS FILTER DENGAN PERIODE_ID = 2 (STRICT EQUALITY)
  // =================================================================
  
  // Filter semua target yang HANYA memiliki periode_id = 2 (strict equality)
  const strictPeriodeIdFilter = props.monitoringTargets.filter(t => t.periode_id === 2);
  console.log('STRICT FILTER: TARGETS WITH PERIODE_ID = 2 ONLY:', strictPeriodeIdFilter);
  
  // Untuk masing-masing subkegiatan, cari target dengan periode_id = 2
  props.subkegiatanTugas.forEach(subkegiatan => {
    const targetsForSubkegiatan = props.monitoringTargets.filter(t => 
      t.task_id === subkegiatan.id && t.periode_id === 2
    );
    console.log(`TARGETS FOR SUBKEGIATAN ${subkegiatan.id} WITH STRICT PERIODE_ID = 2:`, targetsForSubkegiatan);
    
    if (targetsForSubkegiatan.length > 0) {
      console.log(`DATA TARGET SUBKEGIATAN ${subkegiatan.id}:`, {
        kinerja_fisik: targetsForSubkegiatan[0].kinerja_fisik,
        keuangan: targetsForSubkegiatan[0].keuangan,
        periode_id: targetsForSubkegiatan[0].periode_id,
      });
    }
  });
  
  // Cek apakah ada target dengan periode_id = 2 untuk subkegiatan manapun
  const subkegiatanIds = props.subkegiatanTugas.map(sk => sk.id);
  const strictSubkegiatanTargets = props.monitoringTargets.filter(t => 
    subkegiatanIds.includes(t.task_id) && t.periode_id === 2
  );
  
  console.log('STRICT FILTER: SUBKEGIATAN TARGETS WITH PERIODE_ID = 2:', strictSubkegiatanTargets);
  
  if (strictSubkegiatanTargets.length === 0) {
    console.warn('PERINGATAN: Tidak ada target dengan periode_id = 2 untuk subkegiatan manapun (filter ketat)');
  } else {
    console.log(`DITEMUKAN ${strictSubkegiatanTargets.length} TARGET DENGAN PERIODE_ID = 2 UNTUK SUBKEGIATAN`);
  }
  
  // =================================================================
  
  // Debug: Cek struktur periode_id
  if (props.monitoringTargets.length > 0) {
    const sample = props.monitoringTargets[0];
    console.log('PERIODE ID TYPE:', typeof sample.periode_id, 'VALUE:', sample.periode_id);
    
    // Kelompokkan targets berdasarkan periode_id untuk melihat distribusi data
    const targetsByPeriod = props.monitoringTargets.reduce((acc, target) => {
      const periodId = target.periode_id || 'undefined';
      if (!acc[periodId]) acc[periodId] = [];
      acc[periodId].push(target);
      return acc;
    }, {});
    
    console.log('TARGETS GROUPED BY PERIODE_ID:', targetsByPeriod);
    
    // Cek khusus untuk periode_id = 2 (Triwulan 1)
    const triwulan1Targets = props.monitoringTargets.filter(t => t.periode_id === 2);
    console.log('STRICT PERIODE_ID === 2 TARGETS ONLY:', triwulan1Targets);
    
    // Periksa apakah ada target untuk subkegiatan dengan periode_id = 2
    const subkegiatanIds = props.subkegiatanTugas.map(sk => sk.id);
    const subkegiatanTriwulan1Targets = props.monitoringTargets.filter(t => 
      subkegiatanIds.includes(t.task_id) && t.periode_id === 2
    );
    console.log('SUBKEGIATAN TARGETS WITH STRICT PERIODE_ID === 2:', subkegiatanTriwulan1Targets);
    
    if (subkegiatanTriwulan1Targets.length === 0) {
      console.warn('PERINGATAN: Tidak ada target periode Triwulan 1 untuk subkegiatan manapun!');
    }
  }
  
  // Untuk melihat distribusi task_id pada monitoringTargets
  const taskDistribution = {};
  props.monitoringTargets.forEach(target => {
    if (!taskDistribution[target.task_id]) taskDistribution[target.task_id] = 0;
    taskDistribution[target.task_id]++;
  });
  console.log('DISTRIBUSI TARGET BERDASARKAN TASK_ID:', taskDistribution);
});

// Update the formatPercentage function to handle very large values
const formatPercentage = (value: string | number): string => {
  if (value === '-') return '-';
  
  // If it's already a string with % sign, extract the numeric part
  if (typeof value === 'string' && value.includes('%')) {
    value = parseFloat(value.replace('%', ''));
  }
  
  // Format as number with 2 decimal places and add % sign
  if (!isNaN(Number(value))) {
    // Cap extremely large values to prevent display issues
    const numValue = parseFloat(value.toString());
    
    // If the value is unreasonably large (over 1000%), cap it at a reasonable level
    if (numValue > 1000) {
      console.warn(`Very large percentage value detected: ${numValue}, capping at 100%`);
      return '100.00%';
    }
    
    return `${numValue.toFixed(2)}%`;
  }
  
  return value.toString();
}

// Create programData computed property to combine tasks and monitoring data
const programData = computed(() => {
  const data: Array<{
    id: number;
    kode: string;
    program: string;
    targetFisik: string;
    targetKeuangan: string;
    realisasiFisik: string;
    realisasiKeuangan: string;
    capaianFisik: string;
    capaianKeuangan: string;
    capaianTahunanFisik: string;
    capaianTahunanKeuangan: string;
    keterangan: string;
    pptk: string;
    type: string;
    indentLevel: number;
    bidangUrusan: string;
    _realisasiFisikValue?: number; // Helper property for calculations
    _realisasiKeuanganValue?: number; // Helper property for calculations
    _subItems?: number[]; // Helper property for tracking sub-items
    _targetKeuanganValue?: number; // Helper property for tracking target keuangan
  }> = [];

  // Only add bidang urusan as header
  const bidangUrusanIndex = props.bidangUrusan ? data.length : -1;

  if (props.bidangUrusan) {
    data.push({
      id: props.bidangUrusan.id,
      kode: props.bidangUrusan.nomor_kode,
      program: `BIDANG URUSAN: ${props.bidangUrusan.nomenklatur}`,
      targetFisik: '-',
      targetKeuangan: '-',
      realisasiFisik: '-',
      realisasiKeuangan: '-',
      capaianFisik: '-',
      capaianKeuangan: '-',
      capaianTahunanFisik: '-',
      capaianTahunanKeuangan: '-',
      keterangan: props.bidangUrusan.deskripsi || '-',
      pptk: '-',
      type: 'bidang_urusan',
      indentLevel: 0,
      bidangUrusan: '-',
      _subItems: [] // Track program IDs
    });
  }
  
  // Track program and kegiatan indices for later calculations
  const programIndices = new Map<number, number>(); // program ID -> data index
  const kegiatanIndices = new Map<number, {dataIndex: number, programId: number}>(); // kegiatan ID -> {data index, program ID}
  const subkegiatanData = new Map<number, {kegiatanId: number, realisasiFisik?: number, realisasiKeuangan?: number}>();
  
  // Add program data
  props.programTugas.forEach(program => {
    // Find targets directly associated with this task by task_id AND strict periode_id = 2
    const targetsForTask = props.monitoringTargets.filter(t => 
      t.task_id === program.id && t.periode_id === 2
    );
    console.log(`Program ${program.id} has ${targetsForTask.length} targets with strict periode_id = 2:`, targetsForTask);
    
    // Calculate aggregate target data if multiple targets exist
    let kinerjaFisik = '-';
    let keuangan = '-';
    let keuanganValue = 0;
    let deskripsi = program.monitoring && program.monitoring[0] ? program.monitoring[0].deskripsi : 'Program';
    
    if (targetsForTask.length > 0) {
      const avgKinerjaFisik = targetsForTask.reduce((sum, t) => sum + t.kinerja_fisik, 0) / targetsForTask.length;
      const totalKeuangan = targetsForTask.reduce((sum, t) => sum + t.keuangan, 0);
      kinerjaFisik = `${avgKinerjaFisik.toFixed(2)}%`;
      keuangan = `Rp ${totalKeuangan.toLocaleString('id-ID')}`;
      keuanganValue = totalKeuangan;
      // If we have a target with deskripsi, use that
      if (targetsForTask[0] && targetsForTask[0].deskripsi) {
        deskripsi = targetsForTask[0].deskripsi;
      }
    }
    
    const programIndex = data.length;
    data.push({
      id: program.id,
      kode: program.kode_nomenklatur?.nomor_kode || '-',
      program: program.kode_nomenklatur?.nomenklatur || '-',
      targetFisik: kinerjaFisik,
      targetKeuangan: keuangan,
      _targetKeuanganValue: keuanganValue,
      realisasiFisik: '-',
      realisasiKeuangan: '-',
      capaianFisik: '-',
      capaianKeuangan: '-',
      capaianTahunanFisik: '-',
      capaianTahunanKeuangan: '-',
      keterangan: deskripsi,
      pptk: program.nama_pptk || '-',
      type: 'program',
      indentLevel: 0,
      bidangUrusan: props.bidangUrusan?.nomenklatur || '-',
      _subItems: [] // Track kegiatan IDs
    });
    
    programIndices.set(program.id, programIndex);
    
    // Add kegiatan that belong to this program
    props.kegiatanTugas.forEach(kegiatan => {
      // Check if this kegiatan belongs to current program
      if (kegiatan.kode_nomenklatur?.nomor_kode?.startsWith(program.kode_nomenklatur?.nomor_kode)) {
        // Find targets directly associated with this task by task_id AND strict periode_id = 2
        const kegiatanTargets = props.monitoringTargets.filter(t => 
          t.task_id === kegiatan.id && t.periode_id === 2
        );
        console.log(`Kegiatan ${kegiatan.id} has ${kegiatanTargets.length} targets with strict periode_id = 2:`, kegiatanTargets);
        
        let kinerjaFisikKegiatan = '-';
        let keuanganKegiatan = '-';
        let deskripsiKegiatan = kegiatan.monitoring && kegiatan.monitoring[0] ? kegiatan.monitoring[0].deskripsi : 'Kegiatan';
        
        if (kegiatanTargets.length > 0) {
          const avgKinerjaFisik = kegiatanTargets.reduce((sum, t) => sum + t.kinerja_fisik, 0) / kegiatanTargets.length;
          const totalKeuangan = kegiatanTargets.reduce((sum, t) => sum + t.keuangan, 0);
          kinerjaFisikKegiatan = `${avgKinerjaFisik.toFixed(2)}%`;
          keuanganKegiatan = `Rp ${totalKeuangan.toLocaleString('id-ID')}`;
          // If we have a target with deskripsi, use that
          if (kegiatanTargets[0] && kegiatanTargets[0].deskripsi) {
            deskripsiKegiatan = kegiatanTargets[0].deskripsi;
          }
        }
        
        const kegiatanIndex = data.length;
        data.push({
          id: kegiatan.id,
          kode: kegiatan.kode_nomenklatur?.nomor_kode || '-',
          program: `   ${kegiatan.kode_nomenklatur?.nomenklatur || '-'}`,
          targetFisik: kinerjaFisikKegiatan,
          targetKeuangan: keuanganKegiatan,
          realisasiFisik: '-',
          realisasiKeuangan: '-',
          capaianFisik: '-',
          capaianKeuangan: '-',
          capaianTahunanFisik: '-',
          capaianTahunanKeuangan: '-',
          keterangan: deskripsiKegiatan,
          pptk: kegiatan.nama_pptk || '-',
          type: 'kegiatan',
          indentLevel: 1,
          bidangUrusan: '-',
          _subItems: [] // Track subkegiatan IDs
        });
        
        kegiatanIndices.set(kegiatan.id, {dataIndex: kegiatanIndex, programId: program.id});
        
        // Add program's subItems reference
        if (data[programIndex]._subItems) {
          data[programIndex]._subItems.push(kegiatan.id);
        }
        
        // Add subkegiatan that belong to this kegiatan
        props.subkegiatanTugas.forEach(subkegiatan => {
          // Check if this subkegiatan belongs to current kegiatan
          if (subkegiatan.kode_nomenklatur?.nomor_kode?.startsWith(kegiatan.kode_nomenklatur?.nomor_kode)) {
            // Find targets directly associated with this task by task_id AND strict periode_id = 2
            const subkegiatanTargets = props.monitoringTargets.filter(t => 
              t.task_id === subkegiatan.id && t.periode_id === 2
            );
            console.log(`Subkegiatan ${subkegiatan.id} has ${subkegiatanTargets.length} targets with strict periode_id = 2:`, subkegiatanTargets);
            
            // Find realisasi data for this subkegiatan AND strict periode_id = 2
            const realisasiData = props.monitoringRealisasi.filter(r => 
              r.task_id === subkegiatan.id && r.periode_id === 2
            );
            console.log(`Subkegiatan ${subkegiatan.id} has ${realisasiData.length} realisasi records with strict periode_id = 2:`, realisasiData);
            
            let kinerjaFisikSubkegiatan = '-';
            let keuanganSubkegiatan = '-';
            let realisasiFisik = '-';
            let realisasiKeuangan = '-';
            let capaianFisik = '-';
            let capaianKeuangan = '-';
            let capaianTahunanFisik = '-';
            let capaianTahunanKeuangan = '-';
            let deskripsiSubkegiatan = 'Sub Kegiatan';
            let pptkSubkegiatan = '-';
            
            if (subkegiatanTargets.length > 0) {
              // Ambil data langsung dari database tanpa kalkulasi
              const target = subkegiatanTargets[0]; // Ambil target pertama
              
              // Debug target data dengan detail
              console.log(`TARGET DATA FOR DISPLAY (SUBKEGIATAN ${subkegiatan.id}):`, {
                id: target.id,
                task_id: target.task_id,
                kinerja_fisik: target.kinerja_fisik,
                keuangan: target.keuangan,
                periode: target.periode,
                periode_id: target.periode_id
              });
              
              // Tampilkan data murni dari database
              kinerjaFisikSubkegiatan = target.kinerja_fisik ? `${target.kinerja_fisik}%` : '-';
              keuanganSubkegiatan = target.keuangan ? `Rp ${target.keuangan.toLocaleString('id-ID')}` : '-';
              
              // Get deskripsi and nama_pptk from monitoringTargets
              if (target.deskripsi) {
                deskripsiSubkegiatan = target.deskripsi;
              }
              if (target.nama_pptk) {
                pptkSubkegiatan = target.nama_pptk;
              }
            }
            
            // Populate realisasi data if exists
            let realisasiFisikValue = 0;
            let realisasiKeuanganValue = 0;
            
            if (realisasiData.length > 0) {
              const latestRealisasi = realisasiData[0]; // Use the first realisasi record
              realisasiFisikValue = latestRealisasi.kinerja_fisik;
              realisasiKeuanganValue = latestRealisasi.keuangan;
              realisasiFisik = `${realisasiFisikValue.toFixed(2)}%`;
              realisasiKeuangan = `Rp ${realisasiKeuanganValue.toLocaleString('id-ID')}`;
              
              // Override with more recent data from realisasi if available
              if (latestRealisasi.deskripsi) {
                deskripsiSubkegiatan = latestRealisasi.deskripsi;
              }
              if (latestRealisasi.nama_pptk) {
                pptkSubkegiatan = latestRealisasi.nama_pptk;
              }
              
              // Calculate capaian if target exists
              if (kinerjaFisikSubkegiatan !== '-' && realisasiFisik !== '-') {
                const targetValue = parseFloat(kinerjaFisikSubkegiatan.replace('%', ''));
                if (!isNaN(targetValue) && targetValue > 0) {
                  const capaian = (realisasiFisikValue / targetValue) * 100;
                  capaianFisik = `${capaian.toFixed(2)}%`;
                  
                  // Calculate kinerja tahunan (based on S27/100*100 formula, assuming S27 is capaian fisik)
                  const kinerjaFisikTahunan = capaian; // Same as capaian fisik
                  capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
                }
              }
              
              if (keuanganSubkegiatan !== '-' && realisasiKeuangan !== '-') {
                const targetValue = parseFloat(keuanganSubkegiatan.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(targetValue) && targetValue > 0) {
                  // Use Math.min to cap the value at 100%
                  const capaian = Math.min(100, (realisasiKeuanganValue / targetValue) * 100);
                  capaianKeuangan = `${capaian.toFixed(2)}%`;
                  
                  // Calculate keuangan tahunan (same as capaian)
                  capaianTahunanKeuangan = capaianKeuangan;
                }
              }
              
              // Store subkegiatan data for aggregation
              subkegiatanData.set(subkegiatan.id, {
                kegiatanId: kegiatan.id, 
                realisasiFisik: realisasiFisikValue,
                realisasiKeuangan: realisasiKeuanganValue
              });
              
              // Add kegiatan's subItems reference
              if (data[kegiatanIndex]._subItems) {
                data[kegiatanIndex]._subItems.push(subkegiatan.id);
              }
            }
            
            data.push({
              id: subkegiatan.id,
              kode: subkegiatan.kode_nomenklatur?.nomor_kode || '-',
              program: `      ${subkegiatan.kode_nomenklatur?.nomenklatur || '-'}`,
              targetFisik: kinerjaFisikSubkegiatan,
              targetKeuangan: keuanganSubkegiatan,
              realisasiFisik: realisasiFisik,
              realisasiKeuangan: realisasiKeuangan,
              capaianFisik: capaianFisik,
              capaianKeuangan: capaianKeuangan,
              capaianTahunanFisik: capaianTahunanFisik,
              capaianTahunanKeuangan: capaianTahunanKeuangan,
              keterangan: deskripsiSubkegiatan,
              pptk: pptkSubkegiatan,
              type: 'subkegiatan',
              indentLevel: 2,
              bidangUrusan: '-',
              _realisasiFisikValue: realisasiFisikValue,
              _realisasiKeuanganValue: realisasiKeuanganValue
            });
          }
        });
      }
    });
  });
  
  // Calculate realisasi for kegiatan (average fisik, sum keuangan from subkegiatan)
  kegiatanIndices.forEach((info, kegiatanId) => {
    const kegiatanItem = data[info.dataIndex];
    const relatedSubkegiatan = [...subkegiatanData.entries()]
      .filter(([_, data]) => data.kegiatanId === kegiatanId)
      .map(([id, _]) => id);
    
    if (relatedSubkegiatan.length > 0) {
      // Calculate average realisasi fisik
      const totalFisik = relatedSubkegiatan.reduce((sum, id) => {
        const subData = subkegiatanData.get(id);
        return sum + (subData?.realisasiFisik || 0);
      }, 0);
      const avgFisik = totalFisik / relatedSubkegiatan.length;
      
      // Calculate sum realisasi keuangan
      const totalKeuangan = relatedSubkegiatan.reduce((sum, id) => {
        const subData = subkegiatanData.get(id);
        return sum + (subData?.realisasiKeuangan || 0);
      }, 0);
      
      // Update kegiatan values
      kegiatanItem.realisasiFisik = `${avgFisik.toFixed(2)}%`;
      kegiatanItem.realisasiKeuangan = `Rp ${totalKeuangan.toLocaleString('id-ID')}`;
      kegiatanItem._realisasiFisikValue = avgFisik;
      kegiatanItem._realisasiKeuanganValue = totalKeuangan;
      
      // Calculate capaian
      if (kegiatanItem.targetFisik !== '-') {
        const targetFisik = parseFloat(kegiatanItem.targetFisik.replace('%', ''));
        if (!isNaN(targetFisik) && targetFisik > 0) {
          const capaian = (avgFisik / targetFisik) * 100;
          kegiatanItem.capaianFisik = `${capaian.toFixed(2)}%`;
          
          // Calculate kinerja tahunan
          const kinerjaFisikTahunan = capaian;
          kegiatanItem.capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
        }
      }
      
      if (kegiatanItem.targetKeuangan !== '-') {
        const targetKeuangan = parseFloat(kegiatanItem.targetKeuangan.replace(/[^0-9.-]+/g, ''));
        if (!isNaN(targetKeuangan) && targetKeuangan > 0) {
          // Use Math.min to cap the value at 100%
          const capaian = Math.min(100, (totalKeuangan / targetKeuangan) * 100);
          kegiatanItem.capaianKeuangan = `${capaian.toFixed(2)}%`;
          
          // Calculate keuangan tahunan (same as capaian)
          kegiatanItem.capaianTahunanKeuangan = kegiatanItem.capaianKeuangan;
        }
      }
    }
  });
  
  // Calculate realisasi for program (average fisik, sum keuangan from kegiatan)
  programIndices.forEach((programIndex, programId) => {
    const programItem = data[programIndex];
    const relatedKegiatan = [...kegiatanIndices.entries()]
      .filter(([_, info]) => info.programId === programId)
      .map(([kegiatanId, info]) => info.dataIndex);
    
    if (relatedKegiatan.length > 0) {
      // Calculate average realisasi fisik from kegiatan
      let validKegiatanCount = 0;
      const totalFisik = relatedKegiatan.reduce((sum, index) => {
        const kegiatanItem = data[index];
        if (kegiatanItem._realisasiFisikValue !== undefined) {
          validKegiatanCount++;
          return sum + kegiatanItem._realisasiFisikValue;
        }
        return sum;
      }, 0);
      
      const avgFisik = validKegiatanCount > 0 ? totalFisik / validKegiatanCount : 0;
      
      // Calculate sum realisasi keuangan from kegiatan
      const totalKeuangan = relatedKegiatan.reduce((sum, index) => {
        const kegiatanItem = data[index];
        return sum + (kegiatanItem._realisasiKeuanganValue || 0);
      }, 0);
      
      // Update program values
      if (validKegiatanCount > 0) {
        programItem.realisasiFisik = `${avgFisik.toFixed(2)}%`;
        programItem._realisasiFisikValue = avgFisik;
      }
      
      if (totalKeuangan > 0) {
        programItem.realisasiKeuangan = `Rp ${totalKeuangan.toLocaleString('id-ID')}`;
        programItem._realisasiKeuanganValue = totalKeuangan;
      }
      
      // Calculate capaian
      if (programItem.targetFisik !== '-' && validKegiatanCount > 0) {
        const targetFisik = parseFloat(programItem.targetFisik.replace('%', ''));
        if (!isNaN(targetFisik) && targetFisik > 0) {
          const capaian = (avgFisik / targetFisik) * 100;
          programItem.capaianFisik = `${capaian.toFixed(2)}%`;
          
          // Calculate kinerja tahunan
          const kinerjaFisikTahunan = capaian;
          programItem.capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
        }
      }
      
      if (programItem.targetKeuangan !== '-' && totalKeuangan > 0) {
        const targetKeuangan = parseFloat(programItem.targetKeuangan.replace(/[^0-9.-]+/g, ''));
        if (!isNaN(targetKeuangan) && targetKeuangan > 0) {
          // Use Math.min to cap the value at 100%
          const capaian = Math.min(100, (totalKeuangan / targetKeuangan) * 100);
          programItem.capaianKeuangan = `${capaian.toFixed(2)}%`;
          
          // Calculate keuangan tahunan (same as capaian)
          programItem.capaianTahunanKeuangan = programItem.capaianKeuangan;
        }
      }
      
      // Add program to bidang urusan subItems
      if (bidangUrusanIndex >= 0 && data[bidangUrusanIndex]._subItems) {
        data[bidangUrusanIndex]._subItems.push(programId);
      }
    }
  });
  
  // Calculate realisasi for bidang urusan (sum keuangan from all programs)
  if (bidangUrusanIndex >= 0) {
    const bidangUrusanItem = data[bidangUrusanIndex];
    if (bidangUrusanItem._subItems && bidangUrusanItem._subItems.length > 0) {
      // Calculate average kinerja fisik from all programs
      let validProgramCount = 0;
      const totalProgramFisik = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        if (programItem._realisasiFisikValue !== undefined) {
          validProgramCount++;
          return sum + programItem._realisasiFisikValue;
        }
        return sum;
      }, 0);
      
      const avgProgramFisik = validProgramCount > 0 ? totalProgramFisik / validProgramCount : 0;
      
      // Sum up all program keuangan
      const totalBidangUrusanKeuangan = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        return sum + (programItem._realisasiKeuanganValue || 0);
      }, 0);
      
      // Sum up all program target keuangan
      const totalBidangUrusanTargetKeuangan = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        return sum + (programItem._targetKeuanganValue || 0);
      }, 0);
      
      // Update target keuangan for bidang urusan
      if (totalBidangUrusanTargetKeuangan > 0) {
        bidangUrusanItem.targetKeuangan = `Rp ${totalBidangUrusanTargetKeuangan.toLocaleString('id-ID')}`;
      }
      
      // Update realisasi fisik for bidang urusan
      if (validProgramCount > 0) {
        bidangUrusanItem.realisasiFisik = `${avgProgramFisik.toFixed(2)}%`;
        
        // Calculate average target fisik from all programs
        let validTargetCount = 0;
        const totalTargetFisik = bidangUrusanItem._subItems.reduce((sum, programId) => {
          const programItem = data[programIndices.get(programId) || 0];
          if (programItem.targetFisik !== '-') {
            validTargetCount++;
            const targetValue = parseFloat(programItem.targetFisik.replace('%', ''));
            return sum + (isNaN(targetValue) ? 0 : targetValue);
          }
          return sum;
        }, 0);
        
        const avgTargetFisik = validTargetCount > 0 ? totalTargetFisik / validTargetCount : 0;
        
        // Set target fisik for bidang urusan
        if (avgTargetFisik > 0) {
          bidangUrusanItem.targetFisik = `${avgTargetFisik.toFixed(2)}%`;
          
          // Calculate capaian fisik for bidang urusan
          const capaianFisik = (avgProgramFisik / avgTargetFisik) * 100;
          bidangUrusanItem.capaianFisik = `${capaianFisik.toFixed(2)}%`;
          
          // Calculate kinerja fisik tahunan
          const kinerjaFisikTahunan = capaianFisik;
          bidangUrusanItem.capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
        }
      }
      
      if (totalBidangUrusanKeuangan > 0) {
        bidangUrusanItem.realisasiKeuangan = `Rp ${totalBidangUrusanKeuangan.toLocaleString('id-ID')}`;
        
        // Calculate capaian for bidang urusan
        if (totalBidangUrusanTargetKeuangan > 0) {
          // Use Math.min to cap the value at 100%
          const capaian = Math.min(100, (totalBidangUrusanKeuangan / totalBidangUrusanTargetKeuangan) * 100);
          bidangUrusanItem.capaianKeuangan = `${capaian.toFixed(2)}%`;
          
          // Calculate keuangan tahunan (same as capaian)
          bidangUrusanItem.capaianTahunanKeuangan = bidangUrusanItem.capaianKeuangan;
        }
      }
    }
  }
  
  console.log('Final program data:', data);
  
  // Format percentages before returning data
  return data.map(item => {
    if (item.capaianKeuangan !== '-') {
      item.capaianKeuangan = formatPercentage(item.capaianKeuangan);
    }
    if (item.capaianTahunanKeuangan !== '-') {
      item.capaianTahunanKeuangan = formatPercentage(item.capaianTahunanKeuangan);
    }
    return item;
  });
});

// Track edited items
const editedItems = ref<Record<number, {
  realisasiFisik: string;
  realisasiKeuangan: string;
  capaianFisik: string;
  capaianKeuangan: string;
  keterangan: string;
  pptk: string;
}>>({});

// Function to handle input changes
const handleInputChange = (id: number, field: 'realisasiFisik' | 'realisasiKeuangan' | 'capaianFisik' | 'capaianKeuangan' | 'keterangan' | 'pptk', value: string) => {
  if (!editedItems.value[id]) {
    // Cek jika sudah ada data realisasi untuk item ini
    const existingData = programData.value.find(item => item.id === id);
    editedItems.value[id] = {
      realisasiFisik: existingData?.realisasiFisik || '-',
      realisasiKeuangan: existingData?.realisasiKeuangan || '-',
      capaianFisik: existingData?.capaianFisik || '-',
      capaianKeuangan: existingData?.capaianKeuangan || '-',
      keterangan: existingData?.keterangan || '-',
      pptk: existingData?.pptk || '-'
    };
  }
  
  // Special handling for numerical fields
  if (field === 'realisasiKeuangan') {
    // Just store the raw value without immediate formatting
    // This allows users to type the full number without interference
    editedItems.value[id][field] = value;
  } else {
    editedItems.value[id][field] = value;
  }
  
  // Auto-calculate capaian when realisasi is updated
  if (field === 'realisasiFisik' || field === 'realisasiKeuangan') {
    const subkegiatan = programData.value.find(p => p.id === id);
    if (subkegiatan) {
      if (field === 'realisasiFisik' && subkegiatan.targetFisik !== '-') {
        const targetValue = parseFloat(subkegiatan.targetFisik.replace('%', ''));
        const realisasiValue = parseFloat(value.replace('%', ''));
        if (!isNaN(targetValue) && !isNaN(realisasiValue) && targetValue > 0) {
          const capaian = (realisasiValue / targetValue) * 100;
          editedItems.value[id].capaianFisik = formatPercentage(capaian);
        }
      }
      
      if (field === 'realisasiKeuangan' && subkegiatan.targetKeuangan !== '-') {
        const targetValue = parseFloat(subkegiatan.targetKeuangan.replace(/[^0-9.-]+/g, ''));
        const realisasiValue = parseFloat(value.replace(/[^0-9.-]+/g, ''));
        if (!isNaN(targetValue) && !isNaN(realisasiValue) && targetValue > 0) {
          // Use Math.min to cap the value at 100%
          const capaian = Math.min(100, (realisasiValue / targetValue) * 100);
          editedItems.value[id].capaianKeuangan = formatPercentage(capaian);
        }
      }
    }
  }
};

// Helper function for formatting currency
const formatCurrency = (value: string): string => {
  if (!value) return "Rp 0";
  // Remove non-numeric characters except digits and decimal points
  const numericValue = value.replace(/[^0-9.]/g, '');
  // Only format if it's a valid number
  if (numericValue && !isNaN(parseFloat(numericValue))) {
    return `Rp ${parseFloat(numericValue).toLocaleString('id-ID')}`;
  }
  return value;
}

// Function to save data
const saveData = (id: number) => {
  const item = editedItems.value[id];
  if (!item) return;

  // Calculate capaian manually if not entered
  if (item.capaianFisik === '-' && item.realisasiFisik !== '-') {
    // Find the target value for this subkegiatan
    const subkegiatan = programData.value.find(p => p.id === id);
    if (subkegiatan && subkegiatan.targetFisik !== '-') {
      const targetValue = parseFloat(subkegiatan.targetFisik.replace('%', ''));
      const realisasiValue = parseFloat(item.realisasiFisik.replace('%', ''));
      if (!isNaN(targetValue) && !isNaN(realisasiValue) && targetValue > 0) {
        const capaian = (realisasiValue / targetValue) * 100;
        item.capaianFisik = formatPercentage(capaian);
      }
    }
  }

  // Calculate capaian keuangan similarly
  if (item.capaianKeuangan === '-' && item.realisasiKeuangan !== '-') {
    const subkegiatan = programData.value.find(p => p.id === id);
    if (subkegiatan && subkegiatan.targetKeuangan !== '-') {
      const targetValue = parseFloat(subkegiatan.targetKeuangan.replace(/[^0-9.-]+/g, ''));
      const realisasiValue = parseFloat(item.realisasiKeuangan.replace(/[^0-9.-]+/g, ''));
      if (!isNaN(targetValue) && !isNaN(realisasiValue) && targetValue > 0) {
        // Use Math.min to cap the value at 100%
        const capaian = Math.min(100, (realisasiValue / targetValue) * 100);
        item.capaianKeuangan = formatPercentage(capaian);
      }
    }
  }

  // Make sure we're sending clean numeric values to the server
  const cleanRealisasiKeuangan = item.realisasiKeuangan.replace(/[^0-9.-]+/g, '');
  const cleanNumber = isNaN(parseFloat(cleanRealisasiKeuangan)) ? 0 : parseFloat(cleanRealisasiKeuangan);

  // Send data to server
  router.post('/triwulan1/save-realisasi', {
    id: id,
    realisasi_fisik: item.realisasiFisik.replace('%', ''),
    realisasi_keuangan: cleanNumber, // Ensure this is a number, not a string with formatting
    capaian_fisik: item.capaianFisik.replace('%', ''),
    capaian_keuangan: item.capaianKeuangan.replace('%', ''),
    keterangan: item.keterangan,
    nama_pptk: item.pptk
  }, {
    onSuccess: () => {
      alert('Data berhasil disimpan');
      // Tambahkan atau perbarui data realisasi di properti monitoringRealisasi setelah berhasil disimpan
      const existingIndex = props.monitoringRealisasi.findIndex(r => r.task_id === id);
      
      if (existingIndex !== -1) {
        // Update existing realisasi data
        props.monitoringRealisasi[existingIndex].kinerja_fisik = parseFloat(item.realisasiFisik.replace('%', ''));
        props.monitoringRealisasi[existingIndex].keuangan = cleanNumber; // Use the clean number here too
      } else {
        // Add new realisasi data
        // Cari monitoring ID dari subkegiatan
        const skpdTugas = props.subkegiatanTugas.find(sk => sk.id === id);
        const monitoringId = skpdTugas?.monitoring?.[0]?.id || 0;
        
        props.monitoringRealisasi.push({
          id: Date.now(), // Temporary ID until reload
          kinerja_fisik: parseFloat(item.realisasiFisik.replace('%', '')),
          keuangan: cleanNumber, // Use the clean number here too
          periode: 'Triwulan 1',
          monitoring_id: monitoringId,
          task_id: id,
          monitoring_anggaran_id: 0, // This will be set on the server
          deskripsi: item.keterangan,
          nama_pptk: item.pptk
        });
      }
      
      // Update the item in programData to show the new values
      const itemIndex = programData.value.findIndex(p => p.id === id);
      if (itemIndex !== -1) {
        programData.value[itemIndex].realisasiKeuangan = formatCurrency(item.realisasiKeuangan);
        programData.value[itemIndex].capaianKeuangan = formatPercentage(item.capaianKeuangan);
        programData.value[itemIndex].capaianTahunanKeuangan = formatPercentage(item.capaianKeuangan); // Same as capaian for now
        programData.value[itemIndex].keterangan = item.keterangan;
        programData.value[itemIndex].pptk = item.pptk;
        
        // Also update the base data in monitoringTargets or monitoringRealisasi
        const targetIndex = props.monitoringTargets.findIndex(t => t.task_id === id);
        if (targetIndex !== -1) {
          props.monitoringTargets[targetIndex].deskripsi = item.keterangan;
          props.monitoringTargets[targetIndex].nama_pptk = item.pptk;
        }
        
        // Update realisasi if it exists
        const realisasiIndex = props.monitoringRealisasi.findIndex(r => r.task_id === id);
        if (realisasiIndex !== -1) {
          props.monitoringRealisasi[realisasiIndex].deskripsi = item.keterangan;
          props.monitoringRealisasi[realisasiIndex].nama_pptk = item.pptk;
        }
      }
      
      // Clear edited item
      delete editedItems.value[id];
    },
    onError: (errors) => {
      console.error('Error saving data:', errors);
      alert('Terjadi kesalahan saat menyimpan data: ' + Object.values(errors).join(', '));
    }
  });
};
</script>

<template>
    <Head title="Monitoring Triwulan 1" />

    <AppLayout :breadcrumbs="breadcrumbs">
 <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section -->

            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-600">Triwulan1</h2>
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
                </div>
            </div>


            <!-- Program table with targets -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-600">Data Monitoring Triwulan 1</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">Kode</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center min-w-[180px] w-[180px]">BIDANG URUSAN & PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                                <th colspan="8" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">Triwulan 1</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center min-w-[180px] w-[180px]">Keterangan</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center min-w-[180px] w-[180px]">PPTK</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">Aksi</th>
                            </tr>
                            <tr>
                                <th colspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">TARGET</th>
                                <th colspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">REALISASI</th>
                                <th colspan="4" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">CAPAIAN</th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KINERJA FISIK (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KEUANGAN (RP)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KINERJA FISIK (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KEUANGAN (RP)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KINERJA FISIK (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KEUANGAN (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KINERJA TAHUNAN (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KEUANGAN TAHUNAN (%)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="programData.length === 0">
                                <td colspan="13" class="px-4 py-4 text-center text-gray-500">
                                    Belum ada data tersedia
                                </td>
                            </tr>
                            <tr v-for="(item, index) in programData" :key="item.id" 
                                :class="[
                                    'hover:bg-blue-50 transition-colors',
                                    index % 2 === 0 ? 'bg-white' : 'bg-gray-50',
                                    item.type === 'bidang_urusan' ? 'font-extrabold bg-blue-50' : '',
                                    item.type === 'program' ? 'font-bold bg-gray-100' : '',
                                ]">
                                <td class="px-3 py-2 text-sm">{{ item.kode }}</td>
                                <td class="px-3 py-2 text-sm" >{{ item.program }}</td>
                                <td class="px-3 py-2 text-center text-sm">{{ item.targetFisik }}</td>
                                <td class="px-3 py-2 text-right text-sm">{{ item.targetKeuangan }}</td>
                                
                                <!-- Realisasi columns with inputs for subkegiatan -->
                                <td class="px-3 py-2 text-center">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                                            :class="{ 'bg-gray-50 hover:bg-blue-50': true }"
                                            :value="editedItems[item.id]?.realisasiFisik || item.realisasiFisik"
                                            @input="(e: Event) => handleInputChange(item.id, 'realisasiFisik', (e.target as HTMLInputElement).value)"
                                            placeholder="0.00%"
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiFisik }}</span>
                                    </template>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                                            :class="{ 'bg-gray-50 hover:bg-blue-50': true }"
                                            :value="editedItems[item.id]?.realisasiKeuangan || item.realisasiKeuangan"
                                            @input="(e: Event) => handleInputChange(item.id, 'realisasiKeuangan', (e.target as HTMLInputElement).value)"
                                            placeholder="Rp 0"
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiKeuangan }}</span>
                                    </template>
                                </td>
                                
                                <!-- Capaian columns with inputs for subkegiatan -->
                                <td class="px-3 py-2 text-center">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                                            :class="{ 'bg-gray-50 hover:bg-blue-50': true }"
                                            :value="editedItems[item.id]?.capaianFisik || item.capaianFisik"
                                            @input="(e: Event) => handleInputChange(item.id, 'capaianFisik', (e.target as HTMLInputElement).value)"
                                            placeholder="0.00%"
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.capaianFisik }}</span>
                                    </template>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-24 h-8 border border-gray-300 rounded px-2 py-1 text-right text-xs"
                                            :class="{ 'bg-gray-50 hover:bg-blue-50': true }"
                                            :value="editedItems[item.id]?.capaianKeuangan || item.capaianKeuangan"
                                            @input="(e: Event) => handleInputChange(item.id, 'capaianKeuangan', (e.target as HTMLInputElement).value)"
                                            placeholder="0.00%"
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.capaianKeuangan }}</span>
                                    </template>
                                </td>
                                
                                <td class="px-3 py-2 text-center text-sm">{{ item.capaianTahunanFisik }}</td>
                                <td class="px-3 py-2 text-right text-sm">{{ item.capaianTahunanKeuangan }}</td>
                                <td class="px-3 py-2">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-full h-8 border border-gray-300 rounded px-2 py-1 text-xs"
                                            :class="{ 'bg-gray-50 hover:bg-blue-50': true }"
                                            :value="editedItems[item.id]?.keterangan || item.keterangan"
                                            @input="(e: Event) => handleInputChange(item.id, 'keterangan', (e.target as HTMLInputElement).value)"
                                            placeholder="Keterangan"
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.keterangan }}</span>
                                    </template>
                                </td>
                                <td class="px-3 py-2 min-w-[180px] w-[180px]">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-full h-8 border border-gray-300 rounded px-2 py-1 text-xs"
                                            :class="{ 'bg-gray-50 hover:bg-blue-50': true }"
                                            :value="editedItems[item.id]?.pptk || item.pptk"
                                            @input="(e: Event) => handleInputChange(item.id, 'pptk', (e.target as HTMLInputElement).value)"
                                            placeholder="Nama PPTK"
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.pptk }}</span>
                                    </template>
                                </td>
                                
                                <!-- Actions column -->
                                <td class="px-3 py-2 text-center">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <button 
                                            class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors"
                                            @click="saveData(item.id)"
                                        >
                                            Simpan
                                        </button>
                                    </template>
                                    <template v-else>
                                        -
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

<style scoped>
/* Removing the old styles and adding new ones based on Sumberdana.vue */
/* Table responsive */
@media (max-width: 768px) {
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }
}

/* Input styling */
input[type="number"] {
  -moz-appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Button styling */
button {
  transition: all 0.2s ease-in-out;
}

/* Hover effect */
.hover\:bg-blue-50:hover {
  transition: background-color 0.2s ease-in-out;
}
</style>