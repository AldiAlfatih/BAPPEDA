<script setup lang="ts">
interface Props {
  tugas: {
    kode_nomenklatur: {
      nomor_kode: string;
      nomenklatur: string;
    };
    skpd?: {
      nama_skpd?: string;
      nama_dinas?: string;
      namaSkpd?: string;
      namaDinas?: string;
      kode_organisasi?: string;
      kodeOrganisasi?: string;
      user_penanggung_jawab?: {
        name: string;
        user_detail?: {
          nip?: string;
        } | null;
      };
      skpd_kepala?: Array<{
        user?: {
          name: string;
          user_detail?: {
            nip?: string;
          } | null;
        };
      }>;
      tim_kerja?: Array<{
        operator?: {
          name: string;
          user_detail?: {
            nip?: string;
          } | null;
        };
        skpd?: {
          nama_skpd?: string;
          nama_dinas?: string;
          kode_organisasi?: string;
          kodeOrganisasi?: string;
        };
      }>;
    };
  };
  triwulanName: string;
  kepalaSkpd?: string;
}

const props = defineProps<Props>();

// Fungsi-fungsi utilitas
const getTimKerjaOperator = () => {
  if (props.tugas?.skpd?.tim_kerja && props.tugas.skpd.tim_kerja.length > 0) {
    const timKerja = props.tugas.skpd.tim_kerja[0];
    if (timKerja?.operator?.name) {
      return {
        name: timKerja.operator.name,
        nip: timKerja.operator.user_detail?.nip || null
      };
    }
  }
  return null;
};

const getSkpdName = () => {
  const skpd = props.tugas?.skpd;
  if (skpd?.nama_skpd) return skpd.nama_skpd;
  if (skpd?.nama_dinas) return skpd.nama_dinas;
  if (skpd?.namaSkpd) return skpd.namaSkpd;
  if (skpd?.namaDinas) return skpd.namaDinas;
  if (skpd?.tim_kerja && skpd.tim_kerja.length > 0) {
    const timKerja = skpd.tim_kerja[0];
    if (timKerja?.skpd?.nama_skpd) return timKerja.skpd.nama_skpd;
    if (timKerja?.skpd?.nama_dinas) return timKerja.skpd.nama_dinas;
  }
  return null;
};

const getKodeOrganisasi = () => {
  const skpd = props.tugas?.skpd;
  if (skpd?.kode_organisasi) return skpd.kode_organisasi;
  if (skpd?.kodeOrganisasi) return skpd.kodeOrganisasi;
  if (skpd?.tim_kerja && skpd.tim_kerja.length > 0) {
    const timKerja = skpd.tim_kerja[0];
    if (timKerja?.skpd?.kode_organisasi) return timKerja.skpd.kode_organisasi;
    if (timKerja?.skpd?.kodeOrganisasi) return timKerja.skpd.kodeOrganisasi;
  }
  return null;
};

const getKepalaSkpd = () => {
  if (props.tugas?.skpd?.user_penanggung_jawab?.name) {
    const name = props.tugas.skpd.user_penanggung_jawab.name;
    const nip = props.tugas.skpd.user_penanggung_jawab.user_detail?.nip;
    return {
      name: name,
      nip: nip || null
    };
  }
  if (props.tugas?.skpd?.skpd_kepala && props.tugas.skpd.skpd_kepala.length > 0) {
    const kepala = props.tugas.skpd.skpd_kepala[0];
    if (kepala?.user?.name) {
      const name = kepala.user.name;
      const nip = kepala.user.user_detail?.nip;
      return {
        name: name,
        nip: nip || null
      };
    }
  }
  return props.kepalaSkpd ? { name: props.kepalaSkpd, nip: null } : null;
};
</script>

<template>
  <!-- Header section -->
  <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
    <div class="flex items-center mb-6">
      <div class="rounded-full bg-blue-100 p-3 mr-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-600">{{ triwulanName }}</h2>
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
        <div v-if="getKepalaSkpd()">
          <p class="text-lg font-semibold text-gray-500">{{ getKepalaSkpd()?.name || 'Tidak tersedia' }}</p>
          <p v-if="getKepalaSkpd()?.nip" class="text-sm font-mono text-gray-500">NIP: {{ getKepalaSkpd()?.nip }}</p>
        </div>
        <p v-else class="text-lg font-semibold text-gray-500">Tidak tersedia</p>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Penanggung Jawab</h3>
        <div v-if="getTimKerjaOperator()">
          <p class="text-lg font-semibold text-gray-500">{{ getTimKerjaOperator()?.name || 'Tidak tersedia' }}</p>
          <p v-if="getTimKerjaOperator()?.nip" class="text-sm font-mono text-gray-500">NIP: {{ getTimKerjaOperator()?.nip }}</p>
        </div>
        <p v-else class="text-lg font-semibold text-gray-500">Tidak tersedia</p>
      </div>
    </div>
  </div>
</template>
