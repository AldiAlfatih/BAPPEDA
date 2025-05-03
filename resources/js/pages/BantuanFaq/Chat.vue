<script setup lang="ts">
import { defineProps, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

const { bantuan, faqs } = defineProps<{
  bantuan: {
    id: number;
    judul: string;
    deskripsi: string;
    created_at: string;
  };
  faqs: Array<{
    id: number;
    deskripsi: string;
  }>;
}>();

const newMessage = ref('');

const sendMessage = async () => {
  if (newMessage.value.trim()) {
    try {
      // Kirim pesan ke server
      const response = await axios.post(/bantuan/${bantuan.id}/chat, {
        message: newMessage.value
      });

      // Tambahkan pesan yang baru dikirim ke daftar pesan
      faqs.push(response.data);
      newMessage.value = ''; // Kosongkan input setelah mengirim
    } catch (error) {
      console.error('Gagal mengirim pesan:', error);
    }
  }
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header dengan Detail Bantuan -->
      <div class="bg-white p-4 shadow rounded-lg">
        <h1 class="text-2xl font-bold">{{ bantuan.judul }}</h1>
        <p class="text-sm text-gray-700">{{ bantuan.deskripsi || 'Tidak ada deskripsi.' }}</p>
        <p class="text-sm text-gray-600">{{ new Date(bantuan.created_at).toLocaleDateString('id-ID') }}</p>
      </div>

      <!-- Tampilan Chat -->
      <div class="bg-white p-4 shadow rounded-lg mt-6">
        <h3 class="text-lg font-semibold">Pesan Chat</h3>
        <div v-if="faqs.length > 0">
          <ul>
            <li v-for="faq in faqs" :key="faq.id" class="text-sm text-gray-700 mt-4">
              <div class="bg-gray-100 p-4 rounded-lg">
                <p>{{ faq.deskripsi || 'Deskripsi FAQ tidak tersedia.' }}</p>
              </div>
            </li>
          </ul>
        </div>
        <div v-else>
          <p class="text-sm text-gray-700">Tidak ada FAQ terkait untuk chat ini.</p>
        </div>

        <!-- Form Input Pesan -->
        <div class="mt-4">
          <textarea class="border p-2 w-full rounded-lg" v-model="newMessage" placeholder="Tulis pesan..."></textarea>
          <button @click="sendMessage" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mt-2">
            Kirim Pesan
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>