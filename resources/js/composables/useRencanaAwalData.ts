import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export interface Target {
  kinerja_fisik: number;
  keuangan: number;
}

export interface ItemWithKodeNomenklatur {
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

export interface Props {
  user?: any;
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

export function useRencanaAwalData(props: Props, editingTargets: any) {
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

  // Helper untuk key unik per baris subkegiatan (id + sumberDana)
  function getUniqueKey(subKegiatan: any, sumberDana: string) {
    return `${subKegiatan.id}-${sumberDana}`;
  }

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

  return {
    formattedSubKegiatanData,
    calculateKegiatan,
    calculateProgram,
    calculateBidangUrusan,
    getUniqueKey
  };
} 