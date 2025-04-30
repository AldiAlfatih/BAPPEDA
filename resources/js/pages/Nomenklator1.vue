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

// TERIMA PROPS DARI SERVER
const props = defineProps<{
  nomenklatur: Array<{
    id: number,
    nomor_kode: string,
    nomenklatur: string,
    urusan: string,
    bidang_urusan: string,
    program: string,
    kegiatan: string,
    subkegiatan: string,
    sumber: string,
    target: string,
  }>
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Nomenklatur',
    href: '/nomenklatur',
  },
];

// Fungsi untuk navigasi ke halaman create
function goToCreatePage() {
  router.visit('/nomenklatur/create');
}

// Fungsi untuk navigasi ke halaman edit dengan ID
function goToEditPage(id: number) {
    router.visit(`/nomenklatur/${id}/edit`);
}

// Fungsi untuk menghapus data
function deleteNomenklatur(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    // Kirim permintaan DELETE ke server
    router.delete(`/nomenklatur/${id}`)
  };
}
</script>


<template>
  <Head title="Nomenklatur" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
        <div class="py-1 leading-none mt-2 mr-2">

          <Button size="sm" class="ml-auto flex items-center gap-1 px-2 py-1 text-sm" @click="goToCreatePage">
            <Plus class="w-4 h-4" />
            Tambahkan
          </Button>
        </div>

        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[100px]">No</TableHead>
              <TableHead>Kode</TableHead>
              <TableHead>Urusan</TableHead>
              <TableHead>Bidang Urusan</TableHead> 
              <TableHead>Program</TableHead>
              <TableHead>Kegiatan</TableHead>
              <TableHead>Sub Kegiatan</TableHead> 
              <TableHead>Aksi</TableHead> 
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="(kode, index) in props.nomenklatur" :key="kode.id">
              <TableCell class="py-1 leading-none font-medium">{{ index + 1 }}</TableCell>
              <TableCell class="py-1 leading-none">{{ kode.nomor_kode }}</TableCell>
              <TableCell class="py-1 leading-none">{{ kode.urusan }}</TableCell> <!-- Menampilkan kolom urusan -->
              <TableCell class="py-1 leading-none">{{ kode.bidang_urusan }}</TableCell> <!-- Menampilkan kolom bidang_urusan -->
              <TableCell class="py-1 leading-none">{{ kode.program }}</TableCell> <!-- Menampilkan kolom program -->
              <TableCell class="py-1 leading-none">{{ kode.kegiatan }}</TableCell> <!-- Menampilkan kolom kegiatan -->
              <TableCell class="py-1 leading-none">{{ kode.subkegiatan }}</TableCell> <!-- Menampilkan kolom subkegiatan -->
              <TableCell class="py-1 leading-none">
                <Button class="bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-4 rounded" @click="goToEditPage(kode.id)">
                  <Pencil class="w-4 h-4" />
                  Edit
                </Button>
                <Button class="ml-2 bg-amber-700 hover:bg-amber-800 text-white text-xs px-2 py-4 rounded" @click="deleteNomenklatur(kode.id)">
                  <Trash2 class="w-4 h-4" />
                  Hapus
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>