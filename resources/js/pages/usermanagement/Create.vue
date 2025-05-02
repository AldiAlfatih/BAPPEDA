<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'User Management',
    href: '/usermanagement',
  },
  {
    title: 'Tambah User',
    href: '/usermanagement/create',
  },
];

// form binding
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  alamat: '',
  nip: '',
  no_hp: '',
  jenis_kelamin: '',
  tgl_lahir: '',
  nama_kepala_skpd: '',
  kode_urusan: '',
  nama_skpd: '',
  kode_organisasi: '',
  errors: {}, // Pastikan errors didefinisikan
});


// submit ke backend
// submit ke backend
function submit() {
  form.post(route('usermanagement.store'), {
    onSuccess: () => form.reset(),
    onError: (errors) => console.log(errors), // Periksa error yang diterima
  });
}

</script>

<template>
  <Head title="Tambah User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Tambah User</h1>

      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <div>
          <Label>Nama</Label>
          <Input v-model="form.name" type="text" required />
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
        </div>

        <div>
          <Label for="email">Email</Label>
          <Input id="email" v-model="form.email" type="email" required />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <Label for="password">Password</Label>
          <Input id="password" v-model="form.password" type="password" required />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
        </div>

        <div>
          <Label for="password_confirmation">Confirm Password</Label>
          <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required />
          <div v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
        </div>

        <div>
          <Label for="role">Role</Label>
          <select id="role" v-model="form.role" required>
            <option value="perangkat_daerah">Perangkat Daerah</option>
            <option value="operator">Operator</option>
          </select>
          <div v-if="form.errors.role" class="text-red-500 text-sm mt-1">{{ form.errors.role }}</div>
        </div>

        <div>
          <Label for="alamat">Alamat</Label>
          <Input id="alamat" v-model="form.alamat" type="text" required />
          <div v-if="form.errors.alamat" class="text-red-500 text-sm mt-1">{{ form.errors.alamat }}</div>
        </div>

        <div>
          <Label for="nip">NIP</Label>
          <Input id="nip" v-model="form.nip" type="text" required />
          <div v-if="form.errors.nip" class="text-red-500 text-sm mt-1">{{ form.errors.nip }}</div>
        </div>

        <div>
          <Label for="no_hp">No HP</Label>
          <Input id="no_hp" v-model="form.no_hp" type="text" required />
          <div v-if="form.errors.no_hp" class="text-red-500 text-sm mt-1">{{ form.errors.no_hp }}</div>
        </div>

        <div>
          <Label for="jenis_kelamin">Jenis Kelamin</Label>
          <select id="jenis_kelamin" v-model="form.jenis_kelamin" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
          <div v-if="form.errors.jenis_kelamin" class="text-red-500 text-sm mt-1">{{ form.errors.jenis_kelamin }}</div>
        </div>

        <div>
          <Label for="tgl_lahir">Tanggal Lahir</Label>
          <Input id="tgl_lahir" v-model="form.tgl_lahir" type="date" required />
          <div v-if="form.errors.tgl_lahir" class="text-red-500 text-sm mt-1">{{ form.errors.tgl_lahir }}</div>
        </div>

        <div v-if="form.role === 'perangkat_daerah'">
          <div>
            <Label for="nama_kepala_skpd">Nama Kepala SKPD</Label>
            <Input id="nama_kepala_skpd" v-model="form.nama_kepala_skpd" type="text" />
          </div>

          <div>
            <Label for="kode_urusan">Kode Urusan</Label>
            <Input id="kode_urusan" v-model="form.kode_urusan" type="text" />
          </div>

          <div>
            <Label for="nama_skpd">Nama SKPD</Label>
            <Input id="nama_skpd" v-model="form.nama_skpd" type="text" />
          </div>

          <div>
            <Label for="kode_organisasi">Kode Organisasi</Label>
            <Input id="kode_organisasi" v-model="form.kode_organisasi" type="text" />
          </div>
        </div>

        <div class="flex gap-4 mt-6">
          <Button type="submit" class="bg-blue-600 hover:bg-blue-700">Simpan</Button>
          <Button type="button" variant="outline" @click="$inertia.visit(route('usermanagement.index'))">
            Batal
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
