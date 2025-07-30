<script setup lang="ts">
import { BookOpen, Folder, LayoutGrid, Bitcoin } from 'lucide-vue-next';
import { router} from '@inertiajs/vue3';

// Removed unused router variable
const goToLogin = () => {
  router.visit('/login');
};
</script>

<template>
    <div class="min-h-screen flex flex-col lg:flex-row">
      <!-- KIRI: Logo dan Judul -->
      <div class="w-full lg:w-1/2 bg-gray-100 flex flex-col items-center justify-center p-6 sm:p-8 lg:p-10 order-1 lg:order-none">
        <img 
          src="/images/logo-parepare.png" 
          alt="Logo Panrita" 
          class="w-32 sm:w-40 md:w-48 mb-6 max-w-full h-auto" 
        />
        <h1 class="text-xl sm:text-2xl md:text-3xl text-black font-bold text-center">
          PANRITA BAPPEDA
        </h1>
        <h2 class="text-base sm:text-lg text-black text-center">
          KOTA PAREPARE
        </h2>
        <p class="mt-6 sm:mt-8 lg:mt-10 text-xs sm:text-sm text-orange-400 text-center max-w-xs sm:max-w-sm">
          Didukung oleh Institut Teknologi Bacharuddin Jusuf Habibie
        </p>
      </div>
  
      <!-- KANAN: Menu Ikon + Link -->
      <div class="w-full lg:w-1/2 bg-green-900 text-white flex flex-col justify-center p-6 sm:p-8 lg:p-16 space-y-6 sm:space-y-8 lg:space-y-10 order-2 lg:order-none">
        <div
          class="flex items-center space-x-3 sm:space-x-4 cursor-pointer hover:underline transform hover:translate-x-1 transition-transform duration-200 p-2 rounded-lg hover:bg-white/10"
          @click="goToLogin"
        >
          <img src="/images/icon-1.png" class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0"/>
          <span class="text-lg sm:text-xl lg:text-2xl leading-tight">Sistem Inovasi Daerah</span>
        </div>
        <div
          class="flex items-center space-x-3 sm:space-x-4 cursor-pointer hover:underline transform hover:translate-x-1 transition-transform duration-200 p-2 rounded-lg hover:bg-white/10"
          @click="goToLogin"
        >
          <img src="/images/icon-2.png" class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0"/>
          <span class="text-lg sm:text-xl lg:text-2xl leading-tight">Monitoring Pembangunan</span>
        </div>
        <div
          class="flex items-center space-x-3 sm:space-x-4 cursor-pointer hover:underline transform hover:translate-x-1 transition-transform duration-200 p-2 rounded-lg hover:bg-white/10"
          @click="goToLogin"
        >
          <img src="/images/icon-3.png" class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0"/>
          <span class="text-lg sm:text-xl lg:text-2xl leading-tight">Indikator Kinerja</span>
        </div>
      </div>
    </div>
</template>