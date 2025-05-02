<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps<{
  kodeNomenklatur: Array<{
    id: number,
    nomor_kode: string,
    nomenklatur: string,
    jenis_nomenklatur: number,
    bidang_urusan?: { bidang_urusan: string } | null,
    program?: { program: string } | null,
    kegiatan?: { kegiatan: string } | null,
    subkegiatan?: { subkegiatan: string } | null
  }>
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Kode Nomenklatur', href: '/kodenomenklatur' },
];

function goToCreatePage() {
  console.log('Tombol Tambahkan diklik!');
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

function labelJenisNomenklatur(value: number): string {
  switch (value) {
    case 0: return 'Urusan';
    case 1: return 'Bidang Urusan';
    case 2: return 'Program';
    case 3: return 'Kegiatan';
    case 4: return 'Subkegiatan';
    default: return 'Tidak Dikenal';
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
            <TableHead>Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="(kode, index) in props.kodeNomenklatur" :key="kode.id">
            <TableCell>{{ index + 1 }}</TableCell>
            <TableCell>{{ kode.nomor_kode }}</TableCell>
            <TableCell>{{ kode.nomenklatur }}</TableCell>
            <TableCell>{{ labelJenisNomenklatur(kode.jenis_nomenklatur) }}</TableCell>
            <TableCell>
              <Button class="bg-green-600 hover:bg-green-700 text-white text-xs" @click="goToEditPage(kode.id)">
                <Pencil class="w-4 h-4" />
                Edit
              </Button>
              <Button class="ml-2 bg-amber-700 hover:bg-amber-800 text-white text-xs" @click="deleteKode(kode.id)">
                <Trash2 class="w-4 h-4" />
                Hapus
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>
