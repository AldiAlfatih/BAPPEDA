<!-- /resources/js/Pages/usermanagement/Edit.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    email: string;
    roles: Array<{ id: number; name: string }>;
    userDetail: {
      alamat: string;
      nip: string;
      no_hp: string;
      jenis_kelamin: string;
      tgl_lahir: string;
    };
    profileSkpd: {
      nama_kepala_skpd: string;
      kode_urusan: string;
      nama_skpd: string;
      kode_organisasi: string;
    };
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'User Management', href: '/usermanagement' },
  { title: 'Edit User', href: `/usermanagement/${props.user.id}/edit` },
];

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  role: props.user.roles?.[0]?.name || '',
  alamat: props.user.userDetail?.alamat || '',
  nip: props.user.userDetail?.nip || '',
  no_hp: props.user.userDetail?.no_hp || '',
  jenis_kelamin: props.user.userDetail?.jenis_kelamin || '',
  tgl_lahir: props.user.userDetail?.tgl_lahir || '',
  nama_kepala_skpd: props.user.profileSkpd?.nama_kepala_skpd || '',
  kode_urusan: props.user.profileSkpd?.kode_urusan || '',
  nama_skpd: props.user.profileSkpd?.nama_skpd || '', 
  kode_organisasi: props.user.profileSkpd?.kode_organisasi || '',
});

const isPerangkatDaerah = computed(() => form.role === 'perangkat_daerah');
const loading = ref(false);

function onSubmit() {
  loading.value = true;
  form.put(`/usermanagement/${props.user.id}`, {
    onSuccess: () => {
      loading.value = false;
    },
    onError: () => {
      loading.value = false;
    },
  });
}
</script>

<template>
  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <div class="bg-white shadow-md rounded-lg dark:bg-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Edit User</h2>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Perbarui data user dan informasi tambahan
          </p>
        </div>

        <div class="p-6">
          <form @submit.prevent="onSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Data User -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Data User</h3>
                
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                  <input 
                    v-model="form.name" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                  <input 
                    v-model="form.email" 
                    type="email"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password (kosongkan jika tidak ingin mengubah)</label>
                  <input 
                    v-model="form.password" 
                    type="password"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">{{ form.errors.password_confirmation }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                  <select 
                    v-model="form.role"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  >
                    <option value="perangkat_daerah">Perangkat Daerah</option>
                    <option value="operator">Operator</option>
                  </select>
                  <p v-if="form.errors.role" class="text-sm text-red-600">{{ form.errors.role }}</p>
                </div>
              </div>

              <!-- Data Detail User -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Detail User</h3>
                
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                  <input 
                    v-model="form.alamat" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.alamat" class="text-sm text-red-600">{{ form.errors.alamat }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                  <input 
                    v-model="form.nip" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.nip" class="text-sm text-red-600">{{ form.errors.nip }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. HP</label>
                  <input 
                    v-model="form.no_hp" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.no_hp" class="text-sm text-red-600">{{ form.errors.no_hp }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                  <select 
                    v-model="form.jenis_kelamin"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  >
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                  <p v-if="form.errors.jenis_kelamin" class="text-sm text-red-600">{{ form.errors.jenis_kelamin }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                  <input 
                    v-model="form.tgl_lahir" 
                    type="date"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.tgl_lahir" class="text-sm text-red-600">{{ form.errors.tgl_lahir }}</p>
                </div>
              </div>
            </div>

            <!-- Data Profile SKPD (Hanya muncul jika role Perangkat Daerah) -->
            <div v-if="isPerangkatDaerah" class="mt-8 space-y-4">
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Data Perangkat Daerah</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kepala SKPD</label>
                  <input 
                    v-model="form.nama_kepala_skpd" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.nama_kepala_skpd" class="text-sm text-red-600">{{ form.errors.nama_kepala_skpd }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Urusan</label>
                  <input 
                    v-model="form.kode_urusan" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.kode_urusan" class="text-sm text-red-600">{{ form.errors.kode_urusan }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama SKPD</label>
                  <input 
                    v-model="form.nama_skpd" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.nama_skpd" class="text-sm text-red-600">{{ form.errors.nama_skpd }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Organisasi</label>
                  <input 
                    v-model="form.kode_organisasi" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.kode_organisasi" class="text-sm text-red-600">{{ form.errors.kode_organisasi }}</p>
                </div>
              </div>
            </div>

            <div class="flex justify-end mt-8 gap-4">
              <button 
                type="button" 
                class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                @click="$inertia.visit('/usermanagement')"
              >
                Kembali
              </button>
              <button 
                type="submit" 
                class="px-4 py-2 text-sm font-medium rounded-md border border-transparent bg-blue-600 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700"
                :disabled="loading"
              >
                {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>