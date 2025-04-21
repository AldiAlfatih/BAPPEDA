<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';
defineProps<{
  title?: string;
  description?: string;
}>();
</script>

<template>

  <div class="relative grid h-screen lg:grid-cols-2">
    <!-- BAGIAN KIRI -->
    <div class="relative flex flex-col items-center justify-center bg-[#E6E4E4] text-black p-10">
      <img src="/images/logo-parepare.png" alt="Logo Kota Parepare" class="w-48 mb-6" />
      <h1 class="text-3xl text-black font-bold">PANRITA BAPPEDA</h1>
      <h2 class="text-lg text-black">KOTA PAREPARE</h2>
      <p class="text-sm text-orange-400 mt-10 text-center max-w">
        Didukung oleh Institut Teknologi Bacharuddin Jusuf Habibie
      </p>
    </div>

    <!-- BAGIAN KANAN -->
    <div class="flex items-center justify-center bg-[#094126] px-8 py-12 sm:px-0">
      <div class="mx-auto w-full max-w-md space-y-6">
        <div class="text-center">
          <h1 v-if="title" class="text-2xl font-semibold">{{ title }}</h1>
          <p v-if="description" class="text-sm text-gray-500 mt-2">{{ description }}</p>
        </div>
        <slot />
      </div>
    </div>
  </div>

</template>
