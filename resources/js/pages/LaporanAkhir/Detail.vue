<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { 
    ArrowLeft, 
    Upload, 
    Eye, 
    Download, 
    FileText, 
    Trash2,
    Calendar,
    User,
    Building2,
    AlertCircle,
    CheckCircle,
    FolderOpen
} from 'lucide-vue-next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogClose
} from '@/components/ui/dialog';

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
    periodeOptions: {
        [key: string]: string;
    };
}>();

// Get flash messages
const page = usePage();
const flashMessage = computed(() => {
    const pageProps = page.props as any;
    return pageProps.flash || {};
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Arsip Monitoring', href: '/arsip-monitoring' },
    { title: `Arsip Monitoring ${props.skpd.nama_skpd}`, href: `/arsip-monitoring/${props.skpd.id}` },
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
    keterangan: ''
});

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
        }
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
            }
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
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Detail Arsip Monitoring</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ skpd.nama_skpd }} - {{ tugas.kode_nomenklatur.nomor_kode }} {{ tugas.kode_nomenklatur.nomenklatur }}
                    </p>
                </div>
            </div>

            <!-- Flash Messages -->
            <div v-if="flashMessage.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <div class="flex items-center">
                    <CheckCircle class="w-5 h-5 mr-2" />
                    {{ flashMessage.success }}
                </div>
            </div>

            <div v-if="flashMessage.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <div class="flex items-center">
                    <AlertCircle class="w-5 h-5 mr-2" />
                    {{ flashMessage.error }}
                </div>
            </div>

            <!-- Informasi Perangkat Daerah -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-6">
                <div class="flex items-center mb-4">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <Building2 class="h-6 w-6 text-blue-600" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-700">Informasi Perangkat Daerah</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 mb-1">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.nama_skpd }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 mb-1">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.kode_organisasi }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 mb-1">Tahun</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ tahun }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 mb-1">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.kepala_name }}</p>
                        <p class="text-sm font-mono text-gray-500">NIP: {{ skpd.kepala_nip || '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 mb-1">Penanggung Jawab</h3>
                        <p class="text-lg font-semibold text-gray-800">{{ skpd.operator_name }}</p>
                        <p class="text-sm font-mono text-gray-500">NIP: {{ skpd.operator_nip || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tugas -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-700 mb-4">Informasi Tugas</h2>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-sm font-medium text-green-700 mb-1">Urusan/Tugas Perangkat Daerah</h3>
                    <p class="text-lg font-semibold text-green-800">
                        {{ tugas.kode_nomenklatur.nomor_kode }} - {{ tugas.kode_nomenklatur.nomenklatur }}
                    </p>
                </div>
            </div>

            <!-- Tabel Arsip Monitoring -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-700">Arsip Monitoring</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola dokumen monitoring dari rencana awal hingga triwulan 4 - Tahun {{ tahun }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Periode
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status File
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Informasi File
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(data, periode) in arsipData" :key="periode" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="rounded-full p-2 mr-3"
                                             :class="{
                                                 'bg-blue-100': periode === 'rencana_awal',
                                                 'bg-green-100': periode === 'triwulan_1',
                                                 'bg-yellow-100': periode === 'triwulan_2',
                                                 'bg-orange-100': periode === 'triwulan_3',
                                                 'bg-red-100': periode === 'triwulan_4'
                                             }">
                                            <FolderOpen class="w-4 h-4"
                                                       :class="{
                                                           'text-blue-600': periode === 'rencana_awal',
                                                           'text-green-600': periode === 'triwulan_1',
                                                           'text-yellow-600': periode === 'triwulan_2',
                                                           'text-orange-600': periode === 'triwulan_3',
                                                           'text-red-600': periode === 'triwulan_4'
                                                       }" />
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ data.label }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="data.arsip" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <CheckCircle class="w-3 h-3 mr-1" />
                                        File Tersedia
                                    </span>
                                    <span v-else 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <AlertCircle class="w-3 h-3 mr-1" />
                                        Belum Ada File
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="data.arsip">
                                        <div class="text-sm font-medium text-gray-900 flex items-center">
                                            <FileText class="w-4 h-4 mr-2" :class="getFileIcon(data.arsip.tipe_file)" />
                                            {{ data.arsip.nama_file }}
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <div class="flex items-center">
                                                <Calendar class="w-3 h-3 mr-1" />
                                                {{ data.arsip.tanggal_upload }}
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <User class="w-3 h-3 mr-1" />
                                                {{ data.arsip.uploaded_by }} • {{ data.arsip.ukuran_file }}
                                            </div>
                                            <div v-if="data.arsip.keterangan" class="mt-1 text-xs text-gray-400">
                                                {{ data.arsip.keterangan }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-sm text-gray-400 italic">
                                        Belum ada file yang diunggah
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- Tombol Upload -->
                                        <Button
                                            @click="openUploadModal(periode)"
                                            size="sm"
                                            :variant="data.arsip ? 'outline' : 'default'"
                                            class="flex items-center"
                                        >
                                            <Upload class="w-4 h-4 mr-1" />
                                            {{ data.arsip ? 'Ganti' : 'Unggah' }}
                                        </Button>

                                        <!-- Tombol Detail/View (hanya jika ada file) -->
                                        <Button
                                            v-if="data.arsip"
                                            @click="viewFile(data.arsip.id)"
                                            size="sm"
                                            variant="outline"
                                            class="flex items-center text-blue-600 hover:text-blue-800"
                                        >
                                            <Eye class="w-4 h-4 mr-1" />
                                            Detail
                                        </Button>

                                        <!-- Tombol Download (hanya jika ada file) -->
                                        <Button
                                            v-if="data.arsip"
                                            @click="downloadFile(data.arsip.id)"
                                            size="sm"
                                            variant="outline"
                                            class="flex items-center text-green-600 hover:text-green-800"
                                        >
                                            <Download class="w-4 h-4 mr-1" />
                                            Download
                                        </Button>

                                        <!-- Tombol Delete (hanya jika ada file) -->
                                        <Button
                                            v-if="data.arsip"
                                            @click="openDeleteModal(data.arsip.id)"
                                            size="sm"
                                            variant="outline"
                                            class="flex items-center text-red-600 hover:text-red-800"
                                        >
                                            <Trash2 class="w-4 h-4 mr-1" />
                                            Hapus
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
                    <DialogDescription>
                        Unggah file untuk periode {{ periodeOptions[selectedPeriode] || selectedPeriode }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitUpload" class="space-y-4">
                    <div>
                        <Label for="file">File (PDF, DOC, DOCX, XLS, XLSX - Max 10MB)</Label>
                        <Input
                            id="file"
                            type="file"
                            accept=".pdf,.doc,.docx,.xls,.xlsx"
                            @change="handleFileChange"
                            required
                            class="mt-1"
                        />
                        <div v-if="uploadForm.errors.file" class="text-red-500 text-sm mt-1">
                            {{ uploadForm.errors.file }}
                        </div>
                    </div>

                    <div>
                        <Label for="keterangan">Keterangan (Opsional)</Label>
                        <textarea
                            id="keterangan"
                            v-model="uploadForm.keterangan"
                            placeholder="Tambahkan keterangan untuk file ini..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            rows="3"
                        ></textarea>
                        <div v-if="uploadForm.errors.keterangan" class="text-red-500 text-sm mt-1">
                            {{ uploadForm.errors.keterangan }}
                        </div>
                    </div>

                    <DialogFooter>
                        <DialogClose asChild>
                            <Button type="button" variant="outline">Batal</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="uploadForm.processing">
                            <Upload class="w-4 h-4 mr-2" />
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
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus file ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <DialogClose asChild>
                        <Button type="button" variant="outline">Batal</Button>
                    </DialogClose>
                    <Button @click="confirmDelete" variant="destructive">
                        <Trash2 class="w-4 h-4 mr-2" />
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