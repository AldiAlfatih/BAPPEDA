<script setup lang="ts">
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button, Input, Select } from '@/components/ui';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
  kodenomenklatur: {
    id: number,
    nomor_kode: string,
    nomenklatur: string,
    jenis_kode: number,
    detail: {
      urusan?: string,
      bidang_urusan?: string,
      program?: string,
      kegiatan?: string,
      subkegiatan?: string,
    } | null
  }
}>();

const form = useForm({
  nomor_kode: props.kodenomenklatur.nomor_kode,
  nomenklatur: props.kodenomenklatur.nomenklatur,
  jenis_kode: props.kodenomenklatur.jenis_kode,
  urusan: props.kodenomenklatur.detail?.urusan || '',
  bidang_urusan: props.kodenomenklatur.detail?.bidang_urusan || '',
  program: props.kodenomenklatur.detail?.program || '',
  kegiatan: props.kodenomenklatur.detail?.kegiatan || '',
  subkegiatan: props.kodenomenklatur.detail?.subkegiatan || '',
});

function submitForm() {
  form.put(`/kodenomenklatur/${props.kodenomenklatur.id}`, {
    onSuccess: () => {
      // Handle the success scenario, for example, show a message or navigate
    },
    onError: () => {
      // Handle the error scenario, for example, show a message
    },
  });
}
</script>

<template>
  <Head title="Edit Kode Nomenklatur" />
  <div class="p-4">
    <h2>Edit Kode Nomenklatur</h2>
    <form @submit.prevent="submitForm">
      <div class="mb-4">
        <label for="nomor_kode" class="block">Nomor Kode</label>
        <Input v-model="form.nomor_kode" id="nomor_kode" />
      </div>

      <div class="mb-4">
        <label for="nomenklatur" class="block">Nomenklatur</label>
        <Input v-model="form.nomenklatur" id="nomenklatur" />
      </div>

      <div class="mb-4">
        <label for="jenis_kode" class="block">Jenis Kode</label>
        <Select v-model="form.jenis_kode" id="jenis_kode">
          <option value="0">Jenis Kode 0</option>
          <option value="1">Jenis Kode 1</option>
          <option value="2">Jenis Kode 2</option>
          <option value="3">Jenis Kode 3</option>
          <option value="4">Jenis Kode 4</option>
        </Select>
      </div>

      <!-- Additional fields for urusan, bidang_urusan, program, kegiatan, subkegiatan -->
      <div v-if="form.jenis_kode >= 1" class="mb-4">
        <label for="urusan" class="block">Urusan</label>
        <Input v-model="form.urusan" id="urusan" />
      </div>
      
      <div v-if="form.jenis_kode >= 2" class="mb-4">
        <label for="bidang_urusan" class="block">Bidang Urusan</label>
        <Input v-model="form.bidang_urusan" id="bidang_urusan" />
      </div>

      <div v-if="form.jenis_kode >= 3" class="mb-4">
        <label for="program" class="block">Program</label>
        <Input v-model="form.program" id="program" />
      </div>

      <div v-if="form.jenis_kode >= 4" class="mb-4">
        <label for="kegiatan" class="block">Kegiatan</label>
        <Input v-model="form.kegiatan" id="kegiatan" />
      </div>

      <div class="mb-4">
        <label for="subkegiatan" class="block">Subkegiatan</label>
        <Input v-model="form.subkegiatan" id="subkegiatan" />
      </div>

      <Button type="submit">Update</Button>
    </form>
  </div>
</template>
