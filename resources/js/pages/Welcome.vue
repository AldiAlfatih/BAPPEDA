<script setup lang="ts">
import { BookOpen, Folder, LayoutGrid, Bitcoin } from 'lucide-vue-next';
import { router} from '@inertiajs/vue3';

// Removed unused router variable
const goToLogin = () => {
  router.visit('/login');
};
</script>

<template>
    <div class="flex h-screen">
      <!-- KIRI: Logo dan Judul -->
      <div class="w-1/2 bg-gray-100 flex flex-col items-center justify-center p-10">
        <img src="/images/logo-parepare.png" alt="Logo Panrita" class="w-48 mb-6" />
        <h1 class="text-3xl text-black font-bold">PANRITA BAPPEDA</h1>
        <h2 class="text-lg text-black">KOTA PAREPARE</h2>
        <p class="mt-10 text-sm text-orange-400 text-center">
          Didukung oleh Institut Teknologi Bacharuddin Jusuf Habibie
        </p>
      </div>
  
      <!-- KANAN: Menu Ikon + Link -->
      <div class="w-1/2 bg-green-900 text-white flex flex-col justify-center p-16 space-y-10">
        <div
          class="flex items-center space-x-4 cursor-pointer hover:underline"
          @click="goToLogin"
        >
          <img src="/images/icon-1.png" class="w-8 h-8"/>
          <span class="text-lg">Sistem Inovasi Dan Perancangan Pembangunan</span>
        </div>
        <div
          class="flex items-center space-x-4 cursor-pointer hover:underline"
          @click="goToLogin"
        >
          <img src="/images/icon-2.png" class="w-8 h-8"/>
          <span class="text-lg">e-Monev</span>
        </div>
        <div
          class="flex items-center space-x-4 cursor-pointer hover:underline"
          @click="goToLogin"
        >
          <img src="/images/icon-3.png" class="w-8 h-8"/>
          <span class="text-lg">e-Kinerja</span>
        </div>
        <div
          class="flex items-center space-x-4 cursor-pointer hover:underline"
          @click="goToLogin"
        >
          <img src="/images/icon-4.png" class="w-8 h-8"/>
          <span class="text-lg">Dana Pembangunan Daerah</span>
        </div>
      </div>
    </div>
</template>