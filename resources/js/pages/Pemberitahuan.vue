<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
          <!-- Table Placeholder -->
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-left">
              <thead>
                <tr>
                  <th class="px-4 py-2">KODE</th>
                  <th class="px-4 py-2">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                  <th class="px-4 py-2">POKOK (RP)</th>
                  <th class="px-4 py-2">PARSIAL (RP)</th>
                  <th class="px-4 py-2">PERUBAHAN (RP)</th>
                  <th class="px-4 py-2">SUMBER DANA</th>
                  <th class="px-4 py-2">KINERJA FISIK (%)</th>
                  <th class="px-4 py-2">TRIWULAN 1 KEUANGAN (RP)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2 bg-yellow-200">URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR</td>
                  <td class="px-4 py-2">216.551.782.886,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">180.512</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">180.512</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">1 01</td>
                  <td class="px-4 py-2 bg-yellow-200">URUSAN PEMERINTAHAN BIDANG PENDIDIKAN</td>
                  <td class="px-4 py-2">216.551.782.886,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">180.512</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">180.512</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">1 01 01</td>
                  <td class="px-4 py-2 bg-green-200">PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA</td>
                  <td class="px-4 py-2">170.351.678.486,00</td>
                  <td class="px-4 py-2">164.871.072.942,00</td>
                  <td class="px-4 py-2">164.871.072.942,00</td>
                  <td class="px-4 py-2">35.71</td>
                  <td class="px-4 py-2">35.71</td>
                  <td class="px-4 py-2">168.645.000</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">1 01 01 2.01</td>
                  <td class="px-4 py-2 bg-yellow-200">Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah</td>
                  <td class="px-4 py-2">6.300.000</td>
                  <td class="px-4 py-2">6.300.000</td>
                  <td class="px-4 py-2">6.300.000</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">6.300.000</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue';
  import { type BreadcrumbItem } from '@/types';
  import { Head } from '@inertiajs/vue3';

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
  ];
  </script>
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
