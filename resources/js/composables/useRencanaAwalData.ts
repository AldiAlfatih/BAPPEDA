import { computed } from 'vue';

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
      dau: boolean; // ✅ FIXED: dau
      dau_peruntukan: boolean; // ✅ FIXED: dau_peruntukan
      dak_fisik: boolean;
      dak_non_fisik: boolean;
      blud: boolean;
    };
    values: {
      dau: number; // ✅ FIXED: dau
      dau_peruntukan: number; // ✅ FIXED: dau_peruntukan
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
  // Add a computed property to transform subkegiatan data into hierarchical structure
  const formattedSubKegiatanData = computed(() => {
    const result: any[] = [];

    if (!props.subkegiatanTugas) {
      return result;
    }

    // For each subkegiatan, create a hierarchical structure
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

        // ✅ FIXED: Check if we have targets_by_sumber_anggaran (new structure)
        if (subKegiatan.monitoring?.targets_by_sumber_anggaran) {
          const targetsBySumberAnggaran = subKegiatan.monitoring.targets_by_sumber_anggaran;

          // Aggregate targets from all sumber anggaran
          let totalSumberAnggaran = 0;

          Object.values(targetsBySumberAnggaran).forEach((sumberAnggaranData: any) => {
            if (sumberAnggaranData.targets?.length > 0) {
              totalSumberAnggaran++;
              sumberAnggaranData.targets.forEach((target: any, index: number) => {
                if (index < 4) {
                  // Accumulate values from all sumber anggaran
                  normalizedTargets[index].kinerja_fisik += target.kinerja_fisik || 0;
                  normalizedTargets[index].keuangan += target.keuangan || 0;
                }
              });
            }
          });

          // Calculate average for kinerja_fisik if we have multiple sumber anggaran
          if (totalSumberAnggaran > 1) {
            normalizedTargets.forEach(target => {
              target.kinerja_fisik = target.kinerja_fisik / totalSumberAnggaran;
            });
          }
        }
        // Fallback: check if we have direct targets (old structure)
        else if (subKegiatan.monitoring?.targets?.length > 0) {
          subKegiatan.monitoring.targets.forEach((target: any, index: number) => {
            if (index < 4) {
              normalizedTargets[index] = {
                kinerja_fisik: target.kinerja_fisik || 0,
                keuangan: target.keuangan || 0
              };
            }
          });
        }

        return normalizedTargets;
      };

      if (fundingData) {
        // Check each funding source
        const sources = [
          { key: 'dau', name: 'DAU' }, // ✅ FIXED: dau
          { key: 'dau_peruntukan', name: 'DAU Peruntukan' }, // ✅ FIXED: dau_peruntukan
          { key: 'dak_fisik', name: 'DAK Fisik' },
          { key: 'dak_non_fisik', name: 'DAK Non-Fisik' },
          { key: 'blud', name: 'BLUD' }
        ];

        // Calculate totals and collect active sources
        let totalPokok = 0;
        let totalParsial = 0;
        let totalPerubahan = 0;
        const activeSources: any[] = [];
        let hasActiveSource = false;

        sources.forEach(source => {
          const sourceKey = source.key as keyof typeof fundingData.sumber_anggaran;

          // ✅ FIXED: Akses nilai dari struktur nested yang benar
          const rencanaAwalValue = fundingData.values?.rencana_awal?.[source.key] || 0;
          const parsialValue = fundingData.values?.parsial?.[source.key] || 0;
          const budgetChangeValue = fundingData.values?.budget_change?.[source.key] || 0;

          // Total nilai dari semua kategori
          const totalValue = rencanaAwalValue + parsialValue + budgetChangeValue;

          // ✅ FIXED: Tampilkan data jika checkbox tercentang ATAU ada nilai yang tersimpan
          if (fundingData.sumber_anggaran[sourceKey] || totalValue > 0) {
            hasActiveSource = true;

            // Add to totals
            totalPokok += rencanaAwalValue;
            totalParsial += parsialValue;
            totalPerubahan += budgetChangeValue;

            // Get normalized targets for this source
            const targets = processMonitoringData(subKegiatan);

            // Store active source data
            activeSources.push({
              id: `${subKegiatan.id}-${source.key}`,
              subKegiatan: subKegiatan,
              kegiatan: parentKegiatan,
              program: parentProgram,
              bidangUrusan: parentBidangUrusan,
              sumberDana: source.name,
              pokok: rencanaAwalValue,
              parsial: parsialValue,
              perubahan: budgetChangeValue,
              normalizedTargets: targets,
              isChild: true // Mark as child row
            });
          }
        });

        // Create main row for subkegiatan with totals
        if (hasActiveSource) {
          const mainTargets = processMonitoringData(subKegiatan);

          result.push({
            id: `${subKegiatan.id}-main`,
            subKegiatan: subKegiatan,
            kegiatan: parentKegiatan,
            program: parentProgram,
            bidangUrusan: parentBidangUrusan,
            sumberDana: '', // Empty for main row as per design
            pokok: totalPokok,
            parsial: totalParsial,
            perubahan: totalPerubahan,
            normalizedTargets: mainTargets,
            isMain: true, // Mark as main row
            children: activeSources, // Store child data
            isExpanded: false // Default collapsed
          });
        }

        // If no active sources are found but funding data exists, create a default main row
        if (!hasActiveSource) {
          // Get normalized targets
          const targets = processMonitoringData(subKegiatan);

          result.push({
            id: `${subKegiatan.id}-main`,
            subKegiatan: subKegiatan,
            kegiatan: parentKegiatan,
            program: parentProgram,
            bidangUrusan: parentBidangUrusan,
            sumberDana: 'Belum diisi',
            pokok: 0,
            parsial: 0,
            perubahan: 0,
            normalizedTargets: targets,
            isMain: true,
            children: [],
            isExpanded: false
          });
        }
      }
      // If no funding data but has monitoring, create at least one main row for this subkegiatan
      else if (subKegiatan.monitoring) {
        // Get normalized targets
        const targets = processMonitoringData(subKegiatan);

        result.push({
          id: `${subKegiatan.id}-main`,
          subKegiatan: subKegiatan,
          kegiatan: parentKegiatan,
          program: parentProgram,
          bidangUrusan: parentBidangUrusan,
          sumberDana: subKegiatan.monitoring.sumber_dana || 'Multiple',
          pokok: subKegiatan.monitoring.pagu_pokok || 0,
          parsial: subKegiatan.monitoring.pagu_parsial || 0,
          perubahan: subKegiatan.monitoring.pagu_perubahan || 0,
          normalizedTargets: targets,
          isMain: true,
          children: [],
          isExpanded: false
        });
      }
      // If no funding data and no monitoring, still show the main row with zero values
      else {
        result.push({
          id: `${subKegiatan.id}-main`,
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
          ],
          isMain: true,
          children: [],
          isExpanded: false
        });
      }
    });

    return result;
  });

  // Note: Removed expand/collapse functionality to match triwulan interface
  // All hierarchies are now always expanded

  // Computed property to flatten the hierarchical data for display
  // Always show all children (like in triwulan interface)
  const flattenedSubKegiatanData = computed(() => {
    const result: any[] = [];

    formattedSubKegiatanData.value.forEach(item => {
      // Always add the main row
      result.push({
        ...item,
        isExpanded: true // Always expanded like in triwulan
      });

      // Always add child rows if has children (like in triwulan interface)
      if (item.children && item.children.length > 0) {
        item.children.forEach((child: any) => {
          result.push(child);
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

    // First, calculate sums for each kegiatan based on its subkegiatans (only main rows)
    formattedSubKegiatanData.value.filter(item => item.isMain).forEach(item => {
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

      // For main rows, check all editingTargets for this subKegiatan across all sumber dana
      if (item.isMain) {
        // Find all editingTargets keys that start with this subKegiatan ID
        const subKegiatanId = item.subKegiatan.id;
        Object.keys(editingTargets.value).forEach(key => {
          if (key.startsWith(`${subKegiatanId}-`)) {
            const targets = editingTargets.value[key];
            if (targets && Array.isArray(targets)) {
              targets.forEach((target: any, index: number) => {
                if (index < 4 && target) {
                  // Add kinerja_fisik from editing values (will be averaged later)
                  const kinerja_fisik = parseFloat(target.kinerja_fisik || '0');
                  if (!isNaN(kinerja_fisik) && kinerja_fisik > 0) {
                    kegiatanSums[kegiatanId].targets[index].kinerja_fisik += kinerja_fisik;
                    kegiatanSums[kegiatanId].targets[index].count++;
                    kegiatanSums[kegiatanId].targets[index].has_values = true;
                  }

                  // Sum keuangan from editing values
                  const keuangan = parseFloat(target.keuangan?.toString().replace(/[^\d]/g, '') || '0');
                  if (!isNaN(keuangan) && keuangan > 0) {
                    kegiatanSums[kegiatanId].targets[index].keuangan += keuangan;
                    kegiatanSums[kegiatanId].targets[index].has_values = true;
                  }
                }
              });
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
    formattedSubKegiatanData: flattenedSubKegiatanData, // Use flattened data for display
    hierarchicalSubKegiatanData: formattedSubKegiatanData, // Keep original hierarchical data
    calculateKegiatan,
    calculateProgram,
    calculateBidangUrusan,
    getUniqueKey,
  };
}
