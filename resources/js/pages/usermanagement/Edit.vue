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
    title: 'Edit User',
    href: '/usermanagement/edit',
  },
];

// Props dari backend
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

// Isi form berdasarkan props user
const form = useForm({
  name: props.user.name || '',
  email: props.user.email || '',
  password: '',
  password_confirmation: '',
  role: props.user.roles?.[0]?.name || '',
  alamat: props.user.user_detail?.alamat || '',
  nip: props.user.user_detail?.nip || '',
  no_hp: props.user.user_detail?.no_hp || '',
  jenis_kelamin: props.user.user_detail?.jenis_kelamin || '',
  tgl_lahir: props.user.user_detail?.tgl_lahir || '',
  nama_kepala_skpd: props.user.profile_skpd?.nama_kepala_skpd || '',
  kode_urusan: props.user.profile_skpd?.kode_urusan || '',
  nama_skpd: props.user.profile_skpd?.nama_skpd || '',
  kode_organisasi: props.user.profile_skpd?.kode_organisasi || '',
});

// Submit update user
function submit() {
  form.put(route('usermanagement.update', props.user.id), {
    onSuccess: () => {
      // opsional: form.reset(); kalau mau reset form setelah update
    },
    onError: (errors) => console.log(errors),
  });
}
</script>

<template>
  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Edit User</h1>

      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <div>
          <Label for="name">Nama</Label>
          <Input id="name" v-model="form.name" type="text" required />
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
        </div>

        <div>
          <Label for="email">Email</Label>
          <Input id="email" v-model="form.email" type="email" required />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <Label for="password">Password (Kosongkan jika tidak ingin ubah)</Label>
          <Input id="password" v-model="form.password" type="password" />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
        </div>

        <div>
          <Label for="password_confirmation">Konfirmasi Password</Label>
          <Input id="password_confirmation" v-model="form.password_confirmation" type="password" />
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
          <Button type="submit" class="bg-blue-600 hover:bg-blue-700">
            Simpan Perubahan
          </Button>
          <Button type="button" variant="outline" @click="$inertia.visit(route('usermanagement.index'))">
            Batal
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
