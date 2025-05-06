<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref, computed, watch } from 'vue';

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

const jenisNomenklatur = ref<number | null>(null);
const nomorKode = ref('');
const nomenklatur = ref('');
const urusan = ref<number | null>(null);
const bidangUrusan = ref<number | null>(null);
const program = ref<number | null>(null);
const kegiatan = ref<number | null>(null);
const subkegiatan = ref<number | null>(null);

const urusanOptions = computed(() =>
  props.urusanList.map((item: any) => ({
    label: `${item.nomor_kode} - ${item.nomenklatur}`,
    value: item.id
  }))
);

const bidangUrusanOptions = computed(() => {
  if (!urusan.value) return [];
  
  return props.bidangUrusanList
    .filter((item: any) => item.urusan_id == urusan.value)
    .map((item: any) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

const programOptions = computed(() => {
  if (!bidangUrusan.value) return [];
  
  return props.programList
    .filter((item: any) => item.bidang_urusan_id == bidangUrusan.value)
    .map((item: any) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

const kegiatanOptions = computed(() => {
  if (!program.value) return [];
  
  return props.kegiatanList
    .filter((item: any) => item.program_id == program.value)
    .map((item: any) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

const subkegiatanOptions = computed(() => {
  if (!kegiatan.value) return [];
  
  return props.subkegiatanList
    .filter((item: any) => item.kegiatan_id == kegiatan.value)
    .map((item: any) => ({
      label: `${item.nomor_kode} - ${item.nomenklatur}`,
      value: item.id
    }));
});

function handleJenisChange(value: number) {
  jenisNomenklatur.value = value;
  
  urusan.value = null;
  bidangUrusan.value = null;
  program.value = null;
  kegiatan.value = null;
  subkegiatan.value = null;
  
  // Generate nomor kode untuk Urusan
  if (value === 0) {
    const nextUrusanNumber = props.urusanList.length + 1;
    nomorKode.value = `${nextUrusanNumber}`;
  } else {
    nomorKode.value = '';
  }
}

// Watch perubahan pada urusan untuk update nomor kode bidang urusan
watch(urusan, (newValue) => {
  if (jenisNomenklatur.value === 1 && newValue) {
    const selectedUrusan = props.urusanList.find((item: any) => item.id === newValue);
    if (selectedUrusan) {
      const filteredBidangUrusan = props.bidangUrusanList.filter((item: any) => item.urusan_id === newValue);
      const nextNumber = filteredBidangUrusan.length + 1;
      nomorKode.value = `${selectedUrusan.nomor_kode}.${String(nextNumber).padStart(2, '0')}`;
    }
  }
});

// Watch perubahan pada bidang urusan untuk update nomor kode program
watch(bidangUrusan, (newValue) => {
  if (jenisNomenklatur.value === 2 && newValue) {
    const selectedBidangUrusan = props.bidangUrusanList.find((item: any) => item.id === newValue);
    if (selectedBidangUrusan) {
      const filteredProgram = props.programList.filter((item: any) => item.bidang_urusan_id === newValue);
      const nextNumber = filteredProgram.length + 1;
      nomorKode.value = `${selectedBidangUrusan.nomor_kode}.${String(nextNumber).padStart(3, '0')}`;
    }
  }
});

// Watch perubahan pada program untuk update nomor kode kegiatan
watch(program, (newValue) => {
  if (jenisNomenklatur.value === 3 && newValue) {
    const selectedProgram = props.programList.find((item: any) => item.id === newValue);
    if (selectedProgram) {
      const filteredKegiatan = props.kegiatanList.filter((item: any) => item.program_id === newValue);
      const nextNumber = filteredKegiatan.length + 1;
      nomorKode.value = `${selectedProgram.nomor_kode}.${String(nextNumber).padStart(4, '0')}`;
    }
  }
});

// Watch perubahan pada kegiatan untuk update nomor kode subkegiatan
watch(kegiatan, (newValue) => {
  if (jenisNomenklatur.value === 4 && newValue) {
    const selectedKegiatan = props.kegiatanList.find((item: any) => item.id === newValue);
    if (selectedKegiatan) {
      const filteredSubkegiatan = props.subkegiatanList.filter((item: any) => item.kegiatan_id === newValue);
      const nextNumber = filteredSubkegiatan.length + 1;
      nomorKode.value = `${selectedKegiatan.nomor_kode}.${String(nextNumber).padStart(5, '0')}`;
    }
  }
});

function handleSubmit() {
  const data = {
    jenis_nomenklatur: jenisNomenklatur.value,
    nomor_kode: nomorKode.value,
    nomenklatur: nomenklatur.value,
    urusan: urusan.value,
    bidang_urusan: bidangUrusan.value,
    program: program.value,
    kegiatan: kegiatan.value,
    subkegiatan: subkegiatan.value,
  };

  // Sesuaikan data berdasarkan jenis_nomenklatur
  if (jenisNomenklatur.value === 0) {
    // Urusan hanya mengirim urusan
    data.id_urusan = urusan.value;
    delete data.bidang_urusan;
    delete data.program;
    delete data.kegiatan;
    delete data.subkegiatan;
  } else if (jenisNomenklatur.value === 1) {
    // Bidang Urusan hanya mengirim urusan dan bidang_urusan
    data.id_urusan = urusan.value;
    data.id_bidang_urusan = bidangUrusan.value;
    delete data.program;
    delete data.kegiatan;
    delete data.subkegiatan;
  } else if (jenisNomenklatur.value === 2) {
    // Program hanya mengirim program
    data.id_bidang_urusan = bidangUrusan.value;
    data.id_program = program.value;
    delete data.kegiatan;
    delete data.subkegiatan;
  } else if (jenisNomenklatur.value === 3) {
    // Kegiatan hanya mengirim kegiatan
    data.id_program = program.value;
    data.id_kegiatan = kegiatan.value;
    delete data.subkegiatan;
  } else if (jenisNomenklatur.value === 4) {
    // Subkegiatan hanya mengirim subkegiatan
    data.id_kegiatan = kegiatan.value;
    data.id_sub_kegiatan = subkegiatan.value;
  }

  // Kirim data ke server
  router.post('/kodenomenklatur', data);
}

</script>

<template>
  <Head title="Tambah Kode Nomenklatur" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 space-y-4">
      <h1 class="text-xl font-semibold mb-4">Tambah Kode Nomenklatur</h1>
      <form @submit.prevent="handleSubmit" class="space-y-4">

        <!-- Nomor Kode -->
        <div class="flex flex-col">
          <Label for="nomor_kode">Nomor Kode</Label>
          <Input id="nomor_kode" v-model="nomorKode" type="text"/>
        </div>

        <!-- Jenis Nomenklatur -->
        <div class="flex flex-col">
          <Label for="jenis_nomenklatur">Jenis Nomenklatur</Label>
          <select
            id="jenis_nomenklatur"
            v-model="jenisNomenklatur"
            @change="handleJenisChange(jenisNomenklatur)"
            class="border rounded px-3 py-2"
            required
          >
            <option value="" disabled>Pilih jenis...</option>
            <option
              v-for="option in jenisNomenklaturOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Nomor Kode -->
        <div v-if="jenisNomenklatur >= 1" class="flex flex-col">
          <Label for="urusan">Urusan</Label>
          <select
            id="urusan"
            v-model="urusan"
            class="border rounded px-3 py-2"
            required
          >
            <option value="" disabled selected>Pilih Urusan</option>
            <option
              v-for="option in urusanOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Bidang Urusan -->
        <div v-if="jenisNomenklatur >= 2" class="flex flex-col">
          <Label for="bidang_urusan">Bidang Urusan</Label>
          <select
            id="bidang_urusan"
            v-model="bidangUrusan"
            class="border rounded px-3 py-2"
            required
          >
            <option value="" disabled selected>Pilih Bidang Urusan</option>
            <option
              v-for="option in bidangUrusanOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Program -->
        <div v-if="jenisNomenklatur >= 3" class="flex flex-col">
          <Label for="program">Program</Label>
          <select
            id="program"
            v-model="program"
            class="border rounded px-3 py-2"
            required
          >
            <option value="" disabled selected>Pilih Program</option>
            <option
              v-for="option in programOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Kegiatan -->
        <div v-if="jenisNomenklatur >= 4" class="flex flex-col">
          <Label for="kegiatan">Kegiatan</Label>
          <select
            id="kegiatan"
            v-model="kegiatan"
            class="border rounded px-3 py-2"
            required
          >
            <option value="" disabled selected>Pilih Kegiatan</option>
            <option
              v-for="option in kegiatanOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Subkegiatan -->
        <div v-if="jenisNomenklatur >= 5" class="flex flex-col">
          <Label for="subkegiatan">Subkegiatan</Label>
          <select
            id="subkegiatan"
            v-model="subkegiatan"
            class="border rounded px-3 py-2"
            required
          >
            <option value="" disabled selected>Pilih Subkegiatan</option>
            <option
              v-for="option in subkegiatanOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>

        <!-- Nomenklatur -->
        <div class="flex flex-col">
          <Label for="nomenklatur">Nomenklatur</Label>
          <Input id="nomenklatur" v-model="nomenklatur" type="text" required />
        </div>
        
        
        <div class="flex justify-end">
          <Button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
            Simpan
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
