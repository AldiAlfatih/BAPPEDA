<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { computed, ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/perangkatdaerah' },
  { title: 'Tambah PD', href: '/perangkatdaerah/create' },
];

const props = defineProps({
  users: Array,
  operators: Array, 
});

const form = useForm({
  nama_dinas: '', 
  no_dpa: '',
  kode_organisasi: '',
  role: 'perangkat_daerah',
  user_id: '',
  operator_id: '', 
});

const isFormValid = computed(() => {
  return form.nama_dinas && form.no_dpa && form.kode_organisasi && form.user_id && form.operator_id;
});

// Loading state for form submission
const isSubmitting = ref(false);

function submit() {
  if (!isFormValid.value) {
    console.error('Form tidak valid. Pastikan semua field wajib diisi.');
    return;
  }

  isSubmitting.value = true;
  
  console.log('Data yang akan dikirim:', {
    nama_dinas: form.nama_dinas,
    no_dpa: form.no_dpa,
    kode_organisasi: form.kode_organisasi,
    user_id: form.user_id,
    operator_id: form.operator_id
  });
  
  form.post(route('perangkatdaerah.store'), {
    onSuccess: () => {
      form.reset();
      isSubmitting.value = false;
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
      isSubmitting.value = false;
    },
  });
}

watch(() => route().current(), (newRoute) => {
  if (!newRoute.includes('perangkatdaerah.create')) {
    form.reset();
  }
});
</script>

<template>
  <Head title="Tambah Perangkat Daerah" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <div class="bg-white shadow-md rounded-lg dark:bg-gray-800">
        <div class="p-6">
          <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Tambah Perangkat Daerah</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Silakan isi data perangkat daerah dengan lengkap</p>
          </div>

          <form @submit.prevent="submit">
            <div class="mt-8 space-y-6">
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Data Perangkat Daerah</h3>

                <!-- Nama Kepala SKPD -->
                <div class="space-y-2 mb-4">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kepala SKPD</label>
                  <select 
                    v-model="form.user_id" 
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                    required
                  >
                    <option value="" disabled>Select Kepala SKPD</option>
                    <option v-for="user in props.users" :key="user.id" :value="user.id">{{ user.name }}</option>
                  </select>
                  <p v-if="form.errors.user_id" class="text-sm text-red-600">{{ form.errors.user_id }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Nama yang dipilih akan otomatis menjadi nama Perangkat Daerah</p>
                </div>

                <!-- Nama Operator -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Operator</label>
                  <select 
                    v-model="form.operator_id" 
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                    required
                  >
                    <option value="" disabled>Select Operator</option>
                    <option v-for="operator in props.operators" :key="operator.id" :value="operator.id">{{ operator.name }}</option>
                  </select>
                  <p v-if="form.errors.operator_id" class="text-sm text-red-600">{{ form.errors.operator_id }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Pilih pengguna yang akan menjadi operator SKPD ini</p>
                </div>
              </div>

              <!-- Data Perangkat Daerah -->
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Detail Perangkat Daerah</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Dinas</label>
                    <input 
                      v-model="form.nama_dinas" 
                      type="text"
                      class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                      required
                    />
                    <p v-if="form.errors.nama_dinas" class="text-sm text-red-600">{{ form.errors.nama_dinas }}</p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Urusan</label>
                    <input 
                      v-model="form.no_dpa" 
                      type="text"
                      class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                      required
                    />
                    <p v-if="form.errors.no_dpa" class="text-sm text-red-600">{{ form.errors.no_dpa }}</p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Organisasi</label>
                    <input 
                      v-model="form.kode_organisasi" 
                      type="text"
                      class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                      required
                    />
                    <p v-if="form.errors.kode_organisasi" class="text-sm text-red-600">{{ form.errors.kode_organisasi }}</p>
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-4">
                <button 
                  type="button" 
                  class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                  @click="$inertia.visit('/perangkatdaerah')"
                  :disabled="isSubmitting"
                >
                  Kembali
                </button>
                <button 
                  type="submit" 
                  class="px-4 py-2 text-sm font-medium rounded-md border border-transparent bg-blue-600 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700"
                  :disabled="isSubmitting || !isFormValid"
                >
                  <span v-if="isSubmitting">Menyimpan...</span>
                  <span v-else>Simpan</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>