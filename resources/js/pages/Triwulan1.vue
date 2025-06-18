<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import TabelListSKPD from '@/components/data/TabelListSKPD.vue';
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
      skpd: {
        nama_dinas: string;
        nama_operator: string;
        no_dpa: string;
        kode_organisasi: string;
      } | null;
    }>;
  };
}>();

// State untuk tabel
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('name');
const sortDirection = ref('asc');
const showDetailId = ref<number | null>(null);
const loadingCreate = ref(false);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Monitoring Triwulan 1', href: route('triwulan.index', { tid: 1 }) },
];

// Filter dan Sorting
const filteredData = computed(() => {
  if (!props.users || !props.users.data) return [];
  let data = [...props.users.data];

  // Filter berdasarkan pencarian
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    data = data.filter(item =>
      (item.name || '').toLowerCase().includes(query) ||
      (item.skpd?.nama_dinas || '').toLowerCase().includes(query) ||
      (item.skpd?.nama_operator || '').toLowerCase().includes(query) ||
      (item.skpd?.no_dpa || '').toLowerCase().includes(query) ||
      (item.skpd?.kode_organisasi || '').toLowerCase().includes(query)
    );
  }

  // Sorting
  data.sort((a, b) => {
    let aVal = getFieldValue(a, sortField.value);
    let bVal = getFieldValue(b, sortField.value);

    // Handle null values
    if (aVal === null || aVal === undefined) aVal = '';
    if (bVal === null || bVal === undefined) bVal = '';

    // String comparison
    if (typeof aVal === 'string' && typeof bVal === 'string') {
      return sortDirection.value === 'asc'
        ? aVal.localeCompare(bVal)
        : bVal.localeCompare(aVal);
    }

    // Number comparison
    return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
  });

  return data;
});

// Pagination
const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value));
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredData.value.slice(start, end);
});

// Helper untuk mendapatkan nilai field untuk sorting
function getFieldValue(item: any, field: string) {
  switch(field) {
    case 'nama_dinas':
      return item.skpd?.nama_dinas || '';
    case 'nama_operator':
      return item.skpd?.nama_operator || '';
    case 'no_dpa':
      return item.skpd?.no_dpa || '';
    case 'kode_organisasi':
      return item.skpd?.kode_organisasi || '';
    default:
      return item[field] || '';
  }
}

// Toggle sorting
function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
}

// Navigation
// function goToCreatePage() {
//   loadingCreate.value = true;
//   router.visit('/perangkatdaerah/create', {
//     onFinish: () => (loadingCreate.value = false),
//   });
// }

// function goToEditPage(id: number) {
//   router.visit(route('perangkatdaerah.edit', { id }));
// }

function goToShowPage(id: number) {
  router.visit(route('triwulan.show', { tid: 1, id }));
}

function ShowTugas(tugasId: number, userId: number) {
  router.visit(route('triwulan.detail', { tid: 1, id: userId, taskId: tugasId }));
}

function toggleDetail(id: number) {
  if (showDetailId.value === id) {
    showDetailId.value = null;
  } else {
    showDetailId.value = id;
  }
}

// Reset pagination ketika search berubah
function handleSearchChange() {
  currentPage.value = 1;
}

// Truncate text
function truncateText(text: string | null | undefined, length: number = 30): string {
  if (!text) return '-';
  return text.length > length ? text.slice(0, length) + '...' : text;
}
</script>

<template>
  <Head title="Monitoring Triwulan 1" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Monitoring Triwulan 1</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data</p>
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

      <TabelListSKPD url_detail=" triwulan.show" :users="users"></TabelListSKPD>
    </div>
  </AppLayout>
</template>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Transisi hover untuk baris */
.table-row-hover {
  transition: background-color 0.2s ease;
}

</style>
