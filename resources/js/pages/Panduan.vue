<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

// Breadcrumbs
const breadcrumbs = [{ title: 'Panduan', href: '/panduan' }];

// Interface props dari controller
interface PanduanItem {
    id: number;
    judul: string;
    deskripsi: string;
    file_url?: string | null;
    sampul_url?: string | null;
}

// User role management
interface User {
    id: number;
    name: string;
    role: string;
}

const page = usePage();
const auth = page.props.auth as { user: User };

// Check if current user has restricted access (operator or perangkat_daerah)
const hasRestrictedAccess = computed(() => {
    const userRole = auth?.user?.role?.toLowerCase();
    return userRole === 'operator' || userRole === 'perangkat_daerah';
});

const props = defineProps<{ panduan: PanduanItem[] }>();

// Local reactive copy agar bisa update UI tanpa reload penuh
const localPanduan = ref<PanduanItem[]>([...props.panduan]);

const isFormVisible = ref(false);

// Dialog state management
const confirmDelete = ref<number | null>(null);

const form = ref({
    judul: '',
    deskripsi: '',
    file: null as File | null,
    sampul: null as File | null,
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

const showForm = () => (isFormVisible.value = true);
const hideForm = () => (isFormVisible.value = false);

const MAX_FILE_MB = 10; // harus selaras dengan validasi server
const MAX_SAMPUL_MB = 4; // harus selaras dengan validasi server

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const f = input.files?.[0] ?? null;
    if (f && f.size > MAX_FILE_MB * 1024 * 1024) {
        errors.value.file = `Ukuran file melebihi batas ${MAX_FILE_MB}MB`;
        form.value.file = null;
        return;
    }
    errors.value.file = '' as any;
    form.value.file = f;
};

const handleSampulChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const f = input.files?.[0] ?? null;
    if (f && f.size > MAX_SAMPUL_MB * 1024 * 1024) {
        errors.value.sampul = `Ukuran gambar sampul melebihi batas ${MAX_SAMPUL_MB}MB`;
        form.value.sampul = null;
        return;
    }
    errors.value.sampul = '' as any;
    form.value.sampul = f;
};

const resetForm = () => {
    form.value.judul = '';
    form.value.deskripsi = '';
    form.value.file = null;
    form.value.sampul = null;
    errors.value = {};
};

const handleSubmit = () => {
    isSubmitting.value = true;
    errors.value = {};

    // Client-side guard untuk mencegah 413
    if (form.value.file && form.value.file.size > MAX_FILE_MB * 1024 * 1024) {
        errors.value.file = `Ukuran file melebihi batas ${MAX_FILE_MB}MB`;
        isSubmitting.value = false;
        return;
    }
    if (form.value.sampul && form.value.sampul.size > MAX_SAMPUL_MB * 1024 * 1024) {
        errors.value.sampul = `Ukuran gambar sampul melebihi batas ${MAX_SAMPUL_MB}MB`;
        isSubmitting.value = false;
        return;
    }

    const formData = new FormData();
    formData.append('judul', form.value.judul);
    formData.append('deskripsi', form.value.deskripsi);
    if (form.value.file) formData.append('file', form.value.file);
    if (form.value.sampul) formData.append('sampul', form.value.sampul);

    router.post('/panduan', formData, {
        onSuccess: () => {
            resetForm();
            hideForm();
            isSubmitting.value = false;
            // Reload data terbaru dari server
            router.visit('/panduan', { preserveState: false, preserveScroll: true });
        },
        onError: (err) => {
            errors.value = err || {};
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

const deletePanduan = (id: number) => {
    confirmDelete.value = id;
};

const confirmDeleteAction = () => {
    if (confirmDelete.value) {
        router.delete(`/panduan/${confirmDelete.value}`, {
            onSuccess: () => {
                // Reload data setelah hapus
                router.visit('/panduan', { preserveState: false });
            },
        });
        confirmDelete.value = null;
    }
};

const cancelDelete = () => {
    confirmDelete.value = null;
};

const editPanduan = (id: number) => {
    router.visit(`/panduan/${id}/edit`);
};

const viewFile = (fileUrl: string | undefined | null) => {
    if (fileUrl) {
        window.open(fileUrl, '_blank');
    } else {
        alert('File tidak tersedia');
    }
};

// Handle image loading errors
const handleImageError = (event: Event) => {
    const img = event.target as HTMLImageElement;
    console.warn('🖼️ Image failed to load:', img.src);
    console.warn('🔄 Switching to fallback image: /images/default-image.svg');
    
    // Set fallback image
    img.src = '/images/default-image.svg';
    // Add error styling
    img.classList.add('opacity-75', 'border-2', 'border-dashed', 'border-gray-300');
    
    // Add tooltip to indicate this is a fallback
    img.title = 'Gambar sampul tidak tersedia - menggunakan gambar default';
};

// Debug: Log all panduan URLs when component mounts
console.log('🐛 DEBUG: Panduan URLs loaded:', props.panduan.map(item => ({
    id: item.id,
    judul: item.judul,
    sampul_url: item.sampul_url
})));
</script>

<template>
    <Head title="Panduan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Tambahkan Panduan - Hanya tampil untuk admin -->
        <div v-if="!hasRestrictedAccess" class="mb-6 flex items-center justify-end">
            <Button @click="showForm" class="flex items-center space-x-2 rounded bg-blue-600 px-2 py-1 text-white hover:bg-blue-700">
                <Plus class="h-4 w-4" />
                <span>Tambahkan Panduan</span>
            </Button>
        </div>

        <!-- Form Tambah Panduan - Hanya tampil untuk admin -->
        <div v-if="isFormVisible && !hasRestrictedAccess" class="mb-8 rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold text-gray-600">Tambah Panduan Baru</h2>
            <form @submit.prevent="handleSubmit" class="grid grid-cols-1 gap-4">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-600">Judul Panduan</label>
                    <input
                        id="judul"
                        v-model="form.judul"
                        type="text"
                        placeholder="Judul Panduan"
                        class="mt-1 block w-full rounded border p-2 text-gray-700"
                        required
                    />
                    <div v-if="errors.judul" class="mt-1 text-sm text-red-500">{{ errors.judul }}</div>
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-600">Deskripsi</label>
                    <textarea
                        id="deskripsi"
                        v-model="form.deskripsi"
                        placeholder="Deskripsi"
                        class="mt-1 block w-full rounded border p-2 text-gray-700"
                        required
                    ></textarea>
                    <div v-if="errors.deskripsi" class="mt-1 text-sm text-red-500">{{ errors.deskripsi }}</div>
                </div>

                <div>
                    <label for="file" class="mb-1 block text-sm font-medium text-gray-700"> File Panduan (PDF, DOC, DOCX - max 2MB) </label>
                    <input
                        id="file"
                        type="file"
                        @change="handleFileChange"
                        class="mt-1 block w-full rounded border border-gray-300 p-2 text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        accept=".pdf,.doc,.docx"
                    />
                    <div v-if="errors.file" class="mt-1 text-sm text-red-500">{{ errors.file }}</div>
                </div>

                <div>
                    <label for="sampul" class="mb-1 block text-sm font-medium text-gray-700"> Sampul Panduan (JPG, JPEG, PNG - max 2MB) </label>
                    <input
                        id="sampul"
                        type="file"
                        @change="handleSampulChange"
                        class="mt-1 block w-full rounded border border-gray-300 p-2 text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        accept="image/*"
                    />
                    <div v-if="errors.sampul" class="mt-1 text-sm text-red-500">{{ errors.sampul }}</div>
                </div>

                <div v-if="errors.error" class="rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                    <p class="font-bold">Terjadi kesalahan:</p>
                    <p>{{ errors.error }}</p>
                </div>

                <div class="mt-2 flex gap-2">
                    <Button type="submit" class="rounded bg-green-800 px-4 py-1 text-white hover:bg-green-700" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                    <Button
                        type="button"
                        @click="hideForm"
                        class="rounded bg-gray-300 px-4 py-1 text-white hover:bg-gray-600"
                        :disabled="isSubmitting"
                    >
                        Batal
                    </Button>
                </div>
            </form>
        </div>

        <!-- Daftar Panduan -->
        <div class="mx-3 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="item in localPanduan" :key="item.id" class="flex flex-col rounded-lg bg-white p-4 shadow-lg">
                <img 
                    :src="item.sampul_url ?? '/images/default-image.png'" 
                    :alt="`Sampul ${item.judul}`" 
                    class="mb-4 h-32 w-full rounded object-contain transition-all duration-200 hover:scale-105" 
                    @error="handleImageError"
                    loading="lazy"
                />
                <div class="text-base text-gray-700">
                    <p class="mb-2 font-bold text-gray-800">{{ item.judul }}</p>
                    <p class="mb-4 text-justify text-sm leading-relaxed">{{ item.deskripsi }}</p>
                    <div class="flex flex-wrap justify-end gap-2">
                        <!-- Tombol Lihat File - Tersedia untuk semua role -->
                        <Button
                            v-if="item.file_url"
                            @click="viewFile(item.file_url)"
                            class="rounded bg-orange-500 px-4 py-2 text-xs font-bold text-white hover:bg-orange-600"
                        >
                            Lihat File
                        </Button>
                        <Button v-else class="cursor-not-allowed rounded bg-gray-400 px-4 py-2 text-xs font-bold text-white" disabled>
                            Tidak Ada File
                        </Button>
                        
                        <!-- Tombol Edit dan Hapus - Hanya untuk admin -->
                        <template v-if="!hasRestrictedAccess">
                            <Button @click="editPanduan(item.id)" class="rounded bg-green-900 px-4 py-2 text-xs font-bold text-white hover:bg-green-700">
                                Edit
                            </Button>
                            <Button @click="deletePanduan(item.id)" class="rounded bg-red-700 px-4 py-2 text-xs font-bold text-white hover:bg-red-900">
                                Hapus
                            </Button>
                        </template>
                    </div>
                </div>
            </div>

            <div v-if="localPanduan.length === 0" class="col-span-3 rounded-lg bg-gray-200 py-5 text-center">
                <p class="text-gray-900">Belum ada panduan tersedia. Silakan tambahkan panduan baru.</p>
            </div>
        </div>
    </AppLayout>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog :open="confirmDelete !== null" @update:open="cancelDelete">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>Yakin ingin menghapus panduan ini? Tindakan ini tidak dapat dibatalkan.</DialogDescription>
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
