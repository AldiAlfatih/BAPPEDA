<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import TabelList from '@/components/Triwulan/TabelList.vue';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      skpd: Array<{
        nama_dinas: string;
        nama_operator: string;
        no_dpa: string;
        kode_organisasi: string;
      }> | null;
    }>;
  };
  tid: number;
  tahun: number;
  periode: any;
  triwulanName: string;
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Monitoring Triwulan', href: route('triwulan.index', { tid: props.tid }) },
];

function goToShowPage(id: number) {
  router.visit(route('triwulan.show', { tid: props.tid, id }));
}

</script>

<template>
  <Head :title="`Monitoring ${triwulanName}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Monitoring {{ triwulanName }}</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data monitoring triwulan</p>
        </div>
      </div>

      <!-- TabelList Komponen -->
      <TabelList :users="props.users.data" @showDetail="goToShowPage" />
    </div>
  </AppLayout>
</template>

<style scoped>
/* Transisi hover untuk baris */
.table-row-hover {
  transition: background-color 0.2s ease;
}
</style>