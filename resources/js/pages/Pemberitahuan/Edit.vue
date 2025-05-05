<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/vue3'

interface Pemberitahuan {
  id: number
  judul: string
  isi: string
  tanggal_dibuat: string
  created_at: string
  updated_at: string
}

const props = defineProps<{
  pemberitahuan: Pemberitahuan
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Pemberitahuan',
    href: '/pemberitahuan',
  },
  {
    title: 'Edit',
    href: '/pemberitahuan/edit',
  },
]

const form = useForm({
  judul: props.pemberitahuan.judul,
  isi: props.pemberitahuan.isi,
  tanggal_dibuat: props.pemberitahuan.tanggal_dibuat,
})

function submit() {
  form.put(`/pemberitahuan/${props.pemberitahuan.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-4 max-w-2xl">
      <h1 class="text-2xl font-bold">Edit Pemberitahuan</h1>

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
            Perbarui
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
