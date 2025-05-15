<script setup lang="ts">
import { Head, useForm} from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { type BreadcrumbItem } from '@/types';

// Mendapatkan data panduan yang dikirim dari Inertia (data panduan yang akan diedit)
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Panduan', href: '/panduan' },
  { title: 'Edit', href: '/panduan/edit' },
]

// Mendapatkan data panduan yang dikirim dari Inertia (data panduan yang akan diedit)
const props = defineProps<{
  panduan: {
    id: number;
    judul: string;
    deskripsi: string;
    file: string | null;
  };
}>();

// Form data untuk mengedit panduan
const form = useForm({
  judul: props.panduan.judul,
  deskripsi: props.panduan.deskripsi,
  file: null as File | null,
});

const handleFileChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    form.file = input.files[0];  // Menyimpan file yang dipilih ke form
  }
};

const handleUpdate = async () => {
  const formData = new FormData();
  formData.append('judul', form.judul);
  formData.append('deskripsi', form.deskripsi);

  // Hanya menambahkan file jika ada file yang dipilih
  if (form.file) {
    formData.append('file', form.file);
  }

  try {
    form.put(`/panduan/${props.panduan.id}`, {
          method: 'put',
          ...Object.fromEntries(formData),
          onSuccess: () => {
              Inertia.visit('/panduan'); // Kembali ke halaman panduan setelah sukses
              alert('Panduan berhasil diperbarui!');
          },
          onError: (error: any) => {
              alert('Terjadi kesalahan saat menyimpan panduan');
              console.error('Error:', error);
          },
      });
  } catch (error) {
    console.error('Terjadi kesalahan:', error);
  }
};

// Membatalkan dan kembali ke halaman sebelumnya
const cancelEdit = () => {
  Inertia.visit('/panduan');  // Kembali ke halaman panduan tanpa perubahan
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit Panduan" />
    <div class="p-6 bg-white rounded-lg shadow-md">
      <h2 class="text-lg font-bold mb-4">Edit Panduan</h2>
      <!-- Formulir untuk mengedit panduan -->
      <form @submit.prevent="handleUpdate" enctype="multipart/form-data">
        <div class="mb-4">
          <label class="block text-sm font-semibold">Judul:</label>
          <input
            type="text"
            class="w-full border border-gray-300 rounded px-4 py-2"
            v-model="form.judul"
            required
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-semibold">Deskripsi:</label>
          <textarea
            class="w-full border border-gray-300 rounded px-4 py-2"
            v-model="form.deskripsi"
            required
          ></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-semibold">Masukkan Panduan (File):</label>
          <input
            type="file"
            class="w-full border border-gray-300 rounded px-4 py-2"
            @change="handleFileChange"
          />
        </div>

        <div class="flex justify-end gap-2">
          <button
            type="button"
            @click="cancelEdit"
            class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-1 rounded"
          >
            Batal
          </button>
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
