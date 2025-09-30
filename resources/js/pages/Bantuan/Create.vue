<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Bantuan', href: '/bantuan' },
  { title: 'Tambah', href: '/bantuan/create' },
]

const form = useForm({
  judul: '',
  balasan: '',
  status: 0,
  file: null as File | null,
})

function submit() {
  form.post(route('bantuan.store'), {
    onSuccess: () => {
      form.reset('judul', 'balasan', 'file')
    },
  })
}
</script>

<template>
  <Head title="Tambah Bantuan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white shadow-xl/30 rounded px-8 pt-6 pb-8 mb-4 mt-4 ml-3 mr-3">
      <h1 class="text-xl font-bold mb-4 mt-2 text-gray-600">Tambah Bantuan</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Judul -->
        <div>
          <label class="text-gray-600">Judul</label>
          <input v-model="form.judul" type="text" class="border rounded p-2 w-full" required />
          <div v-if="form.errors.judul" class="text-red-600 text-sm mt-1">{{ form.errors.judul }}</div>
        </div>

        <!-- Pesan -->
        <div>
          <label class="text-gray-600">Pesan</label>
          <textarea v-model="form.balasan" class="border rounded p-2 w-full" rows="5"></textarea>
          <div v-if="form.errors.balasan" class="text-red-600 text-sm mt-1">{{ form.errors.balasan }}</div>
        </div>

        <!-- File -->
        <div class="text-gray-600">
          <label>Lampiran (opsional)</label>
          <input
            type="file"
            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
            @change="(e: Event) => form.file = (e.target as HTMLInputElement).files?.[0] ?? null"
            class="block w-xs text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none"
          />
          <div v-if="form.errors.file" class="text-red-600 text-sm mt-1">{{ form.errors.file }}</div>
        </div>
 
       <!-- Status tersembunyi (default 0) -->
       <input type="hidden" v-model.number="form.status" />

        <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded" :disabled="form.processing">
          Simpan
        </button>
      </form>
    </div>
  </AppLayout>
</template>
