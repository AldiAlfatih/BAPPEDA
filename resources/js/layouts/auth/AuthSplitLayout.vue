<script setup lang="ts">
<<<<<<< HEAD
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
=======
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';
defineProps<{
  title?: string;
  description?: string;
>>>>>>> bd447f1 (database pertama)
}>();
</script>

<template>
<<<<<<< HEAD
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col bg-muted p-10 text-white dark:border-r lg:flex">
            <div class="absolute inset-0 bg-zinc-900" />
            <Link :href="route('home')" class="relative z-20 flex items-center text-lg font-medium">
                <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
                {{ name }}
            </Link>
            <div v-if="quote" class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-sm text-neutral-300">{{ quote.author }}</footer>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
=======

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
    <div class="flex items-center justify-center bg-green-900 px-8 py-12 sm:px-0">
      <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <div class="text-center">
          <h1 v-if="title" class="text-2xl text-black font-bold">{{ title }}</h1>
        </div>
        <slot />
      </div>
    </div>
  </div>

</template>
>>>>>>> bd447f1 (database pertama)
