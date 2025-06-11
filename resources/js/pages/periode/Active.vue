<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

interface Periode {
  id: number;
  tahap: {
    id: number;
    tahap: string;
  };
  tanggal_mulai: string;
  tanggal_selesai: string;
  status: number;
}

const props = defineProps<{
  periode: Periode[];
  error?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Periode Aktif', href: route('periode.belum-selesai') },
];

function formatDate(date: string) {
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(date).toLocaleDateString('id-ID', options);
}

function getStatusLabel(status: number) {
  if (status === 1) return 'Terbuka';
  if (status === 0) return 'Tertutup';
  return 'Selesai';
}

function getStatusClass(status: number) {
  if (status === 1) return 'bg-green-100 text-green-800';
  if (status === 0) return 'bg-gray-100 text-gray-800';
  return 'bg-blue-100 text-blue-800';
}
</script>

<template>
  <Head title="Periode Aktif" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 bg-white rounded-lg shadow">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Periode Aktif</h1>
        <a 
          :href="route('periode.index')" 
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
        >
          Kembali ke Daftar Periode
        </a>
      </div>

      <div v-if="error" class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg">
        {{ error }}
      </div>

      <div v-if="periode.length === 0" class="p-8 text-center text-gray-500">
        <div class="mb-2 text-5xl">📅</div>
        <h3 class="mb-2 text-xl font-medium">Tidak Ada Periode Aktif</h3>
        <p>Semua periode saat ini sedang tertutup atau sudah selesai.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahap</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(p, index) in periode" :key="p.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ p.tahap.tahap }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(p.tanggal_mulai) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(p.tanggal_selesai) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                  :class="getStatusClass(p.status)"
                >
                  {{ getStatusLabel(p.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template> 