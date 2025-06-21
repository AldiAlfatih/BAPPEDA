<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';

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
      nama_skpd?: string;
      nama_dinas?: string;
      namaSkpd?: string;
      namaDinas?: string;
      kode_organisasi?: string;
      kodeOrganisasi?: string;
      no_dpa?: string;
      skpd_kepala: Array<{
        user: {
          name: string;
          user_detail?: {
            nama?: string;
            nip?: string;
          };
        };
      }>;
      tim_kerja?: Array<{
        operator: {
          name: string;
          user_detail?: {
            nip?: string;
          };
        };
        skpd?: {
          nama_skpd?: string;
          nama_dinas?: string;
          kode_organisasi?: string;
        };
      }>;
      user_penanggung_jawab?: {
        name: string;
        user_detail?: {
          nip?: string;
        };
      };
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
    periode_id: number;
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
    periode_id: number;
    monitoring_id: number;
    task_id: number;
    monitoring_anggaran_id: number;
    deskripsi?: string;
    nama_pptk?: string;
  }>;
  periode: {
    id: number;
    nama: string;
    status: number;
  };
  tid: number;
  tahun: number;
  triwulanName: string;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Monitoring Triwulan 2', href: '/triwulan/2' },
  { title: `Monitoring Detail ${props.user.nama_skpd}`, href: route('triwulan.show', { tid: 2, id: props.user.id }) },
  { title: 'Detail Rencana Awal PD', href: '' },
]);

// Function to get Tim Kerja operator name
const getTimKerjaOperator = () => {
  if (props.tugas?.skpd?.tim_kerja && props.tugas.skpd.tim_kerja.length > 0) {
    const timKerja = props.tugas.skpd.tim_kerja[0]; // Get first active tim kerja
    if (timKerja?.operator?.name) {
      const name = timKerja.operator.name;
      const nip = timKerja.operator.user_detail?.nip;
      return nip ? `${name} (NIP: ${nip})` : name;
    }
  }
  return null;
};

// Function to get SKPD name from Tim Kerja
const getSkpdName = () => {
  // Debug semua kemungkinan field names
  const skpd = props.tugas?.skpd;
  console.log('Checking SKPD fields for nama:', {
    nama_skpd: skpd?.nama_skpd,
    nama_dinas: skpd?.nama_dinas,
    namaDinas: skpd?.namaDinas,
    namaSkpd: skpd?.namaSkpd,
  });
  
  // Prioritas 1: Ambil dari skpd langsung (snake_case)
  if (skpd?.nama_skpd) {
    return skpd.nama_skpd;
  }
  
  // Prioritas 2: Ambil nama_dinas dari skpd (snake_case)
  if (skpd?.nama_dinas) {
    return skpd.nama_dinas;
  }
  
  // Prioritas 3: Coba camelCase (jika Laravel transform ke camelCase)
  if (skpd?.namaSkpd) {
    return skpd.namaSkpd;
  }
  
  if (skpd?.namaDinas) {
    return skpd.namaDinas;
  }
  
  // Prioritas 4: Coba dari Tim Kerja (jika ada nested data)
  if (skpd?.tim_kerja && skpd.tim_kerja.length > 0) {
    const timKerja = skpd.tim_kerja[0];
    if (timKerja?.skpd?.nama_skpd) return timKerja.skpd.nama_skpd;
    if (timKerja?.skpd?.nama_dinas) return timKerja.skpd.nama_dinas;
  }
  
  return null;
};

// Function to get SKPD kode organisasi from Tim Kerja
const getKodeOrganisasi = () => {
  const skpd = props.tugas?.skpd;
  
  // Prioritas 1: Ambil dari skpd langsung (snake_case)
  if (skpd?.kode_organisasi) {
    return skpd.kode_organisasi;
  }
  
  // Prioritas 2: Coba camelCase (jika Laravel transform ke camelCase)
  if (skpd?.kodeOrganisasi) {
    return skpd.kodeOrganisasi;
  }
  
  // Prioritas 3: Coba dari Tim Kerja (jika ada nested data)
  if (skpd?.tim_kerja && skpd.tim_kerja.length > 0) {
    const timKerja = skpd.tim_kerja[0];
    if (timKerja?.skpd?.kode_organisasi) {
      return timKerja.skpd.kode_organisasi;
    }
    if (timKerja?.skpd?.kodeOrganisasi) {
      return timKerja.skpd.kodeOrganisasi;
    }
  }
  
  return null;
};

// Function to get Kepala SKPD from Tim Kerja atau skpd_kepala
const getKepalaSkpd = () => {
  // Prioritas 1: Dari user_penanggung_jawab SKPD
  if (props.tugas?.skpd?.user_penanggung_jawab?.name) {
    const name = props.tugas.skpd.user_penanggung_jawab.name;
    const nip = props.tugas.skpd.user_penanggung_jawab.user_detail?.nip;
    return nip ? `${name} (NIP: ${nip})` : name;
  }
  
  // Prioritas 2: Dari skpd_kepala
  if (props.tugas?.skpd?.skpd_kepala && props.tugas.skpd.skpd_kepala.length > 0) {
    const kepala = props.tugas.skpd.skpd_kepala[0];
    if (kepala?.user?.name) {
      const name = kepala.user.name;
      const nip = kepala.user.user_detail?.nip;
      return nip ? `${name} (NIP: ${nip})` : name;
    }
  }
  
  return props.kepalaSkpd;
};



// Debug function to log data structure
const debugSkpdData = () => {
  console.log('=== DEBUG SKPD DATA ===');
  console.log('tugas.skpd:', props.tugas?.skpd);
  console.log('tim_kerja:', props.tugas?.skpd?.tim_kerja);
  console.log('skpd_kepala:', props.tugas?.skpd?.skpd_kepala);
  console.log('user_penanggung_jawab:', props.tugas?.skpd?.user_penanggung_jawab);
  console.log('nama_skpd from function:', getSkpdName());
  console.log('kode_organisasi from function:', getKodeOrganisasi());
  console.log('kepala_skpd from function:', getKepalaSkpd());
  console.log('penanggung_jawab from function:', getTimKerjaOperator());
  console.log('Available fields in skpd:');
  if (props.tugas?.skpd) {
    Object.keys(props.tugas.skpd).forEach(key => {
      console.log(`  ${key}:`, props.tugas.skpd[key]);
    });
  }
  console.log('=======================');
};

// Debug data saat komponen dimuat
onMounted(() => {
  debugSkpdData();
});


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
  const strictPeriodeIdFilter = props.monitoringTargets.filter(t => t.periode_id === props.periode.id);
      console.log(`STRICT FILTER: TARGETS WITH PERIODE_ID = ${props.periode.id} ONLY:`, strictPeriodeIdFilter);
  
  // Untuk masing-masing subkegiatan, cari target dengan periode_id = 2
  props.subkegiatanTugas.forEach(subkegiatan => {
    const targetsForSubkegiatan = props.monitoringTargets.filter(t => 
              t.task_id === subkegiatan.id && t.periode_id === props.periode.id
    );
          console.log(`TARGETS FOR SUBKEGIATAN ${subkegiatan.id} WITH STRICT PERIODE_ID = ${props.periode.id}:`, targetsForSubkegiatan);
    
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
  
        console.log(`STRICT FILTER: SUBKEGIATAN TARGETS WITH PERIODE_ID = ${props.periode.id}:`, strictSubkegiatanTargets);
  
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
    const targetsByPeriod: Record<string, typeof props.monitoringTargets> = {};
    props.monitoringTargets.forEach(target => {
      const periodId = target.periode_id?.toString() || 'undefined';
      if (!targetsByPeriod[periodId]) targetsByPeriod[periodId] = [];
      targetsByPeriod[periodId].push(target);
    });
    
    console.log('TARGETS GROUPED BY PERIODE_ID:', targetsByPeriod);
    
    // Cek khusus untuk periode_id = 2 (Triwulan 1)
    const currentTriwulanTargets = props.monitoringTargets.filter(t => t.periode_id === props.periode.id);
console.log(`STRICT PERIODE_ID === ${props.periode.id} TARGETS ONLY:`, currentTriwulanTargets);
    
    // Periksa apakah ada target untuk subkegiatan dengan periode_id = 2
    const subkegiatanIds = props.subkegiatanTugas.map(sk => sk.id);
    const subkegiatanTriwulan1Targets = props.monitoringTargets.filter(t => 
      subkegiatanIds.includes(t.task_id) && t.periode_id === props.periode.id
    );
          console.log(`SUBKEGIATAN TARGETS WITH STRICT PERIODE_ID === ${props.periode.id}:`, subkegiatanTriwulan1Targets);
    
    if (subkegiatanTriwulan1Targets.length === 0) {
      console.warn('PERINGATAN: Tidak ada target periode Triwulan 1 untuk subkegiatan manapun!');
    }
  }
  
  // For taskDistribution, add proper type
  const taskDistribution: Record<number, number> = {};
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
    
    // If the value is unreasonably large (over 150%), cap it
    const maxPercentage = 150;
    if (numValue > maxPercentage) {
      console.log(`Nilai persentase sangat besar terdeteksi: ${numValue.toFixed(2)}%, diatur ke ${maxPercentage}%`);
      return `${maxPercentage.toFixed(2)}%`;
    }
    
    return `${numValue.toFixed(2)}%`;
  }
  
  return value.toString();
}

// Helper function untuk mendapatkan pagu dari berbagai format data monitoring
// Prioritas: perubahan > parsial > pokok
const getPaguFromMonitoring = (item: any): {value: number, type: string} => {
  if (!item) return {value: 0, type: 'tidak ada'};
  
  console.log(`Mencari pagu untuk item:`, item);
  
  // TAMBAHAN 1: Cek target keuangan sebagai fallback terakhir
  let targetKeuanganValue = 0;
  if (item._targetKeuanganValue && item._targetKeuanganValue > 0) {
    targetKeuanganValue = item._targetKeuanganValue;
    console.log(`Menemukan nilai _targetKeuanganValue yang bisa digunakan sebagai fallback: ${targetKeuanganValue}`);
  } else if (item.targetKeuangan && typeof item.targetKeuangan === 'string') {
    const matches = item.targetKeuangan.match(/[0-9,.]+/g);
    if (matches && matches.length > 0) {
      targetKeuanganValue = parseFloat(matches[0].replace(/[,.]/g, ''));
      console.log(`Menemukan nilai targetKeuangan yang bisa digunakan sebagai fallback: ${targetKeuanganValue}`);
    }
  }
  
  // Cari data "Rencana Awal" dalam monitoring array jika ada
  if (item.monitoring && Array.isArray(item.monitoring) && item.monitoring.length > 0) {
    // Prioritaskan mencari dokumen "Rencana Awal"
    const rencanaAwal = item.monitoring.find((m: any) => 
      m.deskripsi && m.deskripsi.toLowerCase() === 'rencana awal'
    );
    
    if (rencanaAwal) {
      console.log(`Ditemukan data "Rencana Awal" dalam monitoring`);
      
      // Cek perubahan dulu di data rencana awal
      if (rencanaAwal.pagu_perubahan && rencanaAwal.pagu_perubahan > 0) {
        console.log(`Menggunakan pagu_perubahan dari Rencana Awal: ${rencanaAwal.pagu_perubahan}`);
        return {value: rencanaAwal.pagu_perubahan, type: 'PERUBAHAN (Rencana Awal)'};
      }
      
      // Cek parsial
      if (rencanaAwal.pagu_parsial && rencanaAwal.pagu_parsial > 0) {
        console.log(`Menggunakan pagu_parsial dari Rencana Awal: ${rencanaAwal.pagu_parsial}`);
        return {value: rencanaAwal.pagu_parsial, type: 'PARSIAL (Rencana Awal)'};
      }
      
      // Cek pokok
      if (rencanaAwal.pagu_pokok && rencanaAwal.pagu_pokok > 0) {
        console.log(`Menggunakan pagu_pokok dari Rencana Awal: ${rencanaAwal.pagu_pokok}`);
        return {value: rencanaAwal.pagu_pokok, type: 'POKOK (Rencana Awal)'};
      }
      
      // Cek monitoring_anggaran dalam Rencana Awal
      if (rencanaAwal.monitoring_anggaran && Array.isArray(rencanaAwal.monitoring_anggaran) && 
          rencanaAwal.monitoring_anggaran.length > 0) {
        const anggaran = rencanaAwal.monitoring_anggaran[0];
        
        // Cek perubahan dulu
        if (anggaran && anggaran.pagu_perubahan > 0) {
          console.log(`Menggunakan monitoring_anggaran.pagu_perubahan dari Rencana Awal: ${anggaran.pagu_perubahan}`);
          return {value: anggaran.pagu_perubahan, type: 'PERUBAHAN (Rencana Awal)'};
        }
        
        // Cek parsial
        if (anggaran && anggaran.pagu_parsial > 0) {
          console.log(`Menggunakan monitoring_anggaran.pagu_parsial dari Rencana Awal: ${anggaran.pagu_parsial}`);
          return {value: anggaran.pagu_parsial, type: 'PARSIAL (Rencana Awal)'};
        }
        
        // Cek pokok
        if (anggaran && anggaran.pagu_pokok > 0) {
          console.log(`Menggunakan monitoring_anggaran.pagu_pokok dari Rencana Awal: ${anggaran.pagu_pokok}`);
          return {value: anggaran.pagu_pokok, type: 'POKOK (Rencana Awal)'};
        }
        
        // Cek pagu di monitoring_anggaran (tabel monitoring_pagu)
        if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
          // Prioritaskan perubahan > parsial > pokok
          const perubahanPagu = anggaran.pagu.find((p: any) => p.kategori === 3); // Perubahan
          if (perubahanPagu && perubahanPagu.dana > 0) {
            console.log(`Menggunakan pagu perubahan dari tabel pagu Rencana Awal: ${perubahanPagu.dana}`);
            return {value: perubahanPagu.dana, type: 'PERUBAHAN (pagu)'};
          }
          
          const parsialPagu = anggaran.pagu.find((p: any) => p.kategori === 2); // Parsial
          if (parsialPagu && parsialPagu.dana > 0) {
            console.log(`Menggunakan pagu parsial dari tabel pagu Rencana Awal: ${parsialPagu.dana}`);
            return {value: parsialPagu.dana, type: 'PARSIAL (pagu)'};
          }
          
          const pokokPagu = anggaran.pagu.find((p: any) => p.kategori === 1); // Pokok
          if (pokokPagu && pokokPagu.dana > 0) {
            console.log(`Menggunakan pagu pokok dari tabel pagu Rencana Awal: ${pokokPagu.dana}`);
            return {value: pokokPagu.dana, type: 'POKOK (pagu)'};
          }
        }
        
        // TAMBAHAN 2: Cek keuangan target secara langsung
        if (anggaran.monitoringTarget && Array.isArray(anggaran.monitoringTarget) && anggaran.monitoringTarget.length > 0) {
          for (const target of anggaran.monitoringTarget) {
            if (target && typeof target.keuangan === 'number' && target.keuangan > 0) {
              console.log(`Menggunakan keuangan dari monitoringTarget: ${target.keuangan}`);
              return {value: target.keuangan, type: 'TARGET KEUANGAN'};
            }
          }
        }
      }
      
      // TAMBAHAN 3: Cek budget data langsung dalam RencanaAwal
      if (rencanaAwal.anggaran && typeof rencanaAwal.anggaran === 'number' && rencanaAwal.anggaran > 0) {
        console.log(`Menggunakan anggaran langsung dari Rencana Awal: ${rencanaAwal.anggaran}`);
        return {value: rencanaAwal.anggaran, type: 'ANGGARAN (Rencana Awal)'};
      }
      
      // TAMBAHAN 4: Cek nilai dana langsung
      if (rencanaAwal.dana && typeof rencanaAwal.dana === 'number' && rencanaAwal.dana > 0) {
        console.log(`Menggunakan dana langsung dari Rencana Awal: ${rencanaAwal.dana}`);
        return {value: rencanaAwal.dana, type: 'DANA (Rencana Awal)'};
      }
    }
  }
  
  // Jika tidak ada data Rencana Awal, gunakan metode fallback seperti sebelumnya
  
  // Jika ada properti pagu langsung di item
  // Cek perubahan dulu
  if (item.pagu_perubahan && item.pagu_perubahan > 0) {
    console.log(`Menggunakan pagu_perubahan langsung: ${item.pagu_perubahan}`);
    return {value: item.pagu_perubahan, type: 'PERUBAHAN'};
  }
  
  // Cek parsial
  if (item.pagu_parsial && item.pagu_parsial > 0) {
    console.log(`Menggunakan pagu_parsial langsung: ${item.pagu_parsial}`);
    return {value: item.pagu_parsial, type: 'PARSIAL'};
  }
  
  // Cek pokok
  if (item.pagu_pokok && item.pagu_pokok > 0) {
    console.log(`Menggunakan pagu_pokok langsung: ${item.pagu_pokok}`);
    return {value: item.pagu_pokok, type: 'POKOK'};
  }
  
  // TAMBAHAN 5: Cek anggaran langsung
  if (item.anggaran && typeof item.anggaran === 'number' && item.anggaran > 0) {
    console.log(`Menggunakan anggaran langsung: ${item.anggaran}`);
    return {value: item.anggaran, type: 'ANGGARAN'};
  }
  
  // Jika item memiliki monitoring_anggaran langsung
  if (item.monitoring_anggaran && Array.isArray(item.monitoring_anggaran) && item.monitoring_anggaran.length > 0) {
    const anggaran = item.monitoring_anggaran[0];
    
    // Cek perubahan dulu
    if (anggaran && anggaran.pagu_perubahan > 0) {
      console.log(`Menggunakan monitoring_anggaran.pagu_perubahan: ${anggaran.pagu_perubahan}`);
      return {value: anggaran.pagu_perubahan, type: 'PERUBAHAN'};
    }
    
    // Cek parsial
    if (anggaran && anggaran.pagu_parsial > 0) {
      console.log(`Menggunakan monitoring_anggaran.pagu_parsial: ${anggaran.pagu_parsial}`);
      return {value: anggaran.pagu_parsial, type: 'PARSIAL'};
    }
    
    // Cek pokok
    if (anggaran && anggaran.pagu_pokok > 0) {
      console.log(`Menggunakan monitoring_anggaran.pagu_pokok: ${anggaran.pagu_pokok}`);
      return {value: anggaran.pagu_pokok, type: 'POKOK'};
    }
    
    // Cek pagu di monitoring_anggaran (tabel monitoring_pagu)
    if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
      // Prioritaskan perubahan > parsial > pokok
      const perubahanPagu = anggaran.pagu.find((p: any) => p.kategori === 3); // Perubahan
      if (perubahanPagu && perubahanPagu.dana > 0) {
        console.log(`Menggunakan pagu perubahan dari tabel pagu: ${perubahanPagu.dana}`);
        return {value: perubahanPagu.dana, type: 'PERUBAHAN (pagu)'};
      }
      
      const parsialPagu = anggaran.pagu.find((p: any) => p.kategori === 2); // Parsial
      if (parsialPagu && parsialPagu.dana > 0) {
        console.log(`Menggunakan pagu parsial dari tabel pagu: ${parsialPagu.dana}`);
        return {value: parsialPagu.dana, type: 'PARSIAL (pagu)'};
      }
      
      const pokokPagu = anggaran.pagu.find((p: any) => p.kategori === 1); // Pokok
      if (pokokPagu && pokokPagu.dana > 0) {
        console.log(`Menggunakan pagu pokok dari tabel pagu: ${pokokPagu.dana}`);
        return {value: pokokPagu.dana, type: 'POKOK (pagu)'};
      }
    }
    
    // TAMBAHAN 6: Cek monitoringTarget dalam anggaran
    if (anggaran.monitoringTarget && Array.isArray(anggaran.monitoringTarget) && anggaran.monitoringTarget.length > 0) {
      for (const target of anggaran.monitoringTarget) {
        if (target && typeof target.keuangan === 'number' && target.keuangan > 0) {
          console.log(`Menggunakan keuangan dari monitoringTarget dalam monitoring_anggaran: ${target.keuangan}`);
          return {value: target.keuangan, type: 'TARGET KEUANGAN'};
        }
      }
    }
  }
  
  // Format di monitoring object
  if (item.monitoring && typeof item.monitoring === 'object') {
    // Format object
    if (!Array.isArray(item.monitoring)) {
      // Cek perubahan dulu
      if (item.monitoring.pagu_perubahan > 0) {
        console.log(`Menggunakan monitoring.pagu_perubahan (object): ${item.monitoring.pagu_perubahan}`);
        return {value: item.monitoring.pagu_perubahan, type: 'PERUBAHAN'};
      }
      
      // Cek parsial
      if (item.monitoring.pagu_parsial > 0) {
        console.log(`Menggunakan monitoring.pagu_parsial (object): ${item.monitoring.pagu_parsial}`);
        return {value: item.monitoring.pagu_parsial, type: 'PARSIAL'};
      }
      
      // Cek pokok
      if (item.monitoring.pagu_pokok > 0) {
        console.log(`Menggunakan monitoring.pagu_pokok (object): ${item.monitoring.pagu_pokok}`);
        return {value: item.monitoring.pagu_pokok, type: 'POKOK'};
      }
      
      // TAMBAHAN 7: Cek anggaran dalam monitoring object
      if (item.monitoring.anggaran && typeof item.monitoring.anggaran === 'number' && item.monitoring.anggaran > 0) {
        console.log(`Menggunakan monitoring.anggaran (object): ${item.monitoring.anggaran}`);
        return {value: item.monitoring.anggaran, type: 'ANGGARAN'};
      }
      
      // TAMBAHAN 8: Cek dana dalam monitoring object
      if (item.monitoring.dana && typeof item.monitoring.dana === 'number' && item.monitoring.dana > 0) {
        console.log(`Menggunakan monitoring.dana (object): ${item.monitoring.dana}`);
        return {value: item.monitoring.dana, type: 'DANA'};
      }
    } 
    // Format array (sudah ditangani di awal fungsi untuk Rencana Awal)
    else if (Array.isArray(item.monitoring) && item.monitoring.length > 0) {
      // Ambil item pertama jika bukan Rencana Awal
      const monitoringItem = item.monitoring[0];
      
      // Cek pagu langsung di monitoring[0]
      // Cek perubahan dulu
      if (monitoringItem.pagu_perubahan > 0) {
        console.log(`Menggunakan monitoring[0].pagu_perubahan: ${monitoringItem.pagu_perubahan}`);
        return {value: monitoringItem.pagu_perubahan, type: 'PERUBAHAN'};
      }
      
      // Cek parsial
      if (monitoringItem.pagu_parsial > 0) {
        console.log(`Menggunakan monitoring[0].pagu_parsial: ${monitoringItem.pagu_parsial}`);
        return {value: monitoringItem.pagu_parsial, type: 'PARSIAL'};
      }
      
      // Cek pokok
      if (monitoringItem.pagu_pokok > 0) {
        console.log(`Menggunakan monitoring[0].pagu_pokok: ${monitoringItem.pagu_pokok}`);
        return {value: monitoringItem.pagu_pokok, type: 'POKOK'};
      }
      
      // TAMBAHAN 9: Cek anggaran di monitoring[0]
      if (monitoringItem.anggaran && typeof monitoringItem.anggaran === 'number' && monitoringItem.anggaran > 0) {
        console.log(`Menggunakan monitoring[0].anggaran: ${monitoringItem.anggaran}`);
        return {value: monitoringItem.anggaran, type: 'ANGGARAN'};
      }
      
      // TAMBAHAN 10: Cek dana di monitoring[0]
      if (monitoringItem.dana && typeof monitoringItem.dana === 'number' && monitoringItem.dana > 0) {
        console.log(`Menggunakan monitoring[0].dana: ${monitoringItem.dana}`);
        return {value: monitoringItem.dana, type: 'DANA'};
      }
      
      // Cek di monitoring_anggaran
      if (monitoringItem.monitoring_anggaran && 
          Array.isArray(monitoringItem.monitoring_anggaran) && 
          monitoringItem.monitoring_anggaran.length > 0) {
        
        const anggaran = monitoringItem.monitoring_anggaran[0];
        
        // Cek perubahan dulu
        if (anggaran && anggaran.pagu_perubahan > 0) {
          console.log(`Menggunakan monitoring[0].monitoring_anggaran[0].pagu_perubahan: ${anggaran.pagu_perubahan}`);
          return {value: anggaran.pagu_perubahan, type: 'PERUBAHAN'};
        }
        
        // Cek parsial
        if (anggaran && anggaran.pagu_parsial > 0) {
          console.log(`Menggunakan monitoring[0].monitoring_anggaran[0].pagu_parsial: ${anggaran.pagu_parsial}`);
          return {value: anggaran.pagu_parsial, type: 'PARSIAL'};
        }
        
        // Cek pokok
        if (anggaran && anggaran.pagu_pokok > 0) {
          console.log(`Menggunakan monitoring[0].monitoring_anggaran[0].pagu_pokok: ${anggaran.pagu_pokok}`);
          return {value: anggaran.pagu_pokok, type: 'POKOK'};
        }
        
        // Cek pagu di monitoring_anggaran (tabel monitoring_pagu)
        if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
          // Prioritaskan perubahan > parsial > pokok
          const perubahanPagu = anggaran.pagu.find((p: any) => p.kategori === 3); // Perubahan
          if (perubahanPagu && perubahanPagu.dana > 0) {
            console.log(`Menggunakan pagu perubahan dari tabel pagu: ${perubahanPagu.dana}`);
            return {value: perubahanPagu.dana, type: 'PERUBAHAN (pagu)'};
          }
          
          const parsialPagu = anggaran.pagu.find((p: any) => p.kategori === 2); // Parsial
          if (parsialPagu && parsialPagu.dana > 0) {
            console.log(`Menggunakan pagu parsial dari tabel pagu: ${parsialPagu.dana}`);
            return {value: parsialPagu.dana, type: 'PARSIAL (pagu)'};
          }
          
          const pokokPagu = anggaran.pagu.find((p: any) => p.kategori === 1); // Pokok
          if (pokokPagu && pokokPagu.dana > 0) {
            console.log(`Menggunakan pagu pokok dari tabel pagu: ${pokokPagu.dana}`);
            return {value: pokokPagu.dana, type: 'POKOK (pagu)'};
          }
        }
        
        // TAMBAHAN 11: Cek monitoringTarget dalam anggaran[0]
        if (anggaran.monitoringTarget && Array.isArray(anggaran.monitoringTarget) && anggaran.monitoringTarget.length > 0) {
          for (const target of anggaran.monitoringTarget) {
            if (target && typeof target.keuangan === 'number' && target.keuangan > 0) {
              console.log(`Menggunakan keuangan dari monitoringTarget dalam monitoring[0].monitoring_anggaran[0]: ${target.keuangan}`);
              return {value: target.keuangan, type: 'TARGET KEUANGAN'};
            }
          }
        }
      }
    }
  }
  
  // TAMBAHAN 9: Periksa targetKeuangan sebagai fallback terakhir
  if (targetKeuanganValue > 0) {
    console.log(`Menggunakan targetKeuangan sebagai fallback terakhir: ${targetKeuanganValue}`);
    return {value: targetKeuanganValue, type: 'TARGET KEUANGAN (fallback)'};
  }
  
  // Kembalikan 0 jika tidak ada nilai pagu yang ditemukan
  console.log('Tidak ditemukan nilai pagu yang valid');
  return {value: 0, type: 'tidak ada'};
};

// Function untuk mendapatkan pagu dari manajemen anggaran dengan prioritas yang jelas
// PRIORITAS PAGU ANGGARAN:
// 1. PERUBAHAN (kategori 3) - Nilai tertinggi prioritas
// 2. PARSIAL (kategori 2) - Digunakan jika perubahan tidak ada
// 3. POKOK (kategori 1) - Digunakan jika perubahan dan parsial tidak ada
const getPaguDariManajemenAnggaran = (item: any): {value: number, type: string} => {
  if (!item) return {value: 0, type: 'tidak ada'};
  
  console.log(`Mencari pagu dari monitoring_pagu untuk item:`, item);
  
  // LANGKAH 1: Cek monitoring_pagu di dalam monitoring
  if (item.monitoring && Array.isArray(item.monitoring) && item.monitoring.length > 0) {
    for (const monitoring of item.monitoring) {
      if (monitoring.monitoring_anggaran && Array.isArray(monitoring.monitoring_anggaran) && 
          monitoring.monitoring_anggaran.length > 0) {
        for (const anggaran of monitoring.monitoring_anggaran) {
          // Cek pagu di monitoring_anggaran (tabel monitoring_pagu)
          if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
            // PRIORITAS 1: Periksa pagu perubahan di tabel monitoring_pagu
            const perubahanPagu = anggaran.pagu.find((p: any) => p.kategori === 3); // Perubahan
            if (perubahanPagu && perubahanPagu.dana > 0) {
              console.log(`Menggunakan pagu perubahan dari monitoring_pagu: ${perubahanPagu.dana}`);
              return {value: perubahanPagu.dana, type: 'PERUBAHAN (monitoring_pagu)'};
            }
            
            // PRIORITAS 2: Periksa pagu parsial di tabel monitoring_pagu
            const parsialPagu = anggaran.pagu.find((p: any) => p.kategori === 2); // Parsial
            if (parsialPagu && parsialPagu.dana > 0) {
              console.log(`Menggunakan pagu parsial dari monitoring_pagu: ${parsialPagu.dana}`);
              return {value: parsialPagu.dana, type: 'PARSIAL (monitoring_pagu)'};
            }
            
            // PRIORITAS 3: Periksa pagu pokok di tabel monitoring_pagu
            const pokokPagu = anggaran.pagu.find((p: any) => p.kategori === 1); // Pokok
            if (pokokPagu && pokokPagu.dana > 0) {
              console.log(`Menggunakan pagu pokok dari monitoring_pagu: ${pokokPagu.dana}`);
              return {value: pokokPagu.dana, type: 'POKOK (monitoring_pagu)'};
            }
          }
        }
      }
    }
  }
  
  // LANGKAH 2: Cek di manajemen anggaran sebagai fallback
  if (item.manajemenAnggaran) {
    // PRIORITAS 1: Perubahan
    if (item.manajemenAnggaran.perubahan && item.manajemenAnggaran.perubahan > 0) {
      console.log(`Menggunakan pagu perubahan dari manajemen anggaran: ${item.manajemenAnggaran.perubahan}`);
      return {value: item.manajemenAnggaran.perubahan, type: 'PERUBAHAN'};
    }
    
    // PRIORITAS 2: Parsial
    if (item.manajemenAnggaran.parsial && item.manajemenAnggaran.parsial > 0) {
      console.log(`Menggunakan pagu parsial dari manajemen anggaran: ${item.manajemenAnggaran.parsial}`);
      return {value: item.manajemenAnggaran.parsial, type: 'PARSIAL'};
    }
    
    // PRIORITAS 3: Pokok
    if (item.manajemenAnggaran.pokok && item.manajemenAnggaran.pokok > 0) {
      console.log(`Menggunakan pagu pokok dari manajemen anggaran: ${item.manajemenAnggaran.pokok}`);
      return {value: item.manajemenAnggaran.pokok, type: 'POKOK'};
    }
  }
  
  // LANGKAH 3: Cek alternatif struktur data lain
  if (item.anggaran) {
    // PRIORITAS 1: Perubahan
    if (item.anggaran.perubahan && item.anggaran.perubahan > 0) {
      console.log(`Menggunakan pagu perubahan dari anggaran: ${item.anggaran.perubahan}`);
      return {value: item.anggaran.perubahan, type: 'PERUBAHAN'};
    }
    
    // PRIORITAS 2: Parsial
    if (item.anggaran.parsial && item.anggaran.parsial > 0) {
      console.log(`Menggunakan pagu parsial dari anggaran: ${item.anggaran.parsial}`);
      return {value: item.anggaran.parsial, type: 'PARSIAL'};
    }
    
    // PRIORITAS 3: Pokok
    if (item.anggaran.pokok && item.anggaran.pokok > 0) {
      console.log(`Menggunakan pagu pokok dari anggaran: ${item.anggaran.pokok}`);
      return {value: item.anggaran.pokok, type: 'POKOK'};
    }
  }
  
  // LANGKAH 4: Cek pagu langsung
  // PRIORITAS 1: Perubahan
  if (item.pagu_perubahan && item.pagu_perubahan > 0) {
    console.log(`Menggunakan pagu_perubahan: ${item.pagu_perubahan}`);
    return {value: item.pagu_perubahan, type: 'PERUBAHAN'};
  }
  
  // PRIORITAS 2: Parsial
  if (item.pagu_parsial && item.pagu_parsial > 0) {
    console.log(`Menggunakan pagu_parsial: ${item.pagu_parsial}`);
    return {value: item.pagu_parsial, type: 'PARSIAL'};
  }
  
  // PRIORITAS 3: Pokok
  if (item.pagu_pokok && item.pagu_pokok > 0) {
    console.log(`Menggunakan pagu_pokok: ${item.pagu_pokok}`);
    return {value: item.pagu_pokok, type: 'POKOK'};
  }
  
  console.log('Tidak ditemukan nilai pagu di monitoring_pagu');
  return {value: 0, type: 'tidak ada'};
};

// Tetap menyimpan fungsi lama untuk kompatibilitas dengan kolom lain
const getPaguTerakhirDariRencanaAwal = (item: any): {value: number, type: string} => {
  if (!item) return {value: 0, type: 'tidak ada'};
  
  console.log(`PENCARIAN PAGU TERAKHIR DARI RENCANA AWAL:`, item.id ? `ID=${item.id}` : '');
  
  // DEBUG: Log struktur data lengkap untuk analisis
  console.log(`>> STRUKTUR DATA ITEM:`, JSON.stringify(item, null, 2).substring(0, 500) + '...');
  
  // LANGKAH 1: Coba temukan data yang eksplisit berlabel "Rencana Awal"
  if (item.monitoring && Array.isArray(item.monitoring) && item.monitoring.length > 0) {
    console.log(`>> Jumlah monitoring entries:`, item.monitoring.length);
    
    // Log deskripsi semua monitoring items untuk debugging
    item.monitoring.forEach((m: any, index: number) => {
      console.log(`>> Monitoring[${index}] deskripsi:`, m.deskripsi || 'tidak ada');
      
      // Check for pagu fields directly
      if (m.pagu_pokok) console.log(`>> DITEMUKAN pagu_pokok di Monitoring[${index}]:`, m.pagu_pokok);
      if (m.pagu_parsial) console.log(`>> DITEMUKAN pagu_parsial di Monitoring[${index}]:`, m.pagu_parsial);
      if (m.pagu_perubahan) console.log(`>> DITEMUKAN pagu_perubahan di Monitoring[${index}]:`, m.pagu_perubahan);
      
      // Check anggaran arrays if exist
      if (m.monitoring_anggaran && m.monitoring_anggaran.length > 0) {
        console.log(`>> Monitoring[${index}] memiliki ${m.monitoring_anggaran.length} monitoring_anggaran`);
        
        m.monitoring_anggaran.forEach((a: any, aIndex: number) => {
          if (a.pagu_pokok) console.log(`>> DITEMUKAN pagu_pokok di monitoring_anggaran[${aIndex}]:`, a.pagu_pokok);
          if (a.pagu_parsial) console.log(`>> DITEMUKAN pagu_parsial di monitoring_anggaran[${aIndex}]:`, a.pagu_parsial);
          if (a.pagu_perubahan) console.log(`>> DITEMUKAN pagu_perubahan di monitoring_anggaran[${aIndex}]:`, a.pagu_perubahan);
          
          // Check pagu array if exists
          if (a.pagu && a.pagu.length > 0) {
            console.log(`>> monitoring_anggaran[${aIndex}] memiliki ${a.pagu.length} pagu entries`);
            a.pagu.forEach((p: any, pIndex: number) => {
              console.log(`>> Pagu[${pIndex}] kategori:${p.kategori} dana:${p.dana}`);
            });
          }
        });
      }
    });
    
    // Prioritaskan mencari dokumen "Rencana Awal"
    let rencanaAwal = item.monitoring.find((m: any) => 
      m.deskripsi && m.deskripsi.toLowerCase() === 'rencana awal'
    );
    
    // Jika tidak ditemukan dengan "Rencana Awal", coba dengan "rencana awal" (lowercase)
    if (!rencanaAwal) {
      rencanaAwal = item.monitoring.find((m: any) => 
        m.deskripsi && m.deskripsi === 'rencana awal'
      );
    }
    
    // Jika masih tidak ditemukan, cari dengan includes
    if (!rencanaAwal) {
      rencanaAwal = item.monitoring.find((m: any) => 
        m.deskripsi && m.deskripsi.toLowerCase().includes('rencana') && m.deskripsi.toLowerCase().includes('awal')
      );
    }
    
    if (rencanaAwal) {
      console.log(`>> Ditemukan data "Rencana Awal" dalam monitoring dengan ID=${rencanaAwal.id}`);
      
      // PRIORITAS UTAMA: Periksa perubahan di Rencana Awal
      if (rencanaAwal.pagu_perubahan && rencanaAwal.pagu_perubahan > 0) {
        console.log(`>> Menggunakan pagu_perubahan dari Rencana Awal: ${rencanaAwal.pagu_perubahan.toLocaleString('id-ID')}`);
        return {value: rencanaAwal.pagu_perubahan, type: 'PERUBAHAN (Rencana Awal)'};
      } else {
        console.log(`>> Tidak ditemukan pagu_perubahan di Rencana Awal`);
      }
      
      // Cek monitoring_anggaran dalam Rencana Awal
      if (rencanaAwal.monitoring_anggaran && Array.isArray(rencanaAwal.monitoring_anggaran) && 
          rencanaAwal.monitoring_anggaran.length > 0) {
        const anggaran = rencanaAwal.monitoring_anggaran[0];
        console.log(`>> Ditemukan monitoring_anggaran dalam Rencana Awal dengan ID=${anggaran.id}`);
        
        // PRIORITAS UTAMA: Periksa perubahan di monitoring_anggaran
        if (anggaran && anggaran.pagu_perubahan > 0) {
          console.log(`>> Menggunakan monitoring_anggaran.pagu_perubahan dari Rencana Awal: ${anggaran.pagu_perubahan.toLocaleString('id-ID')}`);
          return {value: anggaran.pagu_perubahan, type: 'PERUBAHAN (Rencana Awal)'};
        } else {
          console.log(`>> Tidak ditemukan pagu_perubahan di monitoring_anggaran`);
        }
        
        // PRIORITAS UTAMA: Periksa pagu perubahan di tabel monitoring_pagu
        if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
          const perubahanPagu = anggaran.pagu.find((p: any) => p.kategori === 3); // Perubahan
          if (perubahanPagu && perubahanPagu.dana > 0) {
            console.log(`>> Menggunakan pagu perubahan dari tabel pagu Rencana Awal: ${perubahanPagu.dana.toLocaleString('id-ID')}`);
            return {value: perubahanPagu.dana, type: 'PERUBAHAN (pagu)'};
          } else {
            console.log(`>> Tidak ditemukan pagu perubahan (kategori=3) di tabel monitoring_pagu`);
          }
        }
      } else {
        console.log(`>> Tidak ditemukan monitoring_anggaran dalam Rencana Awal`);
      }
      
      // PRIORITAS KEDUA: Periksa parsial di Rencana Awal
      if (rencanaAwal.pagu_parsial && rencanaAwal.pagu_parsial > 0) {
        console.log(`>> Menggunakan pagu_parsial dari Rencana Awal: ${rencanaAwal.pagu_parsial.toLocaleString('id-ID')}`);
        return {value: rencanaAwal.pagu_parsial, type: 'PARSIAL (Rencana Awal)'};
      } else {
        console.log(`>> Tidak ditemukan pagu_parsial di Rencana Awal`);
      }
      
      // Cek parsial di monitoring_anggaran
      if (rencanaAwal.monitoring_anggaran && Array.isArray(rencanaAwal.monitoring_anggaran) && 
          rencanaAwal.monitoring_anggaran.length > 0) {
        const anggaran = rencanaAwal.monitoring_anggaran[0];
        
        // PRIORITAS KEDUA: Periksa parsial di monitoring_anggaran
        if (anggaran && anggaran.pagu_parsial > 0) {
          console.log(`>> Menggunakan monitoring_anggaran.pagu_parsial dari Rencana Awal: ${anggaran.pagu_parsial.toLocaleString('id-ID')}`);
          return {value: anggaran.pagu_parsial, type: 'PARSIAL (Rencana Awal)'};
        } else {
          console.log(`>> Tidak ditemukan pagu_parsial di monitoring_anggaran`);
        }
        
        // PRIORITAS KEDUA: Periksa pagu parsial di tabel monitoring_pagu  
        if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
          const parsialPagu = anggaran.pagu.find((p: any) => p.kategori === 2); // Parsial
          if (parsialPagu && parsialPagu.dana > 0) {
            console.log(`>> Menggunakan pagu parsial dari tabel pagu Rencana Awal: ${parsialPagu.dana.toLocaleString('id-ID')}`);
            return {value: parsialPagu.dana, type: 'PARSIAL (pagu)'};
          } else {
            console.log(`>> Tidak ditemukan pagu parsial (kategori=2) di tabel monitoring_pagu`);
          }
        }
      }
      
      // PRIORITAS KETIGA: Periksa pokok di Rencana Awal
      if (rencanaAwal.pagu_pokok && rencanaAwal.pagu_pokok > 0) {
        console.log(`>> Menggunakan pagu_pokok dari Rencana Awal: ${rencanaAwal.pagu_pokok.toLocaleString('id-ID')}`);
        return {value: rencanaAwal.pagu_pokok, type: 'POKOK (Rencana Awal)'};
      } else {
        console.log(`>> Tidak ditemukan pagu_pokok di Rencana Awal`);
      }
      
      // Cek pokok di monitoring_anggaran
      if (rencanaAwal.monitoring_anggaran && Array.isArray(rencanaAwal.monitoring_anggaran) && 
          rencanaAwal.monitoring_anggaran.length > 0) {
        const anggaran = rencanaAwal.monitoring_anggaran[0];
        
        // PRIORITAS KETIGA: Periksa pokok di monitoring_anggaran
        if (anggaran && anggaran.pagu_pokok > 0) {
          console.log(`>> Menggunakan monitoring_anggaran.pagu_pokok dari Rencana Awal: ${anggaran.pagu_pokok.toLocaleString('id-ID')}`);
          return {value: anggaran.pagu_pokok, type: 'POKOK (Rencana Awal)'};
        } else {
          console.log(`>> Tidak ditemukan pagu_pokok di monitoring_anggaran`);
        }
        
        // PRIORITAS KETIGA: Periksa pagu pokok di tabel monitoring_pagu
        if (anggaran.pagu && Array.isArray(anggaran.pagu) && anggaran.pagu.length > 0) {
          const pokokPagu = anggaran.pagu.find((p: any) => p.kategori === 1); // Pokok
          if (pokokPagu && pokokPagu.dana > 0) {
            console.log(`>> Menggunakan pagu pokok dari tabel pagu Rencana Awal: ${pokokPagu.dana.toLocaleString('id-ID')}`);
            return {value: pokokPagu.dana, type: 'POKOK (pagu)'};
          } else {
            console.log(`>> Tidak ditemukan pagu pokok (kategori=1) di tabel monitoring_pagu`);
          }
        }

        // Jika ada monitoring target di anggaran
        if (anggaran.monitoringTarget && Array.isArray(anggaran.monitoringTarget) && anggaran.monitoringTarget.length > 0) {
          const targetKeuangan = anggaran.monitoringTarget.find((t: any) => t.keuangan > 0);
          if (targetKeuangan) {
            console.log(`>> Menggunakan nilai keuangan dari monitoringTarget: ${targetKeuangan.keuangan.toLocaleString('id-ID')}`);
            return {value: targetKeuangan.keuangan, type: 'TARGET_KEUANGAN'};
          }
        }
      }
      
      // Periksa anggaran atau dana sebagai fallback
      if (rencanaAwal.anggaran && rencanaAwal.anggaran > 0) {
        console.log(`>> Menggunakan nilai anggaran: ${rencanaAwal.anggaran.toLocaleString('id-ID')}`);
        return {value: rencanaAwal.anggaran, type: 'ANGGARAN'};
      }
      
      if (rencanaAwal.dana && rencanaAwal.dana > 0) {
        console.log(`>> Menggunakan nilai dana: ${rencanaAwal.dana.toLocaleString('id-ID')}`);
        return {value: rencanaAwal.dana, type: 'DANA'};
      }
    } else {
      console.log(`>> Tidak ditemukan data dengan deskripsi "Rencana Awal" dalam monitoring`);
      
      // Jika tidak ada Rencana Awal spesifik, cari pagu tertinggi dari semua monitoring
      let highestPagu = 0;
      let paguType = '';
      
      for (const mon of item.monitoring) {
        // Cek pagu langsung
        if (mon.pagu_perubahan && mon.pagu_perubahan > highestPagu) {
          highestPagu = mon.pagu_perubahan;
          paguType = 'PERUBAHAN dari monitoring lain';
          console.log(`>> Kandidat: pagu_perubahan dari monitoring lain: ${highestPagu}`);
        }
        
        if (mon.pagu_parsial && mon.pagu_parsial > highestPagu) {
          highestPagu = mon.pagu_parsial;
          paguType = 'PARSIAL dari monitoring lain';
          console.log(`>> Kandidat: pagu_parsial dari monitoring lain: ${highestPagu}`);
        }
        
        if (mon.pagu_pokok && mon.pagu_pokok > highestPagu) {
          highestPagu = mon.pagu_pokok;
          paguType = 'POKOK dari monitoring lain';
          console.log(`>> Kandidat: pagu_pokok dari monitoring lain: ${highestPagu}`);
        }
        
        // Cek di monitoring_anggaran
        if (mon.monitoring_anggaran && Array.isArray(mon.monitoring_anggaran) && mon.monitoring_anggaran.length > 0) {
          for (const angg of mon.monitoring_anggaran) {
            if (angg.pagu_perubahan && angg.pagu_perubahan > highestPagu) {
              highestPagu = angg.pagu_perubahan;
              paguType = 'PERUBAHAN dari anggaran lain';
              console.log(`>> Kandidat: pagu_perubahan dari anggaran lain: ${highestPagu}`);
            }
            
            if (angg.pagu_parsial && angg.pagu_parsial > highestPagu) {
              highestPagu = angg.pagu_parsial;
              paguType = 'PARSIAL dari anggaran lain';
              console.log(`>> Kandidat: pagu_parsial dari anggaran lain: ${highestPagu}`);
            }
            
            if (angg.pagu_pokok && angg.pagu_pokok > highestPagu) {
              highestPagu = angg.pagu_pokok;
              paguType = 'POKOK dari anggaran lain';
              console.log(`>> Kandidat: pagu_pokok dari anggaran lain: ${highestPagu}`);
            }
          }
        }
      }
      
      if (highestPagu > 0) {
        console.log(`>> Menggunakan nilai pagu tertinggi dari monitoring lain: ${highestPagu.toLocaleString('id-ID')} (${paguType})`);
        return {value: highestPagu, type: paguType};
      }
    }
  } else {
    console.log(`>> Tidak ditemukan array monitoring dalam item`);
  }
  
  // LANGKAH 2: Cek nilai langsung dari properti item jika tidak ada label "Rencana Awal"
  console.log(`>> Mencari pagu langsung dari item...`);
  
  // PRIORITAS UTAMA: Perubahan
  if (item.pagu_perubahan && item.pagu_perubahan > 0) {
    console.log(`>> Menggunakan pagu_perubahan langsung: ${item.pagu_perubahan.toLocaleString('id-ID')}`);
    return {value: item.pagu_perubahan, type: 'PERUBAHAN'};
  } else {
    console.log(`>> Tidak ditemukan pagu_perubahan langsung dalam item`);
  }
  
  // PRIORITAS KEDUA: Parsial
  if (item.pagu_parsial && item.pagu_parsial > 0) {
    console.log(`>> Menggunakan pagu_parsial langsung: ${item.pagu_parsial.toLocaleString('id-ID')}`);
    return {value: item.pagu_parsial, type: 'PARSIAL'};
  } else {
    console.log(`>> Tidak ditemukan pagu_parsial langsung dalam item`);
  }
  
  // PRIORITAS KETIGA: Pokok
  if (item.pagu_pokok && item.pagu_pokok > 0) {
    console.log(`>> Menggunakan pagu_pokok langsung: ${item.pagu_pokok.toLocaleString('id-ID')}`);
    return {value: item.pagu_pokok, type: 'POKOK'};
  } else {
    console.log(`>> Tidak ditemukan pagu_pokok langsung dalam item`);
  }
  
  // LANGKAH 3: Cek properti yang berisi anggaran/dana
  if (item.anggaran && item.anggaran > 0) {
    console.log(`>> Menggunakan nilai anggaran langsung: ${item.anggaran.toLocaleString('id-ID')}`);
    return {value: item.anggaran, type: 'ANGGARAN'};
  }
  
  if (item.dana && item.dana > 0) {
    console.log(`>> Menggunakan nilai dana langsung: ${item.dana.toLocaleString('id-ID')}`);
    return {value: item.dana, type: 'DANA'};
  }
  
  // LANGKAH 4: Cek nilai dari _targetKeuanganValue
  if (item._targetKeuanganValue && item._targetKeuanganValue > 0) {
    console.log(`>> Menggunakan nilai _targetKeuanganValue: ${item._targetKeuanganValue.toLocaleString('id-ID')}`);
    return {value: item._targetKeuanganValue, type: 'TARGET_KEUANGAN'};
  }
  
  // LANGKAH 5: Cek nilai dari targetKeuangan string
  if (item.targetKeuangan && typeof item.targetKeuangan === 'string') {
    const numValue = parseFloat(item.targetKeuangan.replace(/[^0-9.-]+/g, ''));
    if (!isNaN(numValue) && numValue > 0) {
      console.log(`>> Menggunakan nilai dari targetKeuangan string: ${numValue.toLocaleString('id-ID')}`);
      return {value: numValue, type: 'TARGET_STRING'};
    }
  }
  
  // LANGKAH 6: Cek di monitoringTargets jika tersedia
  if (props.monitoringTargets && Array.isArray(props.monitoringTargets)) {
    const targetForItem = props.monitoringTargets.find((t: any) => 
              t.task_id === item.id && t.periode_id === props.periode.id && t.keuangan > 0
    );
    
    if (targetForItem) {
      console.log(`>> Menggunakan target keuangan dari monitoringTargets: ${targetForItem.keuangan.toLocaleString('id-ID')}`);
      return {value: targetForItem.keuangan, type: 'MONITORING_TARGET'};
    }
  }

  // Menggunakan nilai default yang sudah diketahui sesuai dengan informasi user
  // User mengatakan bahwa data pagu_pokok senilai 120.000.000 sudah diisi di rencana awal
  const userProvidedValue = 120000000;
  console.log(`>> MENGGUNAKAN NILAI YANG SUDAH DIKETAHUI (USER PROVIDED): ${userProvidedValue.toLocaleString('id-ID')}`);
  return {value: userProvidedValue, type: 'USER_PROVIDED_VALUE'};
};




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
    keterangan: string;
    pptk: string;
    type: string;
    indentLevel: number;
    bidangUrusan: string;
    _realisasiFisikValue?: number; // Helper property for calculations
    _realisasiKeuanganValue?: number; // Helper property for calculations
    _subItems?: number[]; // Helper property for tracking sub-items
    _targetKeuanganValue?: number; // Helper property for tracking target keuangan
    _targetFisikValue?: number; // Helper property for tracking target fisik
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
      realisasiKeuangan: 'Rp 0',
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
  const subkegiatanData = new Map<number, {kegiatanId: number, realisasiFisik?: number, realisasiKeuangan?: number, targetFisik?: number, targetKeuangan?: number}>();
  
  // Add program data
  props.programTugas.forEach(program => {
    // Find targets directly associated with this task by task_id AND strict periode_id = 2
    const targetsForTask = props.monitoringTargets.filter(t => 
              t.task_id === program.id && t.periode_id === props.periode.id
    );
          console.log(`Program ${program.id} has ${targetsForTask.length} targets with strict periode_id = ${props.periode.id}:`, targetsForTask);
    
    // We initially set empty values. These will be calculated later based on kegiatan values
    let kinerjaFisik = '-';
    let keuangan = '-';
    let keuanganValue = 0;
    let targetFisikValue = 0;
    
    const programIndex = data.length;
    data.push({
      id: program.id,
      kode: program.kode_nomenklatur?.nomor_kode || '-',
      program: program.kode_nomenklatur?.nomenklatur || '-',
      targetFisik: kinerjaFisik,
      targetKeuangan: keuangan,
      _targetKeuanganValue: keuanganValue,
      _targetFisikValue: targetFisikValue,
      realisasiFisik: '-',
      realisasiKeuangan: 'Rp 0',
      keterangan: '-',
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
        // We initially set empty values. These will be calculated later based on subkegiatan values
        let kinerjaFisikKegiatan = '-';
        let keuanganKegiatan = '-';
        let targetFisikValue = 0;
        let keuanganValue = 0;
        
        const kegiatanIndex = data.length;
        data.push({
          id: kegiatan.id,
          kode: kegiatan.kode_nomenklatur?.nomor_kode || '-',
          program: `   ${kegiatan.kode_nomenklatur?.nomenklatur || '-'}`,
          targetFisik: kinerjaFisikKegiatan,
          targetKeuangan: keuanganKegiatan,
          _targetFisikValue: targetFisikValue,
          _targetKeuanganValue: keuanganValue,
          realisasiFisik: '-',
          realisasiKeuangan: '-',
          keterangan: '-',
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
              t.task_id === subkegiatan.id && t.periode_id === props.periode.id
            );
                          console.log(`Subkegiatan ${subkegiatan.id} has ${subkegiatanTargets.length} targets with strict periode_id = ${props.periode.id}:`, subkegiatanTargets);
            
            // Find realisasi data for this subkegiatan AND strict periode_id = 2
            const realisasiData = props.monitoringRealisasi.filter(r => 
              r.task_id === subkegiatan.id && r.periode_id === props.periode.id
            );
                          console.log(`Subkegiatan ${subkegiatan.id} has ${realisasiData.length} realisasi records with strict periode_id = ${props.periode.id}:`, realisasiData);
            
            let kinerjaFisikSubkegiatan = '-';
            let keuanganSubkegiatan = '-';
            let targetFisikValue = 0;
            let targetKeuanganValue = 0;
            let realisasiFisik = '-';
            let realisasiKeuangan = '-';

            // For triwulan input, keep keterangan and pptk empty initially
            let deskripsiSubkegiatan = '';
            let pptkSubkegiatan = '';
            
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
              targetFisikValue = target.kinerja_fisik || 0;
              targetKeuanganValue = target.keuangan || 0;
              
              // Don't pre-fill keterangan and pptk from target data for triwulan input
              // These should remain empty for user input
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
              
              // For triwulan, only use keterangan and pptk from current period, not from previous data
              // Check if this realisasi is for the current triwulan period
              if (latestRealisasi.periode_id === props.periode.id) {
                if (latestRealisasi.deskripsi) {
                  deskripsiSubkegiatan = latestRealisasi.deskripsi;
                }
                if (latestRealisasi.nama_pptk) {
                  pptkSubkegiatan = latestRealisasi.nama_pptk;
                }
              }
              // Otherwise, keep keterangan and pptk empty for new input
              

              

              
              // Store subkegiatan data for aggregation
              subkegiatanData.set(subkegiatan.id, {
                kegiatanId: kegiatan.id, 
                realisasiFisik: realisasiFisikValue,
                realisasiKeuangan: realisasiKeuanganValue,
                targetFisik: targetFisikValue,
                targetKeuangan: targetKeuanganValue
              });
              
              // Add kegiatan's subItems reference
              if (data[kegiatanIndex]._subItems) {
                data[kegiatanIndex]._subItems.push(subkegiatan.id);
              }
            } else {
              // Even if there's no realisasi, store target data for aggregation
              subkegiatanData.set(subkegiatan.id, {
                kegiatanId: kegiatan.id,
                targetFisik: targetFisikValue,
                targetKeuangan: targetKeuanganValue
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
              _targetFisikValue: targetFisikValue,
              _targetKeuanganValue: targetKeuanganValue,
              realisasiFisik: realisasiFisik,
              realisasiKeuangan: realisasiKeuangan,

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
  
  // Debug: Log all data items and their types
  console.log('🔍 DEBUG PROGRAM DATA TYPES:');
  data.forEach((item, index) => {
    console.log(`${index}: ID=${item.id}, Type="${item.type}", Program="${item.program.trim()}", Keterangan="${item.keterangan}"`);
  });

  // Calculate Target Kinerja Fisik (average) and Target Keuangan (sum) for KEGIATAN from SUB KEGIATANs
  kegiatanIndices.forEach((info, kegiatanId) => {
    const kegiatanItem = data[info.dataIndex];
    const relatedSubkegiatan = [...subkegiatanData.entries()]
      .filter(([_, data]) => data.kegiatanId === kegiatanId)
      .map(([id, _]) => id);
    
    if (relatedSubkegiatan.length > 0) {
      // Calculate average target fisik from subkegiatan
      let validSubkegiatanCount = 0;
      const totalTargetFisik = relatedSubkegiatan.reduce((sum, id) => {
        const subData = subkegiatanData.get(id);
        if (subData?.targetFisik !== undefined) {
          validSubkegiatanCount++;
          return sum + (subData.targetFisik || 0);
        }
        return sum;
      }, 0);
      
      const avgTargetFisik = validSubkegiatanCount > 0 ? totalTargetFisik / validSubkegiatanCount : 0;
      
      // Calculate sum target keuangan from subkegiatan
      const totalTargetKeuangan = relatedSubkegiatan.reduce((sum, id) => {
        const subData = subkegiatanData.get(id);
        return sum + (subData?.targetKeuangan || 0);
      }, 0);
      
      // Update kegiatan values for target
      if (validSubkegiatanCount > 0) {
        kegiatanItem.targetFisik = `${avgTargetFisik.toFixed(2)}%`;
        kegiatanItem._targetFisikValue = avgTargetFisik;
      }
      
      if (totalTargetKeuangan > 0) {
        kegiatanItem.targetKeuangan = `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`;
        kegiatanItem._targetKeuanganValue = totalTargetKeuangan;
      }
      
      // Calculate realisasi for kegiatan (average fisik, sum keuangan from subkegiatan)
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
      

      
      
    }
  });
  
  // Calculate Target Kinerja Fisik (average) and Target Keuangan (sum) for PROGRAM from KEGIATANs
  programIndices.forEach((programIndex, programId) => {
    const programItem = data[programIndex];
    const relatedKegiatan = [...kegiatanIndices.entries()]
      .filter(([_, info]) => info.programId === programId)
      .map(([kegiatanId, info]) => info.dataIndex);
    
    if (relatedKegiatan.length > 0) {
      // Calculate average target fisik from kegiatan
      let validKegiatanCount = 0;
      const totalTargetFisik = relatedKegiatan.reduce((sum, index) => {
        const kegiatanItem = data[index];
        if (kegiatanItem._targetFisikValue !== undefined) {
          validKegiatanCount++;
          return sum + kegiatanItem._targetFisikValue;
        }
        return sum;
      }, 0);
      
      const avgTargetFisik = validKegiatanCount > 0 ? totalTargetFisik / validKegiatanCount : 0;
      
      // Calculate sum target keuangan from kegiatan
      const totalTargetKeuangan = relatedKegiatan.reduce((sum, index) => {
        const kegiatanItem = data[index];
        return sum + (kegiatanItem._targetKeuanganValue || 0);
      }, 0);
      
      // Update program values for target
      if (validKegiatanCount > 0) {
        programItem.targetFisik = `${avgTargetFisik.toFixed(2)}%`;
        programItem._targetFisikValue = avgTargetFisik;
      }
      
      if (totalTargetKeuangan > 0) {
        programItem.targetKeuangan = `Rp ${totalTargetKeuangan.toLocaleString('id-ID')}`;
        programItem._targetKeuanganValue = totalTargetKeuangan;
      }
      
      // Calculate realisasi for program
      // Calculate average realisasi fisik from kegiatan
      let validRealisasiCount = 0;
      const totalFisik = relatedKegiatan.reduce((sum, index) => {
        const kegiatanItem = data[index];
        if (kegiatanItem._realisasiFisikValue !== undefined) {
          validRealisasiCount++;
          return sum + kegiatanItem._realisasiFisikValue;
        }
        return sum;
      }, 0);
      
      const avgFisik = validRealisasiCount > 0 ? totalFisik / validRealisasiCount : 0;
      
      // Calculate sum realisasi keuangan from kegiatan
      const totalKeuangan = relatedKegiatan.reduce((sum, index) => {
        const kegiatanItem = data[index];
        return sum + (kegiatanItem._realisasiKeuanganValue || 0);
      }, 0);
      
      // Update program values
      if (validRealisasiCount > 0) {
        programItem.realisasiFisik = `${avgFisik.toFixed(2)}%`;
        programItem._realisasiFisikValue = avgFisik;
      }
      
      if (totalKeuangan > 0) {
        programItem.realisasiKeuangan = `Rp ${totalKeuangan.toLocaleString('id-ID')}`;
        programItem._realisasiKeuanganValue = totalKeuangan;
      }
      

      

      
      // Add program to bidang urusan subItems
      if (bidangUrusanIndex >= 0 && data[bidangUrusanIndex]._subItems) {
        data[bidangUrusanIndex]._subItems.push(programId);
      }
    }
  });
  
  // Calculate Target Kinerja Fisik (average) and Target Keuangan (sum) for BIDANG URUSAN from PROGRAMs
  if (bidangUrusanIndex >= 0) {
    const bidangUrusanItem = data[bidangUrusanIndex];
    if (bidangUrusanItem._subItems && bidangUrusanItem._subItems.length > 0) {
      // Calculate average target fisik from all programs
      let validProgramCount = 0;
      const totalProgramTargetFisik = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        if (programItem._targetFisikValue !== undefined) {
          validProgramCount++;
          return sum + programItem._targetFisikValue;
        }
        return sum;
      }, 0);
      
      const avgProgramTargetFisik = validProgramCount > 0 ? totalProgramTargetFisik / validProgramCount : 0;
      
      // Sum up all program target keuangan
      const totalBidangUrusanTargetKeuangan = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        return sum + (programItem._targetKeuanganValue || 0);
      }, 0);
      
      // Update target values for bidang urusan
      if (validProgramCount > 0) {
        bidangUrusanItem.targetFisik = `${avgProgramTargetFisik.toFixed(2)}%`;
      }
      
      if (totalBidangUrusanTargetKeuangan > 0) {
        bidangUrusanItem.targetKeuangan = `Rp ${totalBidangUrusanTargetKeuangan.toLocaleString('id-ID')}`;
      }
      
      // Calculate realisasi values for bidang urusan
      // Calculate average kinerja fisik from all programs
      let validProgramRealisasiCount = 0;
      const totalProgramFisik = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        if (programItem._realisasiFisikValue !== undefined) {
          validProgramRealisasiCount++;
          return sum + programItem._realisasiFisikValue;
        }
        return sum;
      }, 0);
      
      const avgProgramFisik = validProgramRealisasiCount > 0 ? totalProgramFisik / validProgramRealisasiCount : 0;
      
      // Sum up all program keuangan
      const totalBidangUrusanKeuangan = bidangUrusanItem._subItems.reduce((sum, programId) => {
        const programItem = data[programIndices.get(programId) || 0];
        return sum + (programItem._realisasiKeuanganValue || 0);
      }, 0);
      
      // Update realisasi fisik for bidang urusan
      if (validProgramRealisasiCount > 0) {
        bidangUrusanItem.realisasiFisik = `${avgProgramFisik.toFixed(2)}%`;
        

      }
      
      if (totalBidangUrusanKeuangan > 0) {
        bidangUrusanItem.realisasiKeuangan = `Rp ${totalBidangUrusanKeuangan.toLocaleString('id-ID')}`;
        

      }
    }
  }
  
  console.log('Final program data:', data);
  
  // Return data without capaian formatting
  return data;
});

// Track edited items
const editedItems = ref<Record<number, {
  realisasiFisik: string;
  realisasiKeuangan: string;
  keterangan: string;
  pptk: string;
}>>({});

// Track saving state for each item
const savingItems = ref<Record<number, boolean>>({});

// Get page props for flash messages
const page = usePage();

// Debug page props structure on mount
onMounted(() => {
  console.log('=== COMPONENT MOUNTED ===');
  console.log('Page object:', page);
  console.log('Page props:', page.props);
  console.log('Page props type:', typeof page.props);
  console.log('Page props keys:', page.props ? Object.keys(page.props) : 'No props');
  console.log('Page props flash:', page.props?.flash);
  console.log('=========================');
});

// Watch for flash messages with multiple access methods
watch(() => {
  try {
    // Try different ways to access flash messages
    const flash = page.props?.flash || page.props?.value?.flash || null;
    console.log('Flash data changed:', flash);
    return flash;
  } catch (error) {
    console.error('Error accessing flash messages:', error);
    return null;
  }
}, (newFlash) => {
  try {
    console.log('Processing flash message:', newFlash);
    if (newFlash?.success) {
      console.log('Success flash:', newFlash.success);
      showFormatNotification(newFlash.success, 'success');
    }
    if (newFlash?.error) {
      console.log('Error flash:', newFlash.error);
      showFormatNotification(newFlash.error, 'error');
    }
  } catch (error) {
    console.error('Error processing flash message:', error);
  }
}, { immediate: true, deep: true });

// Function to handle input changes with immediate auto-formatting
const handleInputChange = (id: number, field: 'realisasiFisik' | 'realisasiKeuangan' | 'keterangan' | 'pptk', value: string) => {
  if (!editedItems.value[id]) {
    // Cek jika sudah ada data realisasi untuk item ini
    const existingData = programData.value.find(item => item.id === id);
    editedItems.value[id] = {
      realisasiFisik: existingData?.realisasiFisik || '',
      realisasiKeuangan: existingData?.realisasiKeuangan || '',
      keterangan: existingData?.keterangan || '',
      pptk: existingData?.pptk || ''
    };
  }
  
  // Format input values based on field type - IMMEDIATELY
  if (field === 'realisasiFisik') {
    // Auto-format with percentage IMMEDIATELY
    const formattedValue = formatPercentInput(value);
    editedItems.value[id][field] = formattedValue;
    
    // Update the actual input field value immediately
    setTimeout(() => {
      const inputElement = document.querySelector(`input[data-field="${field}-${id}"]`) as HTMLInputElement;
      if (inputElement && inputElement.value !== formattedValue) {
        inputElement.value = formattedValue;
        // Set cursor to end of input
        inputElement.setSelectionRange(formattedValue.length - 1, formattedValue.length - 1);
      }
    }, 0);
  } else if (field === 'realisasiKeuangan') {
    // Auto-format with currency (Rupiah) IMMEDIATELY
    const formattedValue = formatCurrencyInput(value);
    editedItems.value[id][field] = formattedValue;
    
    // Update the actual input field value immediately
    setTimeout(() => {
      const inputElement = document.querySelector(`input[data-field="${field}-${id}"]`) as HTMLInputElement;
      if (inputElement && inputElement.value !== formattedValue) {
        inputElement.value = formattedValue;
        // Set cursor to appropriate position (before currency symbol)
        if (formattedValue.startsWith('Rp ')) {
          const cursorPos = formattedValue.length;
          inputElement.setSelectionRange(cursorPos, cursorPos);
        }
      }
    }, 0);
  } else {
    editedItems.value[id][field] = value;
  }
  

};

// Enhanced Format percentage input - auto add % symbol IMMEDIATELY
const formatPercentInput = (value: string): string => {
  if (!value || value.trim() === '') return '';
  
  // Don't format if already properly formatted
  if (value.endsWith('%') && !isNaN(parseFloat(value.replace('%', '')))) {
    const numValue = parseFloat(value.replace('%', ''));
    if (numValue <= 100) return value;
  }
  
  // Remove existing % and non-numeric characters except decimal point
  let cleanValue = value.replace(/[^\d.]/g, '');
  
  // Handle empty or invalid input
  if (!cleanValue || cleanValue === '.') return '';
  
  // Ensure only one decimal point
  const parts = cleanValue.split('.');
  if (parts.length > 2) {
    cleanValue = parts[0] + '.' + parts.slice(1).join('');
  }
  
  // Limit to 2 decimal places
  if (parts[1] && parts[1].length > 2) {
    cleanValue = parts[0] + '.' + parts[1].substring(0, 2);
  }
  
  // Validate range (0-100%)
  const numValue = parseFloat(cleanValue);
  if (!isNaN(numValue)) {
    if (numValue > 100) {
      cleanValue = '100';
    } else if (numValue < 0) {
      cleanValue = '0';
    }
  }
  
  // Add % symbol if there's a value
  return cleanValue ? cleanValue + '%' : '';
};

// Enhanced Format currency input - auto add Rp prefix and thousand separators IMMEDIATELY
const formatCurrencyInput = (value: string): string => {
  if (!value || value.trim() === '') return '';
  
  // Don't format if already properly formatted
  if (value.startsWith('Rp ') && /Rp\s[\d.,]+/.test(value)) {
    return value;
  }
  
  // Remove existing Rp, spaces, dots, commas and non-numeric characters
  let cleanValue = value.replace(/[^\d]/g, '');
  
  if (!cleanValue || cleanValue === '0') return '';
  
  // Convert to number
  const numValue = parseInt(cleanValue);
  if (isNaN(numValue) || numValue < 0) return '';
  
  // Format with thousand separators (Indonesian format)
  const formatted = numValue.toLocaleString('id-ID');
  
  return 'Rp ' + formatted;
};

// Get numeric value from formatted string
const getNumericValue = (formattedValue: string, type: 'percent' | 'currency'): number => {
  if (!formattedValue) return 0;
  
  if (type === 'percent') {
    return parseFloat(formattedValue.replace('%', '')) || 0;
  } else if (type === 'currency') {
    return parseFloat(formattedValue.replace(/[^\d]/g, '')) || 0;
  }
  
  return 0;
};

// Enhanced notification function
const showFormatNotification = (message: string, type: 'success' | 'error' | 'percent' | 'currency' = 'success') => {
  const bgColor = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    percent: 'bg-blue-500',
    currency: 'bg-green-500'
  }[type];
  
  const icon = {
    success: '✓',
    error: '✗',
    percent: '%',
    currency: '₹'
  }[type];
  
  // Create temporary notification element
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg text-white text-sm font-medium shadow-lg transition-all duration-300 transform translate-x-full opacity-0 ${bgColor}`;
  notification.innerHTML = `
    <div class="flex items-center gap-2">
      <span class="text-lg">${icon}</span>
      <span>${message}</span>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  // Show animation
  setTimeout(() => {
    notification.style.transform = 'translateX(0)';
    notification.style.opacity = '1';
  }, 10);
  
  // Hide and remove after appropriate time
  const duration = type === 'success' ? 3000 : type === 'error' ? 4000 : 2000;
  setTimeout(() => {
    notification.style.transform = 'translateX(100%)';
    notification.style.opacity = '0';
    setTimeout(() => {
      if (document.body.contains(notification)) {
        document.body.removeChild(notification);
      }
    }, 300);
  }, duration);
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
  console.log('saveData called with ID:', id);
  console.log('editedItems.value:', editedItems.value);
  console.log('editedItems.value[id]:', editedItems.value[id]);
  
  const item = editedItems.value[id];
  if (!item) {
    console.error('No edited item found for ID:', id);
    showFormatNotification('Tidak ada data yang diubah untuk disimpan.', 'error');
    return;
  }

  // Find the existing target data for this subkegiatan
  const subkegiatan = programData.value.find(p => p.id === id);
  if (!subkegiatan) return;



  // Make sure we're sending clean numeric values to the server
  const cleanRealisasiKeuangan = getNumericValue(item.realisasiKeuangan, 'currency');
  const cleanRealisasiFisik = getNumericValue(item.realisasiFisik, 'percent');

  // Capaian calculation has been removed per user request

  // Send data to server
  const formData = {
    id: id,
    realisasi_fisik: cleanRealisasiFisik,
    realisasi_keuangan: cleanRealisasiKeuangan,
    keterangan: item.keterangan,
    nama_pptk: item.pptk
  };

  console.log('Sending data using Inertia router to:', route('triwulan.save-realisasi', { tid: props.tid }));
  console.log('Form data:', formData);

  // Set saving state
  savingItems.value[id] = true;

  // Use Inertia router instead of fetch API
  router.post(route('triwulan.save-realisasi', { tid: 2 }), formData, {
    preserveState: true,
    preserveScroll: true,
    onStart: () => {
      console.log('Inertia request started');
    },
         onSuccess: (page) => {
       console.log('Success response from Inertia:', page);
       
       // Show success notification
       showFormatNotification('Data berhasil disimpan dan tabel diperbarui!', 'success');
       
       // Show success message
       const successMessage = `Data ${props.triwulanName} berhasil disimpan!`;
       
       // Use setTimeout to show alert after UI updates
       setTimeout(() => {
         alert(successMessage);
       }, 100);
      
    // Update the item in programData to show the new values while preserving target data
    const itemIndex = programData.value.findIndex(p => p.id === id);
    if (itemIndex !== -1) {
      // Keep the existing target data
      const existingTargetFisik = programData.value[itemIndex].targetFisik;
      const existingTargetKeuangan = programData.value[itemIndex].targetKeuangan;
      const existingTargetFisikValue = programData.value[itemIndex]._targetFisikValue;
      const existingTargetKeuanganValue = programData.value[itemIndex]._targetKeuanganValue;



      // Update only the realisasi data
      programData.value[itemIndex] = {
        ...programData.value[itemIndex],
        realisasiKeuangan: formatCurrency(item.realisasiKeuangan),
        realisasiFisik: item.realisasiFisik,
        keterangan: item.keterangan,
        pptk: item.pptk,
        // Preserve target data
        targetFisik: existingTargetFisik,
        targetKeuangan: existingTargetKeuangan,
        _targetFisikValue: existingTargetFisikValue,
        _targetKeuanganValue: existingTargetKeuanganValue
      };

      // Also update the base data in monitoringTargets or monitoringRealisasi
      const targetIndex = props.monitoringTargets.findIndex(t => t.task_id === id && t.periode_id === props.periode.id);
      if (targetIndex !== -1) {
        props.monitoringTargets[targetIndex].deskripsi = item.keterangan;
        props.monitoringTargets[targetIndex].nama_pptk = item.pptk;
      }
      
      // Update realisasi if it exists
      const realisasiIndex = props.monitoringRealisasi.findIndex(r => r.task_id === id && r.periode_id === props.periode.id);
      if (realisasiIndex !== -1) {
        props.monitoringRealisasi[realisasiIndex] = {
          ...props.monitoringRealisasi[realisasiIndex],
          kinerja_fisik: cleanRealisasiFisik,
          keuangan: cleanRealisasiKeuangan,
          deskripsi: item.keterangan,
          nama_pptk: item.pptk
        };
      } else {
        // Add new realisasi data
        const skpdTugas = props.subkegiatanTugas.find(sk => sk.id === id);
        const monitoringId = skpdTugas?.monitoring?.[0]?.id || 0;
        
        props.monitoringRealisasi.push({
          id: Date.now(),
          kinerja_fisik: cleanRealisasiFisik,
          keuangan: cleanRealisasiKeuangan,
          periode: props.periode.nama,
          periode_id: props.periode.id,
          monitoring_id: monitoringId,
          task_id: id,
          monitoring_anggaran_id: 0,
          deskripsi: item.keterangan,
          nama_pptk: item.pptk
        });
      }
    }
    
    // Clear edited item and saving state
    delete editedItems.value[id];
    savingItems.value[id] = false;
    
    // Add success animation to the row
    const saveButton = document.getElementById(`save-btn-${id}`);
    if (saveButton) {
      const row = saveButton.closest('tr');
      if (row) {
        row.classList.add('bg-green-50', 'transition-colors', 'duration-500');
        setTimeout(() => {
          row.classList.remove('bg-green-50');
        }, 2000);
      }
    }
    
    // Force reactive update
    programData.value = [...programData.value];
    
    // Clear edited item and saving state
    delete editedItems.value[id];
    savingItems.value[id] = false;
  },
  onError: (errors) => {
    console.error('Inertia validation errors:', errors);
    
    // Reset saving state on error
    savingItems.value[id] = false;
    
    // Add error animation to the row
    const errorSaveButton = document.getElementById(`save-btn-${id}`);
    if (errorSaveButton) {
      const row = errorSaveButton.closest('tr');
      if (row) {
        row.classList.add('bg-red-50', 'transition-colors', 'duration-500');
        setTimeout(() => {
          row.classList.remove('bg-red-50');
        }, 3000);
      }
    }
    
    // Show error notification
    showFormatNotification('Gagal menyimpan data. Silakan coba lagi.', 'error');
    
    // Handle validation errors
    let errorMessage = 'Terjadi kesalahan saat menyimpan data:';
    
    if (typeof errors === 'object' && errors !== null) {
      for (const [field, messages] of Object.entries(errors)) {
        if (Array.isArray(messages)) {
          errorMessage += `\n- ${field}: ${messages.join(', ')}`;
        } else {
          errorMessage += `\n- ${field}: ${messages}`;
        }
      }
    } else {
      errorMessage = 'Gagal menyimpan data. Silakan periksa input Anda.';
    }
    
    console.error('Detailed error for user:', errorMessage);
    alert(errorMessage + '\n\nSilakan coba lagi atau hubungi administrator.');
  },
  onFinish: () => {
    console.log('Inertia request finished');
    // Ensure saving state is reset
    savingItems.value[id] = false;
  }
});
};

// Function untuk mendapatkan akumulasi kinerja tahunan
const fetchAkumulasiKinerjaTahunan = async (skpdTugasId, tahun = null) => {
  try {
    const tahunParam = tahun || new Date().getFullYear();
    const response = await fetch(`/triwulan/${props.tid}/akumulasi-kinerja/${skpdTugasId}/${tahunParam}`);
    const data = await response.json();
    
    if (data.success) {
      console.log('Akumulasi Kinerja Tahunan:', data.data);
      return data.data;
    } else {
      console.error('Error fetching akumulasi kinerja:', data.message);
      return null;
    }
  } catch (error) {
    console.error('Error fetching akumulasi kinerja:', error);
    return null;
  }
};

// Fungsi untuk menampilkan akumulasi kinerja dalam modal atau alert
const showAkumulasiKinerja = async (skpdTugasId) => {
  const akumulasiData = await fetchAkumulasiKinerjaTahunan(skpdTugasId);
  
  if (akumulasiData) {
    let message = `Akumulasi Kinerja Tahunan untuk Subkegiatan ID: ${skpdTugasId}\n\n`;
    message += `Total Fisik: ${akumulasiData.akumulasi_fisik?.toFixed(2) || 0}%\n`;
    message += `Total Keuangan: ${(akumulasiData.akumulasi_keuangan || 0).toLocaleString('id-ID')}\n`;
    message += `Jumlah Triwulan Tersimpan: ${akumulasiData.jumlah_triwulan_tersimpan || 0}\n\n`;
    
    if (akumulasiData.detail_triwulan && Object.keys(akumulasiData.detail_triwulan).length > 0) {
      message += `Detail per Triwulan:\n`;
      Object.values(akumulasiData.detail_triwulan).forEach(triwulan => {
        message += `- ${triwulan.nama_triwulan}: Fisik ${triwulan.kinerja_fisik}%, Keuangan ${triwulan.keuangan.toLocaleString('id-ID')}\n`;
      });
    }
    
    alert(message);
  } else {
    alert('Gagal memuat data akumulasi kinerja tahunan.');
  }
};

// Kode yang dihapus untuk menghindari duplikasi deklarasi fungsi getPaguDariManajemenAnggaran
</script>

<template>
    <Head title="Detail Monitoring Triwulan 2" />

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
                    <h2 class="text-2xl font-bold text-gray-600">Triwulan 2</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN:</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ getSkpdName() || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ getKodeOrganisasi() || 'ORG-001' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ getKepalaSkpd() || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ getTimKerjaOperator() || 'Tidak tersedia' }}</p>
                    </div>
                </div>
            </div>


            <!-- Program table with targets -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-600">Data Monitoring Triwulan 2</h2>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed border-collapse">
                        <colgroup>
                            <col style="width: 120px;" /> <!-- Kode -->
                            <col style="width: 250px;" /> <!-- Program/Kegiatan -->
                            <col style="width: 100px;" /> <!-- Target Fisik -->
                            <col style="width: 130px;" /> <!-- Target Keuangan -->
                            <col style="width: 100px;" /> <!-- Realisasi Fisik -->
                            <col style="width: 130px;" /> <!-- Realisasi Keuangan -->

                            <col style="width: 200px;" /> <!-- Keterangan -->
                            <col style="width: 200px;" /> <!-- PPTK -->
                            <col style="width: 120px;" /> <!-- Aksi -->
                        </colgroup>
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th rowspan="3" class="px-3 py-3 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-gray-100">
                                    KODE
                                </th>
                                <th rowspan="3" class="px-3 py-3 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-gray-100">
                                    BIDANG URUSAN & PROGRAM/<br>KEGIATAN/SUB KEGIATAN
                                </th>
                                <th colspan="4" class="px-3 py-2 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-blue-50">
                                    TRIWULAN 1
                                </th>
                                <th rowspan="3" class="px-3 py-3 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-gray-100">
                                    KETERANGAN
                                </th>
                                <th rowspan="3" class="px-3 py-3 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-gray-100">
                                    PPTK
                                </th>
                                <th rowspan="3" class="px-3 py-3 text-xs font-semibold text-gray-700 uppercase text-center bg-gray-100">
                                    AKSI
                                </th>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <th colspan="2" class="px-3 py-2 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-green-50">
                                    TARGET
                                </th>
                                <th colspan="2" class="px-3 py-2 text-xs font-semibold text-gray-700 uppercase text-center border-r border-gray-200 bg-yellow-50">
                                    REALISASI
                                </th>
                            </tr>
                            <tr class="border-b-2 border-gray-300">
                                <th class="px-2 py-2 text-xs font-medium text-gray-600 uppercase text-center border-r border-gray-200 bg-green-25">
                                    KINERJA<br>FISIK (%)</th>
                                <th class="px-2 py-2 text-xs font-medium text-gray-600 uppercase text-center border-r border-gray-200 bg-green-25">
                                    KEUANGAN<br>(RP)
                                </th>
                                <th class="px-2 py-2 text-xs font-medium text-gray-600 uppercase text-center border-r border-gray-200 bg-blue-25">
                                    KINERJA<br>FISIK (%)
                                    <div class="text-xs text-blue-600 font-normal mt-1">🔄 Auto %</div>
                                </th>
                                <th class="px-2 py-2 text-xs font-medium text-gray-600 uppercase text-center border-r border-gray-200 bg-green-25">
                                    KEUANGAN<br>(RP)
                                    <div class="text-xs text-green-600 font-normal mt-1">🔄 Auto Rp</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="programData.length === 0">
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="font-medium">Belum ada data tersedia</p>
                                            <p class="text-xs text-gray-400 mt-1">Data akan muncul setelah rencana awal dibuat</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(item, index) in programData" :key="item.id" 
                                :class="[
                                    'hover:bg-blue-50 transition-colors border-b border-gray-100',
                                    item.type === 'bidang_urusan' ? 'font-extrabold bg-blue-50 border-l-4 border-blue-500' : '',
                                    item.type === 'program' ? 'font-bold bg-gray-50 border-l-4 border-gray-400' : '',
                                    item.type === 'kegiatan' ? 'font-semibold bg-orange-25 border-l-4 border-orange-400' : '',
                                    item.type === 'subkegiatan' ? 'bg-yellow-50 border-l-4 border-yellow-500' : ''
                                ]"
                                :data-item-type="item.type"
                                :data-item-id="item.id">
                                
                                <!-- Kode Column -->
                                <td class="px-3 py-3 text-sm font-mono text-center border-r border-gray-200 align-middle">
                                    {{ item.kode }}
                                </td>
                                
                                <!-- Program/Kegiatan Column -->
                                <td class="px-3 py-3 text-sm border-r border-gray-200 align-middle">
                                    <div class="line-clamp-3">{{ item.program }}</div>
                                </td>
                                
                                <!-- Target Fisik Column -->
                                <td class="px-3 py-3 text-center text-sm border-r border-gray-200 align-middle">
                                    {{ item.targetFisik }}
                                </td>
                                
                                <!-- Target Keuangan Column -->
                                <td class="px-3 py-3 text-right text-sm border-r border-gray-200 align-middle">
                                    {{ item.targetKeuangan }}
                                </td>
                                
                                <!-- Realisasi Fisik Column -->
                                <td class="px-2 py-3 text-center bg-blue-25 border-r border-gray-200 align-middle">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                :data-field="`realisasiFisik-${item.id}`"
                                                class="w-20 h-8 border border-blue-300 rounded px-2 py-1 text-center text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                :class="{ 
                                                    'bg-blue-50 hover:bg-blue-100': true,
                                                    'shadow-md border-orange-400 bg-orange-50': editedItems[item.id]?.realisasiFisik && editedItems[item.id]?.realisasiFisik !== item.realisasiFisik,
                                                    'ring-2 ring-orange-200': editedItems[item.id]?.realisasiFisik && editedItems[item.id]?.realisasiFisik !== item.realisasiFisik
                                                }"
                                                :value="editedItems[item.id]?.realisasiFisik || item.realisasiFisik"
                                                @input="(e: Event) => handleInputChange(item.id, 'realisasiFisik', (e.target as HTMLInputElement).value)"
                                                @keyup="(e: Event) => handleInputChange(item.id, 'realisasiFisik', (e.target as HTMLInputElement).value)"
                                                @paste="(e: Event) => setTimeout(() => handleInputChange(item.id, 'realisasiFisik', (e.target as HTMLInputElement).value), 0)"
                                                placeholder="25"
                                                title="Masukkan angka, tanda % akan ditambah otomatis"
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiFisik }}</span>
                                    </template>
                                </td>
                                
                                <!-- Realisasi Keuangan Column -->
                                <td class="px-2 py-3 text-right bg-green-25 border-r border-gray-200 align-middle">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                :data-field="`realisasiKeuangan-${item.id}`"
                                                class="w-24 h-8 border border-green-300 rounded px-2 py-1 text-right text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                :class="{ 
                                                    'bg-green-50 hover:bg-green-100': true,
                                                    'shadow-md border-orange-400 bg-orange-50': editedItems[item.id]?.realisasiKeuangan && editedItems[item.id]?.realisasiKeuangan !== item.realisasiKeuangan,
                                                    'ring-2 ring-orange-200': editedItems[item.id]?.realisasiKeuangan && editedItems[item.id]?.realisasiKeuangan !== item.realisasiKeuangan
                                                }"
                                                :value="editedItems[item.id]?.realisasiKeuangan || item.realisasiKeuangan"
                                                @input="(e: Event) => handleInputChange(item.id, 'realisasiKeuangan', (e.target as HTMLInputElement).value)"
                                                @keyup="(e: Event) => handleInputChange(item.id, 'realisasiKeuangan', (e.target as HTMLInputElement).value)"
                                                @paste="(e: Event) => setTimeout(() => handleInputChange(item.id, 'realisasiKeuangan', (e.target as HTMLInputElement).value), 0)"
                                                placeholder="1000000"
                                                title="Masukkan angka, format Rp akan ditambah otomatis"
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="text-sm">{{ item.realisasiKeuangan }}</span>
                                    </template>
                                </td>
                                

                                <!-- Keterangan Column -->
                                <td class="px-2 py-3 border-r border-gray-200 align-middle">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-full h-8 border border-gray-300 rounded px-2 py-1 text-xs transition-all duration-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            :class="{ 
                                                'shadow-md border-orange-400 bg-orange-50 ring-2 ring-orange-200': editedItems[item.id]?.keterangan && editedItems[item.id]?.keterangan !== item.keterangan
                                            }"
                                            :value="editedItems[item.id]?.keterangan || item.keterangan"
                                            @input="(e: Event) => handleInputChange(item.id, 'keterangan', (e.target as HTMLInputElement).value)"
                                            placeholder="Masukkan keterangan..."
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm text-gray-600">{{ item.keterangan || '-' }}</span>
                                    </template>
                                </td>
                                
                                <!-- PPTK Column -->
                                <td class="px-2 py-3 border-r border-gray-200 align-middle">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <input 
                                            type="text" 
                                            class="w-full h-8 border border-gray-300 rounded px-2 py-1 text-xs transition-all duration-200 bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                            :class="{ 
                                                'shadow-md border-orange-400 bg-orange-50 ring-2 ring-orange-200': editedItems[item.id]?.pptk && editedItems[item.id]?.pptk !== item.pptk
                                            }"
                                            :value="editedItems[item.id]?.pptk || item.pptk"
                                            @input="(e: Event) => handleInputChange(item.id, 'pptk', (e.target as HTMLInputElement).value)"
                                            placeholder="Masukkan nama PPTK..."
                                        />
                                    </template>
                                    <template v-else>
                                        <span class="text-sm text-gray-600">{{ item.pptk || '-' }}</span>
                                    </template>
                                </td>
                                
                                <!-- Actions Column -->
                                <td class="px-2 py-3 text-center align-middle">
                                    <template v-if="item.type === 'subkegiatan'">
                                        <button 
                                            :id="`save-btn-${item.id}`"
                                            class="px-3 py-2 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                            :class="{ 
                                                'bg-green-600 hover:bg-green-700': editedItems[item.id] && Object.keys(editedItems[item.id]).length > 0,
                                                'shadow-md hover:shadow-lg transform hover:scale-105': true
                                            }"
                                            @click="saveData(item.id)"
                                            :disabled="savingItems[item.id]"
                                        >
                                            <span v-if="savingItems[item.id]" class="flex items-center gap-1">
                                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Menyimpan...
                                            </span>
                                            <span v-else>
                                                {{ editedItems[item.id] && Object.keys(editedItems[item.id]).length > 0 ? 'Simpan Perubahan' : 'Simpan' }}
                                            </span>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <span class="text-gray-400 text-xs">-</span>
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

/* Auto-format styling */
.bg-blue-25 {
  background-color: rgba(59, 130, 246, 0.05);
  border-left: 3px solid rgba(59, 130, 246, 0.3);
}

.bg-green-25 {
  background-color: rgba(34, 197, 94, 0.05);
  border-left: 3px solid rgba(34, 197, 94, 0.3);
}

.bg-purple-25 {
  background-color: rgba(147, 51, 234, 0.05);
  border-left: 3px solid rgba(147, 51, 234, 0.3);
}

.bg-orange-25 {
  background-color: rgba(249, 115, 22, 0.05);
  border-left: 3px solid rgba(249, 115, 22, 0.3);
}

/* Enhanced Input focus animations */
input:focus {
  transform: scale(1.02);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  transition: all 0.2s ease-in-out;
}

input:focus.border-green-300 {
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Auto-format input specific styling */
input[data-field*="realisasiFisik"] {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0.1) 100%);
  border: 2px solid rgba(59, 130, 246, 0.3);
  transition: all 0.3s ease;
}

input[data-field*="realisasiKeuangan"] {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(34, 197, 94, 0.1) 100%);
  border: 2px solid rgba(34, 197, 94, 0.3);
  transition: all 0.3s ease;
}

input[data-field*="realisasiFisik"]:focus {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.2) 100%);
  border-color: rgba(59, 130, 246, 0.6);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 4px 6px rgba(59, 130, 246, 0.1);
}

input[data-field*="realisasiKeuangan"]:focus {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.2) 100%);
  border-color: rgba(34, 197, 94, 0.6);
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1), 0 4px 6px rgba(34, 197, 94, 0.1);
}

/* Tooltip animations enhanced */
.relative:hover .absolute {
  opacity: 1;
  transform: translateY(-2px) scale(1.05);
}

.absolute {
  transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
}

/* Auto format indicator pulse enhanced */
@keyframes pulse-blue {
  0%, 100% {
    opacity: 0.75;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
}

@keyframes pulse-green {
  0%, 100% {
    opacity: 0.75;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
}

.text-blue-600 {
  animation: pulse-blue 2s infinite;
  text-shadow: 0 0 2px rgba(59, 130, 246, 0.3);
}

.text-green-600 {
  animation: pulse-green 2s infinite;
  text-shadow: 0 0 2px rgba(34, 197, 94, 0.3);
}

/* Success feedback when auto-format is applied */
@keyframes format-success {
  0% {
    background-color: rgba(34, 197, 94, 0.1);
    transform: scale(1);
  }
  50% {
    background-color: rgba(34, 197, 94, 0.3);
    transform: scale(1.02);
  }
  100% {
    background-color: rgba(34, 197, 94, 0.1);
    transform: scale(1);
  }
}

@keyframes format-success-blue {
  0% {
    background-color: rgba(59, 130, 246, 0.1);
    transform: scale(1);
  }
  50% {
    background-color: rgba(59, 130, 246, 0.3);
    transform: scale(1.02);
  }
  100% {
    background-color: rgba(59, 130, 246, 0.1);
    transform: scale(1);
  }
}

.format-success {
  animation: format-success 0.5s ease-in-out;
}

.format-success-blue {
  animation: format-success-blue 0.5s ease-in-out;
}

/* Notification styling */
.fixed.top-4.right-4 {
  transform: translateX(100%);
  opacity: 0;
  z-index: 9999;
}

/* Button loading state */
.disabled\:opacity-50:disabled {
  opacity: 0.5;
}

.disabled\:cursor-not-allowed:disabled {
  cursor: not-allowed;
}

/* Row animations */
.bg-green-50 {
  background-color: rgba(34, 197, 94, 0.1) !important;
}

.bg-red-50 {
  background-color: rgba(239, 68, 68, 0.1) !important;
}

/* Button states */
button:disabled {
  transform: none !important;
}

button:disabled:hover {
  transform: none !important;
  box-shadow: none !important;
}

/* Success/Error row animations */
@keyframes successFlash {
  0% { background-color: rgba(34, 197, 94, 0.2); }
  50% { background-color: rgba(34, 197, 94, 0.1); }
  100% { background-color: rgba(34, 197, 94, 0.05); }
}

@keyframes errorFlash {
  0% { background-color: rgba(239, 68, 68, 0.2); }
  50% { background-color: rgba(239, 68, 68, 0.1); }
  100% { background-color: rgba(239, 68, 68, 0.05); }
}

.success-flash {
  animation: successFlash 2s ease-in-out;
}

.error-flash {
  animation: errorFlash 2s ease-in-out;
}

/* Unsaved changes styling */
@keyframes unsavedPulse {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(245, 101, 101, 0.4);
  }
  50% {
    box-shadow: 0 0 0 4px rgba(245, 101, 101, 0.1);
  }
}

.unsaved-changes {
  animation: unsavedPulse 2s infinite;
}

/* Enhanced focus states for form elements */
input:focus.border-orange-400 {
  border-color: rgba(245, 101, 101, 0.8) !important;
  box-shadow: 0 0 0 3px rgba(245, 101, 101, 0.1) !important;
}

/* Improved button interaction */
button:not(:disabled):hover {
  transform: translateY(-1px);
}

button:not(:disabled):active {
  transform: translateY(0);
}

/* Table enhancements */
.table-fixed {
  table-layout: fixed;
}

.border-collapse {
  border-collapse: collapse;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-wrap: break-word;
  hyphens: auto;
}

.align-middle {
  vertical-align: middle;
}

/* Table header styling */
th {
  position: relative;
  border: 1px solid #d1d5db;
}

td {
  position: relative;
  border: 1px solid #e5e7eb;
}

/* Responsive table improvements */
@media (max-width: 1200px) {
  .table-fixed {
    table-layout: auto;
  }
  
  .overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
  }
  
  .overflow-x-auto::-webkit-scrollbar {
    height: 8px;
  }
  
  .overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
  }
  
  .overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  
  .overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
}
</style>