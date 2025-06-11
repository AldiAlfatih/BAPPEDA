<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { computed, ref, watch } from 'vue'

interface User {
  id: number
  name: string
  role: string // Menambahkan properti role untuk memeriksa peran pengguna
}

interface Faq {
  id: number
  balasan: string | null
  file: string | null
  created_at: string
  user?: User | null
}

interface Bantuan {
  id: number
  judul: string
  status: number
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Bantuan', href: '/bantuan' },
  { title: 'Chating', href: '/bantuan/chat' },
]

const props = defineProps<{
  bantuan: Bantuan
  faqs: Faq[]
}>()

const auth = usePage().props.auth as { user: User }

const userId = computed(() => auth.user?.id)

// Mengecek apakah pengguna adalah admin
const isAdmin = computed(() => auth.user?.role === 'admin' || auth.user?.role === 'operator')

const form = useForm({
  balasan: '',
  file: null as File | null,
})

const errorMessage = ref<string>('')

const sendBalasan = () => {
  if (form.balasan.trim() === '') {
    // Jika form.balasan kosong, tampilkan pesan error
    errorMessage.value = 'Harap isi balasan sebelum mengirim.'
    return // Jangan lanjutkan jika kosong
  }

  form.post(route('bantuan.chat.send', props.bantuan.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      form.clearErrors()
      errorMessage.value = ''
    },
  })
}

watch(() => form.balasan, (newValue) => {
  if (newValue.trim() !== '') {
    errorMessage.value = '' // Hapus pesan error jika balasan sudah diisi
  }
})

const formatDate = (dateStr: string) => {
  return format(new Date(dateStr), 'dd MMM yyyy HH:mm')
}

const updateChatStatus = () => {
  form.post(route('bantuan.chat.selesai', props.bantuan.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload();
    },
  });
};

const navigateBack = () => {
  window.location.href = '/bantuan';
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-2 ml-6 mr-6 mt-5 w-auto border rounded-md ">
      <h1 class="text-2xl font-bold text-gray-600">Percakapan Bantuan</h1>
      <h1 class="text-xl font-semibold text-gray-500">{{ bantuan.judul }}</h1>
    </div>

    <!-- Daftar Chat -->
    <div class="pr-6 p-6 pb-2 flex items-center justify-center">
      <div class="w-full border rounded-md">
        <div class="space-y-4 mb-6">
          <div
            v-for="faq in faqs"
            :key="faq.id"
            class="flex"
            :class="faq.user?.id === userId ? 'justify-end' : 'justify-start'"
          >
            <div
              class="max-w-[75%] p-4 rounded-lg shadow-sm"
              :class="faq.user?.id === userId ? 'bg-blue-100 text-right' : 'bg-gray-100 text-left'"
            >
              <p class="font-semibold text-lg mb-1">
                {{ faq.user?.name ?? 'Pengguna' }}
              </p>
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
    <div class="p-6 " v-if="bantuan.status !== 2">
      <input
          type="file"
          @change="(e: Event) => form.file = (e.target as HTMLInputElement).files?.[0] ?? null"
          class="block w-xs text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none"
        />


      <!-- Form Balasan -->
      <form @submit.prevent="sendBalasan" class="space-y-4 mt-3" enctype="multipart/form-data">
        <p v-if="errorMessage" class="text-red-500 text-base font-semibold mb-1">{{ errorMessage }}</p>
        <textarea
          v-model="form.balasan"
          class="w-full p-3 border rounded-md focus:outline-none focus:ring"
          rows="4"
          placeholder="Tulis balasan kamu di sini..."

        ></textarea>

        <!-- Tombol Selesaikan Chat hanya untuk admin -->
        <button
          v-if="isAdmin && bantuan.status !== 2"
          type="button"
          class="px-2 py-1 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
          @click="updateChatStatus">Selesaikan Chat</button>

        <!-- Tombol Kirim -->
        <div class="text-right">
          <button
            type="button"
            class="px-4 py-2 mr-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
            @click="navigateBack">Kembali</button>

          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-7 py-2 mr-2 text-sm font-medium rounded-md"
            :disabled="form.processing"
          >
            Kirim
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
