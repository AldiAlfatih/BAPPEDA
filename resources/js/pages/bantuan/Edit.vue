<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
  {
    title: 'Edit Bantuan',
    href: '/bantuan/edit',
  },
];

const props = defineProps<{
  bantuan: {
    id: number;
    judul: string;
    tgl_dibuat: string;
    // Tambahkan properti lain sesuai kebutuhan
  };
}>();


const form = useForm({
  judul: props.bantuan.judul,
  tgl_dibuat: props.bantuan.tgl_dibuat,
  // Tambahkan field lainnya sesuai dengan form yang kamu buat
});

const loading = ref(false);

function onSubmit() {
  loading.value = true;
  form.put(`/bantuan/${props.bantuan.id}`, {
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
    <Head title="Edit Bantuan" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex flex-col gap-6 p-4">
            <div class="bg-white shadow-md rounded-lg dark:bg-gray-800">

                <div class="p-6">
                <form @submit.prevent="onSubmit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Judul Bantuan -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Bantuan</label>
                        <input 
                            v-model="form.judul" 
                            type="text"
                            class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        <p v-if="form.errors.judul" class="text-sm text-red-600">{{ form.errors.judul }}</p>
                        </div>

                        <!-- Tanggal Dibuat -->
                        <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Dibuat</label>
                        <input 
                            v-model="form.tgl_dibuat" 
                            type="date"
                            class="w-full rounded-md border border-gray-300 py-2 px-3 text-gray-800 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        <p v-if="form.errors.tgl_dibuat" class="text-sm text-red-600">{{ form.errors.tgl_dibuat }}</p>
                        </div>
                    </div>
                    </div>

                    <div class="flex justify-end mt-8 gap-4">
                    <button 
                        type="button" 
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        @click="$inertia.visit('/bantuan')"
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
