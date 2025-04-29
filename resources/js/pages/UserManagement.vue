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

// Terima props dari server
const props = defineProps<{
  users: {
    data: Array<{
      id: number,
      name: string,
      email: string,
      roles: Array<{
        name: string
      }>,
    }>
  }
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'User Management',
    href: '/usermanagement',
  },
];

// Navigasi ke halaman create
function goToCreatePage() {
  router.visit('/usermanagement/create');
}

// Navigasi ke halaman edit
function goToEditPage(id: number) {
  router.visit(`/usermanagement/${id}/edit`);
}

// Hapus user
const deletingId = ref<number | null>(null);

function deleteUser(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    deletingId.value = id;
    router.delete(`/usermanagement/${id}`, {
      onFinish: () => {
        deletingId.value = null;
      }
    });
  }
}
</script>

<template>
  <Head title="User Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">

        <div class="flex justify-end py-4">
          <Button size="sm" class="flex items-center gap-2" @click="goToCreatePage">
            <Plus class="w-4 h-4" />
            Tambahkan
          </Button>
        </div>

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
            <TableRow v-for="(user, index) in props.users.data" :key="user.id">
              <TableCell>{{ index + 1 }}</TableCell>
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>{{ (user.roles && user.roles.length > 0) ? user.roles[0].name : '-' }}</TableCell>
              <TableCell>
                <div class="flex items-center justify-center gap-2">
                  <Button class="bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-4 rounded" @click="goToEditPage(user.id)">
                    <Pencil class="w-4 h-4" />
                    Edit
                  </Button>
                  <Button
                    :disabled="deletingId === user.id"
                    class="ml-2 bg-amber-700 hover:bg-amber-800 text-white text-xs px-2 py-4 rounded"
                    @click="deleteUser(user.id)"
                  >
                    <Trash2 class="w-4 h-4" />
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
