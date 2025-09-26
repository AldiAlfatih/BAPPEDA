<script setup lang="ts">
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { 
  Plus, 
  Pencil, 
  Eye, 
  Search, 
  ArrowUpDown,
  Info,
  User,
  Binoculars
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
import { Pagination } from '@/components/ui/pagination';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      operator_name: string | null;
      operator_nip: string | null;
      kepala_name: string | null;
      kepala_nip: string | null;
      skpd: Array<{
        id: number;
        nama_skpd: string;
        kode_organisasi: string;

        kepalaAktif?: {
          user?: {
            name: string;
            userDetail?: {
              nip?: string;
            };
          };
        };
        operatorAktif?: {
          operator?: {
            name: string;
            userDetail?: {
              nip?: string;
            };
          };
        };
      }>;
    }>;
  };
  filters?: {
    search?: string;
  };
}>();

// ====== ROLE & ACCESS ======
const page = usePage<{
  props: {
    auth?: {
      user?: {
        role?: string;
        roles?: string[];
      };
    };
  };
}>();

// Flag role operator (cover 2 skema: single role string / array of roles)
const isOperator = computed<boolean>(() => {
  const u = page.props?.auth?.user ?? {};
  return u?.role === 'operator' || (Array.isArray(u?.roles) && u.roles.includes('operator'));
});

// ====== State untuk tabel ======
const searchQuery = ref(props.filters?.search || '');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('nama_dinas');
const sortDirection = ref<'asc' | 'desc'>('asc');
const showDetailId = ref<number | null>(null);
const loadingCreate = ref(false);

// ====== Breadcrumbs ======
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/manajemen-tim/perangkatdaerah' },
];

// ====== Helpers ======
function getNamaDinas(user: any): string {
  return user.skpd?.[0]?.nama_skpd || '-';
}

function getKodeOrganisasi(user: any): string {
  return user.skpd?.[0]?.kode_organisasi || '-';
}

function getOperatorNip(user: any): string {
  if (user.operator_nip) return user.operator_nip;
  return user.skpd?.[0]?.operatorAktif?.operator?.userDetail?.nip || '-';
}

function getKepalaNip(user: any): string {
  if (user.kepala_nip) return user.kepala_nip;
  return user.skpd?.[0]?.kepalaAktif?.user?.userDetail?.nip || '-';
}

// ====== Filter & Sorting ======
const filteredData = computed(() => {
  const query = searchQuery.value.toLowerCase();
  let data = [...props.users.data];
  
  if (query) {
    data = data.filter(item => 
      getNamaDinas(item).toLowerCase().includes(query) ||
      (item.operator_name || '').toLowerCase().includes(query) ||
      (item.kepala_name || '').toLowerCase().includes(query) ||
      getKodeOrganisasi(item).toLowerCase().includes(query) ||
      getOperatorNip(item).toLowerCase().includes(query) ||
      getKepalaNip(item).toLowerCase().includes(query)
    );
  }

  if (sortField.value) {
    data.sort((a, b) => {
      const aVal = getFieldValue(a, sortField.value);
      const bVal = getFieldValue(b, sortField.value);
      
      if (aVal === null || aVal === undefined) return 1;
      if (bVal === null || bVal === undefined) return -1;
      
      // Numeric comparison
      if (!isNaN(aVal as any) && !isNaN(bVal as any)) {
        return sortDirection.value === 'asc'
          ? Number(aVal) - Number(bVal)
          : Number(bVal) - Number(aVal);
      }
      
      // String comparison
      return sortDirection.value === 'asc'
        ? String(aVal).localeCompare(String(bVal))
        : String(bVal).localeCompare(String(aVal));
    });
  }
  
  return data;
});

// Pagination
const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value));
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredData.value.slice(start, end);
});

// Sorting helper
function getFieldValue(item: any, field: string) {
  switch(field) {
    case 'nama_dinas':
      return getNamaDinas(item);
    case 'operator_name':
      return item.operator_name || '';
    case 'kepala_name':
      return item.kepala_name || '';
    case 'kode_organisasi':
      return getKodeOrganisasi(item);
    default:
      return item[field] || '';
  }
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
}

// ====== Navigation (guarded) ======
function goToCreatePage() {
  if (isOperator.value) return; // block operator
  loadingCreate.value = true;
  router.visit('/manajemen-tim/perangkatdaerah/create', {
    onFinish: () => (loadingCreate.value = false),
  });
}

function goToEditPage(id: number) {
  if (isOperator.value) return; // block operator
  router.visit(route('perangkatdaerah.edit', { id }));
}

function goToShowPage(id: number) {
  router.visit(route('perangkatdaerah.show', { id }));
}

function toggleDetail(id: number) {
  showDetailId.value = showDetailId.value === id ? null : id;
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
  <Head title="Perangkat Daerah" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-600 dark:text-gray-100">Perangkat Daerah</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ isOperator ? 'Lihat data perangkat daerah ' : 'Kelola data perangkat daerah' }}
          </p>
        </div>

        <!-- Tambah PD: tersembunyi untuk Operator -->
        <Button 
          v-if="!isOperator"
          class="flex items-center bg-blue-800 gap-2 shadow-lg transition-all duration-300 transform hover:scale-105" 
          @click="goToCreatePage"
          :disabled="loadingCreate"
        >
          <Plus class="w-4 h-4" />
          <span v-if="loadingCreate">Membuka...</span>
          <span v-else>Tambahkan PD</span>
        </Button>
      </div>

      <!-- Search dan filter -->
      <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="relative w-full sm:w-96">
          <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari nama dinas, penanggung jawab, NIP, kode..." 
            class="pl-10 pr-4 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
            @input="handleSearchChange"
          />
        </div>
        <div class="flex gap-2 items-center">
          <span class="text-sm text-gray-500">Tampilkan:</span>
          <select v-model="itemsPerPage" class="rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
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
                  <TableHead class="cursor-pointer group" @click="toggleSort('operator_name')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Nama Penanggung Jawab
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" 
                        :class="{'opacity-100': sortField === 'operator_name'}" />
                    </div>
                  </TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('kepala_name')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Nama Kepala Daerah
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" 
                        :class="{'opacity-100': sortField === 'kepala_name'}" />
                    </div>
                  </TableHead>
                  <TableHead class="hidden lg:table-cell text-gray-600">Kode Organisasi</TableHead>
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
                          <span>{{ truncateText(getNamaDinas(user)) }}</span>
                          <TooltipProvider v-if="getNamaDinas(user) !== '-' && getNamaDinas(user).length > 30">
                            <Tooltip>
                              <TooltipTrigger>
                                <Info class="w-4 h-4 text-blue-500" />
                              </TooltipTrigger>
                              <TooltipContent>
                                <div class="max-w-md p-2">{{ getNamaDinas(user) }}</div>
                              </TooltipContent>
                            </Tooltip>
                          </TooltipProvider>
                        </div>
                      </TableCell>
                      <TableCell class="text-gray-500">
                        <div>
                          <div>{{ user.operator_name || '-' }}</div>
                        </div>
                      </TableCell>
                      <TableCell class="text-gray-500">
                        <div>
                          <div>{{ user.kepala_name || '-' }}</div>
                        </div>
                      </TableCell>
                      <TableCell class="hidden lg:table-cell font-mono text-gray-500">{{ getKodeOrganisasi(user) }}</TableCell>
                      <TableCell>
                        <div class="flex items-center gap-2">
                          <!-- Tombol Edit: disembunyikan untuk Operator -->
                          <Button
                            v-if="!isOperator"
                            size="sm"
                            class="bg-green-900 hover:bg-green-700 text-white" 
                            @click.stop="goToEditPage(user.id)"
                          >
                            <Pencil class="w-4 h-4 mr-2 ml-2" />
                          </Button>

                          <Button size="sm" class="bg-orange-500 hover:bg-orange-700 text-white" 
                            @click.stop="goToShowPage(user.id)"
                          >
                            <Binoculars class="w-4 h-4 mr-2 ml-2" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                    
                    <!-- Detail ekspansi -->
                    <TableRow v-if="showDetailId === user.id" class="bg-blue-50/50 dark:bg-blue-900/10">
                      <TableCell colspan="6" class="animate-fadeIn">
                        <div class="p-4 space-y-3">
                          <h3 class="text-lg font-semibold text-gray-600">Detail Perangkat Daerah</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                              <div>
                                <p class="text-sm text-gray-600">Kepala Daerah:</p>
                                <p class="font-medium text-gray-500">{{ user.kepala_name || '-' }}</p>
                                <p class="text-sm font-mono text-gray-400">NIP: {{ getKepalaNip(user) }}</p>
                              </div>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Nama Dinas:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ getNamaDinas(user) }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Nama Penanggung Jawab:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ user.operator_name || '-' }}</p>
                              <p class="text-sm font-mono text-gray-400 dark:text-gray-300">NIP: {{ getOperatorNip(user) }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Kode Organisasi:</p>
                              <p class="font-mono text-gray-500 dark:text-gray-200">{{ getKodeOrganisasi(user) }}</p>
                            </div>
                          </div>
                          <div class="pt-3 flex justify-end gap-2">
                            <!-- Tombol Edit: disembunyikan untuk Operator -->
                            <Button
                              v-if="!isOperator"
                              size="sm"
                              class="bg-green-900 hover:bg-green-700 text-white" 
                              @click.stop="goToEditPage(user.id)"
                            >
                              <Pencil class="w-4 h-4 mr-2" />
                              Edit Data
                            </Button>

                            <Button
                              size="sm"
                              class="bg-orange-500 hover:bg-orange-700 text-white" 
                              @click.stop="goToShowPage(user.id)"
                            >
                              <Binoculars class="w-4 h-4 mr-1" />
                              Lihat Detail Lengkap
                            </Button>
                          </div>
                        </div>
                      </TableCell>
                    </TableRow>
                  </template>
                </template>
                <TableRow v-else>
                  <TableCell colspan="6" class="text-center py-10">
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
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :total-items="filteredData.length"
        :items-per-page="itemsPerPage"
        @update:current-page="currentPage = $event"
      />
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
