<script setup lang="ts">
interface Props {
  selectedPeriodeId: number | null;
  semuaPeriodeAktif?: Array<{ 
    id: number; 
    tahap: { id: number; tahap: string }; 
    tahun: { id: number; tahun: string } 
  }>;
  tahunAktif?: { id: number; tahun: string } | null;
}

interface Emits {
  (e: 'periodeChange', event: Event): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const handlePeriodeChange = (event: Event) => {
  emit('periodeChange', event);
};
</script>

<template>
  <!-- Header section -->
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

      <!-- Period selector -->
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
            v-for="periode in semuaPeriodeAktif"
            :key="periode.id"
            :value="periode.id"
            :selected="periode.id === selectedPeriodeId"
          >
            {{ periode.tahap.tahap }} - {{ periode.tahun.tahun }}
          </option>
        </select>

        <div class="ml-4 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
          <span class="text-xs font-medium text-gray-500">Tahun Anggaran</span>
          <div class="text-lg font-bold text-blue-600 text-center">{{ tahunAktif?.tahun || 'Belum ada' }}</div>
        </div>
      </div>
    </div>
  </div>
</template> 