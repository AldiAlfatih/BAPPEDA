<script setup lang="ts">
import { ref, defineProps } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

// Breadcrumbs
const breadcrumbs = [{ title: 'Panduan', href: '/panduan' }];

// Props dari controller
interface PanduanItem {
  id: number;
  judul: string;
  deskripsi: string;
  file_url?: string | null;
  sampul_url?: string | null;
}

const { panduan } = defineProps({
  panduan: {
    type: Array as () => PanduanItem[],
    default: () => []
  }
});

const localPanduan = ref<PanduanItem[]>(panduan);
const isFormVisible = ref(false);
const form = ref({
  judul: '',
  deskripsi: '',
  file: null as File | null,
  sampul: null as File | null,
});

const showForm = () => {
  isFormVisible.value = true;
};

const hideForm = () => {
  isFormVisible.value = false;
};

const handleFileChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    form.value.file = input.files[0];
  }
};

const handleSampulChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    form.value.sampul = input.files[0];
  }
};

interface FormErrors {
  judul?: string;
  deskripsi?: string;
  file?: string;
  sampul?: string;
  error?: string;
  [key: string]: string | undefined;
}

const errors = ref<FormErrors>({});
const isSubmitting = ref(false);

const handleSubmit = () => {
  isSubmitting.value = true;
  errors.value = {};

  const formData = new FormData();
  formData.append('judul', form.value.judul);
  formData.append('deskripsi', form.value.deskripsi);

  if (form.value.file) {
    formData.append('file', form.value.file);
  }

  if (form.value.sampul) {
    formData.append('sampul', form.value.sampul);
  }

  router.post('/panduan', formData, {
    onSuccess: () => {
      form.value.judul = '';
      form.value.deskripsi = '';
      form.value.file = null;
      form.value.sampul = null;
      hideForm();
      isSubmitting.value = false;

      // Reload halaman setelah berhasil dengan preserveState: false
      router.visit('/panduan', {
        preserveState: false,
        preserveScroll: true
      });
    },
    onError: (err) => {
      console.error('Form submission errors:', err);
      errors.value = err;
      isSubmitting.value = false;
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const deletePanduan = (id: number) => {
  if (confirm('Yakin ingin menghapus panduan ini?')) {
    router.delete(`/panduan/${id}`, {
      onSuccess: () => {
        // Reload halaman setelah berhasil menghapus
        router.visit('/panduan', {
          preserveState: false
        });
      }
    });
  }
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
</script>

<template>
  <Head title="Panduan" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-end items-center mb-6">
      <Button @click="showForm" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded flex items-center space-x-2">
        <Plus class="w-4 h-4" />
        <span>Tambahkan Panduan</span>
      </Button>
    </div>

    <!-- Form Tambah Panduan -->
    <div v-if="isFormVisible" class="mb-8 bg-white p-6 rounded shadow">
      <h2 class="text-lg font-bold mb-4">Tambah Panduan Baru</h2>
      <form @submit.prevent="handleSubmit" class="grid grid-cols-1 gap-4">
        <div>
          <label for="judul" class="block text-sm font-medium text-gray-700">Judul Panduan*</label>
          <input id="judul" v-model="form.judul" type="text" placeholder="Judul Panduan" class="mt-1 block w-full border rounded p-2" required />
          <div v-if="errors.judul" class="text-red-500 text-sm mt-1">{{ errors.judul }}</div>
        </div>

        <div>
          <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi*</label>
          <textarea id="deskripsi" v-model="form.deskripsi" placeholder="Deskripsi" class="mt-1 block w-full border rounded p-2" required></textarea>
          <div v-if="errors.deskripsi" class="text-red-500 text-sm mt-1">{{ errors.deskripsi }}</div>
        </div>

        <div>
          <label for="file" class="block text-sm font-medium text-gray-700">File Panduan (PDF, DOC, DOCX - max 2MB)</label>
          <input id="file" type="file" @change="handleFileChange" class="mt-1 block w-full" accept=".pdf,.doc,.docx" />
          <div class="text-xs text-gray-500 mt-1">Opsional: Anda dapat mengunggah file panduan jika ada</div>
          <div v-if="errors.file" class="text-red-500 text-sm mt-1">{{ errors.file }}</div>
        </div>

        <div>
          <label for="sampul" class="block text-sm font-medium text-gray-700">Sampul Panduan (JPG, JPEG, PNG - max 2MB)</label>
          <input id="sampul" type="file" @change="handleSampulChange" class="mt-1 block w-full" accept="image/*" />
          <div class="text-xs text-gray-500 mt-1">Opsional: Anda dapat mengunggah gambar sampul jika ada</div>
          <div v-if="errors.sampul" class="text-red-500 text-sm mt-1">{{ errors.sampul }}</div>
        </div>

        <div v-if="errors.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <p class="font-bold">Terjadi kesalahan:</p>
          <p>{{ errors.error }}</p>
        </div>

        <div class="flex gap-2 mt-2">
          <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded" :disabled="isSubmitting">
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
          </Button>
          <Button type="button" @click="hideForm" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded" :disabled="isSubmitting">Batal</Button>
        </div>
      </form>
    </div>

    <!-- Daftar Panduan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mx-3">
      <div v-for="item in localPanduan" :key="item.id" class="flex flex-col bg-white rounded-lg shadow-lg p-4">
        <img
          :src="item.sampul_url ?? '/images/default-image.png'"
          alt="Panduan Sampul"
          class="w-full h-32 object-contain rounded mb-4"
        />
        <div class="text-base text-gray-700 dark:text-gray-200">
          <p class="font-bold text-gray-800 mb-2">{{ item.judul }}</p>
          <p class="text-sm text-justify leading-relaxed mb-4">{{ item.deskripsi }}</p>
          <div class="flex justify-end gap-2">
            <Button v-if="item.file_url" @click="viewFile(item.file_url)" class="bg-green-500 hover:bg-green-600 text-white text-xs px-4 py-2 rounded font-bold">
              Lihat File
            </Button>
            <Button v-else class="bg-gray-400 text-white text-xs px-4 py-2 rounded font-bold cursor-not-allowed">
              Tidak Ada File
            </Button>
            <Button @click="editPanduan(item.id)" class="bg-yellow-400 hover:bg-yellow-700 text-white text-xs px-4 py-2 rounded font-bold">
              Edit
            </Button>
            <Button @click="deletePanduan(item.id)" class="bg-red-600 hover:bg-red-700 text-white text-xs px-4 py-2 rounded font-bold">
              Hapus
            </Button>
          </div>
        </div>
      </div>

      <div v-if="localPanduan.length === 0" class="col-span-3 text-center py-10 bg-gray-50 rounded-lg">
        <p class="text-gray-500">Belum ada panduan tersedia. Silakan tambahkan panduan baru.</p>
      </div>
    </div>
  </AppLayout>
</template>
