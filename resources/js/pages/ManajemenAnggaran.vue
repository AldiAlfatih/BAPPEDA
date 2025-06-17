<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import TabelListSKPD from '@/components/data/TabelListSKPD.vue';
import { Button } from '@/components/ui/button';
import {
  Plus,
  Pencil,
  Eye,
  Search,
  ChevronLeft,
  ChevronRight,
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
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      user_detail?: {
        nip?: string;
      } | null;
      nama_dinas: string | null;
      operator_name: string | null;
      kepala_name: string | null;
      kode_organisasi: string | null;
    }>;
  };
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Manajemen Anggaran', href: '/manajemenanggaran' },
];


function goToShowPage(id: number) {
  router.visit(route('manajemenanggaran.show', { id }));
}

</script>

<template>
  <Head title="Monitoring" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Anggaran Perangkat Daerah</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data Manajemen Anggaran Perangkat Daerah</p>
        </div>
        <!-- <Button
          class="flex items-center gap-2 shadow-lg transition-all duration-300 transform hover:scale-105"
          @click="goToCreatePage"
          :disabled="loadingCreate"
        >
          <Plus class="w-4 h-4" />
          <span v-if="loadingCreate">Membuka...</span>
          <span v-else>Tambahkan PD</span>
        </Button> -->
      </div>
      <TabelListSKPD url_detail="manajemenanggaran.show" :users="users"></TabelListSKPD>
    </div>
  </AppLayout>
</template>
