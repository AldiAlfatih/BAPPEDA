<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
  {
    title: 'Tambah Bantuan',
    href: '/bantuan/create',
  },
];

const props = defineProps<{
  statusBantuanOptions: { id: number; nama: string }[];
}>();

const form = useForm({
  jenis_bantuan: '',
  penerima: '',
  tanggal_disalurkan: '',
  status_bantuan_id: '',
});

function submit() {
  form.post('/bantuan', {
    onSuccess: () => {
      form.reset();
    },
  });
}
</script>

<template>
    <Head title="Tambah Bantuan" />
  
    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Tambah Data Bantuan</h1>
  
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block mb-1 font-medium">Jenis Bantuan</label>
            <input v-model="form.jenis_bantuan" type="text" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.jenis_bantuan" class="text-red-600 text-sm">{{ form.errors.jenis_bantuan }}</div>
          </div>
  
          <div>
            <label class="block mb-1 font-medium">Penerima</label>
            <input v-model="form.penerima" type="text" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.penerima" class="text-red-600 text-sm">{{ form.errors.penerima }}</div>
          </div>
  
          <div>
            <label class="block mb-1 font-medium">Tanggal Disalurkan</label>
            <input v-model="form.tanggal_disalurkan" type="date" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.tanggal_disalurkan" class="text-red-600 text-sm">{{ form.errors.tanggal_disalurkan }}</div>
          </div>
  
          <div>
            <label class="block mb-1 font-medium">Status Bantuan</label>
            <select v-model="form.status_bantuan_id" class="w-full border rounded px-3 py-2">
              <option value="">-- Pilih Status --</option>
              <option v-for="status in props.statusBantuanOptions" :key="status.id" :value="status.id">
                {{ status.nama }}
              </option>
            </select>
            <div v-if="form.errors.status_bantuan_id" class="text-red-600 text-sm">{{ form.errors.status_bantuan_id }}</div>
          </div>
  
          <div class="flex justify-end">
            <Button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
              Simpan
            </Button>
          </div>
        </form>
      </div>
    </AppLayout>
</template>