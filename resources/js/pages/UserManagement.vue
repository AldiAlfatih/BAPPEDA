
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { 
  Plus, 
  Pencil, 
  Trash2, 
  Search, 
  ChevronLeft, 
  ChevronRight, 
  ArrowUpDown,
  FileText,
  Info,
  UserCircle
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
import { 
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogFooter
} from '@/components/ui/dialog';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
// import { Badge } from '@/components/ui/badge';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      email: string;
      roles: Array<{ name: string }>;
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
const confirmDelete = ref<number | null>(null);
const loadingCreate = ref(false);
const deletingId = ref<number | null>(null);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'User Management', href: '/manajemen-tim/usermanagement' },
];

// Filter dan Sorting
const filteredData = computed(() => {
  let data = [...props.users.data];
  
  // Filter berdasarkan pencarian
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    data = data.filter(item => 
      item.name.toLowerCase().includes(query) || 
      item.email.toLowerCase().includes(query) ||
      (item.roles?.[0]?.name || '').toLowerCase().includes(query)
    );
  }
  
  // Sorting
  data.sort((a, b) => {
    let aVal = getFieldValue(a, sortField.value);
    let bVal = getFieldValue(b, sortField.value);
    
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
  if (field === 'roles') {
    return item.roles?.[0]?.name || '';
  }
  return item[field];
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
function goToCreatePage() {
  loadingCreate.value = true;
  router.visit('/manajemen-tim/usermanagement/create', {
    onFinish: () => (loadingCreate.value = false),
  });
}

function goToEditPage(id: number) {
  router.visit(`/manajemen-tim/usermanagement/${id}/edit`);
}

function deleteUser(id: number) {
  confirmDelete.value = id;
}

function confirmDeleteAction() {
  if (confirmDelete.value) {
    deletingId.value = confirmDelete.value;
    router.delete(`/manajemen-tim/usermanagement/${confirmDelete.value}`, {
      onFinish: () => {
        deletingId.value = null;
        confirmDelete.value = null;
      }
    });
  }
}

function cancelDelete() {
  confirmDelete.value = null;
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

// Get role badge color

</script>

<template>
  <Head title="User Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-600 dark:text-gray-100">User Management</h1>
          <p class="text-sm text-gray-500 dark:text-gray-500">Kelola data pengguna sistem</p>
        </div>
        <Button 
          class="flex items-center bg-blue-800 gap-2 shadow-lg transition-all duration-300 transform hover:scale-105" 
          @click="goToCreatePage"
          :disabled="loadingCreate"
        >
          <Plus class="w-4 h-4" />
          <span v-if="loadingCreate">Membuka...</span>
          <span v-else>Tambahkan</span>
        </Button>
      </div>

      <!-- Search dan filter -->
      <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="relative w-full sm:w-96">
          <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari nama, email, atau role..." 
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
                  <TableHead class="w-16 text-center text-gray-600">No</TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('name')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Nama
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" 
                        :class="{'opacity-100': sortField === 'name'}" />
                    </div>
                  </TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('email')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Email
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" 
                        :class="{'opacity-100': sortField === 'email'}" />
                    </div>
                  </TableHead>
                  <TableHead class="cursor-pointer group" @click="toggleSort('roles')">
                    <div class="flex items-center gap-1 text-gray-600">
                      Role
                      <ArrowUpDown class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" 
                        :class="{'opacity-100': sortField === 'roles'}" />
                    </div>
                  </TableHead>
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
                      <TableCell class="text-gray-500">{{ user.name }}</TableCell>
                      <TableCell class="text-gray-500">{{ user.email }}</TableCell>
                      <TableCell>
                        <Badge class="text-gray-600">
                          {{ user.roles?.[0]?.name || 'Tidak ada' }}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div class="flex items-center gap-2">
                          <Button size="sm" class="bg-green-900 hover:bg-green-700 text-white" 
                            @click.stop="goToEditPage(user.id)">
                            <Pencil class="w-4 h-4 mr-2" />
                            <span class="hidden sm:inline">Edit</span>
                          </Button>
                          <Button size="sm" class="bg-red-700 hover:bg-red-700 text-white" 
                            @click.stop="deleteUser(user.id)"
                            :disabled="deletingId === user.id">
                            <Trash2 class="w-4 h-4 mr-1" />
                            <span class="hidden sm:inline">{{ deletingId === user.id ? 'Menghapus...' : 'Hapus' }}</span>
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                    
                    <!-- Detail ekspansi -->
                    <TableRow v-if="showDetailId === user.id" class="bg-blue-50/50 dark:bg-blue-900/10">
                      <TableCell colspan="5" class="animate-fadeIn">
                        <div class="p-4 space-y-3">
                          <h3 class="text-lg font-semibold text-gray-600">Detail Pengguna</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                              <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                                <UserCircle class="h-12 w-12 text-blue-600" />
                              </div>
                              <div>
                                <p class="text-sm text-gray-600">User ID:</p>
                                <p class="font-mono text-lg text-gray-500">{{ user.id }}</p>
                              </div>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Role:</p>
                              <Badge class="text-gray-500">
                                {{ user.roles?.[0]?.name || 'Tidak ada' }}
                              </Badge>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Nama Lengkap:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ user.name }}</p>
                            </div>
                            <div>
                              <p class="text-sm text-gray-600">Email:</p>
                              <p class="font-medium text-gray-500 dark:text-gray-200">{{ user.email }}</p>
                            </div>
                          </div>
                          <div class="pt-3 flex justify-end gap-2">
                            <Button size="sm" class="bg-green-900 hover:bg-green-700 text-white" 
                              @click.stop="goToEditPage(user.id)">
                              <Pencil class="w-4 h-4 mr-2" />
                              Edit Pengguna
                            </Button>
                          </div>
                        </div>
                      </TableCell>
                    </TableRow>
                  </template>
                </template>
                <TableRow v-else>
                  <TableCell colspan="5" class="text-center py-10">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <p class="text-gray-600 text-lg">Tidak ada data yang ditemukan</p>
                      <p class="text-gray-600 text-sm" v-if="searchQuery">Coba ubah kriteria pencarian</p>
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
    </div>
  </AppLayout>
  
  <!-- Dialog Konfirmasi Hapus -->
  <Dialog :open="confirmDelete !== null" @update:open="cancelDelete">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Konfirmasi Hapus</DialogTitle>
        <DialogDescription>
          Apakah Anda yakin ingin menghapus data pengguna ini? Tindakan ini tidak dapat dibatalkan.
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex gap-2 justify-end pt-4">
        <Button variant="outline" @click="cancelDelete">Batal</Button>
        <Button class="bg-red-600 hover:bg-red-700 text-white" @click="confirmDeleteAction">
          <Trash2 class="w-4 h-4 mr-2" />
          Hapus
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
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