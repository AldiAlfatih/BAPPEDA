<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps<{
  kodenomenklatur: Array<{
    id: number,
    nomor_kode: string,
    nomenklatur: string,
    jenis_kode: string,
    detail: {
      urusan?: string,
      bidang_urusan?: string,
      program?: string,
      kegiatan?: string,
      subkegiatan?: string,
    } | null
  }>
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Kode Nomenklatur', href: '/KodeNomenklatur' },
];

function goToCreatePage() {
  router.visit('/kodenomenklatur/create');
}


function goToEditPage(id: number) {
  router.visit(`/kodenomenklatur/${id}/edit`);
}

function deleteKode(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    router.delete(`/kodenomenklatur/${id}`);
  }
}
</script>

<template>
  <Head title="Kode Nomenklatur" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <div class="flex justify-end">
        <Button class="flex items-center gap-1" @click="goToCreatePage">
          <Plus class="w-4 h-4" />
          Tambahkan
        </Button>
      </div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>No</TableHead>
            <TableHead>Kode</TableHead>
            <TableHead>Nomenklatur</TableHead>
            <TableHead>Jenis</TableHead>
            <TableHead>Urusan</TableHead>
            <TableHead>Bidang Urusan</TableHead>
            <TableHead>Program</TableHead>
            <TableHead>Kegiatan</TableHead>
            <TableHead>Subkegiatan</TableHead>
            <TableHead>Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="(item, index) in props.kodenomenklatur" :key="item.id">
            <TableCell>{{ index + 1 }}</TableCell>
            <TableCell>{{ item.nomor_kode }}</TableCell>
            <TableCell>{{ item.nomenklatur }}</TableCell>
            <TableCell>{{ item.jenis_kode }}</TableCell>
            <TableCell>{{ item.detail?.urusan || '-' }}</TableCell>
            <TableCell>{{ item.detail?.bidang_urusan || '-' }}</TableCell>
            <TableCell>{{ item.detail?.program || '-' }}</TableCell>
            <TableCell>{{ item.detail?.kegiatan || '-' }}</TableCell>
            <TableCell>{{ item.detail?.subkegiatan || '-' }}</TableCell>
            <TableCell class="flex gap-2">
              <Button variant="outline" size="sm" @click="goToEditPage(item.id)">
                <Pencil class="w-4 h-4" />
              </Button>
              <Button variant="destructive" size="sm" @click="deleteKode(item.id)">
                <Trash2 class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>
