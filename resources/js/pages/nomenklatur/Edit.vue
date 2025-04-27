<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

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
  // nomor_kode: props.nomenklatur.nomor_kode,
  nomenklatur: props.nomenklatur.nomenklatur,
  urusan: props.nomenklatur.urusan,
  bidang_urusan: props.nomenklatur.bidang_urusan,
  program: props.nomenklatur.program,
  kegiatan: props.nomenklatur.kegiatan,
  subkegiatan: props.nomenklatur.subkegiatan,
});


// Fungsi update data
function updateNomenklatur() {
  router.put(`/nomenklatur/${props.nomenklatur.id}`, form.value, {
    onSuccess: () => {
      router.visit('/nomenklatur');
    },
    onError: (errors) => {
      console.error('Error updating Nomenklatur:', errors);
      // Tambahkan logika tambahan kalau mau
    },
  });
}


</script>

<template>
  <Head title="Edit Nomenklatur" />

  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Edit Nomenklatur</h1>

      <form @submit.prevent="updateNomenklatur">
        <div class="flex flex-col gap-4">

          <!-- <div>
            <label>Kode</label>
            <input v-model="form.nomor_kode" class="border rounded w-full" />
          </div> -->

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

          <button type="submit" class="bg-blue-500 text-white rounded px-4 py-2">Update</button>

        </div>
      </form>
    </div>
  </AppLayout>
</template>
