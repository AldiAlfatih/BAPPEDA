<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { ref} from 'vue';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    email: string;
    roles: string[];
    userDetail?: {
      alamat: string;
      nip: string;
      no_hp: string;
      jenis_kelamin: string;
    };
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'User', href: '/usermanagement' },
  { title: 'Edit User', href: `/usermanagement/${props.user.id}/edit` },
];

const isSubmitting = ref(false);
const showPassword = ref(false);

// Form dengan data yang sudah terisi dari database
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  role: props.user.roles.length > 0 ? props.user.roles[0] : 'operator',
  alamat: props.user.userDetail?.alamat || '',
  nip: props.user.userDetail?.nip || '',
  no_hp: props.user.userDetail?.no_hp || '',
  jenis_kelamin: props.user.userDetail?.jenis_kelamin || '',
});

function submit() {
  isSubmitting.value = true;

  // Ganti form.post menjadi form.put untuk konsistensi dengan REST API
  form.put(`/usermanagement/${props.user.id}`, {
    onSuccess: () => {
      window.location.href = '/usermanagement';
    },
    onError: (errors) => {
      console.error(errors);
      isSubmitting.value = false;
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

function goBack() {
  window.history.back();
}
</script>

<template>
  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto p-4">
      <Card class="max-w-3xl mx-auto">
        <CardHeader>
          <div class="flex items-center justify-between">
            <div>
              <CardTitle class="text-xl font-bold">Edit User</CardTitle>
              <CardDescription>
                Perbarui informasi pengguna
              </CardDescription>
            </div>
            <Button variant="outline" @click="goBack" class="flex items-center gap-1">
              <ArrowLeft class="w-4 h-4" />
              Kembali
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Informasi Dasar -->
              <div class="space-y-4">
                <div>
                  <Label for="name">Nama</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    class="mt-1"
                  />
                  <div v-if="form.errors.name" class="text-sm text-red-500 mt-1">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div>
                  <Label for="email">Email <span class="text-red-500">*</span></Label>
                  <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    class="mt-1"
                  />
                  <div v-if="form.errors.email" class="text-sm text-red-500 mt-1">
                    {{ form.errors.email }}
                  </div>
                </div>

                <div>
                  <Label for="role">Role</Label>
                  <Select v-model="form.role">
                    <SelectTrigger class="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="perangkat_daerah">Perangkat Daerah</SelectItem>
                      <SelectItem value="operator">Operator</SelectItem>
                    </SelectContent>
                  </Select>
                  <div v-if="form.errors.role" class="text-sm text-red-500 mt-1">
                    {{ form.errors.role }}
                  </div>
                </div>
              </div>

              <!-- Informasi Detail -->
              <div class="space-y-4">
                <div>
                  <Label for="alamat">Alamat</Label>
                  <Input
                    id="alamat"
                    v-model="form.alamat"
                    class="mt-1"
                  />
                  <div v-if="form.errors.alamat" class="text-sm text-red-500 mt-1">
                    {{ form.errors.alamat }}
                  </div>
                </div>

                <div>
                  <Label for="nip">NIP</Label>
                  <Input
                    id="nip"
                    v-model="form.nip"
                    class="mt-1"
                  />
                  <div v-if="form.errors.nip" class="text-sm text-red-500 mt-1">
                    {{ form.errors.nip }}
                  </div>
                </div>

                <div>
                  <Label for="no_hp">No. HP</Label>
                  <Input
                    id="no_hp"
                    v-model="form.no_hp"
                    class="mt-1"
                  />
                  <div v-if="form.errors.no_hp" class="text-sm text-red-500 mt-1">
                    {{ form.errors.no_hp }}
                  </div>
                </div>

                <div>
                  <Label for="jenis_kelamin">Jenis Kelamin</Label>
                  <Select v-model="form.jenis_kelamin">
                    <SelectTrigger class="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Laki-laki">Laki-laki</SelectItem>
                      <SelectItem value="Perempuan">Perempuan</SelectItem>
                    </SelectContent>
                  </Select>
                  <div v-if="form.errors.jenis_kelamin" class="text-sm text-red-500 mt-1">
                    {{ form.errors.jenis_kelamin }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Password Section -->
            <div class="border-t pt-6">
              <h3 class="text-lg font-medium mb-4">Ubah Password (opsional)</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="password">Password Baru</Label>
                  <Input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    v-model="form.password"
                    class="mt-1"
                  />
                  <div v-if="form.errors.password" class="text-sm text-red-500 mt-1">
                    {{ form.errors.password }}
                  </div>
                </div>

                <div>
                  <Label for="password_confirmation">Konfirmasi Password</Label>
                  <Input
                    id="password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    v-model="form.password_confirmation"
                    class="mt-1"
                  />
                </div>
              </div>
              <div class="mt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" v-model="showPassword" class="rounded" />
                  <span class="text-sm">Tampilkan password</span>
                </label>
              </div>
            </div>
          </form>
        </CardContent>
        <CardFooter class="flex justify-end gap-3">
          <Button variant="outline" @click="goBack">
            Batal
          </Button>
          <Button
            type="submit"
            @click="submit"
            :disabled="isSubmitting"
            class="flex items-center gap-1"
          >
            <Save class="w-4 h-4" />
            <span v-if="isSubmitting">Menyimpan...</span>
            <span v-else>Simpan Perubahan</span>
          </Button>
        </CardFooter>
      </Card>
    </div>
  </AppLayout>
</template>
