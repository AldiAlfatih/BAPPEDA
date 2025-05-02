<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

defineProps<{
  faqs: Array<{
    id: number
    deskripsi: string
    file: string | null
    tgl: string
    bantuan: { judul: string }
  }>
}>()
</script>

<template>
  <Head title="FAQ Bantuan" />
  <div>
    <h1 class="text-xl font-bold mb-4">Daftar FAQ Bantuan</h1>
    <Link href="/bantuanfaq/create" class="bg-blue-500 text-white px-3 py-1 rounded">Tambah</Link>
    <ul class="mt-4 space-y-3">
      <li v-for="faq in faqs" :key="faq.id" class="border p-3 rounded">
        <p class="font-bold">{{ faq.bantuan.judul }}</p>
        <p>{{ faq.deskripsi }}</p>
        <p v-if="faq.file">
          <a :href="`/storage/${faq.file}`" target="_blank" class="text-blue-600">Lihat File</a>
        </p>
        <p class="text-sm text-gray-500">Tanggal: {{ faq.tgl }}</p>
        <div class="space-x-2 mt-2">
          <Link :href="`/bantuanfaq/${faq.id}/edit`" class="text-blue-600">Edit</Link>
          <Link :href="`/bantuanfaq/${faq.id}`" method="delete" as="button" class="text-red-600">Hapus</Link>
        </div>
      </li>
    </ul>
  </div>
</template>
