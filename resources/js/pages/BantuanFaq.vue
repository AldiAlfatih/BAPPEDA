<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'BantuanFaq',
    href: '/bantuanfaq',
  },
];

const props = defineProps<{
  faqs: Array<{
    id: number
    deskripsi: string
    file: string | null
    tgl: string
    bantuan: { judul: string }
  }>
}>()

// Menghasilkan URL absolut ke file
const fileUrl = (file: string) => {
  return file ? `${window.location.origin}/${file}` : '#'
}

// Mendapatkan nama file untuk download
const getFileName = (file: string) => {
  return file.split('/').pop() ?? 'downloaded_file'
}
</script>

<template>
  <Head title="Bantuan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex justify-end">
                <Link
                    href="/bantuanfaq/create"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                    Tambah
                </Link>
            </div>
            <ul class="space-y-3">
            <li v-for="faq in faqs" :key="faq.id" class="border p-3 rounded">
                <p class="font-bold mt-2 mb-2">{{ faq.bantuan.judul }}</p>
                <p>{{ faq.deskripsi }}</p>
                <p v-if="faq.file">
                    <span class="flex items-center space-x-2 mt-2 mb-2">
                    <a :href="fileUrl(faq.file)" target="_blank" class="text-blue-600 underline">Preview</a>
                    <span>|</span>
                    <a :href="fileUrl(faq.file)" :download="getFileName(faq.file)" class="text-green-600 underline">Download</a>
                    </span>
                </p>
                <p class="text-sm text-gray-500">Tanggal: {{ faq.tgl }}</p>
                <div class="space-x-2 mt-2">
                <Link :href="`/bantuanfaq/${faq.id}/edit`" class="text-blue-600">Edit</Link>
                <span>|</span>
                <Link :href="`/bantuanfaq/${faq.id}`" method="delete" as="button" class="text-red-600">Hapus</Link>
                </div>
            </li>
            </ul>
        </div>
    </AppLayout>
</template>
