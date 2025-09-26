<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Inertia, Method } from '@inertiajs/inertia';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Periode', href: '/monitoring/periode' }];

const props = defineProps<{
    periode?: Array<{
        id: number;
        tanggal_mulai: string;
        tanggal_selesai: string;
        status: number;
        tahap?: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    }>;
    tahuns: Array<{ id: number; tahun: string }>;
}>();

const periodeData = computed(() => props.periode ?? []);

const statusList = ref(periodeData.value.map((item) => ({ id: item.id, status: item.status })));

// Dialog state management
const confirmDialog = ref({
    open: false,
    title: '',
    message: '',
    onConfirm: () => {}
});

function openConfirmDialog(title: string, message: string, onConfirm: () => void) {
    confirmDialog.value = {
        open: true,
        title,
        message,
        onConfirm
    };
}

function closeConfirmDialog() {
    confirmDialog.value.open = false;
}

function handleConfirmAction() {
    confirmDialog.value.onConfirm();
    closeConfirmDialog();
}

function toggleStatus(id: number) {
    const item = statusList.value.find((i) => i.id === id);
    if (!item) return;

    const newStatus = item.status === 1 ? 0 : 1;

    Inertia.put(
        `/monitoring/periode/${id}/status`,
        { status: newStatus },
        {
            onSuccess: () => {
                item.status = newStatus;
            },
            onError: () => {
                alert('Gagal mengubah status.');
            },
        },
    );
}

function getStatusLabel(status: number) {
    return status === 1 ? 'Buka' : 'Tutup';
}

function editItem(id: number) {
    Inertia.visit(`/monitoring/periode/${id}/edit`);
}

const searchQuery = ref('');
const yearFilter = ref<number | null>(null);

const filteredperiode = computed(() => {
    return periodeData.value.filter((p) => {
        const matchesSearch = p.tahap?.tahap.toLowerCase().includes(searchQuery.value.toLowerCase()) ?? true;
        const matchesYear = yearFilter.value ? p.tahun.id === yearFilter.value : true;
        return matchesSearch && matchesYear;
    });
});

function formatDate(date: string) {
    const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('id-ID', options);
}

const years = computed(() => props.tahuns);

function deleteItem(id: number) {
    openConfirmDialog(
        'Konfirmasi Hapus',
        'Apakah Anda yakin ingin menghapus periode ini?',
        () => {
            Inertia.delete(`/monitoring/periode/${id}`);
        }
    );
}

// === ✅ Tambahan untuk tombol generate
const isGenerating = ref(false);
function generatePeriode() {
    openConfirmDialog(
        'Konfirmasi Generate Periode',
        'Apakah Anda ingin membuat otomatis periode untuk tahun berjalan?',
        () => {
            isGenerating.value = true;
            Inertia.visit('/monitoring/periode/generate', {
                method: 'POST' as Method,
            preserveScroll: true,
            data: { tahun: yearFilter.value }, // Mengirimkan tahun yang dipilih
            onFinish: () => {
                isGenerating.value = false;
            },
            onError: () => {
                alert('Gagal membuat periode.');
            },
        });
        }
    );
}
</script>

<template>
    <Head title="Periode" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-2 p-1">
            <div class="mb-2 flex items-center justify-between">
                <div class="ml-4">
                    <select v-model="yearFilter" class="rounded-md border border-gray-300 px-2 py-1">
                        <option value="" disabled selected>Pilih Tahun</option>
                        <option v-for="year in years" :key="year.id" :value="year.id">{{ year.tahun }}</option>
                    </select>
                </div>
                <button
                    @click="generatePeriode"
                    :disabled="isGenerating"
                    class="pi pi-plus h-xs rounded-md bg-blue-600 px-2 py-1 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ isGenerating ? 'Memproses...' : 'Generate Periode Otomatis' }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded bg-white shadow">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">No</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tahap</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Mulai</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Berakhir</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-if="filteredperiode.length === 0">
                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">Tidak ada data periode tersedia</td>
                        </tr>
                        <tr v-for="(p, index) in filteredperiode" :key="p.id">
                            <td class="px-4 py-2 text-sm text-gray-600">{{ index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ p?.tahap?.tahap || '' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(p.tanggal_mulai) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(p.tanggal_selesai) }}</td>
                            <td class="px-4 py-2">
                                <span
                                    :class="{
                                        'text-green-600': p.status === 1,
                                        'text-red-600': p.status === 0,
                                    }"
                                >
                                    {{ getStatusLabel(p.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <button @click="toggleStatus(p.id)" class="text-sm text-blue-600 hover:text-blue-700">
                                    {{ p.status === 1 ? 'Tutup' : 'Buka' }}
                                </button>
                                <button @click="editItem(p.id)" class="ml-2 text-sm text-green-600 hover:text-green-700">Edit</button>
                                <button @click="deleteItem(p.id)" class="ml-2 text-sm text-red-600 hover:text-red-700">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>

    <!-- Dialog Konfirmasi -->
    <Dialog :open="confirmDialog.open" @update:open="closeConfirmDialog">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ confirmDialog.title }}</DialogTitle>
                <DialogDescription>{{ confirmDialog.message }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button variant="outline" @click="closeConfirmDialog">Batal</Button>
                <Button 
                    class="bg-blue-600 text-white hover:bg-blue-700" 
                    @click="handleConfirmAction"
                    v-if="confirmDialog.title === 'Konfirmasi Generate Periode'"
                >
                    OK
                </Button>
                <Button 
                    class="bg-red-600 text-white hover:bg-red-700" 
                    @click="handleConfirmAction"
                    v-else
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Hapus
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
