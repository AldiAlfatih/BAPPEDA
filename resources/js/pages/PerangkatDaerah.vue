<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Eye } from 'lucide-vue-next';
import { ref } from 'vue';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      skpd: {
        nama_dinas: string;
        nama_operator: string;
        no_dpa: string;
        kode_organisasi: string;
      } | null;
    }>;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Perangkat Daerah', href: '/PerangkatDaerah' },
];

const loadingCreate = ref(false);

function goToCreatePage() {
  loadingCreate.value = true;
  router.visit('/perangkatdaerah/create', {
    onFinish: () => (loadingCreate.value = false),
  });
}

function goToEditPage(id: number) {
  router.visit(route('perangkatdaerah.edit', { id }));
}

function goToShow(id: number) {
  router.visit(route('perangkatdaerah.show', { id }));
}
</script>

<template>
  <Head title="User Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">

      <div class="flex justify-end">
        <Button
          class="flex items-center gap-1 px-3 py-2 text-sm"
          @click="goToCreatePage"
          :disabled="loadingCreate"
        >
          <Plus class="w-4 h-4" />
          <span v-if="loadingCreate">Membuka...</span>
          <span v-else>Tambahkan PD</span>
        </Button>
      </div>

      <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[50px]">No</TableHead>
              <TableHead>Nama Dinas</TableHead>
              <TableHead>Nama Penanggung Jawab</TableHead>
              <TableHead>Nama Kepala Daerah</TableHead>

              <TableHead>No DPA</TableHead>
              <TableHead>Kode Organisasi</TableHead>
              <TableHead class="text-center">Aksi</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <TableRow v-if="props.users.data.length === 0">
              <TableCell colspan="7" class="text-center text-sm py-4 text-gray-500">
                Tidak ada data pengguna.
              </TableCell>
            </TableRow>
            <TableRow
              v-for="(user, index) in props.users.data" :key="user.id"
            >
              <TableCell>{{ index + 1 }}</TableCell>
              <TableCell>{{ user.skpd ? user.skpd.nama_dinas : '-' }}</TableCell>
              <TableCell>{{ user.skpd ? user.skpd.nama_operator : '-' }}</TableCell>
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.skpd ? user.skpd.no_dpa : '-' }}</TableCell>
              <TableCell>{{ user.skpd ? user.skpd.kode_organisasi : '-' }}</TableCell>
              <TableCell>
                <div class="flex items-center justify-center gap-2">

                  <Button
                    class="bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-2"
                    @click="goToEditPage(user.id)"
                  >
                    <Pencil class="w-4 h-4 mr-1" />
                    Edit
                  </Button>

                  <Button
                    class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-2"
                    @click="goToShow(user.id)"
                  >
                    <Eye class="w-4 h-4 mr-1" />
                    Show
                  </Button>


                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
