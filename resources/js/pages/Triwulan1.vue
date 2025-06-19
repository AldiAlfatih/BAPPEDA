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
      skpd: Array<{
        nama_dinas: string;
        nama_operator: string;
        no_dpa: string;
        kode_organisasi: string;
      }> | null;
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
    data = data.filter(item => {
      const skpdList = Array.isArray(item.skpd) ? item.skpd : [];
      // Gabungkan semua nama dinas, operator, kepala skpd, no dpa, kode organisasi dari seluruh skpd user
      const namaDinas = skpdList.map(s => s?.nama_dinas || '').join(' ').toLowerCase();
      const namaOperator = skpdList.map(s => s?.nama_operator || '').join(' ').toLowerCase();
      const namaKepalaSkpd = skpdList.map(s => s?.nama_kepala_skpd || '').join(' ').toLowerCase();
      const noDpa = skpdList.map(s => s?.no_dpa || '').join(' ').toLowerCase();
      const kodeOrganisasi = skpdList.map(s => s?.kode_organisasi || '').join(' ').toLowerCase();
      return (
        (item.name || '').toLowerCase().includes(query) ||
        namaDinas.includes(query) ||
        namaOperator.includes(query) ||
        namaKepalaSkpd.includes(query) ||
        noDpa.includes(query) ||
        kodeOrganisasi.includes(query)
      );
    });
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
  const skpdList = Array.isArray(item.skpd) ? item.skpd : [];
  const firstSkpd = skpdList.length > 0 ? skpdList[0] : null;
  
  switch(field) {
    case 'nama_dinas':
      return firstSkpd?.nama_dinas || '';
    case 'nama_operator':
      return firstSkpd?.nama_operator || '';
    case 'nama_kepala_skpd':
      return firstSkpd?.nama_kepala_skpd || '';
    case 'no_dpa':
      return firstSkpd?.no_dpa || '';
    case 'kode_organisasi':
      return firstSkpd?.kode_organisasi || '';
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

<<<<<<< HEAD
      <!-- Search dan filter -->
      <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="relative w-full sm:w-96">
          <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
          <Input
            v-model="searchQuery"
            placeholder="Cari nama dinas, penanggung jawab, kepala SKPD, kode..."
            class="pl-10 pr-4 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
            @input="handleSearchChange"
          />
        </div>
        <div class="flex gap-2 items-center">
          <span class="text-sm text-gray-500">Tampilkan:</span>
          <select v-model="itemsPerPage" class="rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>

      <!-- Main Card dengan Table -->
      <Card class="shadow-lg border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl">
        <CardContent class="p-0">
          <div class="overflow-x-auto">
            <Table>
              <TableHeader class="bg-gray-50 dark:bg-gray-800">
                <TableRow>
                  <TableHead class="w-16 text-center">No</TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('nama_dinas')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Nama Dinas
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity"
                        :class="{'opacity-100': sortField === 'nama_dinas'}" />
                    </div>
                  </TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('nama_operator')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Operator/Tim
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity"
                        :class="{'opacity-100': sortField === 'nama_operator'}" />
                    </div>
                  </TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('nama_kepala_skpd')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Kepala SKPD
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity"
                        :class="{'opacity-100': sortField === 'nama_kepala_skpd'}" />
                    </div>
                  </TableHead>
                  <TableHead class="hidden md:table-cell text-gray-600">No DPA</TableHead>
                  <TableHead class="hidden md:table-cell text-gray-600">Kode Organisasi</TableHead>
                  <TableHead class="text-gray-600">Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <template v-if="paginatedData.length > 0">
                  <template v-for="(user, index) in paginatedData" :key="user.id">
                    <!-- Baris utama -->
                    <TableRow class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer"
                      :class="{'bg-blue-50 dark:bg-blue-900/20': showDetailId === user.id}"
                      @click="toggleDetail(user.id)">
                      <TableCell class="text-center font-medium text-gray-500">
                        {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                      </TableCell>
                      <TableCell class="font-medium">
                        <div class="flex items-center gap-2 text-gray-500">
                          <span>{{ truncateText(getFieldValue(user, 'nama_dinas')) }}</span>
                          <TooltipProvider v-if="getFieldValue(user, 'nama_dinas') && getFieldValue(user, 'nama_dinas').length > 30">
                            <Tooltip>
                              <TooltipTrigger>
                                <Info class="w-4 h-4 text-blue-500" />
                              </TooltipTrigger>
                              <TooltipContent>
                                <div class="max-w-md p-2">{{ getFieldValue(user, 'nama_dinas') }}</div>
                              </TooltipContent>
                            </Tooltip>
                          </TooltipProvider>
                        </div>
                      </TableCell>
                      <TableCell class="text-gray-500">{{ getFieldValue(user, 'nama_operator') }}</TableCell>
                      <TableCell class="text-gray-500">{{ getFieldValue(user, 'nama_kepala_skpd') }}</TableCell>
                      <TableCell class="hidden md:table-cell font-mono text-gray-500">{{ getFieldValue(user, 'no_dpa') }}</TableCell>
                      <TableCell class="hidden md:table-cell font-mono text-gray-500">{{ getFieldValue(user, 'kode_organisasi') }}</TableCell>
                      <TableCell>
                        <div class="flex items-center gap-2">
                          <!-- <Button size="sm" class="bg-green-600 hover:bg-green-700 text-white"
                            @click.stop="goToEditPage(user.id)">
                            <Pencil class="w-4 h-4 mr-2" />
                            <span class="hidden sm:inline">Edit</span>
                          </Button> -->
                          <Button size="sm" class="bg-orange-500 hover:bg-orange-700 text-white"
                            @click.stop="goToShowPage(user.id)">
                            <Eye class="w-4 h-4 mr-1" />
                            <span class="hidden sm:inline">Detail</span>
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>

                    <!-- Detail ekspansi -->
                    <TableRow v-if="showDetailId === user.id" class="bg-blue-50/50 dark:bg-blue-900/10">
                      <TableCell colspan="7" class="animate-fadeIn">
                        <div class="p-4 space-y-3">
                          <h3 class="text-lg font-semibold text-gray-600">Detail Perangkat Daerah</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="flex items-center gap-3">
                              <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                                <Building2 class="h-12 w-12 text-blue-600" />
                              </div>
                              <div>
                                <p class="text-sm text-gray-600">ID Perangkat Daerah:</p>
                                <p class="font-mono text-lg text-gray-400">{{ user.id }}</p>
                              </div>
                            </div>
                            <div class="flex items-center gap-3">
                              <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                                <User class="h-12 w-12 text-green-600" />
                              </div>
                              <div>
                                <p class="text-sm text-gray-600">Kepala SKPD:</p>
                                <p class="font-medium text-lg text-gray-500">{{ getFieldValue(user, 'nama_kepala_skpd') }}</p>
                              </div>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Nama Dinas:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'nama_dinas') }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Operator/Penanggung Jawab Tim:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'nama_operator') }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Penanggung Jawab SKPD:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ user.name || '-' }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">No DPA:</p>
                              <p class="font-mono text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'no_dpa') }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Kode Organisasi:</p>
                              <p class="font-mono text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'kode_organisasi') }}</p>
                            </div>
                          </div>
                          <div class="pt-3 flex justify-end gap-2">
                            <!-- <Button size="sm" class="bg-green-600 hover:bg-green-700 text-white"
                              @click.stop="goToEditPage(user.id)">
                              <Pencil class="w-4 h-4 mr-2" />
                              Edit Data
                            </Button> -->
                            <Button size="sm" class="bg-orange-500 hover:bg-orange-700 text-white"
                              @click.stop="goToShowPage(user.id)">
                              <Eye class="w-4 h-4 mr-1" />
                              Lihat Detail Lengkap
                            </Button>
                          </div>
                        </div>
                      </TableCell>
                    </TableRow>
                  </template>
                </template>
                <TableRow v-else>
                  <TableCell colspan="7" class="text-center py-10">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <p class="text-gray-500 text-lg">Tidak ada data yang ditemukan</p>
                      <p class="text-gray-400 text-sm" v-if="searchQuery">Coba ubah kriteria pencarian</p>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} sampai
          {{ Math.min(currentPage * itemsPerPage, filteredData.length) }}
          dari {{ filteredData.length }} data
        </div>
        <div class="flex gap-2 items-center">
          <Button
            variant="outline"
            size="sm"
            @click="currentPage = Math.max(1, currentPage - 1)"
            :disabled="currentPage === 1"
            class="flex items-center gap-1"
          >
            <ChevronLeft class="w-4 h-4" />
            <span class="hidden sm:inline">Sebelumnya</span>
          </Button>

          <div class="hidden sm:flex gap-1">
            <Button
              v-for="page in totalPages"
              :key="page"
              :variant="page === currentPage ? 'default' : 'outline'"
              size="sm"
              @click="currentPage = page"
              class="w-10 h-10"
            >
              {{ page }}
            </Button>
          </div>

          <div class="sm:hidden">
            <span class="text-sm">{{ currentPage }} / {{ totalPages }}</span>
          </div>

          <Button
            variant="outline"
            size="sm"
            @click="currentPage = Math.min(totalPages, currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="flex items-center gap-1"
          >
            <span class="hidden sm:inline">Selanjutnya</span>
            <ChevronRight class="w-4 h-4" />
          </Button>
        </div>
      </div>
=======
      <TabelListSKPD url_detail=" triwulan.show" :users="users"></TabelListSKPD>
>>>>>>> 1653c22a8692dd307d928021242200888c562522
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
