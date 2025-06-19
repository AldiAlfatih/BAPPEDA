<script setup lang="ts">
import { getUserNip } from '@/utils/formatters';

interface Props {
  tugas?: any;
  user?: any;
}

const props = defineProps<Props>();
</script>

<template>
  <!-- Information Card -->
  <div class="rounded-lg bg-white p-4 shadow-lg border border-gray-100">
    <div class="flex items-center mb-2">
      <h2 class="text-lg font-semibold text-gray-600 mb-2">Informasi Perangkat Daerah</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-2">KODE/URUSAN PEMERINTAHAN:</h3>
        <p class="text-lg font-semibold text-gray-500">
          {{ tugas?.kode_nomenklatur?.nomor_kode }} - {{ tugas?.kode_nomenklatur?.nomenklatur }}
        </p>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
        <p class="text-lg font-semibold text-gray-500">{{ tugas?.skpd?.nama_dinas || 'Tidak tersedia' }}</p>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
        <p class="text-lg font-semibold text-gray-500">{{ tugas?.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
        <p class="text-lg font-semibold text-gray-500">{{ tugas?.skpd?.nama_skpd || 'Tidak tersedia' }}</p>
        <p class="text-sm font-mono text-gray-500">{{ getUserNip(user || {}) || 'Tidak tersedia' }}</p>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama Penanggung Jawab</h3>
        <p class="text-lg font-semibold text-gray-500">{{ tugas?.skpd?.nama_operator || 'Tidak tersedia' }}</p>
        <p class="text-sm font-mono text-gray-500">{{ tugas?.skpd?.nip_operator || '-' }}</p>
      </div>
    </div>
  </div>
</template> 