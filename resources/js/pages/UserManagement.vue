<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
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
      id: number,
      name: string,
      email: string,
      roles: Array<{ name: string }>
    }>
  }
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'User Management', href: '/usermanagement' },
];

const loadingCreate = ref(false);
const deletingId = ref<number | null>(null);

function goToCreatePage() {
  loadingCreate.value = true;
  router.visit('/usermanagement/create', {
    onFinish: () => (loadingCreate.value = false),
  });
}

function goToEditPage(id: number) {
  router.visit(`/usermanagement/${id}/edit`);
}

function deleteUser(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    deletingId.value = id;
    router.delete(`/usermanagement/${id}`, {
      onFinish: () => (deletingId.value = null),
    });
  }
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
          <span v-else>Tambahkan</span>
        </Button>
      </div>

      <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[50px]">No</TableHead>
              <TableHead>Nama</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Role</TableHead>
              <TableHead class="text-center">Aksi</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <TableRow v-if="props.users.data.length === 0">
              <TableCell colspan="5" class="text-center text-sm py-4 text-gray-500">
                Tidak ada data pengguna.
              </TableCell>
            </TableRow>

            <TableRow
              v-for="(user, index) in props.users.data":key="user.id"
            >
              <TableCell>{{ index + 1 }}</TableCell>
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>
                {{ user.roles?.[0]?.name || '-' }}
              </TableCell>
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
                    :disabled="deletingId === user.id"
                    @click="deleteUser(user.id)"
                  >
                    <Trash2 class="w-4 h-4 mr-1" />
                    <span v-if="deletingId === user.id">Menghapus...</span>
                    <span v-else>Hapus</span>
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
