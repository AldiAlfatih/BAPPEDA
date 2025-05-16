<script setup lang="ts">
import { defineProps, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
  kodeNomenklatur: {
    id: number,
    nomor_kode: string,
    nomenklatur: string,
    jenis_nomenklatur: number,
  },
  detail: {
    id_urusan?: number,
    id_bidang_urusan?: number,
    id_program?: number,
    id_kegiatan?: number,
    id_sub_kegiatan?: number
  } | null,
  urusanList: Array<{id: number, nomor_kode: string, nomenklatur: string}>,
  bidangUrusanList: Array<{id: number, nomor_kode: string, nomenklatur: string, urusan_id: number}>,
  programList: Array<{id: number, nomor_kode: string, nomenklatur: string, urusan_id: number, bidang_urusan_id: number}>,
  kegiatanList: Array<{id: number, nomor_kode: string, nomenklatur: string, urusan_id: number, bidang_urusan_id: number, program_id: number}>,
  subkegiatanList: Array<{id: number, nomor_kode: string, nomenklatur: string, urusan_id: number, bidang_urusan_id: number, program_id: number, kegiatan_id: number}>
}>();

const form = useForm({
  nomor_kode: props.kodeNomenklatur.nomor_kode,
  nomenklatur: props.kodeNomenklatur.nomenklatur,
  jenis_nomenklatur: props.kodeNomenklatur.jenis_nomenklatur,
  urusan: props.detail?.id_urusan || null,
  bidang_urusan: props.detail?.id_bidang_urusan || null,
  program: props.detail?.id_program || null,
  kegiatan: props.detail?.id_kegiatan || null,
  subkegiatan: props.detail?.id_sub_kegiatan || null,
});

// Filter dependent dropdowns based on selections
const filteredBidangUrusan = computed(() => {
  if (!form.urusan) return [];
  return props.bidangUrusanList.filter(item => item.urusan_id === form.urusan);
});

const filteredProgram = computed(() => {
  if (!form.urusan || !form.bidang_urusan) return [];
  return props.programList.filter(item =>
    item.urusan_id === form.urusan &&
    item.bidang_urusan_id === form.bidang_urusan
  );
});

const filteredKegiatan = computed(() => {
  if (!form.urusan || !form.bidang_urusan || !form.program) return [];
  return props.kegiatanList.filter(item =>
    item.urusan_id === form.urusan &&
    item.bidang_urusan_id === form.bidang_urusan &&
    item.program_id === form.program
  );
});

const filteredSubkegiatan = computed(() => {
  if (!form.urusan || !form.bidang_urusan || !form.program || !form.kegiatan) return [];
  return props.subkegiatanList.filter(item =>
    item.urusan_id === form.urusan &&
    item.bidang_urusan_id === form.bidang_urusan &&
    item.program_id === form.program &&
    item.kegiatan_id === form.kegiatan
  );
});

function submitForm() {
  form.put(`/kodenomenklatur/${props.kodeNomenklatur.id}`, {
    onSuccess: () => {
      // Handle the success scenario
      console.log('Data updated successfully');
    },
    onError: (errors) => {
      // Handle the error scenario
      console.error('Error updating data:', errors);
    },
  });
}
</script>

<template>
  <Head title="Edit Kode Nomenklatur" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <h2 class="text-lg font-semibold mb-4">Edit Kode Nomenklatur</h2>
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label for="nomor_kode" class="block mb-1">Nomor Kode</label>
          <Input v-model="form.nomor_kode" id="nomor_kode" class="w-full" />
          <div v-if="form.errors.nomor_kode" class="text-red-500 text-sm mt-1">
            {{ form.errors.nomor_kode }}
          </div>
        </div>

        <div class="mb-4">
          <label for="nomenklatur" class="block mb-1">Nomenklatur</label>
          <Input v-model="form.nomenklatur" id="nomenklatur" class="w-full" />
          <div v-if="form.errors.nomenklatur" class="text-red-500 text-sm mt-1">
            {{ form.errors.nomenklatur }}
          </div>
        </div>

        <div class="mb-4">
          <label for="jenis_nomenklatur" class="block mb-1">Jenis Kode</label>
          <Select v-model="form.jenis_nomenklatur" id="jenis_nomenklatur" class="w-full">
            <option :value="0">Urusan</option>
            <option :value="1">Bidang Urusan</option>
            <option :value="2">Program</option>
            <option :value="3">Kegiatan</option>
            <option :value="4">Subkegiatan</option>
          </Select>
          <div v-if="form.errors.jenis_nomenklatur" class="text-red-500 text-sm mt-1">
            {{ form.errors.jenis_nomenklatur }}
          </div>
        </div>

        <!-- Fields for hierarchical relationships -->
        <div v-if="form.jenis_nomenklatur >= 1" class="mb-4">
          <label for="urusan" class="block mb-1">Urusan</label>
          <Select v-model="form.urusan" id="urusan" class="w-full">
            <option value="">Pilih Urusan</option>
            <option v-for="urusan in urusanList" :key="urusan.id" :value="urusan.id">
              {{ urusan.nomor_kode }} - {{ urusan.nomenklatur }}
            </option>
          </Select>
          <div v-if="form.errors.urusan" class="text-red-500 text-sm mt-1">
            {{ form.errors.urusan }}
          </div>
        </div>

        <div v-if="form.jenis_nomenklatur >= 2" class="mb-4">
          <label for="bidang_urusan" class="block mb-1">Bidang Urusan</label>
          <Select v-model="form.bidang_urusan" id="bidang_urusan" class="w-full">
            <option value="">Pilih Bidang Urusan</option>
            <option v-for="bidang in filteredBidangUrusan" :key="bidang.id" :value="bidang.id">
              {{ bidang.nomor_kode }} - {{ bidang.nomenklatur }}
            </option>
          </Select>
          <div v-if="form.errors.bidang_urusan" class="text-red-500 text-sm mt-1">
            {{ form.errors.bidang_urusan }}
          </div>
        </div>

        <div v-if="form.jenis_nomenklatur >= 3" class="mb-4">
          <label for="program" class="block mb-1">Program</label>
          <Select v-model="form.program" id="program" class="w-full">
            <option value="">Pilih Program</option>
            <option v-for="program in filteredProgram" :key="program.id" :value="program.id">
              {{ program.nomor_kode }} - {{ program.nomenklatur }}
            </option>
          </Select>
          <div v-if="form.errors.program" class="text-red-500 text-sm mt-1">
            {{ form.errors.program }}
          </div>
        </div>

        <div v-if="form.jenis_nomenklatur >= 4" class="mb-4">
          <label for="kegiatan" class="block mb-1">Kegiatan</label>
          <Select v-model="form.kegiatan" id="kegiatan" class="w-full">
            <option value="">Pilih Kegiatan</option>
            <option v-for="kegiatan in filteredKegiatan" :key="kegiatan.id" :value="kegiatan.id">
              {{ kegiatan.nomor_kode }} - {{ kegiatan.nomenklatur }}
            </option>
          </Select>
          <div v-if="form.errors.kegiatan" class="text-red-500 text-sm mt-1">
            {{ form.errors.kegiatan }}
          </div>
        </div>

        <div class="mt-6">
          <Button type="submit" class="mr-2" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </Button>
          <Button type="button" variant="outline" @click="$inertia.visit('/kodenomenklatur')">
            Batal
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
