<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
];

defineProps<{
  bantuans: Array<{
    id: number
    judul: string
    tgl_dibuat: string
  }>
}>()
</script>

<template>
  <Head title="Bantuan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <h1 class="text-xl font-bold mb-4">Daftar Bantuan</h1>
            <Link href="/bantuan/create" class="bg-blue-500 text-white px-3 py-1 rounded">Tambah</Link>
            <ul class="mt-4 space-y-2">
            <li v-for="b in bantuans" :key="b.id" class="border p-2 rounded">
                <div class="flex justify-between">
                <div>
                    <p class="font-semibold">{{ b.judul }}</p>
                    <p class="text-sm text-gray-500">Tanggal: {{ b.tgl_dibuat }}</p>
                </div>
                <div class="space-x-2">
                    <Link :href="`/bantuan/${b.id}/edit`" class="text-blue-600">Edit</Link>
                    <Link :href="`/bantuan/${b.id}`" method="delete" as="button" class="text-red-600">Hapus</Link>
                </div>
                </div>
            </li>
            </ul>
        </div>
    </AppLayout>
</template>
