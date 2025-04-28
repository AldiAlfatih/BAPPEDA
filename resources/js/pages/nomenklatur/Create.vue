<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Nomenklatur',
    href: '/nomenklatur',
  },
  {
    title: 'Tambah Nomenklatur',
    href: '/nomenklatur/create',
  },
];

// form binding
const form = useForm({
  nama_kode: '',
  nomenklatur: '',
  urusan: '',
  bidang_urusan: '',
  program: '',
  kegiatan: '',
  subkegiatan: '',
});

// submit ke backend
function submit() {
  form.post(route('nomenklatur.store'), {
    onSuccess: () => form.reset(), // reset form setelah berhasil
  });
}
</script>

<template>
  <Head title="Tambah Nomenklatur" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Tambah Nomenklatur</h1>

      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <div>
          <Label for="nama_kode">Nama Kode</Label>
          <Input id="nama_kode" v-model="form.nama_kode" type="text" required />
          <div v-if="form.errors.nama_kode" class="text-red-500 text-sm mt-1">{{ form.errors.nama_kode }}</div>
        </div>

        <div>
          <Label for="nomenklatur">Nomenklatur</Label>
          <Input id="nomenklatur" v-model="form.nomenklatur" type="text" required />
          <div v-if="form.errors.nomenklatur" class="text-red-500 text-sm mt-1">{{ form.errors.nomenklatur }}</div>
        </div>

        <div>
          <Label for="urusan">Urusan</Label>
          <Input id="urusan" v-model="form.urusan" type="text" required />
          <div v-if="form.errors.urusan" class="text-red-500 text-sm mt-1">{{ form.errors.urusan }}</div>
        </div>

        <div>
          <Label for="bidang_urusan">Bidang Urusan</Label>
          <Input id="bidang_urusan" v-model="form.bidang_urusan" type="text" required />
          <div v-if="form.errors.bidang_urusan" class="text-red-500 text-sm mt-1">{{ form.errors.bidang_urusan }}</div>
        </div>

        <div>
          <Label for="program">Program</Label>
          <Input id="program" v-model="form.program" type="text" required />
          <div v-if="form.errors.program" class="text-red-500 text-sm mt-1">{{ form.errors.program }}</div>
        </div>

        <div>
          <Label for="kegiatan">Kegiatan</Label>
          <Input id="kegiatan" v-model="form.kegiatan" type="text" required />
          <div v-if="form.errors.kegiatan" class="text-red-500 text-sm mt-1">{{ form.errors.kegiatan }}</div>
        </div>

        <div>
          <Label for="subkegiatan">Subkegiatan</Label>
          <Input id="subkegiatan" v-model="form.subkegiatan" type="text" required />
          <div v-if="form.errors.subkegiatan" class="text-red-500 text-sm mt-1">{{ form.errors.subkegiatan }}</div>
        </div>

        <div class="flex gap-4 mt-6">
          <Button type="submit" class="bg-blue-600 hover:bg-blue-700">Simpan</Button>
          <Button type="button" variant="outline" @click="$inertia.visit(route('nomenklatur.index'))">
            Batal
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
