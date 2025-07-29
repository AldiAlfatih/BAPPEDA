<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, Building2, Calendar, CheckCircle, Download, Binoculars, FileText, FolderOpen, Trash2, Upload, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    skpd: {
        id: number;
        nama_skpd: string;
        kode_organisasi: string;
        kepala_name: string;
        kepala_nip: string;
        operator_name: string;
        operator_nip: string;
    };
    tugas: {
        id: number;
        kode_nomenklatur: {
            nomor_kode: string;
            nomenklatur: string;
        };
    };
    arsipData: {
        [key: string]: {
            label: string;
            arsip: {
                id: number;
                nama_file: string;
                ukuran_file: string;
                tipe_file: string;
                tanggal_upload: string;
                uploaded_by: string;
                keterangan: string;
                file_url: string;
            } | null;
        };
    };
    tahun: number;
    tahunAktif?: { id: number; tahun: string; status: number };
    allTahun?: Array<{ id: number; tahun: string; status: number }>;
    periodeOptions: {
        [key: string]: string;
    };
    breadcrumbUserId?: number | null;
}>();

// Get flash messages
const page = usePage();
const flashMessage = computed(() => {
    const pageProps = page.props as any;
    return pageProps.flash || {};
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Arsip Monitoring', href: route('arsip-monitoring.index') },
    { 
        title: `Arsip Monitoring ${props.skpd.nama_skpd}`, 
        href: props.breadcrumbUserId ? route('arsip-monitoring.show', props.breadcrumbUserId) : '' 
    },
    { title: 'Detail Arsip', href: '' },
];

// State untuk upload modal
const uploadModalOpen = ref(false);
const selectedPeriode = ref('');
const deleteModalOpen = ref(false);
const selectedArsipId = ref<number | null>(null);

// Form untuk upload
const uploadForm = useForm({
    file: null as File | null,
    skpd_tugas_id: props.tugas.id,
    periode: '',
    tahun: props.tahun,
    keterangan: '',
});

// Reactive state for selected year - menggunakan computed agar selalu sinkron dengan props
const selectedTahunId = computed(() => props.tahunAktif?.id || null);

// Handler for year change
const handleTahunChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newTahunId = target.value ? parseInt(target.value) : null;

    if (selectedTahunId.value !== newTahunId) {
        // Reload data with the new year
        const selectedTahun = props.allTahun?.find((t) => t.id === newTahunId);
        if (selectedTahun && props.tugas?.id) {
            router.visit(
                route('arsip-monitoring.detail.tahun', {
                    tugasId: props.tugas.id,
                    tahun: selectedTahun.tahun,
                }),
                {
                    preserveState: false,
                },
            );
        }
    }
};

// Helper functions
function goBack() {
    router.visit(`/arsip-monitoring/${props.skpd.id}`);
}

function openUploadModal(periode: string) {
    selectedPeriode.value = periode;
    uploadForm.periode = periode;
    uploadModalOpen.value = true;
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        uploadForm.file = target.files[0];
    }
}

function submitUpload() {
    uploadForm.post(route('arsip-monitoring.upload'), {
        onSuccess: () => {
            uploadModalOpen.value = false;
            uploadForm.reset();
            selectedPeriode.value = '';
        },
        onError: (errors) => {
            console.error('Upload error:', errors);
        },
    });
}

function downloadFile(arsipId: number) {
    window.open(route('arsip-monitoring.download', arsipId), '_blank');
}

function viewFile(arsipId: number) {
    window.open(route('arsip-monitoring.view', arsipId), '_blank');
}

function openDeleteModal(arsipId: number) {
    selectedArsipId.value = arsipId;
    deleteModalOpen.value = true;
}

function confirmDelete() {
    if (selectedArsipId.value) {
        router.delete(route('arsip-monitoring.delete', selectedArsipId.value), {
            onSuccess: () => {
                deleteModalOpen.value = false;
                selectedArsipId.value = null;
            },
        });
    }
}

function getFileIcon(tipeFile: string) {
    switch (tipeFile.toLowerCase()) {
        case 'pdf':
            return 'text-red-600';
        case 'doc':
        case 'docx':
            return 'text-blue-600';
        case 'xls':
        case 'xlsx':
            return 'text-green-600';
        default:
            return 'text-gray-600';
    }
}
</script>

<template>
    <Head title="Detail Arsip Monitoring" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
                <div>
                    <div class="mb-2 flex items-center gap-2"></div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Detail Arsip Monitoring</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ skpd.nama_skpd }} - {{ tugas.kode_nomenklatur.nomor_kode }} {{ tugas.kode_nomenklatur.nomenklatur }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Dropdown Tahun -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Pilih Periode:</label>
                        <select
                            :value="selectedTahunId"
                            @change="handleTahunChange"
                            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">Pilih Tahun</option>
                            <option v-for="tahun in allTahun" :key="tahun.id" :value="tahun.id">
                                {{ tahun.tahun }} {{ tahun.status === 1 ? '(Aktif)' : '' }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-blue-600">Tahun Anggaran</span>
                        <span class="rounded-md bg-blue-100 px-3 py-1 text-sm font-bold text-blue-800">
                            {{ tahunAktif?.tahun || tahun }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <div v-if="flashMessage.success" class="rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                <div class="flex items-center">
                    <CheckCircle class="mr-2 h-5 w-5" />
                    {{ flashMessage.success }}
                </div>
            </div>

            <div v-if="flashMessage.error" class="rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                <div class="flex items-center">
                    <AlertCircle class="mr-2 h-5 w-5" />
                    {{ flashMessage.error }}
                </div>
            </div>

            <!-- Informasi Perangkat Daerah -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <div class="mb-4 flex items-center">
                    <div class="mr-4 rounded-full bg-blue-100 p-3">
                        <Building2 class="h-6 w-6 text-blue-600" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-700">Informasi Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-1 text-sm font-medium text-gray-600">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.nama_skpd }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-1 text-sm font-medium text-gray-600">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.kode_organisasi }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-1 text-sm font-medium text-gray-600">Tahun</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ tahun }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-1 text-sm font-medium text-gray-600">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.kepala_name }}</p>
                        <p class="font-mono text-sm text-gray-500">NIP: {{ skpd.kepala_nip || '-' }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-1 text-sm font-medium text-gray-600">Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.operator_name }}</p>
                        <p class="font-mono text-sm text-gray-500">NIP: {{ skpd.operator_nip || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tugas -->
            <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-xl font-bold text-gray-700">Informasi Tugas</h2>
                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <h3 class="mb-1 text-sm font-medium text-green-700">Urusan/Tugas Perangkat Daerah</h3>
                    <p class="text-lg font-semibold text-green-800">
                        {{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}
                    </p>
                </div>
            </div>

            <!-- Tabel Arsip Monitoring -->
            <div class="overflow-hidden rounded-lg border border-gray-100 bg-white shadow-lg">
                <div class="border-b border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-700">Arsip Monitoring</h2>
                    <p class="mt-1 text-sm text-gray-500">Kelola dokumen monitoring dari rencana awal hingga triwulan 4 - Tahun {{ tahun }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Informasi File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(data, periode) in arsipData" :key="periode" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="mr-3 rounded-full p-2"
                                            :class="{
                                                'bg-blue-100': periode === 'rencana_awal',
                                                'bg-green-100': periode === 'triwulan_1',
                                                'bg-yellow-100': periode === 'triwulan_2',
                                                'bg-orange-100': periode === 'triwulan_3',
                                                'bg-red-100': periode === 'triwulan_4',
                                            }"
                                        >
                                            <FolderOpen
                                                class="h-4 w-4"
                                                :class="{
                                                    'text-blue-600': periode === 'rencana_awal',
                                                    'text-green-600': periode === 'triwulan_1',
                                                    'text-yellow-600': periode === 'triwulan_2',
                                                    'text-orange-600': periode === 'triwulan_3',
                                                    'text-red-600': periode === 'triwulan_4',
                                                }"
                                            />
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ data.label }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        v-if="data.arsip"
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
                                    >
                                        <CheckCircle class="mr-1 h-3 w-3" />
                                        File Tersedia
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"
                                    >
                                        <AlertCircle class="mr-1 h-3 w-3" />
                                        Belum Ada File
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="data.arsip">
                                        <div class="flex items-center text-sm font-medium text-gray-900">
                                            <FileText class="mr-2 h-4 w-4" :class="getFileIcon(data.arsip.tipe_file)" />
                                            {{ data.arsip.nama_file }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <Calendar class="mr-1 h-3 w-3" />
                                                {{ data.arsip.tanggal_upload }}
                                            </div>
                                            <div class="mt-1 flex items-center">
                                                <User class="mr-1 h-3 w-3" />
                                                {{ data.arsip.uploaded_by }} • {{ data.arsip.ukuran_file }}
                                            </div>
                                            <div v-if="data.arsip.keterangan" class="mt-1 text-xs text-gray-400">
                                                {{ data.arsip.keterangan }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-sm text-gray-400 italic">Belum ada file yang diunggah</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <!-- Tombol Upload -->
                                        <Button
                                            @click="openUploadModal(String(periode))"
                                            size="sm"
                                            :variant="data.arsip ? 'outline' : 'default'"
                                            class="flex items-center"
                                        >
                                            <Upload class="mr-1 h-4 w-4" />
                                            {{ data.arsip ? 'Ganti' : 'Unggah' }}
                                        </Button>

                                        <!-- Tombol Detail/View (hanya jika ada file) -->
                                        <Button
                                            v-if="data.arsip"
                                            @click="viewFile(data.arsip.id)"
                                            size="sm"
                                            variant="outline"
                                            class="flex items-center text-orange-600 hover:text-orange-800"
                                        >
                                            <Binoculars class="mr-1 h-4 w-4" />
                                            <!-- Detail -->
                                        </Button>

                                        <!-- Tombol Download (hanya jika ada file) -->
                                        <Button
                                            v-if="data.arsip"
                                            @click="downloadFile(data.arsip.id)"
                                            size="sm"
                                            variant="outline"
                                            class="flex items-center text-green-600 hover:text-green-800"
                                        >
                                            <Download class="mr-1 h-4 w-4" />
                                            <!-- Download -->
                                        </Button>

                                        <!-- Tombol Delete (hanya jika ada file) -->
                                        <Button
                                            v-if="data.arsip"
                                            @click="openDeleteModal(data.arsip.id)"
                                            size="sm"
                                            variant="outline"
                                            class="flex items-center text-red-600 hover:text-red-800"
                                        >
                                            <Trash2 class="mr-1 h-4 w-4" />
                                            <!-- Hapus -->
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <Dialog v-model:open="uploadModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Unggah File Arsip</DialogTitle>
                    <DialogDescription> Unggah file untuk periode {{ periodeOptions[selectedPeriode] || selectedPeriode }} </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitUpload" class="space-y-4">
                    <div>
                        <Label for="file">File (PDF - Max 10MB)</Label>
                        <Input id="file" type="file" accept=".pdf" @change="handleFileChange" required class="mt-1" />
                        <div v-if="uploadForm.errors.file" class="mt-1 text-sm text-red-500">
                            {{ uploadForm.errors.file }}
                        </div>
                    </div>

                    <div>
                        <Label for="keterangan">Keterangan (Opsional)</Label>
                        <textarea
                            id="keterangan"
                            v-model="uploadForm.keterangan"
                            placeholder="Tambahkan keterangan untuk file ini..."
                            class="focus:ring-opacity-50 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                            rows="3"
                        ></textarea>
                        <div v-if="uploadForm.errors.keterangan" class="mt-1 text-sm text-red-500">
                            {{ uploadForm.errors.keterangan }}
                        </div>
                    </div>

                    <DialogFooter>
                        <DialogClose asChild>
                            <Button type="button" variant="outline">Batal</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="uploadForm.processing">
                            <Upload class="mr-2 h-4 w-4" />
                            {{ uploadForm.processing ? 'Mengunggah...' : 'Unggah File' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Modal -->
        <Dialog v-model:open="deleteModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Hapus File Arsip</DialogTitle>
                    <DialogDescription> Apakah Anda yakin ingin menghapus file ini? Tindakan ini tidak dapat dibatalkan. </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <DialogClose asChild>
                        <Button type="button" variant="outline">Batal</Button>
                    </DialogClose>
                    <Button @click="confirmDelete" variant="destructive">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Hapus File
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.notification {
    transition: opacity 0.5s ease-in-out;
}
</style>
