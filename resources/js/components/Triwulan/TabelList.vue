<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
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
import {
  Search,
  ArrowUpDown,
  ChevronLeft,
  ChevronRight,
  Binoculars,
  Info,
} from 'lucide-vue-next';

const props = defineProps<{
  users: Array<any>,
  itemsPerPageDefault?: number,
}>();

const emit = defineEmits(['showDetail']);

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(props.itemsPerPageDefault || 10);
const sortField = ref('name');
const sortDirection = ref<'asc' | 'desc'>('asc');
const showDetailId = ref<number | null>(null);

watch(() => props.users, () => {
  currentPage.value = 1;
});

const filteredData = computed(() => {
  let data = [...props.users];
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    data = data.filter(item => {
      const skpdList = Array.isArray(item.skpd) ? item.skpd : [];
      const namaDinas = skpdList.map((s: any) => s?.nama_dinas || '').join(' ').toLowerCase();
      const namaOperator = skpdList.map((s: any) => s?.nama_operator || '').join(' ').toLowerCase();
      const namaKepalaSkpd = skpdList.map((s: any) => s?.nama_kepala_skpd || '').join(' ').toLowerCase();
      const kodeOrganisasi = skpdList.map((s: any) => s?.kode_organisasi || '').join(' ').toLowerCase();
      return (
        (item.name || '').toLowerCase().includes(query) ||
        namaDinas.includes(query) ||
        namaOperator.includes(query) ||
        namaKepalaSkpd.includes(query) ||
        kodeOrganisasi.includes(query)
      );
    });
  }
  data.sort((a, b) => {
    let aVal = getFieldValue(a, sortField.value);
    let bVal = getFieldValue(b, sortField.value);
    if (aVal === null || aVal === undefined) aVal = '';
    if (bVal === null || bVal === undefined) bVal = '';
    if (typeof aVal === 'string' && typeof bVal === 'string') {
      return sortDirection.value === 'asc'
        ? aVal.localeCompare(bVal)
        : bVal.localeCompare(aVal);
    }
    return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
  });
  return data;
});

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value));
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredData.value.slice(start, end);
});

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
    case 'kode_organisasi':
      return firstSkpd?.kode_organisasi || '';
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

function toggleDetail(id: number) {
  showDetailId.value = showDetailId.value === id ? null : id;
}

function handleSearchChange() {
  currentPage.value = 1;
}

function truncateText(text: string | null | undefined, length: number = 30): string {
  if (!text) return '-';
  return text.length > length ? text.slice(0, length) + '...' : text;
}

function goToShowPage(id: number) {
  emit('showDetail', id);
}

function getOperatorNip(user: any) {
  const skpdList = Array.isArray(user.skpd) ? user.skpd : [];
  const operator = skpdList.find((s: any) => s?.nama_operator);
  return operator?.nip_operator || '';
}

function getKepalaNip(user: any) {
  const skpdList = Array.isArray(user.skpd) ? user.skpd : [];
  const kepalaSkpd = skpdList.find((s: any) => s?.nama_kepala_skpd);
  return kepalaSkpd?.nip_kepala_skpd || '';
}
</script>

<template>
  <!-- Search dan filter -->
  <div class="flex flex-col sm:flex-row gap-4 items-center justify-between mb-4">
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
              <TableHead class="hidden md:table-cell text-gray-600">Kode Organisasi</TableHead>
              <TableHead class="text-gray-600">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="paginatedData.length > 0">
              <template v-for="(user, index) in paginatedData" :key="user.id">
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
                  <TableCell class="hidden md:table-cell font-mono text-gray-500">{{ getFieldValue(user, 'kode_organisasi') }}</TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <Button size="sm" class="bg-orange-500 hover:bg-orange-700 text-white w-15"
                        @click.stop="goToShowPage(user.id)">
                        <Binoculars class="w-4 h-4 mr-1" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
                <TableRow v-if="showDetailId === user.id" class="bg-blue-50/50 dark:bg-blue-900/10">
                  <TableCell colspan="7" class="animate-fadeIn">
                    <div class="p-4 space-y-3">
                      <h3 class="text-lg font-semibold text-gray-600">Detail Perangkat Daerah</h3>
                      <div class="pt-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <p class="text-sm text-gray-600">Nama Dinas:</p>
                            <p class="font-medium text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'nama_dinas') || '-' }}</p>
                          </div>
                          <div>
                            <p class="text-sm text-gray-600">Nama Penanggung Jawab:</p>
                            <p class="font-medium text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'nama_operator') || '-' }}</p>
                            <p class="text-sm font-mono text-gray-400 dark:text-gray-300">NIP: {{ getOperatorNip(user) }}</p>
                          </div>
                          <div>
                            <p class="text-sm text-gray-600">Nama Kepala SKPD:</p>
                            <p class="font-medium text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'nama_kepala_skpd') || '-' }}</p>
                            <p class="text-sm font-mono text-gray-400 dark:text-gray-300">NIP: {{ getKepalaNip(user) }}</p>
                          </div>
                          <div>
                            <p class="text-sm text-gray-600">Kode Organisasi:</p>
                            <p class="font-mono text-gray-500 dark:text-gray-200">{{ getFieldValue(user, 'kode_organisasi') || '-' }}</p>
                          </div>
                        </div>
                      </div>
                      <div class="pt-3 flex justify-end gap-2">
                        <Button size="sm" class="bg-orange-500 hover:bg-orange-700 text-white"
                          @click.stop="goToShowPage(user.id)">
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
  <div v-if="totalPages > 1" class="flex justify-between items-center mt-4">
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
</template>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.table-row-hover {
  transition: background-color 0.2s ease;
}

.table-row-hover { transition: background-color 0.2s ease; }
</style>
