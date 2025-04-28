<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
];

function goToCreatePage() {
  router.visit('/bantuan/create');
}

</script>

<template>
  <Head title="Bantuan" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <!-- Konten Utama Halaman -->
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-900 md:min-h-min"
      >
        <div class="py-1 leading-none">
          <Button
            size="sm"
            class="ml-auto flex items-center gap-1 px-2 py-1 text-sm" @click="goToCreatePage">
            <Plus class="w-4 h-4" />
            Tambahkan
          </Button>
        </div>

        <div class="flex flex-col md:flex-row items-start gap-6 mt-6">
          <img
            src="/images/logo-parepare.png"
            alt="Panduan E-Monev"
            class="w-25 rounded shadow"
          />
          <div class="text-base text-gray-700 dark:text-gray-200">
            <p class="mb-2 font-bold">
              PANDUAN PENGGUNAAN WEBSITE E-MONEV
            </p>
            <p
              class="text-sm text-justify text-gray-700 dark:text-gray-200 max-w-xl leading-relaxed"
            >
              Automasi dan integrasi pencatatan keuangan, manajemen pembayaran hingga supply chain untuk mendukung pertumbuhan bisnis Anda.
            </p>
            <div class="flex items-center mt-4">
              <Button
                class="w-32 bg-yellow-400 hover:bg-yellow-700 text-white text-xs px-4 py-2 rounded font-bold"
              >
                Edit
              </Button>
              <Button
                class="ml-4 w-32 bg-red-700 hover:bg-red-800 text-white text-xs px-4 py-2 rounded font-bold"
              >
                Delete
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
