<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
]

defineProps<{
  bantuans: Array<{
    id: number
    judul: string
    created_at: string
    faqs: Array<{
      deskripsi: string // Mengambil deskripsi dari BantuanFaq
    }>
  }>
}>()

function formatDate(date: string) {
  const options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  };
  return new Date(date).toLocaleDateString('id-ID', options); // Menggunakan format Indonesia (ID)
}
</script>

<template>
  <Head title="Bantuan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <div class="flex justify-end mb-4">
        <Link :href="route('bantuan.create')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
          Tambah
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">No</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Judul</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Deskripsi</th> <!-- Menampilkan Deskripsi -->
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Dibuat</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(b, index) in bantuans" :key="b.id">
              <td class="px-4 py-2 text-sm text-gray-700">{{ index + 1 }}</td>
              <td class="px-4 py-2 text-sm text-gray-800">{{ b.judul }}</td>
              <td class="px-4 py-2 text-sm text-gray-600">{{ b.faqs.length > 0 ? b.faqs[0].deskripsi : 'Tidak ada deskripsi'  }}</td> <!-- Menampilkan Deskripsi -->
              <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(b.created_at) }}</td>
              <td class="px-4 py-2 text-sm text-gray-700 space-x-2">
                <Link :href="route('bantuan.chat', b.id)" class="text-blue-600 hover:underline">Chat</Link>
                <Link :href="route('bantuan.edit', b.id)" class="text-blue-600 hover:underline">Edit</Link>
                <Link :href="route('bantuan.destroy', b.id)" method="delete" as="button" class="text-red-600 hover:underline">Hapus</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>