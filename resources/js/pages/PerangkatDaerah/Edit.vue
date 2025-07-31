<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { defineProps, computed, ref } from 'vue';
import { useForm, router, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/manajemen-tim/perangkatdaerah' },
  { title: 'Edit PD', href: '/manajemen-tim/perangkatdaerah/edit' },
];

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
  users: Array<{ id: number; name: string }>;
  operators: Array<{ id: number; name: string }>;
  current_operator_id: number | null;
}>();

const form = useForm({
  user_id: props.skpd.user_id,
  operator_id: props.current_operator_id || '',
  nama_dinas: props.skpd.nama_dinas || '',
  no_dpa: props.skpd.no_dpa || '',
  kode_organisasi: props.skpd.kode_organisasi || '',
});

// Loading state
const isSubmitting = ref(false);

function submitForm() {
  if (!form.user_id || !form.operator_id || !form.kode_organisasi) {
    alert('Pastikan semua field wajib telah diisi.');
    return;
  }

  isSubmitting.value = true;
  
  form.put(route('perangkatdaerah.update', props.skpd.id), {
    onSuccess: (page) => {
      console.log('Data berhasil diupdate');
      isSubmitting.value = false;
      router.visit('/manajemen-tim/perangkatdaerah');
    },
    onError: (errors) => {
      console.error('Update failed:', errors);
      isSubmitting.value = false;
      
      // Tampilkan error yang lebih user-friendly
      const errorMessages = Object.values(errors).flat().join('\n');
      alert(`Terjadi kesalahan:\n${errorMessages}`);
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

function goBack() {
  router.visit('/manajemen-tim/perangkatdaerah');
}
</script>

<template>
  <Head title="Edit Perangkat Daerah" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6 dark:bg-gray-800">
          <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-200">Edit Perangkat Daerah</h2>
          
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">

              <!-- Kepala SKPD Selection -->
              <div class="space-y-2">
                <Label for="user_id" class="text-gray-700 dark:text-gray-300">
                  Kepala SKPD <span class="text-red-500">*</span>
                </Label>
                <select 
                  v-model="form.user_id" 
                  class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                  required
                >
                  <option value="" disabled>Pilih Kepala SKPD</option>
                  <option v-for="user in props.users" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
                <p v-if="form.errors.user_id" class="text-sm text-red-500">{{ form.errors.user_id }}</p>
              </div>

              <!-- Operator Selection -->
              <div class="space-y-2">
                <Label for="operator_id" class="text-gray-700 dark:text-gray-300">
                  Operator <span class="text-red-500">*</span>
                </Label>
                <select 
                  v-model="form.operator_id" 
                  class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                  required
                >
                  <option value="" disabled>Pilih Operator</option>
                  <option v-for="operator in props.operators" :key="operator.id" :value="operator.id">
                    {{ operator.name }}
                  </option>
                </select>
                <p v-if="form.errors.operator_id" class="text-sm text-red-500">{{ form.errors.operator_id }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Operator dapat bertugas di beberapa SKPD berbeda</p>
              </div>

              <!-- Nama Dinas -->
              <div class="space-y-2">
                <Label for="nama_dinas" class="text-gray-700 dark:text-gray-300">Nama Dinas</Label>
                <Input 
                  v-model="form.nama_dinas" 
                  type="text"
                  placeholder="Masukkan nama dinas"
                  class="w-full"
                />
                <p v-if="form.errors.nama_dinas" class="text-sm text-red-500">{{ form.errors.nama_dinas }}</p>
              </div>

              <!-- Kode Organisasi -->
              <div class="space-y-2">
                <Label for="kode_organisasi" class="text-gray-700 dark:text-gray-300">
                  Kode Organisasi <span class="text-red-500">*</span>
                </Label>
                <Input 
                  v-model="form.kode_organisasi" 
                  type="text"
                  placeholder="Masukkan kode organisasi"
                  class="w-full"
                  required
                />
                <p v-if="form.errors.kode_organisasi" class="text-sm text-red-500">{{ form.errors.kode_organisasi }}</p>
              </div>

              <!-- No DPA -->
              <div class="space-y-2">
                <Label for="no_dpa" class="text-gray-700 dark:text-gray-300">No DPA</Label>
                <Input 
                  v-model="form.no_dpa" 
                  type="text"
                  placeholder="Masukkan nomor DPA"
                  class="w-full"
                />
                <p v-if="form.errors.no_dpa" class="text-sm text-red-500">{{ form.errors.no_dpa }}</p>
              </div>

            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-600">
              <Button 
                variant="outline" 
                type="button" 
                @click="goBack"
                :disabled="isSubmitting || form.processing"
                class="text-gray-700 dark:text-gray-300"
              >
                Kembali
              </Button>
              <Button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white"
                :disabled="isSubmitting || form.processing"
              >
                <span v-if="isSubmitting || form.processing">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>