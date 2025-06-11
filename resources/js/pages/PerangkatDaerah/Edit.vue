<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { defineProps, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/perangkatdaerah' },
  { title: 'Edit PD', href: '/perangkatdaerah/edit' },
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
  nama_dinas: props.skpd.nama_dinas,
  no_dpa: props.skpd.no_dpa,
  kode_organisasi: props.skpd.kode_organisasi,
});

const selectedUserName = computed(() => {
  const user = props.users.find((u) => u.id === form.user_id);
  return user ? user.name : 'Pilih Pengguna';
});

const selectedOperatorName = computed(() => {
  const operator = props.operators.find((o) => o.id === form.operator_id);
  return operator ? operator.name : 'Pilih Operator';
});

function submitForm() {
  form.put(route('perangkatdaerah.update', props.skpd.id), {
    onSuccess: () => {
      router.visit('/perangkatdaerah');
    },
    onError: (errors) => {
      console.error('Update failed:', errors);
    },
  });
}

function goBack() {
  router.visit('/perangkatdaerah');
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
          <h2 class="text-xl font-semibold mb-6">Edit Perangkat Daerah</h2>
          
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              
              <!-- Kepala SKPD Selection -->
              <div class="space-y-2">
                <Label for="user_id">Kepala SKPD <span class="text-red-500">*</span></Label>
                <Select v-model="form.user_id" required>
                  <SelectTrigger>
                    <SelectValue :placeholder="selectedUserName" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="user in props.users" :key="user.id" :value="user.id">
                      {{ user.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="form.errors.user_id" class="text-sm text-red-500">{{ form.errors.user_id }}</p>
              </div>

              <!-- Operator Selection -->
              <div class="space-y-2">
                <Label for="operator_id">Operator <span class="text-red-500">*</span></Label>
                <Select v-model="form.operator_id" required>
                  <SelectTrigger>
                    <SelectValue :placeholder="selectedOperatorName" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="operator in props.operators" :key="operator.id" :value="operator.id">
                      {{ operator.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="form.errors.operator_id" class="text-sm text-red-500">{{ form.errors.operator_id }}</p>
              </div>

              <!-- Nama Dinas -->
              <div class="space-y-2">
                <Label for="nama_dinas">Nama Dinas <span class="text-red-500">*</span></Label>
                <Input 
                  v-model="form.nama_dinas" 
                  id="nama_dinas" 
                  required 
                  placeholder="Masukkan nama dinas"
                />
                <p v-if="form.errors.nama_dinas" class="text-sm text-red-500">{{ form.errors.nama_dinas }}</p>
              </div>

              <!-- No DPA -->
              <div class="space-y-2">
                <Label for="no_dpa">No DPA <span class="text-red-500">*</span></Label>
                <Input 
                  v-model="form.no_dpa" 
                  id="no_dpa" 
                  required 
                  placeholder="Masukkan nomor DPA"
                />
                <p v-if="form.errors.no_dpa" class="text-sm text-red-500">{{ form.errors.no_dpa }}</p>
              </div>

              <!-- Kode Organisasi -->
              <div class="space-y-2">
                <Label for="kode_organisasi">Kode Organisasi <span class="text-red-500">*</span></Label>
                <Input 
                  v-model="form.kode_organisasi" 
                  id="kode_organisasi" 
                  required 
                  placeholder="Masukkan kode organisasi"
                />
                <p v-if="form.errors.kode_organisasi" class="text-sm text-red-500">{{ form.errors.kode_organisasi }}</p>
              </div>

              <!-- Read-only fields for reference -->
              <div class="border-t pt-4">
                <h3 class="text-lg font-medium mb-4 text-gray-700">Informasi Saat Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <Label class="text-sm text-gray-600">Nama SKPD Saat Ini</Label>
                    <div class="text-sm bg-gray-50 p-2 rounded border">
                      {{ props.skpd.nama_skpd }}
                    </div>
                  </div>
                  <div class="space-y-2">
                    <Label class="text-sm text-gray-600">Nama Operator Saat Ini</Label>
                    <div class="text-sm bg-gray-50 p-2 rounded border">
                      {{ props.skpd.nama_operator }}
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4 pt-6 border-t">
              <Button 
                variant="outline" 
                type="button" 
                @click="goBack"
                :disabled="form.processing"
              >
                Kembali
              </Button>
              <Button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white"
                :disabled="form.processing"
              >
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>