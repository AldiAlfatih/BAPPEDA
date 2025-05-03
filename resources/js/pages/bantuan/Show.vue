<template>
    <div class="max-w-4xl mx-auto py-8">
      <h1 class="text-2xl font-bold mb-4">Detail Bantuan</h1>
  
      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-2">{{ bantuan.judul }}</h2>
        <p class="text-sm text-gray-600">Status: <span :class="statusClass">{{ statusLabel }}</span></p>
      </div>
  
      <div class="mt-8">
        <h2 class="text-xl font-semibold mb-4">Daftar Pertanyaan & Balasan</h2>
  
        <div v-if="faqs.length === 0" class="text-gray-500">
          Belum ada pertanyaan.
        </div>
  
        <div v-else class="space-y-4">
          <div
            v-for="faq in faqs"
            :key="faq.id"
            class="bg-gray-100 p-4 rounded border border-gray-300"
          >
            <p class="text-gray-800"><strong>Pertanyaan:</strong> {{ faq.deskripsi || '-' }}</p>
            <p class="text-gray-800 mt-2"><strong>Balasan:</strong> {{ faq.balasan || 'Belum ada balasan' }}</p>
  
            <div v-if="faq.file" class="mt-2">
              <a
                :href="`/${faq.file}`"
                class="text-blue-600 hover:underline"
                target="_blank"
                rel="noopener"
              >
                Lihat File
              </a>
            </div>
  
            <p class="text-xs text-gray-500 mt-2">Dikirim oleh: {{ faq.user?.name || 'Pengguna' }}</p>
          </div>
        </div>
      </div>
  
      <div class="mt-8">
        <Link :href="route('bantuan.index')" class="text-blue-600 hover:underline">← Kembali ke daftar bantuan</Link>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  
  const props = defineProps({
    bantuan: Object,
    faqs: Array,
  })
  
  const statusLabel = computed(() => {
    switch (props.bantuan.status) {
      case 0: return 'Menunggu';
      case 1: return 'Diproses';
      case 2: return 'Selesai';
      default: return 'Tidak Diketahui';
    }
  })
  
  const statusClass = computed(() => {
    switch (props.bantuan.status) {
      case 0: return 'text-yellow-500';
      case 1: return 'text-blue-500';
      case 2: return 'text-green-500';
      default: return 'text-gray-500';
    }
  })
  </script>
  
  <style scoped>
  </style>
  