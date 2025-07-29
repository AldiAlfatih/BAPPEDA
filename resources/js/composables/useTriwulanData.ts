import { computed } from 'vue';

interface Props {
  subkegiatanTugas?: Array<any>;
  kegiatanTugas?: Array<any>;
  programTugas?: Array<any>;
  bidangurusanTugas?: Array<any>;
  monitoringTargets?: Array<any>;
  monitoringRealisasi?: Array<any>;
  periode?: {
    id: number;
    nama: string;
  };
  [key: string]: any; // Allow additional properties
}

export function useTriwulanData(props: Props) {
  // Sumber dana yang tersedia
  const sources = [
    { key: 'dau', name: 'DAU' }, // ✅ FIXED: dau
    { key: 'dau_peruntukan', name: 'DAU Peruntukan' }, // ✅ FIXED: dau_peruntukan
    { key: 'dak_fisik', name: 'DAK Fisik' },
    { key: 'dak_non_fisik', name: 'DAK Non Fisik' },
    { key: 'blud', name: 'BLUD' }
  ];

  // Helper function untuk mendapatkan data sumber anggaran dari struktur real
  function getSumberAnggaranData(subKegiatan: any) {
    // Cari data monitoring dari subKegiatan
    const monitoring = subKegiatan.monitoring?.[0];
    if (!monitoring?.anggaran || monitoring.anggaran.length === 0) {
      return [];
    }

    // Return array of sumber dana dengan data masing-masing
    return monitoring.anggaran.map((anggaran: any) => {
      const sumberAnggaran = anggaran.sumberAnggaran;

      // Cari pagu berdasarkan kategori (1 = pokok/rencana awal)
      const paguPokok = anggaran.pagu?.find((p: any) => p.kategori === 1);
      const paguParsial = anggaran.pagu?.find((p: any) => p.kategori === 2);
      const paguPerubahan = anggaran.pagu?.find((p: any) => p.kategori === 3);

      return {
        sumberDana: sumberAnggaran?.nama || 'Unknown',
        nilai: paguPokok?.dana || 0, // Gunakan pagu pokok untuk kolom pokok
        nilaiParsial: paguParsial?.dana || 0,
        nilaiPerubahan: paguPerubahan?.dana || 0,
        kategori: 1 // Default ke kategori pokok
      };
    });
  }

  // Helper function untuk mendapatkan target dan realisasi data
  function getMonitoringData(subKegiatan: any, sumberDana?: string) {
    const targets = props.monitoringTargets?.filter(t =>
      t.task_id === subKegiatan.id && t.periode_id === props.periode?.id
    ) || [];

    const realisasi = props.monitoringRealisasi?.filter(r =>
      r.task_id === subKegiatan.id && r.periode_id === props.periode?.id
    ) || [];

    // Jika ada sumber dana spesifik, filter berdasarkan sumber dana
    // Untuk saat ini, kita ambil data pertama yang tersedia
    const target = targets[0];
    const realisasiData = realisasi[0];

    return {
      target: target ? {
        kinerja_fisik: target.kinerja_fisik || 0,
        keuangan: target.keuangan || 0
      } : { kinerja_fisik: 0, keuangan: 0 },
      realisasi: realisasiData ? {
        kinerja_fisik: realisasiData.kinerja_fisik || 0,
        keuangan: realisasiData.keuangan || 0,
        deskripsi: realisasiData.deskripsi || '',
        nama_pptk: realisasiData.nama_pptk || ''
      } : { kinerja_fisik: 0, keuangan: 0, deskripsi: '', nama_pptk: '' }
    };
  }

  // Computed property untuk format data subkegiatan dengan multiple sumber dana
  const formattedSubKegiatanData = computed(() => {
    const result: any[] = [];

    if (!props.subkegiatanTugas) {
      return result;
    }

    props.subkegiatanTugas.forEach(subKegiatan => {
      // Cari parent kegiatan, program, dan bidang urusan
      const parentKegiatan = props.kegiatanTugas?.find(k =>
        subKegiatan.kode_nomenklatur?.nomor_kode?.startsWith(k.kode_nomenklatur?.nomor_kode)
      );

      const parentProgram = props.programTugas?.find(p =>
        parentKegiatan?.kode_nomenklatur?.nomor_kode?.startsWith(p.kode_nomenklatur?.nomor_kode)
      );

      const parentBidangUrusan = props.bidangurusanTugas?.find(b =>
        parentProgram?.kode_nomenklatur?.nomor_kode?.startsWith(b.kode_nomenklatur?.nomor_kode)
      );

      // Dapatkan data sumber anggaran (array of sumber dana)
      const sumberAnggaranArray = getSumberAnggaranData(subKegiatan);

      if (sumberAnggaranArray && sumberAnggaranArray.length > 0) {
        // Untuk setiap sumber dana, buat baris terpisah
        sumberAnggaranArray.forEach(sumberAnggaranData => {
          // Dapatkan data monitoring untuk sumber dana ini
          const monitoringData = getMonitoringData(subKegiatan);

          result.push({
            id: `${subKegiatan.id}-${sumberAnggaranData.sumberDana}`,
            originalId: subKegiatan.id,
            subKegiatan: subKegiatan,
            kegiatan: parentKegiatan,
            program: parentProgram,
            bidangUrusan: parentBidangUrusan,
            sumberDana: sumberAnggaranData.sumberDana,
            pokok: sumberAnggaranData.nilai,
            parsial: sumberAnggaranData.nilaiParsial || 0,
            perubahan: sumberAnggaranData.nilaiPerubahan || 0,
            target: monitoringData.target,
            realisasi: monitoringData.realisasi
          });
        });
      } else {
        // Jika tidak ada data sumber anggaran, tampilkan dengan nilai default
        const monitoringData = getMonitoringData(subKegiatan);
        result.push({
          id: `${subKegiatan.id}-default`,
          originalId: subKegiatan.id,
          subKegiatan: subKegiatan,
          kegiatan: parentKegiatan,
          program: parentProgram,
          bidangUrusan: parentBidangUrusan,
          sumberDana: 'Belum diisi',
          pokok: 0,
          parsial: 0,
          perubahan: 0,
          target: monitoringData.target,
          realisasi: monitoringData.realisasi
        });
      }
    });

    return result;
  });

  // Helper untuk key unik per baris subkegiatan (id + sumberDana)
  function getUniqueKey(subKegiatan: any, sumberDana: string) {
    return `${subKegiatan.id}-${sumberDana}`;
  }

  return {
    formattedSubKegiatanData,
    getUniqueKey,
    sources
  };
}
