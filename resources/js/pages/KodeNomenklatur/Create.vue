<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Kode Nomenklatur', href: '/kodenomenklatur' },
  { title: 'Tambah Kode Nomenklatur', href: '/kodenomenklatur/create' },
];

// Form binding
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

// Pilihan jenis kode
const jenisKodeOptions = [
  { value: 0, label: 'Urusan' },
  { value: 1, label: 'Bidang Urusan' },
  { value: 2, label: 'Program' },
  { value: 3, label: 'Kegiatan' },
  { value: 4, label: 'Subkegiatan' },
];

// Fungsi untuk memperbarui nomor kode dan nomenklatur
function updateNamaKode() {
  const parts = [];
  const jenis = form.jenis_kode;
  
  if (jenis === 0) {
    // Urusan: kode dimulai dari 1 dan naik per urusan
    parts.push(`1`);
  }
  if (jenis === 1) {
    // Bidang Urusan: kode dimulai dari 1/01 dan naik per bidang urusan
    parts.push(`${form.urusan}/01`);
  }
  if (jenis === 2) {
    // Program: kode melanjutkan dari bidang urusan
    parts.push(`${form.urusan}/01/01`);
  }
  if (jenis === 3) {
    // Kegiatan: kode melanjutkan dari program
    parts.push(`${form.urusan}/01/01/2.01`);
  }
  if (jenis === 4) {
    // Subkegiatan: kode melanjutkan dari kegiatan
    let subkegiatanCount = 1;  // Menghitung urutan subkegiatan
    parts.push(`${form.urusan}/01/01/2.01/${String(subkegiatanCount).padStart(4, '0')}`);
  }
  
  form.nomor_kode = parts.join('/');
  form.nomenklatur = jenisKodeOptions.find(j => j.value === form.jenis_kode)?.label || '';
}

// Menampilkan input yang sesuai berdasarkan jenis kode yang dipilih
function showField(field: string) {
  const jenis = form.jenis_kode;

  if (jenis === 0 && ['urusan'].includes(field)) return true; // Tampilkan urusan
  if (jenis === 1 && ['urusan', 'bidang_urusan'].includes(field)) return true;
  if (jenis === 2 && ['urusan', 'bidang_urusan', 'program'].includes(field)) return true;
  if (jenis === 3 && ['urusan', 'bidang_urusan', 'program', 'kegiatan'].includes(field)) return true;
  if (jenis === 4) return true; // Tampilkan semua field
  return false;
}

// Reaktif terhadap perubahan data input
watch(
  () => [
    form.jenis_kode,
    form.urusan,
    form.bidang_urusan,
    form.program,
    form.kegiatan,
    form.subkegiatan,
  ],
  updateNamaKode
);

// Fungsi untuk mengirimkan data ke backend
function submit() {
  updateNamaKode(); // Pastikan nama kode terupdate
  console.log(form); // Cek data yang dikirim ke backend
  form.post('/kodenomenklatur', {
    onSuccess: () => form.reset(),
    onError: (errors) => console.log(errors) // Menampilkan error jika ada
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
          <Label for="nomor_kode">Nomor Kode</Label>
          <Input id="nomor_kode" v-model="form.nomor_kode" type="text" disabled />
        </div>

        <!-- Nomenklatur -->
        <div class="grid gap-2">
          <Label for="nomenklatur">Nomenklatur</Label>
          <Input id="nomenklatur" v-model="form.nomenklatur" type="text" disabled />
        </div>

        <!-- Jenis Kode -->
        <div class="grid gap-2">
          <Label for="jenis_kode">Jenis Kode</Label>
          <select id="jenis_kode" v-model="form.jenis_kode" required @change="clearLowerFields">
            <option value="" disabled selected>Pilih Jenis Nomenklatur</option>
            <option v-for="option in jenisKodeOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Urusan -->
        <div v-if="showField('urusan')">
          <Label for="urusan">Urusan</Label>
          <Input id="urusan" v-model="form.urusan" type="text" required />
        </div>

        <!-- Bidang Urusan -->
        <div v-if="showField('bidang_urusan')">
          <Label for="bidang_urusan">Bidang Urusan</Label>
          <Input id="bidang_urusan" v-model="form.bidang_urusan" type="text" required />
        </div>

        <!-- Program -->
        <div v-if="showField('program')">
          <Label for="program">Program</Label>
          <Input id="program" v-model="form.program" type="text" required />
        </div>

        <!-- Kegiatan -->
        <div v-if="showField('kegiatan')">
          <Label for="kegiatan">Kegiatan</Label>
          <Input id="kegiatan" v-model="form.kegiatan" type="text" required />
        </div>

        <!-- Subkegiatan -->
        <div v-if="showField('subkegiatan')">
          <Label for="subkegiatan">Subkegiatan</Label>
          <Input id="subkegiatan" v-model="form.subkegiatan" type="text" required />
        </div>

        <Button type="submit" class="mt-4">Simpan</Button>
      </form>
    </div>
  </AppLayout>
</template>
