import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { getSumberAnggaranId, normalizeKey } from '@/utils/formatters';

export function useTargetEditor(props: any, selectedPeriodeId: any, refreshData: () => void) {
  // State untuk edit target subkegiatan
  const editingTargets = ref<Record<string, any>>({});
  const loadingRow = ref<string | null>(null);
  const successRow = ref<string | null>(null);
  const errorRow = ref<string | null>(null);

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

  // Method untuk handle input target
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

  // Fungsi simpan target
  async function saveTargets(subKegiatan: any, sumberDana?: string) {
    // Cari sumberDana dari formattedSubKegiatanData jika tidak ada di parameter
    let finalSumberDana: string = sumberDana || '';
    
    if (!finalSumberDana) {
      // Jika sumberDana tidak ada di parameter, cari dari subKegiatan
      finalSumberDana = subKegiatan.sumberDana || 'APBD';
    }
    
    const uniqueKey = getUniqueKey(subKegiatan, finalSumberDana);
    loadingRow.value = uniqueKey;
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
        sumber_dana: finalSumberDana,
        periode_id: selectedPeriodeId.value,
        nama_pptk: nama_pptk,
        pagu: {
          pokok: paguValue,
          parsial: 0,
          perubahan: 0
        }
      };
      
      await router.post('/rencanaawal/save-target', payload, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (response: any) => {
          successRow.value = uniqueKey;
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
          errorRow.value = uniqueKey;
          
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

    } catch (e: any) {
      console.error('Exception:', e);
      errorRow.value = uniqueKey;
      loadingRow.value = null;
      alert(e.message || 'Terjadi kesalahan saat memproses data');
    }
  }

  // Fungsi hapus target
  async function deleteTargets(subKegiatan: any, sumberDana?: string) {
    // Cari sumberDana dari formattedSubKegiatanData jika tidak ada di parameter
    let finalSumberDana: string = sumberDana || '';
    
    if (!finalSumberDana) {
      finalSumberDana = subKegiatan.sumberDana || 'APBD';
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

      await router.post(`/rencanaawal/delete-target`, payload, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (response) => {
          successRow.value = uniqueKey;
          
          // Reset form values to empty
          if (editingTargets.value[uniqueKey]) {
            editingTargets.value[uniqueKey] = [0, 1, 2, 3].map(i => ({
              kinerja_fisik: '',
              keuangan: '',
            }));
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

  // Update ensureEditingTargets agar pakai key unik
  function ensureEditingTargets(formattedSubKegiatanData: any) {
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

  return {
    editingTargets,
    loadingRow,
    successRow,
    errorRow,
    getInitialTargets,
    getUniqueKey,
    startEdit,
    onInputTarget,
    saveTargets,
    deleteTargets,
    ensureEditingTargets
  };
} 