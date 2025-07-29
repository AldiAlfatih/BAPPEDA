<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { router } from '@inertiajs/vue3';
import { ArrowUpDown, Binoculars, ChevronLeft, ChevronRight, Info, Search, Wallet, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            nama_dinas: string | null;
            operator_name: string | null;
            kepala_name: string | null;
            kode_organisasi: string | null;
            user_detail?: {
                nip?: string;
            } | null;
            operator_nip?: string | null;
            kepala_nip?: string | null;
        }>;
    };
    url_detail: string;
    showBinocularsButton?: boolean;
    url_detail_partial: string;
    url_budget_change?: string;
    enabledParsialUsers?: number[];
    isBudgetChangeMode?: boolean;
    triwulan4Aktif?: {
        id: number;
        tahap: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    } | null;
}>();

// State untuk tabel
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('name');
const sortDirection = ref('asc');
const showDetailId = ref<number | null>(null);
const loadingCreate = ref(false);
const isEnablingParsial = ref(false);

// Dialog state management
const alertDialog = ref({
    open: false,
    title: '',
    message: '',
    type: 'info' as 'info' | 'success' | 'error',
    onConfirm: () => {}
});

const confirmDialog = ref({
    open: false,
    title: '',
    message: '',
    onConfirm: () => {},
    onCancel: () => {}
});

function showAlert(title: string, message: string, type: 'info' | 'success' | 'error' = 'info') {
    alertDialog.value = {
        open: true,
        title,
        message,
        type,
        onConfirm: () => {
            alertDialog.value.open = false;
        }
    };
}

function showConfirm(title: string, message: string, onConfirm: () => void) {
    confirmDialog.value = {
        open: true,
        title,
        message,
        onConfirm: () => {
            confirmDialog.value.open = false;
            onConfirm();
        },
        onCancel: () => {
            confirmDialog.value.open = false;
        }
    };
}

// Helper function to get NIP from user_detail
function getUserNip(user: any): string {
    return user.user_detail?.nip || user.nip || '';
}

// Helper function to get operator NIP
function getOperatorNip(user: any): string {
    return user.operator_nip || '-';
}

// Helper function to get kepala NIP
function getKepalaNip(user: any): string {
    return user.kepala_nip || '-';
}

// Filter dan Sorting
const filteredData = computed(() => {
    const query = searchQuery.value.toLowerCase();
    const data = [...props.users.data];

    if (query) {
        return data.filter(
            (item) =>
                (item.nama_dinas || '').toLowerCase().includes(query) ||
                (item.operator_name || '').toLowerCase().includes(query) ||
                (item.kepala_name || '').toLowerCase().includes(query) ||
                (item.kode_organisasi || '').toLowerCase().includes(query) ||
                (item.operator_nip || '').toLowerCase().includes(query) ||
                (item.kepala_nip || '').toLowerCase().includes(query),
        );
    }

    if (sortField.value) {
        data.sort((a, b) => {
            const aVal = getFieldValue(a, sortField.value);
            const bVal = getFieldValue(b, sortField.value);

            // Numeric comparison
            if (!isNaN(aVal) && !isNaN(bVal)) {
                return sortDirection.value === 'asc' ? Number(aVal) - Number(bVal) : Number(bVal) - Number(aVal);
            }

            // String comparison
            return sortDirection.value === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
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

// Helper untuk mendapatkan nilai field untuk sorting
function getFieldValue(item: any, field: string) {
    switch (field) {
        case 'nama_dinas':
            return item.nama_dinas || '';
        case 'operator_name':
            return item.operator_name || '';
        case 'kepala_name':
            return item.kepala_name || '';
        case 'kode_organisasi':
            return item.kode_organisasi || '';
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

function goToShowPage(id: number) {
    // If budget change mode is active and enabled, go to budget change page
    if (props.isBudgetChangeMode && isBudgetChangeEnabledForUser(id) && props.url_budget_change) {
        router.visit(route(props.url_budget_change, { id }));
    } else if (isParsialEnabledForUser(id)) {
        // If parsial is enabled, go to parsial page
        router.visit(route(props.url_detail_partial, { id }));
    } else {
        // Otherwise go to normal detail page
        router.visit(route(props.url_detail, { id }));
    }
}

async function toggleParsial(id: number) {
    const user = props.users.data.find((u) => u.id === id);
    const dinasName = user?.nama_dinas || 'Dinas ini';
    const isParsialEnabled = isParsialEnabledForUser(id);

    const action = isParsialEnabled ? 'menutup' : 'membuka';

    showConfirm(
        `Konfirmasi ${isParsialEnabled ? 'Tutup' : 'Buka'} Mode Parsial`,
        `Apakah Anda yakin ingin ${action} parsial pada ${dinasName}?\n\nMode parsial memungkinkan akses ke data Triwulan 1, 2, dan 3.`,
        async () => {

    isEnablingParsial.value = true;

    try {
        const endpoint = isParsialEnabled ? route('manajemenanggaran.disable-parsial') : route('manajemenanggaran.enable-parsial');

        console.log('🔄 Sending parsial request:', {
            endpoint,
            user_id: id,
            action,
            dinas: dinasName,
        });

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                user_id: id,
                confirm: true,
            }),
        });

        console.log('📡 Response received:', {
            status: response.status,
            statusText: response.statusText,
            contentType: response.headers.get('content-type'),
        });

        // Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const responseText = await response.text();
            console.error('❌ Response is not JSON:', responseText);
            throw new Error(`Server returned ${response.status}: ${responseText.substring(0, 200)}...`);
        }

        const result = await response.json();
        console.log('📋 Parsed response:', result);

            if (result.success) {
                const successMessage = isParsialEnabled
                    ? `${result.message}\n\n✅ Mode parsial telah ditutup. Tombol detail (binocular) akan kembali ke mode normal.`
                    : `${result.message}\n\n✅ Sekarang Anda dapat menggunakan tombol detail (binocular) untuk mengakses mode parsial.\n\n📊 Triwulan yang tersedia: ${result.available_triwulan || 'Triwulan 1-3'}`;

                showAlert('Berhasil!', successMessage, 'success');
                
                // Refresh current page to update visual indicators
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                console.error('❌ Server returned error:', result);
                showAlert(
                    'Terjadi Kesalahan',
                    result.message || `Terjadi kesalahan saat ${action} parsial`,
                    'error'
                );
            }
        } catch (err) {
            console.error(`❌ Error ${action} parsial:`, err);
            const errorMessage = err instanceof Error ? err.message : 'Kesalahan tidak diketahui';
            showAlert(
                'Terjadi Kesalahan',
                `Terjadi kesalahan saat ${action} parsial: ${errorMessage}`,
                'error'
            );
        } finally {
            isEnablingParsial.value = false;
        }
        }
    );
}

// Backward compatibility - keep the old function name but redirect to new toggle function
async function goToShowParsial(id: number) {
    await toggleParsial(id);
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

// Check if parsial is enabled for specific user
function isParsialEnabledForUser(userId: number): boolean {
    return props.enabledParsialUsers?.includes(userId) || false;
}

// Check if budget change is enabled for specific user
function isBudgetChangeEnabledForUser(userId: number): boolean {
    // For now, if budget change mode is active, assume all users can access it
    // This should be enhanced based on your business logic
    return props.isBudgetChangeMode || false;
}

// Get button class based on mode
function getButtonClass(userId: number): string {
    if (props.isBudgetChangeMode && isBudgetChangeEnabledForUser(userId)) {
        return 'bg-red-500 hover:bg-red-700'; // Red for budget change
    } else if (isParsialEnabledForUser(userId)) {
        return 'bg-blue-500 hover:bg-blue-700'; // Blue for parsial
    } else {
        return 'bg-orange-500 hover:bg-orange-700'; // Orange for normal
    }
}

// Get button tooltip text
function getButtonTooltip(userId: number): string {
    if (props.isBudgetChangeMode && isBudgetChangeEnabledForUser(userId)) {
        return `Mode Perubahan Anggaran (Triwulan 4 ${props.triwulan4Aktif?.tahun?.tahun})`;
    } else if (isParsialEnabledForUser(userId)) {
        return 'Mode Parsial (Klik untuk akses parsial)';
    } else {
        return 'Mode Normal (Klik untuk rencana awal)';
    }
}
</script>
<template>
    <div>
        <!-- Search dan filter -->
        <div class="mb-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div class="relative w-full sm:w-96">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                <Input
                    v-model="searchQuery"
                    placeholder="Cari nama dinas, penanggung jawab, NIP, kode..."
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
            class="mb-6 overflow-hidden rounded-xl border border-gray-200 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700"
        >
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader class="bg-gray-50 dark:bg-gray-800">
                            <TableRow>
                                <TableHead class="w-16 text-center">No</TableHead>
                                <TableHead class="group cursor-pointer" @click="toggleSort('nama_dinas')">
                                    <div class="flex items-center gap-1 text-gray-600">
                                        Nama Dinas
                                        <ArrowUpDown
                                            class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                            :class="{ 'opacity-100': sortField === 'nama_dinas' }"
                                        />
                                    </div>
                                </TableHead>
                                <TableHead class="group cursor-pointer" @click="toggleSort('nama_operator')">
                                    <div class="flex items-center gap-1 text-gray-600">
                                        Nama Penanggung Jawab
                                        <ArrowUpDown
                                            class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                            :class="{ 'opacity-100': sortField === 'nama_operator' }"
                                        />
                                    </div>
                                </TableHead>
                                <TableHead class="group cursor-pointer" @click="toggleSort('name')">
                                    <div class="flex items-center gap-1 text-gray-600">
                                        Nama Kepala Daerah
                                        <ArrowUpDown
                                            class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                            :class="{ 'opacity-100': sortField === 'name' }"
                                        />
                                    </div>
                                </TableHead>
                                <TableHead class="hidden text-gray-600 lg:table-cell">Kode Organisasi</TableHead>
                                <TableHead class="text-gray-600">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <template v-if="paginatedData.length > 0">
                                <template v-for="(user, index) in paginatedData" :key="user.id">
                                    <!-- Baris utama -->
                                    <TableRow
                                        class="cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': showDetailId === user.id }"
                                        @click="toggleDetail(user.id)"
                                    >
                                        <TableCell class="text-center font-medium text-gray-500">
                                            {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                        </TableCell>
                                        <TableCell class="font-medium">
                                            <div class="flex items-center gap-2 text-gray-500">
                                                <span>{{ truncateText(user.nama_dinas) }}</span>
                                                <TooltipProvider v-if="user.nama_dinas && user.nama_dinas.length > 30">
                                                    <Tooltip>
                                                        <TooltipTrigger>
                                                            <Info class="h-4 w-4 text-blue-500" />
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <div class="max-w-md p-2">{{ user.nama_dinas }}</div>
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
                                        <TableCell class="hidden font-mono text-gray-500 lg:table-cell">{{ user.kode_organisasi || '-' }}</TableCell>
                                        <TableCell>
                                            <div class="flex items-center gap-2">
                                                <!-- <Button size="sm" class="bg-green-600 hover:bg-green-700 text-white"
                            @click.stop="goToEditPage(user.id)">
                            <Pencil class="w-4 h-4 mr-2" />
                            <span class="hidden sm:inline">Edit</span>
                          </Button> -->
                                                <TooltipProvider>
                                                    <Tooltip>
                                                        <TooltipTrigger asChild>
                                                            <Button
                                                                size="sm"
                                                                :class="getButtonClass(user.id)"
                                                                class="relative w-15 text-white"
                                                                @click.stop="goToShowPage(user.id)"
                                                            >
                                                                <Binoculars class="mr-1 h-4 w-4" />
                                                                <!-- Budget change indicator -->
                                                                <span
                                                                    v-if="isBudgetChangeMode && isBudgetChangeEnabledForUser(user.id)"
                                                                    class="absolute -top-1 -right-1 h-3 w-3 animate-pulse rounded-full border border-white bg-yellow-400"
                                                                >
                                                                </span>
                                                            </Button>
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <div class="text-sm">
                                                                <p class="font-medium">{{ getButtonTooltip(user.id) }}</p>
                                                                <p
                                                                    v-if="isBudgetChangeMode && isBudgetChangeEnabledForUser(user.id)"
                                                                    class="mt-1 text-xs text-yellow-600"
                                                                >
                                                                    🔥 Mode Khusus Aktif - Perubahan Anggaran
                                                                </p>
                                                            </div>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                </TooltipProvider>

                                                <TooltipProvider v-if="showBinocularsButton">
                                                    <Tooltip>
                                                        <TooltipTrigger asChild>
                                                            <Button
                                                                size="sm"
                                                                :class="
                                                                    isParsialEnabledForUser(user.id)
                                                                        ? 'bg-red-500 hover:bg-red-600'
                                                                        : 'bg-green-500 hover:bg-green-700'
                                                                "
                                                                class="w-20 text-white"
                                                                @click.stop="toggleParsial(user.id)"
                                                                :disabled="isEnablingParsial"
                                                            >
                                                                <Wallet class="mr-1 h-4 w-4" />
                                                                <span class="text-xs">
                                                                    {{ isParsialEnabledForUser(user.id) ? 'OFF' : 'ON' }}
                                                                </span>
                                                            </Button>
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <div class="text-sm">
                                                                <p class="font-medium">
                                                                    {{
                                                                        isParsialEnabledForUser(user.id)
                                                                            ? '🔴 Klik untuk tutup mode parsial'
                                                                            : '🟢 Klik untuk buka mode parsial'
                                                                    }}
                                                                </p>
                                                                <p class="mt-1 text-xs text-gray-600">
                                                                    {{
                                                                        isParsialEnabledForUser(user.id)
                                                                            ? 'Mode parsial aktif - dapat akses Triwulan 1-3'
                                                                            : 'Mode parsial memungkinkan akses ke Triwulan 1, 2, dan 3'
                                                                    }}
                                                                </p>
                                                            </div>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                </TooltipProvider>
                                            </div>
                                        </TableCell>
                                    </TableRow>

                                    <!-- Detail ekspansi -->
                                    <TableRow v-if="showDetailId === user.id" class="bg-blue-50/50 dark:bg-blue-900/10">
                                        <TableCell :colspan="7" class="animate-fadeIn">
                                            <div class="space-y-3 p-4">
                                                <h3 class="text-lg font-semibold text-gray-600">Detail Perangkat Daerah</h3>
                                                <div class="pt-3">
                                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                        <div>
                                                            <p class="text-sm text-gray-600">Nama Dinas:</p>
                                                            <p class="font-medium text-gray-500 dark:text-gray-200">{{ user.nama_dinas || '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-600">Nama Penanggung Jawab:</p>
                                                            <p class="font-medium text-gray-500 dark:text-gray-200">
                                                                {{ user.operator_name || '-' }}
                                                            </p>
                                                            <p class="font-mono text-sm text-gray-400 dark:text-gray-300">
                                                                NIP: {{ getOperatorNip(user) }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-600">Nama Kepala Daerah:</p>
                                                            <p class="font-medium text-gray-500 dark:text-gray-200">{{ user.kepala_name || '-' }}</p>
                                                            <p class="font-mono text-sm text-gray-400 dark:text-gray-300">
                                                                NIP: {{ getKepalaNip(user) }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-600">Kode Organisasi:</p>
                                                            <p class="font-mono text-gray-500 dark:text-gray-200">
                                                                {{ user.kode_organisasi || '-' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-2 pt-3">
                                                    <!-- <Button size="sm" class="bg-green-600 hover:bg-green-700 text-white"
                              @click.stop="goToEditPage(user.id)">
                              <Pencil class="w-4 h-4 mr-2" />
                              Edit Data
                            </Button> -->
                                                    <Button
                                                        size="sm"
                                                        :class="getButtonClass(user.id)"
                                                        class="relative text-white"
                                                        @click.stop="goToShowPage(user.id)"
                                                    >
                                                        <Binoculars class="mr-1 h-4 w-4" />
                                                        <span v-if="isBudgetChangeMode && isBudgetChangeEnabledForUser(user.id)">
                                                            Perubahan Anggaran
                                                        </span>
                                                        <span v-else-if="isParsialEnabledForUser(user.id)"> Akses Mode Parsial </span>
                                                        <span v-else> Lihat Detail Lengkap </span>
                                                        <!-- Budget change indicator -->
                                                        <span
                                                            v-if="isBudgetChangeMode && isBudgetChangeEnabledForUser(user.id)"
                                                            class="absolute -top-1 -right-1 h-3 w-3 animate-pulse rounded-full border border-white bg-yellow-400"
                                                        >
                                                        </span>
                                                    </Button>
                                                </div>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </template>
                            </template>
                            <TableRow v-else>
                                <TableCell :colspan="7" class="py-10 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <p class="text-lg text-gray-500">Tidak ada data yang ditemukan</p>
                                        <p class="text-sm text-gray-400" v-if="searchQuery">Coba ubah kriteria pencarian</p>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan {{ (currentPage - 1) * itemsPerPage + 1 }} sampai
                {{ Math.min(currentPage * itemsPerPage, filteredData.length) }}
                dari {{ filteredData.length }} data
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="flex items-center gap-1"
                >
                    <ChevronLeft class="h-4 w-4" />
                    <span class="hidden sm:inline">Sebelumnya</span>
                </Button>

                <div class="hidden gap-1 sm:flex">
                    <Button
                        v-for="page in totalPages"
                        :key="page"
                        :variant="page === currentPage ? 'default' : 'outline'"
                        size="sm"
                        @click="currentPage = page"
                        class="h-10 w-10"
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
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>

    <!-- Dialog Alert -->
    <Dialog :open="alertDialog.open" @update:open="(value) => alertDialog.open = value">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ alertDialog.title }}</DialogTitle>
                <DialogDescription>{{ alertDialog.message }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button 
                    @click="alertDialog.onConfirm"
                    :class="{
                        'bg-blue-600 text-white hover:bg-blue-700': alertDialog.type === 'info',
                        'bg-green-600 text-white hover:bg-green-700': alertDialog.type === 'success',
                        'bg-red-600 text-white hover:bg-red-700': alertDialog.type === 'error'
                    }"
                >
                    OK
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Dialog Konfirmasi -->
    <Dialog :open="confirmDialog.open" @update:open="(value) => confirmDialog.open = value">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ confirmDialog.title }}</DialogTitle>
                <DialogDescription>{{ confirmDialog.message }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button variant="outline" @click="confirmDialog.onCancel">Batal</Button>
                <Button class="bg-green-600 text-white hover:bg-green-700" @click="confirmDialog.onConfirm">
                    <Wallet class="mr-2 h-4 w-4" />
                    Konfirmasi
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
