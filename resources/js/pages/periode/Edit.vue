<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
// import { ref } from 'vue'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  periode: {
    id: number
    tanggal_mulai: string
    tanggal_selesai: string
    tahap: {
      id: number
      tahap: string
    }
    tahun: {
      id: number
      tahun: string
    }
    status: number
  }
  tahaps: Array<{
    id: number
    tahap: string
  }>
  tahuns: Array<{
    id: number
    tahun: string
  }>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Periode', href: '/periode' },
  { title: 'Edit', href: `/periode/${props.periode.id}/edit` },
]

const form = useForm({
  tanggal_mulai: props.periode.tanggal_mulai,
  tanggal_selesai: props.periode.tanggal_selesai,
  tahap_id: props.periode.tahap.id,
  tahun_id: props.periode.tahun.id,
  tahap: props.periode.tahap.tahap, // diperlukan jika edit nama tahap langsung
})

function updatePeriode() {
  form.put(`/periode/${props.periode.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Edit Periode" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-2xl mx-auto mt-6 bg-white p-6 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">Edit Periode</h2>

      <form @submit.prevent="updatePeriode" class="space-y-4">
        <div>
        <label class="block text-sm font-medium text-gray-700">Tahap</label>
        <input
            type="text"
            :value="props.periode.tahap.tahap"
            readonly
            class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm"
        />
        </div>



        <div>
          <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
          <input
            type="date"
            id="tanggal_mulai"
            v-model="form.tanggal_mulai"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          />
          <div v-if="form.errors.tanggal_mulai" class="text-sm text-red-600 mt-1">
            {{ form.errors.tanggal_mulai }}
          </div>
        </div>

        <div>
          <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
          <input
            type="date"
            id="tanggal_selesai"
            v-model="form.tanggal_selesai"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
          />
          <div v-if="form.errors.tanggal_selesai" class="text-sm text-red-600 mt-1">
            {{ form.errors.tanggal_selesai }}
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <button
            type="button"
            @click="router.visit('/periode')"
            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded"
          >
            Batal
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded"
            :disabled="form.processing"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
