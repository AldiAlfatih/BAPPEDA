<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'User Management', href: '/usermanagement' },
  { title: 'Tambah User', href: '/usermanagement/create' },
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
  // nama_skpd: '',
  // no_dpa: '',
  // kode_organisasi: '',
  errors: {},
});

// handle submit to backend
function submit() {
  form.post(route('usermanagement.store'), {
    onSuccess: () => form.reset(),
    onError: (errors) => console.log(errors),
  });
}
</script>

<template>
  <Head title="Tambah User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <div class="bg-white shadow-md rounded-lg dark:bg-gray-800">

        <div class="p-6">
          <form @submit.prevent="submit">
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
                    required
                  />
                  <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                  <input 
                    v-model="form.email" 
                    type="email"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                  <input 
                    v-model="form.password" 
                    type="password"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">{{ form.errors.password_confirmation }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" >Role</label>
                  <select 
                    v-model="form.role" 
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                    required
                  >
                    <option value="operator">Operator Bappeda</option>
                    <option value="perangkat_daerah">Kepala Perangkat Daerah</option>
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
                    required
                  />
                  <p v-if="form.errors.alamat" class="text-sm text-red-600">{{ form.errors.alamat }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                  <input 
                    v-model="form.nip" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  <p v-if="form.errors.nip" class="text-sm text-red-600">{{ form.errors.nip }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. HP</label>
                  <input 
                    v-model="form.no_hp" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  <p v-if="form.errors.no_hp" class="text-sm text-red-600">{{ form.errors.no_hp }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                  <select 
                    v-model="form.jenis_kelamin"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                  >
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                  <p v-if="form.errors.jenis_kelamin" class="text-sm text-red-600">{{ form.errors.jenis_kelamin }}</p>
                </div>
              </div>
            </div>

            <!-- Data Profile SKPD (Hanya muncul jika role Perangkat Daerah) -->
            <!-- <div v-if="form.role === 'perangkat_daerah'" class="mt-8 space-y-4">
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Data Perangkat Daerah</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kepala SKPD</label>
                  <input 
                    v-model="form.nama_skpd" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.nama_skpd" class="text-sm text-red-600">{{ form.errors.nama_skpd }}</p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Urusan</label>
                  <input 
                    v-model="form.no_dpa" 
                    type="text"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                  />
                  <p v-if="form.errors.no_dpa" class="text-sm text-red-600">{{ form.errors.no_dpa }}</p>
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
            </div> -->

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
              >
                Simpan User
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
