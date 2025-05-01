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
  { title: 'Tambah Kode Nomenklatur', href: '/kode_nomenklatur/Create' },
];

// form binding
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

// Reset tergantung jenis
function clearLowerFields() {
  form.bidang_urusan = '';
  form.program = '';
  form.kegiatan = '';
  form.subkegiatan = '';
  form.nomor_kode = '';
  form.nomenklatur = '';
}

// Pilihan jenis kode
const jenisKodeOptions = [
  { value: 0, label: 'Urusan' },
  { value: 1, label: 'Bidang Urusan' },
  { value: 2, label: 'Program' },
  { value: 3, label: 'Kegiatan' },
  { value: 4, label: 'Subkegiatan' },
];

// Dummy dropdown data poin 1–5
const options = [
  { value: '1', label: 'Poin 1' },
  { value: '2', label: 'Poin 2' },
  { value: '3', label: 'Poin 3' },
  { value: '4', label: 'Poin 4' },
  { value: '5', label: 'Poin 5' },
];

// Update nomor_kode & nomenklatur secara otomatis
function updateNamaKode() {
  let namaKode = '';
  if (form.jenis_kode >= 0) namaKode += form.urusan || '';
  if (form.jenis_kode >= 1) namaKode += form.bidang_urusan ? '/' + form.bidang_urusan : '';
  if (form.jenis_kode >= 2) namaKode += form.program ? '/' + form.program : '';
  if (form.jenis_kode >= 3) namaKode += form.kegiatan ? '/' + form.kegiatan : '';
  if (form.jenis_kode >= 4) namaKode += form.subkegiatan ? '/' + form.subkegiatan : '';
  form.nomor_kode = namaKode;

  // Update nomenklatur sesuai label jenis kode
  const jenis = jenisKodeOptions.find(j => j.value == form.jenis_kode);
  form.nomenklatur = jenis ? jenis.label : '';
}

function showField(field) {
  const jenis = form.jenis_kode;

  if (jenis === 0 || jenis === 4) return true; // Tampilkan semua field
  if (jenis === 1 && ['urusan', 'bidang_urusan'].includes(field)) return true;
  if (jenis === 2 && ['urusan', 'bidang_urusan', 'program'].includes(field)) return true;
  if (jenis === 3 && ['urusan', 'bidang_urusan', 'program', 'kegiatan'].includes(field)) return true;

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

// Submit ke backend
function submit() {
  updateNamaKode(); // jaga-jaga
  form.post('/kodenomenklatur', {
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
          <select
            id="jenis_kode"
            v-model="form.jenis_kode"
            required
            @change="clearLowerFields"
          >
            <option value="" disabled selected>Pilih Jenis Nomenklatur</option>
            <option v-for="option in jenisKodeOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Urusan -->
        <div v-if="showField('urusan')">
          <Label for="urusan">Urusan</Label>
          <select id="urusan" v-model="form.urusan" required>
            <option value="" disabled selected>Pilih Urusan</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>

        <!-- Bidang Urusan -->
        <div v-if="showField('bidang_urusan')">
          <Label for="bidang_urusan">Bidang Urusan</Label>
          <select id="bidang_urusan" v-model="form.bidang_urusan" required>
            <option value="" disabled selected>Pilih Bidang Urusan</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>

        <!-- Program -->
        <div v-if="showField('program')">
          <Label for="program">Program</Label>
          <select id="program" v-model="form.program" required>
            <option value="" disabled selected>Pilih Program</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>

        <!-- Kegiatan -->
        <div v-if="showField('kegiatan')">
          <Label for="kegiatan">Kegiatan</Label>
          <select id="kegiatan" v-model="form.kegiatan" required>
            <option value="" disabled selected>Pilih Kegiatan</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>

        <!-- Subkegiatan -->
        <div v-if="showField('subkegiatan')">
          <Label for="subkegiatan">Subkegiatan</Label>
          <select id="subkegiatan" v-model="form.subkegiatan" required>
            <option value="" disabled selected>Pilih Subkegiatan</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>

        <!-- Tombol -->
        <div class="flex gap-4 mt-6">
          <Button type="submit" class="bg-blue-600 hover:bg-blue-700">Simpan</Button>
          <Button type="button" variant="outline" @click="$inertia.visit(route('kodenomenklatur.index'))">
            Batal
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

