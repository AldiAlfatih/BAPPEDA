<script setup lang="ts">
import TabelListSKPD from '@/components/data/TabelListSKPD.vue';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { HandCoins } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

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
    enabledParsialUsers?: number[];
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
    isBudgetChangeAvailable?: boolean;
    tahunAktif?: { id: number; tahun: string; status: number };
    allTahun?: Array<{ id: number; tahun: string; status: number }>;
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Manajemen Anggaran', href: '/manajemenanggaran' }];

// State for anggaran operations
const isProcessing = ref(false);

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

// Reactive state for selected year - menggunakan computed agar selalu sinkron dengan props
const selectedTahunId = computed(() => props.tahunAktif?.id || null);

// Handler for year change
const handleTahunChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newTahunId = target.value ? parseInt(target.value) : null;

    if (selectedTahunId.value !== newTahunId) {
        // Reload data with the new year
        const selectedTahun = props.allTahun?.find((t) => t.id === newTahunId);
        if (selectedTahun) {
            router.visit(route('manajemenanggaran.index.tahun', { tahun: selectedTahun.tahun }), {
                preserveState: false,
            });
        }
    }
};

// Computed untuk mengecek apakah Triwulan 4 aktif
const isTriwulan4Active = computed(() => {
    return props.isBudgetChangeAvailable && props.triwulan4Aktif;
});

// Computed untuk pesan perubahan anggaran
const budgetChangeMessage = computed(() => {
    if (isTriwulan4Active.value) {
        return `Periode Triwulan 4 tahun ${props.triwulan4Aktif?.tahun?.tahun} sedang aktif. Klik tombol untuk mengaktifkan mode perubahan anggaran untuk semua perangkat daerah.`;
    }
    return 'Periode Triwulan 4 belum dibuka. Perubahan anggaran hanya dapat dilakukan pada periode Triwulan 4.';
});

function goToShowPage(id: number) {
    router.visit(route('manajemenanggaran.show', { id }));
}

async function handleAnggaranPerubahan() {
    if (!isTriwulan4Active.value) {
        showAlert(
            'Periode Tidak Aktif',
            'Periode Triwulan 4 belum dibuka. Perubahan anggaran hanya dapat dilakukan ketika periode Triwulan 4 aktif.',
            'error'
        );
        return;
    }

    const confirmMessage =
        `Apakah Anda yakin ingin mengaktifkan mode perubahan anggaran untuk SEMUA perangkat daerah?\n\n` +
        `✅ Periode: Triwulan 4 Tahun ${props.triwulan4Aktif?.tahun?.tahun}\n` +
        `✅ Total Perangkat Daerah: ${props.users.data.length}\n\n` +
        `Mode ini akan memungkinkan semua perangkat daerah untuk melakukan perubahan anggaran pada periode Triwulan 4.`;

    showConfirm(
        'Konfirmasi Aktivasi Perubahan Anggaran',
        confirmMessage,
        async () => {
            isProcessing.value = true;

            try {

        // Call backend to enable budget change for all departments
        const response = await fetch('/manajemenanggaran/enable-budget-change-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                confirm: true,
            }),
        });

        const data = await response.json();

                if (data.success) {
                    const successMessage =
                        `${data.message}\n\n` +
                        `📊 Total perangkat daerah yang dapat melakukan perubahan: ${data.enabled_count}\n\n` +
                        `Sekarang Anda dapat menggunakan tombol 🔍 (Binoculars) untuk mengakses mode perubahan anggaran pada setiap perangkat daerah.`;

                    showAlert('Berhasil!', successMessage, 'success');

                    // Refresh page to show updated state
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Gagal', data.message, 'error');
                }
            } catch (err) {
                console.error('Error enabling budget change:', err);
                showAlert(
                    'Terjadi Kesalahan',
                    'Terjadi kesalahan saat mengaktifkan mode perubahan anggaran. Silakan coba lagi.',
                    'error'
                );
            } finally {
                isProcessing.value = false;
            }
        }
    );
}

</script>

<template>
    <Head title="Monitoring" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header dengan judul dan action -->
            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Anggaran Perangkat Daerah</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data Manajemen Anggaran Perangkat Daerah</p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Dropdown Tahun -->

                    <div>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button
                                        @click="handleAnggaranPerubahan"
                                        :disabled="isProcessing || !isTriwulan4Active"
                                        class="flex transform items-center gap-2 shadow-lg transition-all duration-300"
                                        :class="{
                                            'cursor-not-allowed opacity-50': isProcessing || !isTriwulan4Active,
                                            'bg-orange-600 text-white hover:scale-105 hover:bg-orange-700': isTriwulan4Active,
                                            'bg-gray-400': !isTriwulan4Active,
                                        }"
                                    >
                                        <HandCoins class="h-4 w-4" />
                                        {{ isProcessing ? 'Memproses...' : 'Perubahan Anggaran' }}
                                        <span
                                            v-if="isTriwulan4Active"
                                            class="ml-1 inline-block h-2 w-2 animate-pulse rounded-full bg-green-400"
                                        ></span>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <div class="text-sm">
                                        <p v-if="isTriwulan4Active" class="font-medium text-green-600">✅ Triwulan 4 Aktif</p>
                                        <p v-else class="font-medium text-red-600">❌ Triwulan 4 Tidak Aktif</p>
                                        <p class="mt-1">{{ budgetChangeMessage }}</p>
                                        <p v-if="isTriwulan4Active" class="mt-2 text-xs text-gray-600">
                                            Klik untuk mengaktifkan mode perubahan anggaran<br />
                                            untuk semua perangkat daerah
                                        </p>
                                    </div>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </div>
            </div>

            <!-- Status Card untuk Triwulan 4 -->
            <div v-if="isTriwulan4Active" class="rounded-lg border border-orange-200 bg-orange-50 p-4 shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100">
                            <HandCoins class="h-4 w-4 text-orange-600" />
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-orange-800">Mode Perubahan Anggaran Tersedia</h3>
                        <div class="mt-1 text-sm text-orange-700">
                            <p>
                                Periode <strong>Triwulan 4 Tahun {{ triwulan4Aktif?.tahun?.tahun }}</strong> sedang aktif. Klik tombol "Perubahan
                                Anggaran" di atas untuk mengaktifkan mode perubahan anggaran untuk semua perangkat daerah.
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                            Tersedia
                        </span>
                    </div>
                </div>
            </div>

            <TabelListSKPD
                url_detail="manajemenanggaran.show"
                url_detail_partial="manajemenanggaran.show_partial"
                url_budget_change="manajemenanggaran.budget-change"
                :users="users"
                :showBinocularsButton="true"
                :enabledParsialUsers="enabledParsialUsers || []"
                :isBudgetChangeMode="!!isTriwulan4Active"
                :triwulan4Aktif="triwulan4Aktif"
            ></TabelListSKPD>
        </div>
    </AppLayout>

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
                <Button class="bg-orange-600 text-white hover:bg-orange-700" @click="confirmDialog.onConfirm">
                    <HandCoins class="mr-2 h-4 w-4" />
                    Aktifkan
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
