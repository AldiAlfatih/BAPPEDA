<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
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

const form = useForm({
  judul: '',
  tgl_dibuat: '',
})

function submit() {
  form.post('/bantuan')
}
</script>

<template>
  <Head title="Tambah" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <h1 class="text-xl font-bold mb-4">Tambah Bantuan</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label>Judul</label>
          <input v-model="form.judul" type="text" class="border rounded p-2 w-full" />
        </div>
        <div>
          <label>Tanggal Dibuat</label>
          <input v-model="form.tgl_dibuat" type="date" class="border rounded p-2 w-full" />
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </form>
    </div>
  </AppLayout>
</template>
