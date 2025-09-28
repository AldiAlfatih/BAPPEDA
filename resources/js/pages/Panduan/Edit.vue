<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';

interface PanduanItem {
  id: number;
  judul: string;
  deskripsi: string;
  file?: string | null; // storage path
  sampul?: string | null; // storage path
}

const props = defineProps<{ panduan: PanduanItem }>();

const breadcrumbs = [
  { title: 'Panduan', href: '/panduan' },
  { title: 'Edit Panduan', href: '' },
];

const form = ref({
  judul: props.panduan?.judul ?? '',
  deskripsi: props.panduan?.deskripsi ?? '',
  file: null as File | null,
  sampul: null as File | null,
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

function handleFileChange(e: Event) {
  const input = e.target as HTMLInputElement;
  form.value.file = input.files?.[0] ?? null;
}

function handleSampulChange(e: Event) {
  const input = e.target as HTMLInputElement;
  form.value.sampul = input.files?.[0] ?? null;
}

function handleImageError(event: Event) {
  const img = event.target as HTMLImageElement;
  img.src = '/images/default-image.svg';
  img.classList.add('opacity-75', 'border-2', 'border-dashed', 'border-gray-300');
  img.title = 'Gambar sampul tidak tersedia - menggunakan gambar default';
}

function submit() {
  isSubmitting.value = true;
  errors.value = {};

  const fd = new FormData();
  fd.append('_method', 'put');
  fd.append('judul', form.value.judul);
  fd.append('deskripsi', form.value.deskripsi);
  if (form.value.file) fd.append('file', form.value.file);
  if (form.value.sampul) fd.append('sampul', form.value.sampul);

  router.post(`/panduan/${props.panduan.id}` , fd, {
    onError: (err) => {
      errors.value = (err as any) || {};
      isSubmitting.value = false;
    },
    onSuccess: () => {
      isSubmitting.value = false;
      router.visit('/panduan', { preserveState: false });
    },
    onFinish: () => { isSubmitting.value = false; },
  });
}

function cancel() {
  router.visit('/panduan');
}
</script>

<template>
  <Head title="Edit Panduan" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-3xl rounded bg-white p-6 shadow">
      <h1 class="mb-6 text-2xl font-bold text-gray-700">Edit Panduan</h1>

      <form @submit.prevent="submit" class="grid grid-cols-1 gap-4">
        <div>
          <label for="judul" class="block text-sm font-medium text-gray-600">Judul</label>
          <input id="judul" v-model="form.judul" type="text" class="mt-1 block w-full rounded border p-2" required />
          <div v-if="errors.judul" class="mt-1 text-sm text-red-500">{{ errors.judul }}</div>
        </div>

        <div>
          <label for="deskripsi" class="block text-sm font-medium text-gray-600">Deskripsi</label>
          <textarea id="deskripsi" v-model="form.deskripsi" class="mt-1 block w-full rounded border p-2" rows="5" required />
          <div v-if="errors.deskripsi" class="mt-1 text-sm text-red-500">{{ errors.deskripsi }}</div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="file" class="mb-1 block text-sm font-medium text-gray-700">File Panduan (PDF/DOC/DOCX)</label>
            <input id="file" type="file" @change="handleFileChange" accept=".pdf,.doc,.docx" class="mt-1 block w-full rounded border p-2" />
            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah file.</p>
            <div v-if="errors.file" class="mt-1 text-sm text-red-500">{{ errors.file }}</div>
          </div>
          <div>
            <label for="sampul" class="mb-1 block text-sm font-medium text-gray-700">Sampul (JPG/PNG)</label>
            <input id="sampul" type="file" @change="handleSampulChange" accept="image/*" class="mt-1 block w-full rounded border p-2" />
            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah sampul.</p>
            <div v-if="errors.sampul" class="mt-1 text-sm text-red-500">{{ errors.sampul }}</div>
          </div>
        </div>

        <div class="mt-2">
          <p class="mb-1 text-sm font-medium text-gray-600">Pratinjau Sampul Saat Ini</p>
          <img
            :src="props.panduan.sampul ? `/storage/${props.panduan.sampul}` : '/images/default-image.svg'"
            :alt="`Sampul ${props.panduan.judul}`"
            class="h-40 w-full rounded object-contain border"
            @error="handleImageError"
          />
        </div>

        <div v-if="errors.error" class="rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
          <p class="font-bold">Terjadi kesalahan:</p>
          <p>{{ errors.error }}</p>
        </div>

        <div class="mt-4 flex gap-2">
          <Button type="button" variant="outline" @click="cancel">Batal</Button>
          <Button type="submit" :disabled="isSubmitting" class="bg-green-800 text-white hover:bg-green-700">
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
