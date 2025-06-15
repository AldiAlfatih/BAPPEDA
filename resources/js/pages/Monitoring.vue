<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
// Update the import path and extension if needed, for example:
import TabelListSKPD from '@/components/data/TabelListSKPD.vue';
// Or, if the file is named differently or in another folder, adjust accordingly:
// import { TabelListSKPD } from '@/components/data/TabelListSkpd.vue';
// import { TabelListSKPD } from '@/components/TabelListSKPD.vue';
<<<<<<< HEAD
import { 
  Plus, 
  Pencil, 
  Eye, 
  Search, 
  ChevronLeft, 
  ChevronRight, 
=======
import {
  Plus,
  Pencil,
  Eye,
  Search,
  ChevronLeft,
  ChevronRight,
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
  ArrowUpDown,
  FileText,
  Info,
  Building2,
  User
} from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      user_detail?: {
        nip?: string;
      } | null;
      skpd: {
        nama_dinas: string;
        nama_operator: string;
        kode_organisasi: string;
      } | null;
    }>;
  };
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Monitoring', href: '/rencana-awal' },
];


</script>

<template>
  <Head title="Monitoring" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Monitoring Perangkat Daerah</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data Monitoring Perangkat Daerah</p>
        </div>
<<<<<<< HEAD
        <!-- <Button 
          class="flex items-center gap-2 shadow-lg transition-all duration-300 transform hover:scale-105" 
          @click="goToCreatePage"
          :disabled="loadingCreate"
        >
          <Plus class="w-4 h-4" />
          <span v-if="loadingCreate">Membuka...</span>
          <span v-else>Tambahkan PD</span>
        </Button> -->
=======
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
      </div>
      <TabelListSKPD url_detail="monitoring.show" :users="users"></TabelListSKPD>
    </div>
  </AppLayout>
</template>
