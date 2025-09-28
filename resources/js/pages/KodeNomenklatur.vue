<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowUpDown, Info, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
// import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Pagination } from '@/components/ui/pagination';

const props = defineProps<{
    kodeNomenklatur: Array<{
        id: number;
        nomor_kode: string;
        nomenklatur: string;
        jenis_nomenklatur: number;
        bidang_urusan?: { bidang_urusan: string } | null;
        program?: { program: string } | null;
        kegiatan?: { kegiatan: string } | null;
        subkegiatan?: { subkegiatan: string } | null;
    }>;
}>();

// State untuk tabel
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('nomor_kode');
const sortDirection = ref('asc');
const showDetailId = ref<number | null>(null);
const confirmDelete = ref<number | null>(null);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Kode Nomenklatur', href: '/kodenomenklatur' }];

// Filter dan Sorting
const filteredData = computed(() => {
    let data = [...props.kodeNomenklatur];

    // Filter berdasarkan pencarian
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter(
            (item) =>
                item.nomor_kode.toLowerCase().includes(query) ||
                item.nomenklatur.toLowerCase().includes(query) ||
                labelJenisNomenklatur(item.jenis_nomenklatur).toLowerCase().includes(query),
        );
    }

    // Sorting
    data.sort((a, b) => {
        let aVal = getFieldValue(a, sortField.value);
        let bVal = getFieldValue(b, sortField.value);

        // String comparison
        if (typeof aVal === 'string' && typeof bVal === 'string') {
            return sortDirection.value === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
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
    if (field === 'jenis_nomenklatur') {
        return item.jenis_nomenklatur;
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
    router.visit('/kodenomenklatur/create');
}

function goToEditPage(id: number) {
    router.visit(`/kodenomenklatur/${id}/edit`);
}

function deleteKode(id: number) {
    confirmDelete.value = id;
}

function confirmDeleteAction() {
    if (confirmDelete.value) {
        router.delete(`/kodenomenklatur/${confirmDelete.value}`);
        confirmDelete.value = null;
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

// Jenis nomenklatur label
function labelJenisNomenklatur(value: number): string {
    switch (value) {
        case 0:
            return 'Urusan';
        case 1:
            return 'Bidang Urusan';
        case 2:
            return 'Program';
        case 3:
            return 'Kegiatan';
        case 4:
            return 'Subkegiatan';
        default:
            return 'Tidak Dikenal';
    }
}

// Truncate text
function truncateNomenklatur(nomenklatur: string, length: number = 30): string {
    return nomenklatur.length > length ? nomenklatur.slice(0, length) + '...' : nomenklatur;
}

// Reset pagination ketika search berubah
function handleSearchChange() {
    currentPage.value = 1;
}
</script>

<template>
    <Head title="Kode Nomenklatur" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header dengan judul dan action -->
            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-600 dark:text-gray-100">Kode Nomenklatur</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data kode nomenklatur</p>
                </div>
                <Button
                    class="flex transform items-center gap-2 bg-blue-800 shadow-lg transition-all duration-300 hover:scale-105"
                    @click="goToCreatePage"
                >
                    <Plus class="h-4 w-4" />
                    Tambahkan
                </Button>
            </div>

            <!-- Search dan filter -->
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="relative w-full sm:w-96">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Cari kode, nomenklatur, atau jenis..."
                        class="w-full rounded-lg border-gray-300 pr-4 pl-10 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        @input="handleSearchChange"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Tampilkan:</span>
                    <select v-model="itemsPerPage" class="rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <!-- Main Card dengan Table -->
            <Card
                class="overflow-hidden rounded-xl border border-gray-200 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700"
            >
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader class="bg-gray-50 dark:bg-gray-800">
                                <TableRow>
                                    <TableHead class="w-16 text-center text-gray-600">No</TableHead>
                                    <TableHead class="group cursor-pointer" @click="toggleSort('nomor_kode')">
                                        <div class="flex items-center gap-1 text-gray-600">
                                            Kode
                                            <ArrowUpDown
                                                class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                                :class="{ 'opacity-100': sortField === 'nomor_kode' }"
                                            />
                                        </div>
                                    </TableHead>
                                    <TableHead class="group cursor-pointer" @click="toggleSort('nomenklatur')">
                                        <div class="flex items-center gap-1 text-gray-600">
                                            Nomenklatur
                                            <ArrowUpDown
                                                class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                                :class="{ 'opacity-100': sortField === 'nomenklatur' }"
                                            />
                                        </div>
                                    </TableHead>
                                    <TableHead class="group cursor-pointer" @click="toggleSort('jenis_nomenklatur')">
                                        <div class="flex items-center gap-1 text-gray-600">
                                            Jenis
                                            <ArrowUpDown
                                                class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                                :class="{ 'opacity-100': sortField === 'jenis_nomenklatur' }"
                                            />
                                        </div>
                                    </TableHead>
                                    <TableHead class="text-gray-600">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="paginatedData.length > 0">
                                    <template v-for="(kode, index) in paginatedData" :key="kode.id">
                                        <!-- Baris utama -->
                                        <TableRow
                                            class="cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                                            :class="{ 'bg-blue-50 dark:bg-blue-900/20': showDetailId === kode.id }"
                                            @click="toggleDetail(kode.id)"
                                        >
                                            <TableCell class="text-center font-medium text-gray-500">
                                                {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                            </TableCell>
                                            <TableCell class="font-mono text-gray-500">{{ kode.nomor_kode }}</TableCell>
                                            <TableCell>
                                                <div class="flex items-center gap-2 text-gray-500">
                                                    <span>{{ truncateNomenklatur(kode.nomenklatur) }}</span>
                                                    <TooltipProvider v-if="kode.nomenklatur.length > 30">
                                                        <Tooltip>
                                                            <TooltipTrigger>
                                                                <Info class="text-grey-700 h-4 w-4" />
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <div class="max-w-md p-2 text-gray-500">{{ kode.nomenklatur }}</div>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ labelJenisNomenklatur(kode.jenis_nomenklatur) }}
                                                </span>
                                            </TableCell>
                                            <TableCell>
                                                <div class="flex items-center gap-2">
                                                    <!-- <Button variant="outline" size="sm" class="bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800 border-none"
                            @click.stop="toggleDetail(kode.id)">
                            <FileText class="w-4 h-4 text-blue-500" />
                          </Button> -->
                                                    <Button
                                                        size="sm"
                                                        class="bg-green-900 text-white hover:bg-green-700"
                                                        @click.stop="goToEditPage(kode.id)"
                                                    >
                                                        <Pencil class="mr-2 ml-2 h-4 w-4" />
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        class="bg-red-700 text-white hover:bg-red-700"
                                                        @click.stop="deleteKode(kode.id)"
                                                    >
                                                        <Trash2 class="mr-2 ml-2 h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>

                                        <!-- Detail ekspansi -->
                                        <TableRow v-if="showDetailId === kode.id" class="bg-blue-50/50 dark:bg-blue-900/10">
                                            <TableCell colspan="5" class="animate-fadeIn">
                                                <div class="space-y-3 p-4">
                                                    <h3 class="text-lg font-semibold text-gray-600">Detail Lengkap</h3>
                                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                        <div>
                                                            <p class="text-sm text-gray-600">Kode:</p>
                                                            <p class="font-mono text-lg text-gray-500">{{ kode.nomor_kode }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-600">Jenis:</p>
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ labelJenisNomenklatur(kode.jenis_nomenklatur) }}
                                                            </span>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <p class="text-sm text-gray-600">Nomenklatur:</p>
                                                            <p class="font-medium text-gray-500 dark:text-gray-200">{{ kode.nomenklatur }}</p>
                                                        </div>

                                                        <div v-if="kode.bidang_urusan" class="md:col-span-2">
                                                            <p class="text-sm text-gray-600">Bidang Urusan:</p>
                                                            <p class="text-gray-500">{{ kode.bidang_urusan.bidang_urusan }}</p>
                                                        </div>

                                                        <div v-if="kode.program" class="md:col-span-2">
                                                            <p class="text-sm text-gray-600">Program:</p>
                                                            <p class="text-gray-500">{{ kode.program.program }}</p>
                                                        </div>

                                                        <div v-if="kode.kegiatan" class="md:col-span-2">
                                                            <p class="text-sm text-gray-600">Kegiatan:</p>
                                                            <p class="text-gray-500">{{ kode.kegiatan.kegiatan }}</p>
                                                        </div>

                                                        <div v-if="kode.subkegiatan" class="md:col-span-2">
                                                            <p class="text-sm text-gray-600">Subkegiatan:</p>
                                                            <p class="text-gray-500">{{ kode.subkegiatan.subkegiatan }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                </template>
                                <TableRow v-else>
                                    <TableCell colspan="5" class="py-10 text-center">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <p class="text-lg text-gray-600">Tidak ada data yang ditemukan</p>
                                            <p class="text-sm text-gray-600" v-if="searchQuery">Coba ubah kriteria pencarian</p>
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

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog :open="confirmDelete !== null" @update:open="cancelDelete">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription> Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan. </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button variant="outline" @click="cancelDelete">Batal</Button>
                <Button class="bg-red-600 text-white hover:bg-red-700" @click="confirmDeleteAction">
                    <Trash2 class="mr-2 h-4 w-4" />
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
