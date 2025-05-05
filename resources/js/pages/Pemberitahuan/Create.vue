<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/vue3'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Pemberitahuan',
    href: '/pemberitahuan',
  },
  {
    title: 'Pemberitahuan Tambah',
    href: '/pemberitahuan/create',
  },
]

const form = useForm({
  judul: '',
  isi: '',
  tanggal_dibuat: new Date().toISOString().slice(0, 10), // YYYY-MM-DD
})

function submit() {
  form.post('/pemberitahuan', {
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-4 max-w-2xl">
      <h1 class="text-2xl font-bold">Tambah Pemberitahuan</h1>

      <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded shadow">
        <div>
          <label class="block font-semibold mb-1">Judul</label>
          <input
            v-model="form.judul"
            type="text"
            class="w-full border rounded px-3 py-2"
          />
          <div v-if="form.errors.judul" class="text-red-600 text-sm">
            {{ form.errors.judul }}
          </div>
        </div>

        <div>
          <label class="block font-semibold mb-1">Isi</label>
          <textarea
            v-model="form.isi"
            class="w-full border rounded px-3 py-2"
            rows="5"
          />
          <div v-if="form.errors.isi" class="text-red-600 text-sm">
            {{ form.errors.isi }}
          </div>
        </div>

        <div>
          <label class="block font-semibold mb-1">Tanggal Dibuat</label>
          <input
            v-model="form.tanggal_dibuat"
            type="date"
            class="w-full border rounded px-3 py-2"
          />
          <div v-if="form.errors.tanggal_dibuat" class="text-red-600 text-sm">
            {{ form.errors.tanggal_dibuat }}
          </div>
        </div>

        <div class="flex justify-end space-x-2">
          <Link
            href="/pemberitahuan"
            class="px-4 py-2 rounded border text-gray-700 hover:bg-gray-100"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
            :disabled="form.processing"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
