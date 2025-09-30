<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { type BreadcrumbItem } from '@/types'

interface User { id: number; name: string; role?: string }
interface Faq { id: number; balasan: string | null; file: string | null; created_at: string; user?: User | null }
interface Bantuan { id: number; judul: string; status: number }

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Bantuan', href: '/bantuan' },
  { title: 'Chat', href: '/bantuan/chat' },
]

const props = defineProps<{ bantuan: Bantuan; faqs: Faq[] }>()

const auth = usePage().props.auth as { user: User }
const userId = computed(() => auth.user?.id)
const isAdmin = computed(() => auth.user?.role === 'admin' || auth.user?.role === 'operator')

const form = useForm({ balasan: '', file: null as File | null })

function sendBalasan() {
  if (!form.balasan || !form.balasan.trim()) return
  form.post(route('bantuan.chat.send', props.bantuan.id), {
    preserveScroll: true,
    onSuccess: () => form.reset('balasan', 'file'),
  })
}

function updateChatStatus() {
  router.post(route('bantuan.chat.selesai', props.bantuan.id), {}, { preserveScroll: true, onSuccess: () => router.reload() })
}

function formatDate(dateStr: string) {
  return new Date(dateStr).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

function navigateBack() {
  window.location.href = '/bantuan'
}
</script>

<template>
  <Head :title="`Chat - ${props.bantuan?.judul ?? ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 ml-6 mr-6 mt-5 w-auto border rounded-md">
      <h1 class="text-2xl font-bold text-gray-600">Percakapan Bantuan</h1>
      <h2 class="text-xl font-semibold text-gray-500">{{ props.bantuan.judul }}</h2>
    </div>

    <!-- Daftar Chat -->
    <div class="px-6 pt-6">
      <div class="w-full border rounded-md p-4 bg-white">
        <div class="space-y-4">
          <div v-for="faq in props.faqs" :key="faq.id" class="flex" :class="faq.user?.id === userId ? 'justify-end' : 'justify-start'">
            <div class="max-w-[75%] p-3 rounded-lg shadow-sm" :class="faq.user?.id === userId ? 'bg-blue-100 text-right' : 'bg-gray-100 text-left'">
              <p class="font-semibold text-sm mb-1">{{ faq.user?.name ?? 'Pengguna' }}</p>
              <p class="whitespace-pre-line text-sm">{{ faq?.balasan }}</p>
              <p v-if="faq.file" class="mt-2 text-sm text-blue-600">
                <a :href="'/' + faq.file" target="_blank" class="underline">Lihat Lampiran</a>
              </p>
              <p class="text-xs text-gray-500 mt-1">{{ formatDate(faq.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Balasan dan Tombol Selesaikan Chat -->
    <div class="p-6" v-if="props.bantuan.status !== 2">
      <form @submit.prevent="sendBalasan" class="space-y-3" enctype="multipart/form-data">
        <input
          type="file"
          accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
          @change="(e: Event) => form.file = (e.target as HTMLInputElement).files?.[0] ?? null"
          class="block w-xs text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none"
        />

        <textarea
          v-model="form.balasan"
          class="w-full p-3 border rounded-md focus:outline-none focus:ring"
          rows="4"
          placeholder="Tulis balasan kamu di sini..."
        ></textarea>

        <div class="flex justify-between items-center">
          <button
            v-if="isAdmin && props.bantuan.status !== 2"
            type="button"
            class="px-2 py-1 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50"
            @click="updateChatStatus"
          >Selesaikan Chat</button>

          <div class="ml-auto">
            <button type="button" class="px-4 py-2 mr-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50" @click="navigateBack">Kembali</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 text-sm font-medium rounded-md" :disabled="form.processing">Kirim</button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

