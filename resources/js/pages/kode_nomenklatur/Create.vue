<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Kode Nomenklatur',
    href: '/kodenomenklatur',
  },
  {
    title: 'Tambah Kode Nomenklatur',
    href: '/kode_nomenklatur/Create',
  },
];

const form = useForm({
  nomor_kode: '',
  nomenklatur: '',
  jenis_kode: '',
  urusan: '',
  bidang_urusan: '',
  program: '',
  kegiatan: '',
  subkegiatan: '',
});

function clearLowerFields() {
  form.urusan = '';
  form.bidang_urusan = '';
  form.program = '';
  form.kegiatan = '';
  form.subkegiatan = '';
}

const jenisKodeOptions = [
  { value: 0, label: 'Urusan' },
  { value: 1, label: 'Bidang Urusan' },
  { value: 2, label: 'Program' },
  { value: 3, label: 'Kegiatan' },
  { value: 4, label: 'Subkegiatan' },
];

function submit() {
  form.post(route('kode-nomenklatur.store'), {
    onSuccess: () => form.reset(),
  });
}
</script>

<template>
  <Head title="Tambah Kode Nomenklatur" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Tambah Kode Nomenklatur</h1>

      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <!-- Nomor Kode -->
        <div class="grid gap-2">
          <Label for="nomor_kode">Nama Kode</Label>
          <Input id="nomor_kode" v-model="form.nomor_kode" type="text" required />
          <div v-if="form.errors.nomor_kode" class="text-red-500 text-sm mt-1">{{ form.errors.nomor_kode }}</div>
        </div>

        <!-- Nomenklatur -->
        <div class="grid gap-2">
          <Label for="nomenklatur">Nomenklatur</Label>
          <Input id="nomenklatur" v-model="form.nomenklatur" type="text" required />
          <div v-if="form.errors.nomenklatur" class="text-red-500 text-sm mt-1">{{ form.errors.nomenklatur }}</div>
        </div>

        <!-- Jenis Kode -->
        <div class="grid gap-2">
          <Label for="jenis_kode">Jenis Kode</Label>
          <select id="jenis_kode" v-model="form.jenis_kode" required @change="clearLowerFields()">
            <option value="" disabled selected>Pilih Jenis Nomenklatur</option>
            <option v-for="option in jenisKodeOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
          <div v-if="form.errors.jenis_kode" class="text-red-500 text-sm mt-1">{{ form.errors.jenis_kode }}</div>
        </div>

        <!-- Urusan -->
        <div v-if="form.jenis_kode >= 0" class="grid gap-2">
          <Label for="urusan">Urusan</Label>
          <Input id="urusan" v-model="form.urusan" type="text" required placeholder="Masukkan Urusan" />
          <div v-if="form.errors.urusan" class="text-red-500 text-sm mt-1">{{ form.errors.urusan }}</div>
        </div>

        <!-- Bidang Urusan -->
        <div v-if="form.jenis_kode >= 1" class="grid gap-2">
          <Label for="bidang_urusan">Bidang Urusan</Label>
          <Input id="bidang_urusan" v-model="form.bidang_urusan" type="text" required placeholder="Masukkan Bidang Urusan" />
          <div v-if="form.errors.bidang_urusan" class="text-red-500 text-sm mt-1">{{ form.errors.bidang_urusan }}</div>
        </div>

        <!-- Program -->
        <div v-if="form.jenis_kode >= 2" class="grid gap-2">
          <Label for="program">Program</Label>
          <Input id="program" v-model="form.program" type="text" required placeholder="Masukkan Program" />
          <div v-if="form.errors.program" class="text-red-500 text-sm mt-1">{{ form.errors.program }}</div>
        </div>

        <!-- Kegiatan -->
        <div v-if="form.jenis_kode >= 3" class="grid gap-2">
          <Label for="kegiatan">Kegiatan</Label>
          <Input id="kegiatan" v-model="form.kegiatan" type="text" required placeholder="Masukkan Kegiatan" />
          <div v-if="form.errors.kegiatan" class="text-red-500 text-sm mt-1">{{ form.errors.kegiatan }}</div>
        </div>

        <!-- Subkegiatan -->
        <div v-if="form.jenis_kode >= 4" class="grid gap-2">
          <Label for="subkegiatan">Subkegiatan</Label>
          <Input id="subkegiatan" v-model="form.subkegiatan" type="text" required placeholder="Masukkan Subkegiatan" />
          <div v-if="form.errors.subkegiatan" class="text-red-500 text-sm mt-1">{{ form.errors.subkegiatan }}</div>
        </div>

        <!-- Tombol -->
        <div class="flex gap-4 mt-6">
          <Button type="submit" class="bg-blue-600 hover:bg-blue-700">Simpan</Button>
          <Button type="button" variant="outline" @click="$inertia.visit(route('kode-nomenklatur.index'))">
            Batal
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
