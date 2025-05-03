<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
  {
    title: 'Tambah',
    href: '/bantuan/create',
  },
];

// Form data untuk input
const form = useForm({
  judul: '',
  deskripsi: '',
  status: 0,
})

function submit() {
  form.post('/bantuan', {
    onSuccess: () => {
      console.log('Data berhasil disimpan');
    },
    onError: (errors) => {
      console.log('Terjadi kesalahan:', errors);
    }
  })  // Mengirim data ke backend
}
</script>

<template>
  <Head title="Tambah Bantuan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <h1 class="text-xl font-bold mb-4">Tambah Bantuan</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Judul -->
        <div>
          <label>Judul</label>
          <input v-model="form.judul" type="text" class="border rounded p-2 w-full" required />
        </div>
        
        <!-- Deskripsi -->
        <div>
          <label>Deskripsi</label>
          <textarea v-model="form.deskripsi" class="border rounded p-2 w-full" required></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </form>
    </div>
  </AppLayout>
</template>