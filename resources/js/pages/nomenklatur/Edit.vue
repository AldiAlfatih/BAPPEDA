<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Nomenklatur',
    href: '/nomenklatur',
  },
  {
    title: 'Edit Nomenklatur',
    href: '/nomenklatur/edit',
  },
];

const props = defineProps<{
  nomenklatur: {
    id: number,
    nomor_kode: string,
    nomenklatur: string,
    urusan: string,
    bidang_urusan: string,
    program: string,
    kegiatan: string,
    subkegiatan: string,
    sumber?: string,
    target?: string,
  }
}>();

// Form fields di-bind ke props
const form = ref({
  nomor_kode: props.nomenklatur.nomor_kode || '',
  nomenklatur: props.nomenklatur.nomenklatur || '',
  urusan: props.nomenklatur.urusan || '',
  bidang_urusan: props.nomenklatur.bidang_urusan || '',
  program: props.nomenklatur.program || '',
  kegiatan: props.nomenklatur.kegiatan || '',
  subkegiatan: props.nomenklatur.subkegiatan || '',
  sumber: props.nomenklatur.sumber || '',
  target: props.nomenklatur.target || '',
});


// Fungsi update data
const isSubmitting = ref(false);

function updateNomenklatur() {
  isSubmitting.value = true;
  router.put(`/nomenklatur/${props.nomenklatur.id}`, form.value, {
    onFinish: () => isSubmitting.value = false,
    onSuccess: () => alert('Berhasil update data!'),
    onError: (errors) => {
      console.error(errors);
      alert('Terjadi kesalahan saat update!');
    }
  });
}
</script>

<template>
  <Head title="Edit Nomenklatur" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Edit Nomenklatur</h1>

      <form @submit.prevent="updateNomenklatur">
        <div class="flex flex-col gap-4">

          <div>
            <label>Kode</label>
            <input v-model="form.nomor_kode" class="border rounded w-full" />
          </div>

          <div>
            <label>Nomenklatur</label>
            <input v-model="form.nomenklatur" class="border rounded w-full" />
          </div>

          <div>
            <label>Urusan</label>
            <input v-model="form.urusan" class="border rounded w-full" />
          </div>

          <div>
            <label>Bidang Urusan</label>
            <input v-model="form.bidang_urusan" class="border rounded w-full" />
          </div>

          <div>
            <label>Program</label>
            <input v-model="form.program" class="border rounded w-full" />
          </div>

          <div>
            <label>Kegiatan</label>
            <input v-model="form.kegiatan" class="border rounded w-full" />
          </div>

          <div>
            <label>Subkegiatan</label>
            <input v-model="form.subkegiatan" class="border rounded w-full" />
          </div>

          <button type="submit" :disabled="isSubmitting" class="bg-blue-500 text-white rounded px-4 py-2">
            {{ isSubmitting ? 'Updating...' : 'Update' }}
            </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
