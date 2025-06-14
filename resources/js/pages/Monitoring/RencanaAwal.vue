<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue';
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
    monitoring?: {
        pagu_pokok: number;
        pagu_parsial: number;
        pagu_perubahan: number;
        sumber_dana: string;
        targets: Array<{
            kinerja_fisik: number;
            keuangan: number;
        }>;
    };
}

interface User {
    id: number;
    name: string;
    nama_skpd: string;
    skpd_id?: number;
    nip: string;
}

interface ItemWithKodeNomenklatur {
  id: number;
  kode_nomenklatur: {
    id: number;
    nomor_kode: string;
    nomenklatur: string;
    jenis_nomenklatur: number;
    details?: any[];
  };
  monitoring?: {
    targets?: Array<{
      kinerja_fisik: number;
      keuangan: number;
    }>;
  };
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
    dataAnggaranTerakhir?: Record<number, {
      sumber_anggaran: {
        dak: boolean;
        dak_peruntukan: boolean;
        dak_fisik: boolean;
        dak_non_fisik: boolean;
        blud: boolean;
      };
      values: {
        dak: number;
        dak_peruntukan: number;
        dak_fisik: number;
        dak_non_fisik: number;
        blud: number;
      };
    }>;
    periodeAktif?: Array<{ id: number; tahap: { id: number; tahap: string }; tahun: { id: number; tahun: string } }>;
    semuaPeriodeAktif?: Array<{ id: number; tahap: { id: number; tahap: string }; tahun: { id: number; tahun: string } }>;
    tahunAktif?: { id: number; tahun: string } | null;
    bidangurusanTugas?: any[];
}

const props = defineProps<Props>();

// Add reactive state for selected period
const selectedPeriodeId = ref<number | null>(null);

// Tambahkan state untuk edit target subkegiatan
const editingTargets = ref<Record<string, any>>({});
const loadingRow = ref<string | null>(null);
const successRow = ref<string | null>(null);
const errorRow = ref<string | null>(null);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Monitoring', href: '/monitoring' },
    { title: `Monitoring Detail ${props.user?.nama_skpd ?? '-'}`, href: `/monitoring/${props.user?.id}` },
    { title: 'Rencana Awal PD', href: '/rencanaawal' },
]);

// Initialize with the active period if available
onMounted(() => {
    if (props.periodeAktif && props.periodeAktif.length > 0) {
        selectedPeriodeId.value = props.periodeAktif[0].id;
    } else if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
        selectedPeriodeId.value = props.semuaPeriodeAktif[0].id;
    }
});

// Modal and target editing states
const showModal = ref(false);
const currentItem = ref<ItemWithKodeNomenklatur | null>(null);
const targetData = ref({
  tw1: { kinerja_fisik: 0, keuangan: 0 },
  tw2: { kinerja_fisik: 0, keuangan: 0 },
  tw3: { kinerja_fisik: 0, keuangan: 0 },
  tw4: { kinerja_fisik: 0, keuangan: 0 }
});

// Fill targets action
const fillTargets = (item: ItemWithKodeNomenklatur) => {
  currentItem.value = item;
  // Populate target data from existing values if available
  if (item.monitoring?.targets) {
    targetData.value = {
      tw1: {
        kinerja_fisik: item.monitoring.targets[0]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[0]?.keuangan || 0 
      },
      tw2: {
        kinerja_fisik: item.monitoring.targets[1]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[1]?.keuangan || 0 
      },
      tw3: {
        kinerja_fisik: item.monitoring.targets[2]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[2]?.keuangan || 0 
      },
      tw4: {
        kinerja_fisik: item.monitoring.targets[3]?.kinerja_fisik || 0, 
        keuangan: item.monitoring.targets[3]?.keuangan || 0 
      }
    };
  } else {
    // Reset to defaults if no data exists
    targetData.value = {
      tw1: { kinerja_fisik: 0, keuangan: 0 },
      tw2: { kinerja_fisik: 0, keuangan: 0 },
      tw3: { kinerja_fisik: 0, keuangan: 0 },
      tw4: { kinerja_fisik: 0, keuangan: 0 }
    };
  }
  showModal.value = true;
};

// Format angka ke dalam format rupiah
const formatRupiah = (value: number): string => {
  const numberString = value.toString().replace(/[^,\d]/g, '');
  const split = numberString.split(',');
  let sisa = split[0].length % 3;
  let rupiah = split[0].substr(0, sisa);
  const ribuan = split[0].substr(sisa).match(/\d{3}/g);

  if (ribuan) {
    rupiah += (sisa ? '.' : '') + ribuan.join('.');
  }

  return 'Rp ' + rupiah + (split[1] ? ',' + split[1] : '');
};

// Format angka ke dalam format persentase
const formatPercent = (value: number | string): string => {
  if (value === '' || value === undefined || value === null) return '';
  const numValue = parseFloat(String(value).replace(',', '.'));
  if (isNaN(numValue)) return '0%';
  return numValue.toFixed(2) + '%';
};

// Saat input keuangan diubah
const onInputRupiah = (event: Event, triwulan: 'tw1' | 'tw2' | 'tw3' | 'tw4') => {
  const input = event.target as HTMLInputElement;
  const rawValue = input.value.replace(/[^\d]/g, ''); // Hanya ambil angka
  const numericValue = parseInt(rawValue) || 0;

  targetData.value[triwulan].keuangan = numericValue;
};

// Saat input kinerja fisik diubah
const onInputPercent = (event: Event, triwulan: 'tw1' | 'tw2' | 'tw3' | 'tw4') => {
  const input = event.target as HTMLInputElement;
  let rawValue = input.value.replace(/[^\d.,]/g, ''); // Hanya ambil angka dan tanda desimal
  rawValue = rawValue.replace(',', '.'); // Ubah koma menjadi titik untuk decimal

  // Pastikan nilai tidak melebihi 100%
  const numericValue = parseFloat(rawValue);
  if (isNaN(numericValue)) {
    targetData.value[triwulan].kinerja_fisik = 0;
  } else {
    targetData.value[triwulan].kinerja_fisik = Math.min(100, numericValue);
  }
};

// Save targets action
const modalSaveTargets = (item: ItemWithKodeNomenklatur) => {
  if (!currentItem.value || !showModal.value) {
    fillTargets(item); // If not already in edit mode, open the modal first
    return;
  }
  
  // Prepare data for saving
  const targets = [
    { 
      kinerja_fisik: parseFloat(String(targetData.value.tw1.kinerja_fisik)) || 0, 
      keuangan: targetData.value.tw1.keuangan || 0  
    },
    { 
      kinerja_fisik: parseFloat(String(targetData.value.tw2.kinerja_fisik)) || 0, 
      keuangan: targetData.value.tw2.keuangan || 0  
    },
    { 
      kinerja_fisik: parseFloat(String(targetData.value.tw3.kinerja_fisik)) || 0, 
      keuangan: targetData.value.tw3.keuangan || 0  
    },
    { 
      kinerja_fisik: parseFloat(String(targetData.value.tw4.kinerja_fisik)) || 0, 
      keuangan: targetData.value.tw4.keuangan || 0  
    }
  ];
  
  // Determine what kind of item we are saving (bidang urusan, program, kegiatan, or subkegiatan)
  const itemType = item.kode_nomenklatur.jenis_nomenklatur;
  const route = getRouteBasedOnItemType(itemType);
  
  // Send to server
  router.post(route, {
    tugas_id: item.id,
    skpd_id: props.user?.skpd_id || props.tugas?.skpd_id,
    sumber_dana: 'APBD', // Default sumber dana
    deskripsi: 'Rencana Awal',
    tahun: props.tahunAktif?.tahun || new Date().getFullYear(),
    periode_id: selectedPeriodeId.value,
    pagu_pokok: calculateItemTotal(item), // Get appropriate total based on item type
    pagu_parsial: 0,
    pagu_perubahan: 0,
    targets: targets
  }, {
    onSuccess: () => {
      alert('Target berhasil disimpan');
      showModal.value = false;
      currentItem.value = null;
    },
    onError: (errors) => {
      alert('Gagal menyimpan target: ' + Object.values(errors).join('\n'));
    }
  });
};

// Helper to calculate total for an item based on its type
const calculateItemTotal = (item: ItemWithKodeNomenklatur) => {
  const itemType = item.kode_nomenklatur.jenis_nomenklatur;
  
  if (itemType === 1) { // Bidang urusan
    return calculateBidangUrusan.value[item.kode_nomenklatur.id] || 0;
  } else if (itemType === 2) { // Program
    return calculateProgram.value[item.kode_nomenklatur.id] || 0;
  } else if (itemType === 3) { // Kegiatan
    return calculateKegiatan.value[item.id] || 0;
  } else if (itemType === 4) { // Subkegiatan
    // For subkegiatan with specific sumber dana, return the specific amount
    const fundingData = props.dataAnggaranTerakhir?.[item.id];
    if (fundingData) {
      return Object.values(fundingData.values).reduce((sum, val) => sum + val, 0);
    }
    return 0;
  }
  return 0;
};

// Helper to determine the API endpoint based on item type
const getRouteBasedOnItemType = (itemType: number) => {
  // Using the same endpoint for all types for simplicity
  return '/rencana-awal/save-monitoring-data';
};

// Delete item action
const deleteItem = (item: ItemWithKodeNomenklatur) => {
  if (confirm(`Apakah Anda yakin ingin menghapus item ini?\n${item.kode_nomenklatur.nomenklatur}`)) {
    router.delete(`/rencana-awal/delete/${item.id}`, {
      onSuccess: () => {
        alert('Item berhasil dihapus');
      },
      onError: (errors) => {
        alert('Gagal menghapus item: ' + Object.values(errors).join('\n'));
      }
    });
  }
};

// Close modal
const closeModal = () => {
  showModal.value = false;
  currentItem.value = null;
};

// Handler for period change
const handlePeriodeChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  const newPeriodeId = target.value ? parseInt(target.value) : null;

  if (selectedPeriodeId.value !== newPeriodeId) {
    selectedPeriodeId.value = newPeriodeId;

    // Reload data with the new period
    if (props.tugas?.id) {
      router.visit(`/monitoring/rencanaawal/${props.tugas.id}?periode_id=${newPeriodeId || ''}`, {
        preserveState: true,
        only: ['dataAnggaranTerakhir', 'subkegiatanTugas']
      });
    } else if (props.user?.id) {
      router.visit(`/monitoring/${props.user.id}?periode_id=${newPeriodeId || ''}`, {
        preserveState: true,
        only: ['dataAnggaranTerakhir', 'subkegiatanTugas']
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
watch(editingTargets, () => {
  recalculateAllTargets();
}, { deep: true });

// Also watch for changes in the subkegiatan data
watch(() => props.subkegiatanTugas, () => {
  recalculateAllTargets();
}, { deep: true });

// Also watch for changes in the dataAnggaranTerakhir
watch(() => props.dataAnggaranTerakhir, () => {
  recalculateAllTargets();
}, { deep: true });

// Add a computed property to transform subkegiatan data to include bidang urusan and multiple rows per sumber dana
const formattedSubKegiatanData = computed(() => {
  const result: any[] = [];

  if (!props.subkegiatanTugas) {
    return result;
  }

  // For each subkegiatan
  props.subkegiatanTugas.forEach(subKegiatan => {
    // Find the parent kegiatan
    const parentKegiatan = props.kegiatanTugas?.find(k =>
      k.kode_nomenklatur.id === subKegiatan.kode_nomenklatur.details[0]?.id_kegiatan
    );

    if (!parentKegiatan) return;

    // Find the parent program
    const parentProgram = props.programTugas?.find(p =>
      p.kode_nomenklatur.id === parentKegiatan.kode_nomenklatur.details[0]?.id_program
    );

    if (!parentProgram) return;

    // Find the parent bidang urusan
    const parentBidangUrusan = props.bidangurusanTugas?.find(bu =>
      bu.kode_nomenklatur.id === parentProgram.kode_nomenklatur.details[0]?.id_bidang_urusan
    );

    if (!parentBidangUrusan) return;

    // Get the funding data for this subkegiatan
    const fundingData = props.dataAnggaranTerakhir?.[subKegiatan.id];
    
    // Process monitoring data to extract targets more safely
    const processMonitoringData = (subKegiatan: any) => {
      // Initialize an array to hold normalized targets data (one entry per triwulan)
      const normalizedTargets = [
        { kinerja_fisik: 0, keuangan: 0 },
        { kinerja_fisik: 0, keuangan: 0 },
        { kinerja_fisik: 0, keuangan: 0 },
        { kinerja_fisik: 0, keuangan: 0 }
      ];
      
      // First check if we have direct targets
      if (subKegiatan.monitoring?.targets?.length > 0) {
        subKegiatan.monitoring.targets.forEach((target: any, index: number) => {
          if (index < 4) {
            normalizedTargets[index] = {
              kinerja_fisik: target.kinerja_fisik || 0,
              keuangan: target.keuangan || 0
            };
          }
        });
      } 
      // Next check if we have monitoringAnggaran with targets
      else if (subKegiatan.monitoring?.monitoringAnggaran?.length > 0) {
        // For each monitoring anggaran
        subKegiatan.monitoring.monitoringAnggaran.forEach((anggaran: any) => {
          if (anggaran.targets?.length > 0) {
            anggaran.targets.forEach((target: any, index: number) => {
              if (index < 4) {
                // Accumulate values if there are multiple sources
                normalizedTargets[index].kinerja_fisik += target.kinerja_fisik || 0;
                normalizedTargets[index].keuangan += target.keuangan || 0;
              }
            });
          }
        });
        
        // If we have multiple anggaran sources, calculate average for kinerja_fisik
        if (subKegiatan.monitoring.monitoringAnggaran.length > 1) {
          normalizedTargets.forEach(target => {
            target.kinerja_fisik = target.kinerja_fisik / subKegiatan.monitoring.monitoringAnggaran.length;
          });
        }
      }
      
      return normalizedTargets;
    };

    if (fundingData) {
      // Check each funding source
      const sources = [
        { key: 'dak', name: 'DAU' },
        { key: 'dak_peruntukan', name: 'DAU Peruntukan' },
        { key: 'dak_fisik', name: 'DAK Fisik' },
        { key: 'dak_non_fisik', name: 'DAK Non-Fisik' },
        { key: 'blud', name: 'BLUD' }
      ];

      // For each active funding source, create a row
      let hasActiveSource = false;
      sources.forEach(source => {
        const sourceKey = source.key as keyof typeof fundingData.sumber_anggaran;
        const valueKey = source.key as keyof typeof fundingData.values;

        if (fundingData.sumber_anggaran[sourceKey] && fundingData.values[valueKey] > 0) {
          hasActiveSource = true;
          
          // Get normalized targets
          const targets = processMonitoringData(subKegiatan);
          
          result.push({
            id: `${subKegiatan.id}-${source.key}`,
            subKegiatan: subKegiatan,
            kegiatan: parentKegiatan,
            program: parentProgram,
            bidangUrusan: parentBidangUrusan,
            sumberDana: source.name,
            pokok: fundingData.values[valueKey],
            parsial: 0,
            perubahan: 0,
            normalizedTargets: targets
          });
        }
      });

      // If no active sources are found but funding data exists, create a default row
      if (!hasActiveSource) {
        // Get normalized targets
        const targets = processMonitoringData(subKegiatan);
        
        result.push({
          id: `${subKegiatan.id}-default`,
          subKegiatan: subKegiatan,
          kegiatan: parentKegiatan,
          program: parentProgram,
          bidangUrusan: parentBidangUrusan,
          sumberDana: 'Belum diisi',
          pokok: 0,
          parsial: 0,
          perubahan: 0,
          normalizedTargets: targets
        });
      }
    }
    // If no funding data but has monitoring, create at least one row for this subkegiatan
    else if (subKegiatan.monitoring) {
      // Get normalized targets
      const targets = processMonitoringData(subKegiatan);
      
      result.push({
        id: `${subKegiatan.id}-default`,
        subKegiatan: subKegiatan,
        kegiatan: parentKegiatan,
        program: parentProgram,
        bidangUrusan: parentBidangUrusan,
        sumberDana: subKegiatan.monitoring.sumber_dana || 'Multiple',
        pokok: subKegiatan.monitoring.pagu_pokok || 0,
        parsial: subKegiatan.monitoring.pagu_parsial || 0,
        perubahan: subKegiatan.monitoring.pagu_perubahan || 0,
        normalizedTargets: targets
      });
    }
    // If no funding data and no monitoring, still show the row with zero values
    else {
      result.push({
        id: `${subKegiatan.id}-default`,
        subKegiatan: subKegiatan,
        kegiatan: parentKegiatan,
        program: parentProgram,
        bidangUrusan: parentBidangUrusan,
        sumberDana: 'Belum diisi',
        pokok: 0,
        parsial: 0,
        perubahan: 0,
        normalizedTargets: [
          { kinerja_fisik: 0, keuangan: 0 },
          { kinerja_fisik: 0, keuangan: 0 },
          { kinerja_fisik: 0, keuangan: 0 },
          { kinerja_fisik: 0, keuangan: 0 }
        ]
      });
    }
  });

  return result;
});

// Add computed properties to calculate the sums
const calculateKegiatan = computed<Record<number, any>>(() => {
  const kegiatanSums: Record<string, any> = {};

  // First, calculate sums for each kegiatan based on its subkegiatans
  formattedSubKegiatanData.value.forEach(item => {
    const kegiatanId = item.kegiatan.id.toString();
    if (!kegiatanSums[kegiatanId]) {
      kegiatanSums[kegiatanId] = {
        pokok: 0,
        parsial: 0,
        perubahan: 0,
        // Targets for each triwulan (fisik and keuangan)
        targets: [
          { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 1
          { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 2
          { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 3
          { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }  // Triwulan 4
        ]
      };
    }
    
    // Sum the pagu values (total budget for this item)
    kegiatanSums[kegiatanId].pokok += item.pokok || 0;
    kegiatanSums[kegiatanId].parsial += item.parsial || 0;
    kegiatanSums[kegiatanId].perubahan += item.perubahan || 0;
    
    // Use the normalizedTargets we prepared
    if (item.normalizedTargets) {
      item.normalizedTargets.forEach((target: any, index: number) => {
        if (index < 4) {
          // Add kinerja_fisik (we'll calculate average later)
          if (target.kinerja_fisik > 0) {
            kegiatanSums[kegiatanId].targets[index].kinerja_fisik += target.kinerja_fisik;
            kegiatanSums[kegiatanId].targets[index].count++;
            kegiatanSums[kegiatanId].targets[index].has_values = true;
          }
          
          // Sum keuangan values - use the actual keuangan value from each target
          if (target.keuangan > 0) {
            kegiatanSums[kegiatanId].targets[index].keuangan += target.keuangan;
            kegiatanSums[kegiatanId].targets[index].has_values = true;
          }
        }
      });
    }
    
    // Also check editingTargets for any values being edited
    const uniqueKey = getUniqueKey(item.subKegiatan, item.sumberDana);
    if (editingTargets.value[uniqueKey]) {
      editingTargets.value[uniqueKey].forEach((target: any, index: number) => {
        if (index < 4) {
          // Add kinerja_fisik from editing values if they exist
          const kinerja_fisik = parseFloat(target.kinerja_fisik);
          if (!isNaN(kinerja_fisik) && kinerja_fisik > 0) {
            kegiatanSums[kegiatanId].targets[index].kinerja_fisik += kinerja_fisik;
            kegiatanSums[kegiatanId].targets[index].count++;
            kegiatanSums[kegiatanId].targets[index].has_values = true;
          }
          
          // For edited values, we don't immediately add to keuangan since they're not yet saved
          // The refreshData function will handle reloading the calculations after save
        }
      });
    }
  });
  
  // Calculate averages for kinerja_fisik
  Object.keys(kegiatanSums).forEach(kegiatanId => {
    const kegiatan = kegiatanSums[kegiatanId];
    
    // Calculate average for each triwulan's kinerja_fisik
    kegiatan.targets.forEach((target: any) => {
      if (target.count > 0) {
        target.kinerja_fisik = target.kinerja_fisik / target.count;
      }
    });
  });
  
  // Convert to numeric keys for the return value
  const result: Record<number, any> = {};
  Object.keys(kegiatanSums).forEach(key => {
    result[parseInt(key)] = kegiatanSums[key];
  });
  
  return result;
});

const calculateProgram = computed<Record<number, any>>(() => {
  const programSums: Record<string, any> = {};

  // Calculate sums for each program based on its kegiatans
  props.kegiatanTugas?.forEach(kegiatan => {
    const parentProgramId = kegiatan.kode_nomenklatur.details[0]?.id_program;
    if (parentProgramId) {
      const programId = parentProgramId.toString();
      if (!programSums[programId]) {
        programSums[programId] = {
          pokok: 0,
          parsial: 0,
          perubahan: 0,
          // Targets for each triwulan (fisik and keuangan)
          targets: [
            { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 1
            { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 2
            { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 3
            { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }  // Triwulan 4
          ]
        };
      }
      
      // Get the calculated values for this kegiatan
      const kegiatanData = calculateKegiatan.value[kegiatan.id];
      if (kegiatanData) {
        // Sum the pagu values
        programSums[programId].pokok += kegiatanData.pokok || 0;
        programSums[programId].parsial += kegiatanData.parsial || 0;
        programSums[programId].perubahan += kegiatanData.perubahan || 0;
        
        // Process each triwulan's targets
        kegiatanData.targets.forEach((target: any, index: number) => {
          // Add kinerja_fisik (we'll calculate average later)
          if (target.has_values && target.kinerja_fisik > 0) {
            programSums[programId].targets[index].kinerja_fisik += target.kinerja_fisik;
            programSums[programId].targets[index].count++;
            programSums[programId].targets[index].has_values = true;
          }
          
          // Sum the keuangan values from kegiatan targets
          if (target.has_values && target.keuangan > 0) {
            programSums[programId].targets[index].keuangan += target.keuangan;
            programSums[programId].targets[index].has_values = true;
    }
  });
      }
    }
  });
  
  // Calculate averages for kinerja_fisik
  Object.keys(programSums).forEach(programId => {
    const program = programSums[programId];
    
    // Calculate average for each triwulan's kinerja_fisik
    program.targets.forEach((target: any) => {
      if (target.count > 0) {
        target.kinerja_fisik = target.kinerja_fisik / target.count;
      }
    });
  });
  
  // Convert to numeric keys for the return value
  const result: Record<number, any> = {};
  Object.keys(programSums).forEach(key => {
    result[parseInt(key)] = programSums[key];
  });
  
  return result;
});

const calculateBidangUrusan = computed<Record<number, any>>(() => {
  const bidangUrusanSums: Record<string, any> = {};

  // Calculate sums for each bidang urusan based on its programs
  props.programTugas?.forEach(program => {
    const parentBidangUrusanId = program.kode_nomenklatur.details[0]?.id_bidang_urusan;
    if (parentBidangUrusanId) {
      // Find the bidang urusan with this ID
      const bidangUrusan = props.bidangurusanTugas?.find(bu =>
        bu.kode_nomenklatur.id === parentBidangUrusanId
      );

      if (bidangUrusan) {
        const bidangUrusanNomenklaturId = bidangUrusan.kode_nomenklatur.id.toString();
        if (!bidangUrusanSums[bidangUrusanNomenklaturId]) {
          bidangUrusanSums[bidangUrusanNomenklaturId] = {
            pokok: 0,
            parsial: 0,
            perubahan: 0,
            // Targets for each triwulan (fisik and keuangan)
            targets: [
              { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 1
              { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 2
              { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }, // Triwulan 3
              { kinerja_fisik: 0, keuangan: 0, count: 0, has_values: false }  // Triwulan 4
            ]
          };
        }
        
        // Get the calculated values for this program
        const programData = calculateProgram.value[program.kode_nomenklatur.id];
        if (programData) {
          // Sum the pagu values
          bidangUrusanSums[bidangUrusanNomenklaturId].pokok += programData.pokok || 0;
          bidangUrusanSums[bidangUrusanNomenklaturId].parsial += programData.parsial || 0;
          bidangUrusanSums[bidangUrusanNomenklaturId].perubahan += programData.perubahan || 0;
          
          // Process each triwulan's targets
          programData.targets.forEach((target: any, index: number) => {
            // Add kinerja_fisik (we'll calculate average later)
            if (target.has_values && target.kinerja_fisik > 0) {
              bidangUrusanSums[bidangUrusanNomenklaturId].targets[index].kinerja_fisik += target.kinerja_fisik;
              bidangUrusanSums[bidangUrusanNomenklaturId].targets[index].count++;
              bidangUrusanSums[bidangUrusanNomenklaturId].targets[index].has_values = true;
            }
            
            // Sum the keuangan values from program targets
            if (target.has_values && target.keuangan > 0) {
              bidangUrusanSums[bidangUrusanNomenklaturId].targets[index].keuangan += target.keuangan;
              bidangUrusanSums[bidangUrusanNomenklaturId].targets[index].has_values = true;
            }
          });
        }
      }
    }
  });

  // Calculate averages for kinerja_fisik
  Object.keys(bidangUrusanSums).forEach(bidangUrusanId => {
    const bidangUrusan = bidangUrusanSums[bidangUrusanId];
    
    // Calculate average for each triwulan's kinerja_fisik
    bidangUrusan.targets.forEach((target: any) => {
      if (target.count > 0) {
        target.kinerja_fisik = target.kinerja_fisik / target.count;
      }
    });
  });
  
  // Convert to numeric keys for the return value
  const result: Record<number, any> = {};
  Object.keys(bidangUrusanSums).forEach(key => {
    result[parseInt(key)] = bidangUrusanSums[key];
  });
  
  return result;
});

// Helper untuk inisialisasi data target
function getInitialTargets(subKegiatan: any) {
  // Cek apakah sudah ada monitoring/targets
  const targets = subKegiatan.monitoring?.targets || subKegiatan.targets || [];
  // Deep copy: buat array baru dengan objek baru di dalamnya
  return [0, 1, 2, 3].map(i => ({
    kinerja_fisik: targets[i]?.kinerja_fisik || targets[i]?.kinerjaFisik || '',
    keuangan: targets[i]?.keuangan || '',
  }));
}

// Helper untuk key unik per baris subkegiatan (id + sumberDana)
function getUniqueKey(subKegiatan: any, sumberDana: string) {
  return `${subKegiatan.id}-${sumberDana}`;
}

// Fungsi untuk mulai edit
function startEdit(subKegiatan: any) {
  const uniqueKey = getUniqueKey(subKegiatan, subKegiatan.sumberDana);
  editingTargets.value[uniqueKey] = getInitialTargets(subKegiatan);
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
    }
}

// Tambahkan fungsi untuk mendapatkan ID sumber anggaran dari namanya
function getSumberAnggaranId(sumberDanaNama: string): number {
  // Lowercase dan normalisasi nama sumber dana
  const normalizedName = sumberDanaNama?.toLowerCase().trim() || 'dau';
  
  console.log('Mencari ID untuk sumber dana:', normalizedName);
  
  // Mapping nama sumber dana ke ID dengan lebih banyak kemungkinan
  const sumberDanaMapping: Record<string, number> = {
    // Original mappings
    'dau': 1,
    'dau peruntukan': 2,
    'dak fisik': 3,
    'dak non fisik': 4,
    'blud': 5,
    
    // Tambahan alternatif untuk kecocokan lebih baik
    'dak': 1,
    'dak peruntukan': 2,
    'dak non-fisik': 4,
    'apbd': 1,
    'apbn': 2,
    'multiple': 1,
    'belum diisi': 1,
  };
  
  const result = sumberDanaMapping[normalizedName] || 1;
  console.log('Ditemukan ID untuk sumber dana:', result);
  return result; // Default ke ID 1 jika tidak ditemukan
}

// Fungsi simpan target
async function saveTargets(subKegiatan: any, sumberDana?: string) {
  // Cari sumberDana dari formattedSubKegiatanData jika tidak ada di parameter
  let finalSumberDana: string = sumberDana || '';
  
  if (!finalSumberDana) {
    // Jika sumberDana tidak ada di parameter, cari dari subKegiatan atau formattedSubKegiatanData
    finalSumberDana = subKegiatan.sumberDana || '';
    
    if (!finalSumberDana) {
      const matchingItem = formattedSubKegiatanData.value.find(item => item.subKegiatan.id === subKegiatan.id);
      if (matchingItem) {
        finalSumberDana = matchingItem.sumberDana;
      } else {
        // Default fallback jika tidak ditemukan
        finalSumberDana = 'APBD';
      }
    }
  }
  
  const uniqueKey = getUniqueKey(subKegiatan, finalSumberDana);
  loadingRow.value = uniqueKey; // Gunakan uniqueKey, bukan subKegiatan.id
  errorRow.value = null;
  successRow.value = null;
  
  try {
    // Periksa jika editingTargets untuk key ini sudah ada
    if (!editingTargets.value[uniqueKey]) {
      editingTargets.value[uniqueKey] = getInitialTargets(subKegiatan);
}

    const rawTargets = editingTargets.value[uniqueKey];
        
    // Validate and format targets data
    const processedTargets = rawTargets.map((target: any, index: number) => {
      const kinerjaFisik = parseFloat(String(target.kinerja_fisik).replace(',', '.'));
      const keuangan = typeof target.keuangan === 'string' 
        ? parseInt(target.keuangan.replace(/[^\d]/g, '')) 
        : (target.keuangan || 0);
            
      if (isNaN(kinerjaFisik) || kinerjaFisik < 0 || kinerjaFisik > 100) {
        throw new Error(`Kinerja fisik triwulan ${index + 1} harus berupa angka antara 0-100`);
      }
            
      if (isNaN(keuangan) || keuangan < 0) {
        throw new Error(`Keuangan triwulan ${index + 1} harus berupa angka positif`);
      }

    return {
        kinerja_fisik: kinerjaFisik,
        keuangan: keuangan,
        triwulan: index + 1
    };
    });
    
    const sumberAnggaranId = getSumberAnggaranId(finalSumberDana);
    
    // Get pagu data from props.dataAnggaranTerakhir
    const paguData = props.dataAnggaranTerakhir?.[subKegiatan.id]?.values || {};
    const key = normalizeKey(finalSumberDana);
    
    // We need to ensure type safety here - create a safer version of the key lookup
    let paguValue = 0;
    if (paguData) {
      // Use type assertion to make TypeScript happy
      type PaguDataType = {
        dau?: number;
        dau_peruntukan?: number;
        dak_fisik?: number;
        dak_non_fisik?: number;
        blud?: number;
      };
      
      const typedPaguData = paguData as PaguDataType;
      
      if (key === 'dau' && typedPaguData.dau !== undefined) {
        paguValue = typedPaguData.dau;
      } else if (key === 'dau_peruntukan' && typedPaguData.dau_peruntukan !== undefined) {
        paguValue = typedPaguData.dau_peruntukan;
      } else if (key === 'dak_fisik' && typedPaguData.dak_fisik !== undefined) {
        paguValue = typedPaguData.dak_fisik;
      } else if (key === 'dak_non_fisik' && typedPaguData.dak_non_fisik !== undefined) {
        paguValue = typedPaguData.dak_non_fisik;
      } else if (key === 'blud' && typedPaguData.blud !== undefined) {
        paguValue = typedPaguData.blud;
      }
    }
    
    // Pastikan nama_pptk ada (ini sering menjadi masalah jika required di backend)
    const nama_pptk = props.tugas?.skpd?.skpd_kepala?.[0]?.user?.user_detail?.nama || 
                      props.kepalaSkpd || 
                      'Belum diisi';
    
    const payload = {
      skpd_tugas_id: subKegiatan.id,
      tahun: props.tahunAktif?.tahun || new Date().getFullYear(),
      deskripsi: 'Rencana Awal',
      targets: processedTargets,
      sumber_anggaran_id: sumberAnggaranId,
      sumber_dana: finalSumberDana, // Tambahkan sumber_dana ke payload
      periode_id: selectedPeriodeId.value,
      nama_pptk: nama_pptk,
      pagu: {
        pokok: paguValue,
        parsial: 0,
        perubahan: 0
      }
    };
    
    console.log('Sending payload:', payload);
    
    const response = await router.post('/rencanaawal/save-target', payload, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (response: any) => {
        console.log('Success response:', response);
        successRow.value = uniqueKey; // Gunakan uniqueKey, bukan subKegiatan.id
        
        // Update the UI with the new data
        // Find the corresponding item in formattedSubKegiatanData
        const itemToUpdate = formattedSubKegiatanData.value.find(
          item => item.subKegiatan.id === subKegiatan.id && 
                 item.sumberDana === finalSumberDana
        );
        
        if (itemToUpdate && response.data && response.data.targets) {
          // Create updated normalized targets
          const updatedTargets = [
            { kinerja_fisik: 0, keuangan: 0 },
            { kinerja_fisik: 0, keuangan: 0 },
            { kinerja_fisik: 0, keuangan: 0 },
            { kinerja_fisik: 0, keuangan: 0 }
          ];
          
          // Update with the new target values
          response.data.targets.forEach((target: any) => {
            const index = target.periode_id - 1;
            if (index >= 0 && index < 4) {
              updatedTargets[index] = {
                kinerja_fisik: target.kinerja_fisik || 0,
                keuangan: target.keuangan || 0
              };
            }
          });
          
          // Update the item with new targets
          itemToUpdate.normalizedTargets = updatedTargets;
          
          // Update monitoring data in the subKegiatan object to reflect the changes
          if (!subKegiatan.monitoring) {
            subKegiatan.monitoring = {
              monitoringAnggaran: []
            };
          }
          
          if (!subKegiatan.monitoring.monitoringAnggaran) {
            subKegiatan.monitoring.monitoringAnggaran = [];
          }
          
          // Find or create the monitoring anggaran for this source
          let monitoringAnggaran = subKegiatan.monitoring.monitoringAnggaran.find(
            (anggaran: any) => anggaran.sumber_anggaran_id === sumberAnggaranId
          );
          
          if (!monitoringAnggaran) {
            monitoringAnggaran = {
              id: response.data.monitoring_anggaran_id,
              sumber_anggaran_id: sumberAnggaranId,
              targets: []
            };
            subKegiatan.monitoring.monitoringAnggaran.push(monitoringAnggaran);
          }
          
          // Update the targets for this specific monitoring anggaran
          monitoringAnggaran.targets = response.data.targets;
          
          console.log('Updated monitoring data for subKegiatan:', subKegiatan.id, 'sumber dana:', finalSumberDana);
        }
        
        delete editingTargets.value[uniqueKey];
        
        // Show success message temporarily
        setTimeout(() => {
          if (successRow.value === uniqueKey) {
            successRow.value = null;
          }
        }, 3000);
        
        // Refresh data from server after success to update all calculations
        setTimeout(() => {
          refreshData();
        }, 500);
      },
      onError: (err: any) => {
        console.error('Error saving targets:', err);
        errorRow.value = uniqueKey; // Gunakan uniqueKey, bukan subKegiatan.id
        
        // Check for the specific error about period not being active
        if (err.message && err.message.includes('Tidak ada periode yang aktif')) {
          alert('Tidak ada periode yang aktif saat ini. Data Rencana Awal tidak dapat disimpan. Silakan tunggu hingga periode dibuka oleh admin.');
        } else {
          alert(err.message || 'Terjadi kesalahan saat menyimpan target');
        }
      },
      onFinish: () => {
        loadingRow.value = null;
        if (successRow.value) {
          setTimeout(() => {
            if (successRow.value === uniqueKey) {
              successRow.value = null;
            }
          }, 3000);
        }
      }
    });

    return response;
  } catch (e: any) {
    console.error('Exception:', e);
    errorRow.value = uniqueKey; // Gunakan uniqueKey, bukan subKegiatan.id
    loadingRow.value = null;
    alert(e.message || 'Terjadi kesalahan saat memproses data');
  }
}

// Helper function untuk normalisasi key sumber anggaran
function normalizeKey(name: string): string {
    const key = name.toLowerCase().replace(/\s+/g, '_');
    if (key === 'dau') return 'dak';
    if (key === 'dau_peruntukan') return 'dak_peruntukan';
    return key;
}

// Fungsi hapus target
async function deleteTargets(subKegiatan: any, sumberDana?: string) {
  // Cari sumberDana dari formattedSubKegiatanData jika tidak ada di parameter
  let finalSumberDana: string = sumberDana || '';
  
  if (!finalSumberDana) {
    // Jika sumberDana tidak ada di parameter, cari dari subKegiatan atau formattedSubKegiatanData
    finalSumberDana = subKegiatan.sumberDana || '';
    
    if (!finalSumberDana) {
      const matchingItem = formattedSubKegiatanData.value.find(item => item.subKegiatan.id === subKegiatan.id);
      if (matchingItem) {
        finalSumberDana = matchingItem.sumberDana;
      } else {
        // Default fallback jika tidak ditemukan
        finalSumberDana = 'APBD';
      }
    }
  }
  
  const uniqueKey = getUniqueKey(subKegiatan, finalSumberDana);
  loadingRow.value = uniqueKey;
  errorRow.value = null;
  successRow.value = null;
  
  try {
    // Ask for confirmation
    if (!confirm(`Apakah Anda yakin ingin menghapus target untuk sub kegiatan ini dengan sumber dana ${finalSumberDana}?`)) {
      loadingRow.value = null;
      return;
    }
    
    const sumberAnggaranId = getSumberAnggaranId(finalSumberDana);
    console.log('ID sumber anggaran untuk hapus:', sumberAnggaranId);
    
    // Cari monitoring_anggaran_id jika tersedia
    const monitoringAnggaranId = subKegiatan.monitoring?.monitoringAnggaran?.find(
      (anggaran: any) => anggaran.sumber_anggaran_id === sumberAnggaranId
    )?.id || 0;
    
    const payload = {
      skpd_tugas_id: subKegiatan.id,
      tahun: props.tahunAktif?.tahun,
      deskripsi: 'Rencana Awal',
      sumber_anggaran_id: sumberAnggaranId,
      sumber_dana: finalSumberDana,
      periode_id: selectedPeriodeId.value,
      monitoring_anggaran_id: monitoringAnggaranId
    };
    
    console.log('Menghapus data target:', payload);

    await router.post(`/rencanaawal/delete-target`, payload, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (response) => {
        console.log('Response hapus sukses:', response);
        successRow.value = uniqueKey;
        
        // Reset form values to empty
        if (editingTargets.value[uniqueKey]) {
          editingTargets.value[uniqueKey] = [0, 1, 2, 3].map(i => ({
            kinerja_fisik: '',
            keuangan: '',
          }));
        }
        
        // Kosongkan monitoring/targets di frontend
        if (subKegiatan.monitoring) {
          // Find the specific monitoringAnggaran for this source
          const monitoringAnggaran = subKegiatan.monitoring.monitoringAnggaran?.find(
            (anggaran: any) => anggaran.sumber_anggaran_id === sumberAnggaranId
          );
          
          if (monitoringAnggaran) {
            monitoringAnggaran.targets = [];
          }
        }
        
        // Show temporary success message
        setTimeout(() => {
          if (successRow.value === uniqueKey) {
            successRow.value = null;
          }
        }, 3000);
        
        // Reload the page after a slight delay to refresh data
        setTimeout(() => {
          refreshData();
        }, 500);
      },
      onError: (err: any) => {
        console.error('Error saat hapus:', err);
        errorRow.value = uniqueKey;
        
        if (err.response && typeof err.response === 'object' && err.response.data && err.response.data.message) {
          alert('Error: ' + err.response.data.message);
        } else {
          alert('Terjadi kesalahan saat menghapus data. Silakan coba lagi atau hubungi administrator.');
        }
      },
      onFinish: () => {
        loadingRow.value = null;
      }
    });
  } catch (e) {
    console.error('Exception saat hapus:', e);
    errorRow.value = uniqueKey;
    loadingRow.value = null;
  }
}

// Function to handle navigation
function goToMonitoringDetail() {
    router.visit(`/monitoring/${props.user?.id}`);
}

function goToCreate() {
  router.visit('/rencanaawal/create');
}

// Add a function to refresh the data from the server
function refreshData() {
  // Reload data with the current period
  const currentPeriodeId = selectedPeriodeId.value;
  
  if (props.tugas?.id) {
    router.visit(`/monitoring/rencanaawal/${props.tugas.id}?periode_id=${currentPeriodeId || ''}`, {
      preserveState: false, // Changed to false to force a full refresh
      only: ['dataAnggaranTerakhir', 'subkegiatanTugas', 'programTugas', 'kegiatanTugas', 'bidangurusanTugas']
    });
  } else if (props.user?.id) {
    router.visit(`/monitoring/${props.user.id}?periode_id=${currentPeriodeId || ''}`, {
      preserveState: false, // Changed to false to force a full refresh
      only: ['dataAnggaranTerakhir', 'subkegiatanTugas', 'programTugas', 'kegiatanTugas', 'bidangurusanTugas']
    });
  }
}

// Update ensureEditingTargets agar pakai key unik
function ensureEditingTargets() {
    const allRows = new Set<string>();
    if (formattedSubKegiatanData.value) {
        formattedSubKegiatanData.value.forEach((item: any) => {
            const uniqueKey = getUniqueKey(item.subKegiatan, item.sumberDana);
            allRows.add(uniqueKey);
            if (!editingTargets.value[uniqueKey]) {
        editingTargets.value[uniqueKey] = getInitialTargets(item.subKegiatan);
            }
        });
    }
}

onMounted(ensureEditingTargets);

watch([
  () => props.subkegiatanTugas,
  () => formattedSubKegiatanData.value
], ensureEditingTargets, { immediate: true, deep: true });
</script>


<template>
    <Head title="Rencana kinerja" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 bg-gray-100 dark:bg-gray-800">
            <!-- Header section (redesigned like Sumberdana) -->
            <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="rounded-full bg-blue-100 p-2 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-600">Rencana Kinerja</h1>
                            <p class="text-sm text-gray-500">Rencana Awal Perangkat Daerah</p>
                        </div>
                    </div>

                    <!-- Add period selector -->
                    <div class="flex items-center">
                        <label for="periode-selector" class="mr-2 text-gray-700 font-medium">Pilih Periode:</label>
                        <select
                            id="periode-selector"
                            class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="handlePeriodeChange"
                            :value="selectedPeriodeId"
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

                        <div class="ml-4 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-medium text-gray-500">Tahun Anggaran</span>
                            <div class="text-lg font-bold text-blue-600 text-center">{{ props.tahunAktif?.tahun || 'Belum ada' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Card -->
            <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
                <div class="flex items-center mb-2">
                    <h2 class="text-lg font-semibold text-gray-600 mb-2">Informasi Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.tugas?.kode_nomenklatur.nomor_kode }} - {{ props.tugas?.kode_nomenklatur.nomenklatur }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.tugas?.skpd.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.tugas?.skpd.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.kepalaSkpd ?? props.tugas?.skpd.skpd_kepala[0]?.user?.user_detail?.nama ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Program table with targets -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-600">Detail Rencana Kinerja</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="text-center bg-gray-50 border-b">
                                <th rowspan="3" class="border px-2 py-1 align-middle">KODE</th>
                                <th rowspan="3" class="border px-2 py-1 align-middle">BIDANG URUSAN/PROGRAM/KEGIATAN/SUB KEGIATAN</th>
                                <th colspan="3" class="border px-2 py-1 bg-amber-50">PAGU ANGGARAN APBD</th>
                                <th rowspan="3" class="border px-2 py-1 bg-amber-50 align-middle">SUMBER DANA</th>
                                <th colspan="8" class="border px-2 py-1 bg-blue-50">TARGET</th>
                                <th rowspan="3" class="border px-2 py-1 bg-gray-50 align-middle">AKSI</th>
                            </tr>
                            <tr class="text-center bg-gray-50">
                                <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">POKOK (RP)</th>
                                <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">PARSIAL (RP)</th>
                                <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">PERUBAHAN (RP)</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 1</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 2</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 3</th>
                                <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 4</th>
                            </tr>
                            <tr class="text-center bg-blue-50">
                                <!-- Triwulan 1 -->
                                <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
                                <!-- Triwulan 2 -->
                                <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
                                <!-- Triwulan 3 -->
                                <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
                                <!-- Triwulan 4 -->
                                <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
                                <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Display bidang urusan from the selected urusan -->
                            <template v-for="bidangUrusan in props.bidangurusanTugas" :key="bidangUrusan.id">
                                <tr class="bg-blue-50 font-semibold hover:bg-blue-100">
                                    <td class="p-2 border text-left">{{ bidangUrusan.kode_nomenklatur.nomor_kode }}</td>
                                    <td class="p-2 border">{{ bidangUrusan.kode_nomenklatur.nomenklatur }}</td>
                                    <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.pokok?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.parsial?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                    <td class="p-2 border text-left">-</td>
                                    <!-- Triwulan 1 -->
                                    <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                    <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                    <!-- Triwulan 2 -->
                                    <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                    <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                    <!-- Triwulan 3 -->
                                    <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                    <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                    <!-- Triwulan 4 -->
                                    <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                    <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                    <td></td>
                                </tr>

                                <!-- Display programs that belong to this bidang urusan -->
                                <template v-for="program in props.programTugas?.filter(p => p.kode_nomenklatur.details[0]?.id_bidang_urusan === bidangUrusan.kode_nomenklatur.id)" :key="program.id">
                                    <tr class="border bg-gray-50 hover:bg-gray-100 font-medium">
                                        <td class="p-2 border text-left">{{ program.kode_nomenklatur.nomor_kode }}</td>
                                        <td class="p-2 border text-left">{{ program.kode_nomenklatur.nomenklatur }}</td>
                                        <td class="p-2 border text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.pokok?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-2 border text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.parsial?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-2 border text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                        <td class="p-2 border text-center">{{ program.monitoring?.sumber_dana || '-' }}</td>
                                        <!-- Triwulan 1 -->
                                        <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                        <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                        <!-- Triwulan 2 -->
                                        <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                        <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                        <!-- Triwulan 3 -->
                                        <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                        <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                        <!-- Triwulan 4 -->
                                        <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                        <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                        <td></td>
                                    </tr>

                                    <!-- Display kegiatan for this program -->
                                    <template v-for="kegiatan in props.kegiatanTugas?.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                                        <tr class="border hover:bg-gray-50">
                                            <td class="p-2 border text-left">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                                            <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.pokok?.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.parsial?.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.perubahan?.toLocaleString('id-ID') || '0' }}</td>
                                            <td class="p-2 border text-left">{{ kegiatan.monitoring?.sumber_dana || '-' }}</td>
                                            <!-- Triwulan 1 -->
                                            <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                            <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                            <!-- Triwulan 2 -->
                                            <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                            <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                            <!-- Triwulan 3 -->
                                            <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                            <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                            <!-- Triwulan 4 -->
                                            <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                                            <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                                            <td></td>
                                        </tr>

                                        <!-- Display subkegiatan data for this kegiatan with funding details -->
                                        <template v-for="item in formattedSubKegiatanData.filter(sk => sk.kegiatan.id === kegiatan.id)" :key="item.id">
                                            <tr class="border hover:bg-blue-50 transition-all"
                                                :class="{
                                                    'bg-green-50': successRow === item.subKegiatan.id,
                                                    'bg-red-50': errorRow === item.subKegiatan.id,
                                                    'opacity-75': loadingRow === item.subKegiatan.id
                                                }">
                                                <td class="p-2 border text-left">
                                                    <div class="text-xs font-medium text-gray-500 bg-blue-100 px-2 py-1 rounded inline-block">
                                                        {{ item.subKegiatan.kode_nomenklatur.nomor_kode }}
                                                    </div>
                                                </td>
                                                <td class="p-2 border">{{ item.subKegiatan.kode_nomenklatur.nomenklatur }}</td>
                                                <td class="p-2 border text-right font-medium text-green-600">{{ item.pokok.toLocaleString('id-ID') }}</td>
                                                <td class="p-2 border text-right">{{ item.parsial.toLocaleString('id-ID') || '0' }}</td>
                                                <td class="p-2 border text-right">{{ item.perubahan.toLocaleString('id-ID') || '0' }}</td>
                                                <td class="p-2 border text-center">
                                                    <div class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                                        {{ item.sumberDana }}
                                                    </div>
                                                </td>
                                                <!-- Target Triwulan 1-4, Fisik & Keuangan -->
                                                <td class="border px-1 py-1 text-center !bg-blue-50">
                                                    <input
                                                        type="text"
                                                        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[0].kinerja_fisik)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 0, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="0.00%"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[0]?.kinerja_fisik">
                                                        <span>Tersimpan: {{ item.normalizedTargets[0].kinerja_fisik.toFixed(2) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="border px-1 py-1 text-center !bg-green-50">
                                                    <input
                                                        type="text"
                                                        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[0].keuangan)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 0, 'keuangan', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="Rp 0"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[0]?.keuangan">
                                                        <span>Tersimpan: {{ Number(item.normalizedTargets[0].keuangan).toLocaleString('id-ID') }}</span>
                                                    </div>
                                                </td>

                                                <!-- Triwulan 2 -->
                                                <td class="border px-1 py-1 text-center !bg-blue-50">
                                                    <input
                                                        type="text"
                                                        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[1].kinerja_fisik)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 1, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="0.00%"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[1]?.kinerja_fisik">
                                                        <span>Tersimpan: {{ item.normalizedTargets[1].kinerja_fisik.toFixed(2) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="border px-1 py-1 text-center !bg-green-50">
                                                    <input
                                                        type="text"
                                                        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[1].keuangan)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 1, 'keuangan', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="Rp 0"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[1]?.keuangan">
                                                        <span>Tersimpan: {{ Number(item.normalizedTargets[1].keuangan).toLocaleString('id-ID') }}</span>
                                                    </div>
                                                </td>

                                                <!-- Triwulan 3 -->
                                                <td class="border px-1 py-1 text-center !bg-blue-50">
                                                    <input
                                                        type="text"
                                                        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[2].kinerja_fisik)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 2, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="0.00%"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[2]?.kinerja_fisik">
                                                        <span>Tersimpan: {{ item.normalizedTargets[2].kinerja_fisik.toFixed(2) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="border px-1 py-1 text-center !bg-green-50">
                                                    <input
                                                        type="text"
                                                        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[2].keuangan)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 2, 'keuangan', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="Rp 0"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[2]?.keuangan">
                                                        <span>Tersimpan: {{ Number(item.normalizedTargets[2].keuangan).toLocaleString('id-ID') }}</span>
                                                    </div>
                                                </td>

                                                <!-- Triwulan 4 -->
                                                <td class="border px-1 py-1 text-center !bg-blue-50">
                                                    <input
                                                        type="text"
                                                        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[3].kinerja_fisik)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 3, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="0.00%"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[3]?.kinerja_fisik">
                                                        <span>Tersimpan: {{ item.normalizedTargets[3].kinerja_fisik.toFixed(2) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="border px-1 py-1 text-center !bg-green-50">
                                                    <input
                                                        type="text"
                                                        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
                                                        :class="{
                                                            'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
                                                            'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
                                                            'border-gray-300': !successRow && !errorRow
                                                        }"
                                                        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[3].keuangan)"
                                                        @input="onInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 3, 'keuangan', ($event.target as HTMLInputElement)?.value)"
                                                        :disabled="loadingRow === item.subKegiatan.id"
                                                        placeholder="Rp 0"
                                                    />
                                                    <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[3]?.keuangan">
                                                        <span>Tersimpan: {{ Number(item.normalizedTargets[3].keuangan).toLocaleString('id-ID') }}</span>
                                                    </div>
                                                </td>
                                                <!-- Kolom aksi -->
                                                <td class="p-2 border text-center w-40">
                                                    <div class="flex flex-col gap-1 items-center">
                                                        <div class="flex gap-1">
                                                            <button
                                                                class="px-3 py-1 bg-green-600 text-white rounded text-xs mr-1 flex items-center transition-all hover:bg-green-700"
                                                                :class="{'opacity-50 cursor-not-allowed': loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)}"
                                                                :disabled="loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)" 
                                                                @click="saveTargets(item.subKegiatan, item.sumberDana)"
                                                            >
                                                                <svg v-if="loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                <span>{{ loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana) ? 'Menyimpan...' : 'Simpan' }}</span>
                                                            </button>
                                                            <button 
                                                                class="px-3 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition-all" 
                                                                :disabled="loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)" 
                                                                @click="deleteTargets(item.subKegiatan, item.sumberDana)"
                                                            >
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>