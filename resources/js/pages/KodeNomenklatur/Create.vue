<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { ref, computed } from 'vue';

// Ambil data dari Laravel
const { props } = usePage();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Kode Nomenklatur', href: '/kodenomenklatur' },
  { title: 'Tambah Kode Nomenklatur', href: '/kodenomenklatur/create' }
];

// Jenis Nomenklatur
const jenisNomenklaturOptions = [
  { label: 'Urusan', value: 0 },
  { label: 'Bidang Urusan', value: 1 },
  { label: 'Program', value: 2 },
  { label: 'Kegiatan', value: 3 },
  { label: 'Subkegiatan', value: 4 }
];

// Form state
const jenisNomenklatur = ref<number | null>(null);
const nomorKode = ref('');
const nomenklatur = ref('');
const urusan = ref('');
const bidangUrusan = ref('');
const program = ref('');
const kegiatan = ref('');
const subkegiatan = ref('');

// Options
const urusanOptions = computed(() =>
  props.urusanList.map((item: any) => ({
    label: item.nomenklatur,
    value: item.id
  }))
);

const bidangUrusanOptions = computed(() =>
  props.bidangUrusanList
    .filter((item: any) => item.urusan_id == urusan.value)
    .map((item: any) => ({
      label: item.nomenklatur,
      value: item.id
    }))
);

const programOptions = computed(() =>
  props.programList
    .filter((item: any) => item.bidang_urusan_id == bidangUrusan.value)
    .map((item: any) => ({
      label: item.nomenklatur,
      value: item.id
    }))
);

const kegiatanOptions = computed(() =>
  props.kegiatanList
    .filter((item: any) => item.program_id == program.value)
    .map((item: any) => ({
      label: item.nomenklatur,
      value: item.id
    }))
);

// Reset children saat jenis berubah
function handleJenisChange(value: number) {
  jenisNomenklatur.value = value;
  urusan.value = '';
  bidangUrusan.value = '';
  program.value = '';
  kegiatan.value = '';
  subkegiatan.value = '';
}

// Submit form
function handleSubmit() {
  router.post('/kodenomenklatur', {
    jenis_nomenklatur: jenisNomenklatur.value,
    nomor_kode: nomorKode.value,
    nomenklatur: nomenklatur.value,
    urusan: urusan.value,
    bidang_urusan: bidangUrusan.value,
    program: program.value,
    kegiatan: kegiatan.value,
    subkegiatan: subkegiatan.value
  });
}
</script>

<template>
  <Head title="Tambah Kode Nomenklatur" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 space-y-4">
      <h1 class="text-xl font-semibold mb-4">Tambah Kode Nomenklatur</h1>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Jenis -->
        <div class="flex flex-col">
          <Label for="jenis_nomenklatur">Jenis Nomenklatur</Label>
          <Select
            id="jenis_nomenklatur"
            v-model="jenisNomenklatur"
            :options="jenisNomenklaturOptions"
            @update:modelValue="handleJenisChange"
            required
          />
        </div>

        <!-- Nomor Kode -->
        <div class="flex flex-col">
          <Label for="nomor_kode">Nomor Kode</Label>
          <Input id="nomor_kode" v-model="nomorKode" type="text" required />
        </div>

        <!-- Nomenklatur -->
        <div class="flex flex-col">
          <Label for="nomenklatur">Nomenklatur</Label>
          <Input id="nomenklatur" v-model="nomenklatur" type="text" required />
        </div>

        <!-- Urusan -->
        <div v-if="jenisNomenklatur >= 1" class="flex flex-col">
          <Label for="urusan">Urusan</Label>
          <Select id="urusan" v-model="urusan" :options="urusanOptions" required />
        </div>

        <!-- Bidang Urusan -->
        <div v-if="jenisNomenklatur >= 2" class="flex flex-col">
          <Label for="bidang_urusan">Bidang Urusan</Label>
          <Select id="bidang_urusan" v-model="bidangUrusan" :options="bidangUrusanOptions" required />
        </div>

        <!-- Program -->
        <div v-if="jenisNomenklatur >= 3" class="flex flex-col">
          <Label for="program">Program</Label>
          <Select id="program" v-model="program" :options="programOptions" required />
        </div>

        <!-- Kegiatan -->
        <div v-if="jenisNomenklatur >= 4" class="flex flex-col">
          <Label for="kegiatan">Kegiatan</Label>
          <Select id="kegiatan" v-model="kegiatan" :options="kegiatanOptions" required />
        </div>

        <!-- Tombol -->
        <div>
          <Button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white">Simpan</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
