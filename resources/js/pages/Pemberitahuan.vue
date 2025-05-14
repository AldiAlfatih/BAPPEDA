<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
];

const isPopupVisible = ref(false);

const showPopup = () => {
  isPopupVisible.value = true;
};

const closePopup = () => {
  isPopupVisible.value = false;
};
</script>

<template>
  <Head title="Bantuan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Overlay & Popup -->
    <div
      v-if="isPopupVisible"
      class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30"
    >
      <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-md p-6 relative">
        <h2 class="text-lg font-bold mb-4">Tambahkan Panduan</h2>

        <form @submit.prevent>
          <div class="mb-4 flex items-center gap-4">
            <label class="w-24 font-semibold">Title:</label>
            <input
              type="text"
              class="flex-1 border border-gray-300 rounded px-2 py-1"
            />
          </div>

          <div class="mb-4 flex items-center gap-4">
            <label class="w-24 font-semibold">Deskripsi:</label>
            <textarea
              class="flex-1 border border-gray-300 rounded px-2 py-1"
            ></textarea>
          </div>

          <div class="mb-4 flex items-center gap-4">
            <label class="w-40 font-semibold">Masukkan Panduan:</label>
            <input
              type="file"
              class="flex-1 border border-gray-300 rounded px-1 py-1 file:mr-4 file:py-1 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-500 file:text-white hover:file:bg-blue-600"
            />
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <Button
              type="button"
              @click="closePopup"
              class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-1 rounded"
            >
              Batal
            </Button>
            <Button
              type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded"
            >
              Simpan
            </Button>
          </div>
        </form>
      </div>
    </div>

    <!-- Konten Utama Halaman -->
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-900 md:min-h-min"
      >
        <div class="py-1 leading-none">
          <Button
            @click="showPopup"
            size="xs"
            class="ml-auto flex items-center gap-1 px-2 py-1 text-sm"
          >
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