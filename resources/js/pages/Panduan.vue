<script setup lang="ts">
import { ref, defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import { Inertia } from '@inertiajs/inertia';

// Breadcrumbs
const breadcrumbs = [{ title: 'Panduan', href: '/panduan' }];

// Props dari controller
const { panduan } = defineProps({
  panduan: {
    type: Array,
    default: () => []
  }
});

const localPanduan = ref(panduan);
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

const handleSubmit = async () => {
  const formData = new FormData();
  formData.append('judul', form.value.judul);
  formData.append('deskripsi', form.value.deskripsi);
  if (form.value.file) {
    formData.append('file', form.value.file);
  }
  if (form.value.sampul) {
    formData.append('sampul', form.value.sampul);
  }

  try {
    Inertia.post('/panduan', formData, {
      onSuccess: () => {
        Inertia.get('/panduan', {
          onSuccess: (page) => {
            localPanduan.value = page.props.panduan;
          }
        });

        form.value.judul = '';
        form.value.deskripsi = '';
        form.value.file = null;
        form.value.sampul = null;

        hideForm();
      },
    });
  } catch (error) {
    console.error('Gagal menyimpan panduan:', error);
  }
};

const deletePanduan = (id: number) => {
  if (confirm('Yakin ingin menghapus panduan ini?')) {
    Inertia.delete(`/panduan/${id}`, {
      onSuccess: () => {
        Inertia.reload({ only: ['panduan'] });
      },
    });
  }
};

const editPanduan = (id: number) => {
  Inertia.visit(`/panduan/${id}/edit`);
};

const viewFile = (fileUrl: string) => {
  if (fileUrl) {
    window.open(fileUrl, '_blank');
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
        <input v-model="form.judul" type="text" placeholder="Judul Panduan" class="border rounded p-2" required />
        <textarea v-model="form.deskripsi" placeholder="Deskripsi" class="border rounded p-2" required></textarea>
        <p class="font-bold">Masukkan File Panduan</p>
        <input type="file" @change="handleFileChange" />
        <p class="font-bold">Masukkan Sampul Panduan</p>
        <input type="file" @change="handleSampulChange" accept="image/*" />
        <div class="flex gap-2 mt-2">
          <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">Simpan</Button>
          <Button type="button" @click="hideForm" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded">Batal</Button>
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
          <p @click="viewFile(item.file_url)" class="cursor-pointer font-bold text-gray-800 hover:underline">
            {{ item.judul }}
          </p>

          <p class="text-sm text-justify leading-relaxed mb-4">{{ item.deskripsi }}</p>
          <div class="flex justify-end gap-2">
            <Button @click="viewFile(item.file_url)" class="bg-green-500 hover:bg-green-600 text-white text-xs px-4 py-2 rounded font-bold">
              View
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
    </div>
  </AppLayout>
</template>
