<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Link, router } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { id as localeId } from 'date-fns/locale'

// Tipe data lokal untuk pemberitahuan (bisa diganti pakai tipe dari model Laravel via API atau zod schema jika pakai)
interface Pemberitahuan {
  id: number
  judul: string
  isi: string
  tanggal_dibuat: string
  created_at: string
  updated_at: string
}

// Props definition
const props = defineProps<{
  pemberitahuan: Pemberitahuan[]
}>()

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Pemberitahuan',
    href: '/pemberitahuan',
  },
]

// Format tanggal ke bahasa Indonesia
function formatTanggal(tanggal: string): string {
  return format(new Date(tanggal), 'dd MMMM yyyy', { locale: localeId })
}

// Hapus item dengan konfirmasi
function hapus(id: number): void {
  if (confirm('Yakin ingin menghapus pemberitahuan ini?')) {
    router.delete(`/pemberitahuan/${id}`)
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-4">
      <h1 class="text-2xl font-bold">Daftar Pemberitahuan</h1>

      <Link
        href="/pemberitahuan/create"
        class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Tambah Pemberitahuan
      </Link>

      <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-4">Judul</th>
              <th class="p-4">Isi</th>
              <th class="p-4">Tanggal</th>
              <th class="p-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in pemberitahuan"
              :key="item.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="p-4">{{ item.judul }}</td>
              <td class="p-4">{{ item.isi }}</td>
              <td class="p-4">{{ formatTanggal(item.tanggal_dibuat) }}</td>
              <td class="p-4 space-x-2">
                <Link
                  :href="`/pemberitahuan/${item.id}`"
                  class="text-blue-600 hover:underline"
                >
                  Lihat
                </Link>
                <Link
                  :href="`/pemberitahuan/${item.id}/edit`"
                  class="text-yellow-600 hover:underline"
                >
                  Edit
                </Link>
                <button
                  @click="hapus(item.id)"
                  class="text-red-600 hover:underline"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
