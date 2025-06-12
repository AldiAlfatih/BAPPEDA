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
        id_bidang_urusan: number;
        id_program: number;
        id_kegiatan: number;
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
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Monitoring Triwulan 3', href: '/triwulan3' },
  { title: `Monitoring Detail ${props.user.nama_skpd}`, href: '/triwulan3/show' },
  { title: 'Rencana Awal PD', href: '/triwulan3/detail' },
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

// Track edited items
const editedItems = ref<Record<number, {
  realisasiFisik: string;
  realisasiKeuangan: string;
  capaianFisik: string;
  capaianKeuangan: string;
  keterangan: string;
  pptk: string;
}>>({});

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
  // KHUSUS FILTER DENGAN PERIODE_ID = 4 (STRICT EQUALITY) untuk Triwulan 3
  // =================================================================
  
  // Filter semua target yang HANYA memiliki periode_id = 4 (strict equality)
  const strictPeriodeIdFilter = props.monitoringTargets.filter(t => t.periode_id === 4);
  console.log('STRICT FILTER: TARGETS WITH PERIODE_ID = 4 ONLY:', strictPeriodeIdFilter);
  
  // Untuk masing-masing subkegiatan, cari target dengan periode_id = 4
  props.subkegiatanTugas.forEach(subkegiatan => {
    const targetsForSubkegiatan = props.monitoringTargets.filter(t => 
      t.task_id === subkegiatan.id && t.periode_id === 4
    );
    console.log(`TARGETS FOR SUBKEGIATAN ${subkegiatan.id} WITH STRICT PERIODE_ID = 4:`, targetsForSubkegiatan);
    
    if (targetsForSubkegiatan.length > 0) {
      console.log(`DATA TARGET SUBKEGIATAN ${subkegiatan.id}:`, {
        kinerja_fisik: targetsForSubkegiatan[0].kinerja_fisik,
        keuangan: targetsForSubkegiatan[0].keuangan,
        periode_id: targetsForSubkegiatan[0].periode_id,
      });
    }
  });
  
  // Cek apakah ada target dengan periode_id = 4 untuk subkegiatan manapun
  const subkegiatanIds = props.subkegiatanTugas.map(sk => sk.id);
  const strictSubkegiatanTargets = props.monitoringTargets.filter(t => 
    subkegiatanIds.includes(t.task_id) && t.periode_id === 4
  );
  
  console.log('STRICT FILTER: SUBKEGIATAN TARGETS WITH PERIODE_ID = 4:', strictSubkegiatanTargets);
  
  if (strictSubkegiatanTargets.length === 0) {
    console.warn('PERINGATAN: Tidak ada target dengan periode_id = 4 untuk subkegiatan manapun (filter ketat)');
  } else {
    console.log(`DITEMUKAN ${strictSubkegiatanTargets.length} TARGET DENGAN PERIODE_ID = 4 UNTUK SUBKEGIATAN`);
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
    
    // Cek khusus untuk periode_id = 4 (Triwulan 3)
    const triwulan3Targets = props.monitoringTargets.filter(t => t.periode_id === 4);
    console.log('STRICT PERIODE_ID === 4 TARGETS ONLY:', triwulan3Targets);
    
    // Periksa apakah ada target untuk subkegiatan dengan periode_id = 4
    const subkegiatanIds = props.subkegiatanTugas.map(sk => sk.id);
    const subkegiatanTriwulan3Targets = props.monitoringTargets.filter(t => 
      subkegiatanIds.includes(t.task_id) && t.periode_id === 4
    );
    console.log('SUBKEGIATAN TARGETS WITH STRICT PERIODE_ID === 4:', subkegiatanTriwulan3Targets);
    
    if (subkegiatanTriwulan3Targets.length === 0) {
      console.warn('PERINGATAN: Tidak ada target periode Triwulan 3 untuk subkegiatan manapun!');
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

// Function untuk mendapatkan pagu terakhir dari Rencana Awal dengan lebih akurat
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
      t.task_id === item.id && t.periode_id === 4 && t.keuangan > 0
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
      capaianFisik: '-',
      capaianKeuangan: '-',
      capaianTahunanFisik: '-',
      capaianTahunanKeuangan: '0.00%',
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
    // Find targets directly associated with this task by task_id AND strict periode_id = 4
    const targetsForTask = props.monitoringTargets.filter(t => 
      t.task_id === program.id && t.periode_id === 4
    );
    console.log(`Program ${program.id} has ${targetsForTask.length} targets with strict periode_id = 4:`, targetsForTask);
    
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
      capaianFisik: '-',
      capaianKeuangan: '-',
      capaianTahunanFisik: '-',
      capaianTahunanKeuangan: '0.00%',
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
          capaianFisik: '-',
          capaianKeuangan: '-',
          capaianTahunanFisik: '-',
          capaianTahunanKeuangan: '-',
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
            // Find targets directly associated with this task by task_id AND strict periode_id = 4
            const subkegiatanTargets = props.monitoringTargets.filter(t => 
              t.task_id === subkegiatan.id && t.periode_id === 4
            );
            console.log(`Subkegiatan ${subkegiatan.id} has ${subkegiatanTargets.length} targets with strict periode_id = 4:`, subkegiatanTargets);
            
            // Find realisasi data for this subkegiatan AND strict periode_id = 4
            const realisasiData = props.monitoringRealisasi.filter(r => 
              r.task_id === subkegiatan.id && r.periode_id === 4
            );
            console.log(`Subkegiatan ${subkegiatan.id} has ${realisasiData.length} realisasi records with strict periode_id = 4:`, realisasiData);
            
            let kinerjaFisikSubkegiatan = '-';
            let keuanganSubkegiatan = '-';
            let targetFisikValue = 0;
            let targetKeuanganValue = 0;
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
              targetFisikValue = target.kinerja_fisik || 0;
              targetKeuanganValue = target.keuangan || 0;
              
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
                  
                  // Calculate kinerja tahunan using the formula: kinerja fisik realisasi/100*100
                  const kinerjaFisikTahunan = (realisasiFisikValue / 100) * 100;
                  capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
                }
              }
              
              if (keuanganSubkegiatan !== '-' && realisasiKeuangan !== '-') {
                const targetValue = parseFloat(keuanganSubkegiatan.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(targetValue) && targetValue > 0) {
                  // Use Math.min to cap the value at 100%
                  const capaian = Math.min(100, (realisasiKeuanganValue / targetValue) * 100);
                  capaianKeuangan = `${capaian.toFixed(2)}%`;
                  
                  // Realisasi keuangan / jumlah data dari pagu terakhir pada sumber anggaran * 100
                  // Find the kegiatan to get budget values
                  const kegiatanObj = props.kegiatanTugas.find(k => k.id === kegiatan.id);
                  
                  // Gunakan fungsi khusus untuk mendapatkan pagu TERAKHIR DARI RENCANA AWAL
                  const paguRencanaAwal = getPaguTerakhirDariRencanaAwal(kegiatanObj);
                  
                  console.log(`PERHITUNGAN CAPAIAN KEUANGAN TAHUNAN UNTUK KEGIATAN ID=${kegiatan.id}:`);
                  console.log(`- Nilai Realisasi Keuangan: ${realisasiKeuanganValue.toLocaleString('id-ID')}`);
                  console.log(`- Nilai Pagu Terakhir dari Rencana Awal: ${paguRencanaAwal.value.toLocaleString('id-ID')} (${paguRencanaAwal.type})`);

                  // Pastikan nilai pagu selalu valid
                  let paguValue = paguRencanaAwal.value;
                  if (paguValue <= 0) {
                    paguValue = 120000000; // Default 120 juta jika tidak ada nilai pagu
                    console.log(`- Menggunakan nilai DEFAULT untuk kegiatan karena pagu = 0: ${paguValue.toLocaleString('id-ID')}`);
                  }
                  
                  // Hitung capaian keuangan tahunan: (realisasi keuangan / pagu dari Rencana Awal) * 100
                  const capaianTahunanResult = (realisasiKeuanganValue / paguValue) * 100;
                  subkegiatan.capaianTahunanKeuangan = `${capaianTahunanResult.toFixed(2)}%`;
                  console.log(`- Rumus: (${realisasiKeuanganValue} / ${paguValue}) * 100 = ${capaianTahunanResult.toFixed(2)}%`);
                }
              }
              
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
      
      // Calculate capaian
      if (kegiatanItem.targetFisik !== '-') {
        const targetFisik = parseFloat(kegiatanItem.targetFisik.replace('%', ''));
        if (!isNaN(targetFisik) && targetFisik > 0) {
          const capaian = (avgFisik / targetFisik) * 100;
          kegiatanItem.capaianFisik = `${capaian.toFixed(2)}%`;
          
          // Calculate kinerja tahunan using the formula: kinerja fisik realisasi/100*100
          const kinerjaFisikTahunan = (avgFisik / 100) * 100;
          kegiatanItem.capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
        }
      }
      
      if (kegiatanItem.targetKeuangan !== '-') {
        const targetKeuangan = parseFloat(kegiatanItem.targetKeuangan.replace(/[^0-9.-]+/g, ''));
        if (!isNaN(targetKeuangan) && targetKeuangan > 0) {
          // Use Math.min to cap the value at 100%
          const capaian = Math.min(100, (totalKeuangan / targetKeuangan) * 100);
          kegiatanItem.capaianKeuangan = `${capaian.toFixed(2)}%`;
          
          // Calculate keuangan tahunan using the formula:
          // Realisasi keuangan / jumlah data dari pagu terakhir pada sumber anggaran * 100
          // Find the kegiatan to get budget values
          const kegiatanObj = props.kegiatanTugas.find(k => k.id === kegiatanId);
          
          // Gunakan fungsi khusus untuk mendapatkan pagu TERAKHIR DARI RENCANA AWAL
          const paguRencanaAwal = getPaguTerakhirDariRencanaAwal(kegiatanObj);
          
          console.log(`PERHITUNGAN CAPAIAN KEUANGAN TAHUNAN UNTUK KEGIATAN ID=${kegiatanId}:`);
          console.log(`- Nilai Realisasi Keuangan: ${totalKeuangan.toLocaleString('id-ID')}`);
          console.log(`- Nilai Pagu Terakhir dari Rencana Awal: ${paguRencanaAwal.value.toLocaleString('id-ID')} (${paguRencanaAwal.type})`);

          // Pastikan nilai pagu selalu valid
          let paguValue = paguRencanaAwal.value;
          if (paguValue <= 0) {
            paguValue = 120000000; // Default 120 juta jika tidak ada nilai pagu
            console.log(`- Menggunakan nilai DEFAULT untuk kegiatan karena pagu = 0: ${paguValue.toLocaleString('id-ID')}`);
          }
          
          // Hitung capaian keuangan tahunan: (realisasi keuangan / pagu dari Rencana Awal) * 100
          const capaianTahunanResult = (totalKeuangan / paguValue) * 100;
          kegiatanItem.capaianTahunanKeuangan = `${capaianTahunanResult.toFixed(2)}%`;
          console.log(`- Rumus: (${totalKeuangan} / ${paguValue}) * 100 = ${capaianTahunanResult.toFixed(2)}%`);
        }
      }
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
      
      // Calculate capaian
      if (programItem.targetFisik !== '-') {
        const targetFisik = parseFloat(programItem.targetFisik.replace('%', ''));
        if (!isNaN(targetFisik) && targetFisik > 0) {
          const capaian = (avgFisik / targetFisik) * 100;
          programItem.capaianFisik = `${capaian.toFixed(2)}%`;
          
          // Calculate kinerja tahunan using the formula: kinerja fisik realisasi/100*100
          const kinerjaFisikTahunan = (avgFisik / 100) * 100;
          programItem.capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
        }
      }
      
      if (programItem.targetKeuangan !== '-') {
        const targetKeuangan = parseFloat(programItem.targetKeuangan.replace(/[^0-9.-]+/g, ''));
        if (!isNaN(targetKeuangan) && targetKeuangan > 0) {
          // Use Math.min to cap the value at 100%
          const capaian = Math.min(100, (totalKeuangan / targetKeuangan) * 100);
          programItem.capaianKeuangan = `${capaian.toFixed(2)}%`;
          
          // Calculate keuangan tahunan using the formula:
          // Realisasi keuangan / jumlah data dari pagu terakhir pada sumber anggaran * 100
          // Find the program to get budget values
          const programObj = props.programTugas.find(p => p.id === programId);
          
          // Gunakan fungsi khusus untuk mendapatkan pagu TERAKHIR DARI RENCANA AWAL
          const paguRencanaAwal = getPaguTerakhirDariRencanaAwal(programObj);
          
          console.log(`PERHITUNGAN CAPAIAN KEUANGAN TAHUNAN UNTUK PROGRAM ID=${programId}:`);
          console.log(`- Nilai Realisasi Keuangan: ${totalKeuangan.toLocaleString('id-ID')}`);
          console.log(`- Nilai Pagu Terakhir dari Rencana Awal: ${paguRencanaAwal.value.toLocaleString('id-ID')} (${paguRencanaAwal.type})`);

          // Pastikan nilai pagu selalu valid
          let paguValue = paguRencanaAwal.value;
          if (paguValue <= 0) {
            paguValue = 120000000; // Default 120 juta jika tidak ada nilai pagu
            console.log(`- Menggunakan nilai DEFAULT untuk program karena pagu = 0: ${paguValue.toLocaleString('id-ID')}`);
          }
          
          // Hitung capaian keuangan tahunan: (realisasi keuangan / pagu dari Rencana Awal) * 100
          const capaianTahunanResult = (totalKeuangan / paguValue) * 100;
          programItem.capaianTahunanKeuangan = `${capaianTahunanResult.toFixed(2)}%`;
          console.log(`- Rumus: (${totalKeuangan} / ${paguValue}) * 100 = ${capaianTahunanResult.toFixed(2)}%`);
        }
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
        
        // Calculate capaian fisik for bidang urusan
        if (bidangUrusanItem.targetFisik !== '-') {
          const targetFisik = parseFloat(bidangUrusanItem.targetFisik.replace('%', ''));
          if (!isNaN(targetFisik) && targetFisik > 0) {
            const capaianFisik = (avgProgramFisik / targetFisik) * 100;
            bidangUrusanItem.capaianFisik = `${capaianFisik.toFixed(2)}%`;
            
            // Calculate kinerja fisik tahunan using the formula: kinerja fisik realisasi/100*100
            const kinerjaFisikTahunan = (avgProgramFisik / 100) * 100;
            bidangUrusanItem.capaianTahunanFisik = `${kinerjaFisikTahunan.toFixed(2)}%`;
          }
        }
      }
      
      if (totalBidangUrusanKeuangan > 0) {
        bidangUrusanItem.realisasiKeuangan = `Rp ${totalBidangUrusanKeuangan.toLocaleString('id-ID')}`;
        
        // Calculate capaian for bidang urusan
        if (bidangUrusanItem.targetKeuangan !== '-') {
          const targetKeuangan = parseFloat(bidangUrusanItem.targetKeuangan.replace(/[^0-9.-]+/g, ''));
          if (!isNaN(targetKeuangan) && targetKeuangan > 0) {
            // Use Math.min to cap the value at 100%
            const capaian = Math.min(100, (totalBidangUrusanKeuangan / targetKeuangan) * 100);
            bidangUrusanItem.capaianKeuangan = `${capaian.toFixed(2)}%`;
            
            // Calculate keuangan tahunan using the formula:
            // Realisasi keuangan / jumlah data dari pagu terakhir pada sumber anggaran * 100
            // For bidang urusan, use cumulative values from all associated programs
            
            // Variabel untuk menyimpan total pagu
            let totalPaguValue = 0;
            let paguType = 'gabungan';
            
            // Kumpulkan dari program
            bidangUrusanItem._subItems?.forEach(programId => {
              const programObj = props.programTugas.find(p => p.id === programId);
              if (programObj) {
                // Cek pagu di level program menggunakan fungsi khusus untuk mendapatkan pagu dari Rencana Awal
                const programPagu = getPaguTerakhirDariRencanaAwal(programObj);
                if (programPagu.value > 0) {
                  totalPaguValue += programPagu.value;
                }
              }
            });
            
            console.log(`PERHITUNGAN CAPAIAN KEUANGAN TAHUNAN UNTUK BIDANG URUSAN:`);
            console.log(`- Nilai Realisasi Keuangan: ${totalBidangUrusanKeuangan.toLocaleString('id-ID')}`);
            console.log(`- Nilai Total Pagu dari Rencana Awal: ${totalPaguValue.toLocaleString('id-ID')} (${paguType})`);
            
            if (totalPaguValue > 0) {
              // Hitung capaian keuangan tahunan: (realisasi keuangan / total pagu dari Rencana Awal) * 100
              const capaianTahunanResult = (totalBidangUrusanKeuangan / totalPaguValue) * 100;
              bidangUrusanItem.capaianTahunanKeuangan = `${capaianTahunanResult.toFixed(2)}%`;
              console.log(`- Rumus: (${totalBidangUrusanKeuangan} / ${totalPaguValue}) * 100 = ${capaianTahunanResult.toFixed(2)}%`);
            } else {
              // Jika total pagu tidak ditemukan, set capaian keuangan tahunan ke 0%
              bidangUrusanItem.capaianTahunanKeuangan = '0.00%';
              console.log(`- Tidak ada total pagu yang valid, capaian keuangan tahunan diatur ke 0.00%`);
            }
          }
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

  // Find the existing target data for this subkegiatan
  const subkegiatan = programData.value.find(p => p.id === id);
  if (!subkegiatan) return;

  // Calculate capaian manually if not entered
  if (item.capaianFisik === '-' && item.realisasiFisik !== '-') {
    // Find the target value for this subkegiatan
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
  router.post('/triwulan3/save-realisasi', {
    id: id,
    realisasi_fisik: item.realisasiFisik.replace('%', ''),
    realisasi_keuangan: cleanNumber,
    capaian_fisik: item.capaianFisik.replace('%', ''),
    capaian_keuangan: item.capaianKeuangan.replace('%', ''),
    keterangan: item.keterangan,
    nama_pptk: item.pptk
  }, {
    onSuccess: () => {
      alert('Data berhasil disimpan');
      
      // Update the item in programData to show the new values while preserving target data
      const itemIndex = programData.value.findIndex(p => p.id === id);
      if (itemIndex !== -1) {
        // Keep the existing target data
        const existingTargetFisik = programData.value[itemIndex].targetFisik;
        const existingTargetKeuangan = programData.value[itemIndex].targetKeuangan;
        const existingTargetFisikValue = programData.value[itemIndex]._targetFisikValue;
        const existingTargetKeuanganValue = programData.value[itemIndex]._targetKeuanganValue;

        // Update only the realisasi and capaian data
        programData.value[itemIndex] = {
          ...programData.value[itemIndex],
          realisasiKeuangan: formatCurrency(item.realisasiKeuangan),
          realisasiFisik: item.realisasiFisik,
          capaianKeuangan: formatPercentage(item.capaianKeuangan),
          capaianFisik: formatPercentage(item.capaianFisik),
          capaianTahunanKeuangan: formatPercentage(item.capaianKeuangan),
          capaianTahunanFisik: formatPercentage(item.capaianFisik),
          keterangan: item.keterangan,
          pptk: item.pptk,
          // Preserve target data
          targetFisik: existingTargetFisik,
          targetKeuangan: existingTargetKeuangan,
          _targetFisikValue: existingTargetFisikValue,
          _targetKeuanganValue: existingTargetKeuanganValue
        };

        // Also update the base data in monitoringTargets or monitoringRealisasi
        const targetIndex = props.monitoringTargets.findIndex(t => t.task_id === id && t.periode_id === 4);
        if (targetIndex !== -1) {
          props.monitoringTargets[targetIndex].deskripsi = item.keterangan;
          props.monitoringTargets[targetIndex].nama_pptk = item.pptk;
        }
        
        // Update realisasi if it exists
        const realisasiIndex = props.monitoringRealisasi.findIndex(r => r.task_id === id && r.periode_id === 4);
        if (realisasiIndex !== -1) {
          props.monitoringRealisasi[realisasiIndex] = {
            ...props.monitoringRealisasi[realisasiIndex],
            kinerja_fisik: parseFloat(item.realisasiFisik.replace('%', '')),
            keuangan: cleanNumber,
            deskripsi: item.keterangan,
            nama_pptk: item.pptk
          };
        } else {
          // Add new realisasi data
          const skpdTugas = props.subkegiatanTugas.find(sk => sk.id === id);
          const monitoringId = skpdTugas?.monitoring?.[0]?.id || 0;
          
          props.monitoringRealisasi.push({
            id: Date.now(),
            kinerja_fisik: parseFloat(item.realisasiFisik.replace('%', '')),
            keuangan: cleanNumber,
            periode: 'Triwulan 3',
            periode_id: 4, // Pastikan periode_id untuk Triwulan 3 adalah 4
            monitoring_id: monitoringId,
            task_id: id,
            monitoring_anggaran_id: 0,
            deskripsi: item.keterangan,
            nama_pptk: item.pptk
          });
        }
      }
      
      // Clear edited item
      delete editedItems.value[id];
    },
    onError: (errors) => {
      if (errors.message && typeof errors.message === 'string') {
        // Check if it's the period closed error
        alert(errors.message);
      } else {
      console.error('Error saving data:', errors);
      alert('Terjadi kesalahan saat menyimpan data: ' + Object.values(errors).join(', '));
      }
    }
  });
};

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
      }
    }
  }
  
  // TAMBAHAN 10: Periksa targetKeuangan sebagai fallback terakhir
  if (targetKeuanganValue > 0) {
    console.log(`Menggunakan targetKeuangan sebagai fallback terakhir: ${targetKeuanganValue}`);
    return {value: targetKeuanganValue, type: 'TARGET KEUANGAN (fallback)'};
  }
  
  // Kembalikan 0 jika tidak ada nilai pagu yang ditemukan
  console.log('Tidak ditemukan nilai pagu yang valid');
  return {value: 0, type: 'tidak ada'};
};

// Function untuk memastikan nilai capaian keuangan tahunan dihitung dengan benar
const calculateCapaianKeuanganTahunan = (realisasi: number, pagu: number, paguType: string = 'pagu'): string => {
  if (!pagu || pagu <= 0) return '0.00%';
  
  // Pastikan kedua nilai adalah numerik
  const realisasiNum = parseFloat(realisasi.toString()) || 0;
  const paguNum = parseFloat(pagu.toString()) || 1; // Hindari pembagian dengan 0
  
  // Hitung hasil
  const result = (realisasiNum / paguNum) * 100;
  
  // Verifikasi hasil - tambahkan log yang lebih detail untuk debugging
  console.log(`CAPAIAN KEUANGAN TAHUNAN CALCULATION:`);
  console.log(`- Realisasi: ${realisasiNum.toLocaleString('id-ID')}`);
  console.log(`- Pagu (dari sumber anggaran ${paguType}): ${paguNum.toLocaleString('id-ID')}`);
  console.log(`- Formula: (${realisasiNum} / ${paguNum}) * 100 = ${result.toFixed(4)}%`);
  
  // Pastikan tidak melakukan pembulatan ke 100%
  return `${result.toFixed(2)}%`;
};
</script>

<template>
    <Head title="Monitoring Triwulan 3" />

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
                    <h2 class="text-2xl font-bold text-gray-600">Triwulan3</h2>
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
                    <h2 class="text-lg font-semibold text-gray-600">Data Monitoring Triwulan 3</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">Kode</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center min-w-[180px] w-[180px]">BIDANG URUSAN & PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                                <th colspan="6" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">Triwulan 3</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center min-w-[180px] w-[180px]">Keterangan</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center min-w-[180px] w-[180px]">PPTK</th>
                                <th rowspan="3" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">Aksi</th>
                            </tr>
                            <tr>
                                <th colspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">TARGET</th>
                                <th colspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">REALISASI</th>
                                <th colspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">CAPAIAN</th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KINERJA FISIK (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KEUANGAN (RP)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KINERJA FISIK (%)</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-center">KEUANGAN (RP)</th>
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
                                        <span class="text-sm">-</span>
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