<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { useForm, usePage } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { computed } from 'vue'

interface User {
  id: number
  name: string
}

interface Faq {
  id: number
  deskripsi: string | null
  balasan: string | null
  file: string | null
  created_at: string
  user?: User | null
}

interface Bantuan {
  id: number
  judul: string
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Bantuan', href: '/bantuan' },
  { title: 'Chat', href: '#' },
]

const props = defineProps<{
  bantuan: Bantuan
  faqs: Faq[]
}>()

const auth = usePage().props.auth as { user: User }
const userId = computed(() => auth.user?.id)

const form = useForm({
  balasan: '',
  file: null as File | null,
})

const sendBalasan = () => {
  form.post(route('bantuan.chat.send', props.bantuan.id), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      form.clearErrors()
    },
  })
}

const formatDate = (dateStr: string) => {
  return format(new Date(dateStr), 'dd MMM yyyy HH:mm')
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Percakapan Bantuan: {{ bantuan.judul }}</h1>

      <!-- Chat messages -->
      <div class="space-y-4 mb-8">
        <div
          v-for="faq in faqs"
          :key="faq.id"
          class="flex"
          :class="faq.user?.id === userId ? 'justify-end' : 'justify-start'"
        >
          <div
            class="max-w-xs md:max-w-md lg:max-w-xl p-4 rounded-lg shadow-sm"
            :class="faq.user?.id === userId ? 'bg-blue-100 text-right' : 'bg-gray-100 text-left'"
          >
            <p class="font-semibold text-sm mb-1">
              {{ faq.user?.name ?? 'Pengguna' }}
            </p>
            <p class="whitespace-pre-wrap">{{ faq.deskripsi || faq.balasan }}</p>

            <p v-if="faq.file" class="mt-2 text-sm text-blue-600">
              <a :href="'/' + faq.file" target="_blank" class="underline">Lihat Lampiran</a>
            </p>

            <p class="text-xs text-gray-500 mt-1">{{ formatDate(faq.created_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Reply form -->
      <form @submit.prevent="sendBalasan" class="space-y-4" enctype="multipart/form-data">
        <textarea
          v-model="form.balasan"
          class="w-full p-3 border rounded-md focus:outline-none focus:ring"
          rows="4"
          placeholder="Tulis balasan kamu di sini..."
        ></textarea>

        <input
          type="file"
          @change="(e: Event) => form.file = (e.target as HTMLInputElement).files?.[0] ?? null"
          class="block text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none"
        />

        <div class="flex justify-end mt-8 gap-4">
            <button 
                type="button" 
                class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                @click="$inertia.visit('/bantuan')"
                >
                Kembali
            </button>
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md "
            :disabled="form.processing"
          >
            Kirim
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
