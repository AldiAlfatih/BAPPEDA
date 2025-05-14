<script setup lang="ts">
import { defineProps} from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

// Menerima data SKPD dan users dari props
const props = defineProps<{
  skpd: {
    id: number;
    nama_skpd: string;
    nama_operator: string;
    nama_dinas: string;
    no_dpa: string;
    kode_organisasi: string;
    user_id: number;
  };
  users: Array<{ id: number, name: string }>;
}>();

// Membuat form dengan useForm
const form = useForm({
  user_id: props.skpd.user_id,
  nama_operator: props.skpd.nama_operator,
  nama_skpd: props.skpd.nama_skpd,
  nama_dinas: props.skpd.nama_dinas,
  no_dpa: props.skpd.no_dpa,
  kode_organisasi: props.skpd.kode_organisasi,
});

// Fungsi untuk mengirim data ke backend
const submitForm = () => {
  form.post(route('perangkatdaerah.update', props.skpd.id), {
    onFinish: () => {
    }
  });
};
</script>

<template>
  <div class="p-4">
    <h2 class="text-xl font-semibold mb-4">Edit Perangkat Daerah</h2>
    <form @submit.prevent="submitForm">
      <div class="grid grid-cols-1 gap-4">
        <div>
          <Label for="nama_skpd">Nama Operator</Label>
          <Input v-model="form.nama_operator" id="nama_operator" required />
        </div>
        <div>
          <Label for="nama_skpd">Nama SKPD</Label>
          <Input v-model="form.nama_skpd" id="nama_skpd" required />
        </div>

        <div>
          <Label for="nama_dinas">Nama Dinas</Label>
          <Input v-model="form.nama_dinas" id="nama_dinas" required />
        </div>

        <div>
          <Label for="no_dpa">No DPA</Label>
          <Input v-model="form.no_dpa" id="no_dpa" required />
        </div>

        <div>
          <Label for="kode_organisasi">Kode Organisasi</Label>
          <Input v-model="form.kode_organisasi" id="kode_organisasi" required />
        </div>

        <div>
          <Label for="user_id">Pengguna</Label>
          <Select v-model="form.user_id" id="user_id">
            <SelectTrigger>
              <SelectValue :placeholder="form.user_id ? form.user_id : 'Pilih Pengguna'" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="user in props.users" :key="user.id" :value="user.id">
                {{ user.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="mt-4 flex justify-end">
          <Button type="submit" class="bg-green-600 hover:bg-green-700 text-white">Simpan</Button>
        </div>
      </div>
    </form>
  </div>
</template>
