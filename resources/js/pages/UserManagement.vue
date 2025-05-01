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
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

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
const showDeleteDialog = ref(false);
const userToDelete = ref<{id: number, name: string} | null>(null);

function goToCreatePage() {
  loadingCreate.value = true;
  router.visit('/usermanagement/create', {
    onFinish: () => (loadingCreate.value = false),
  });
}

function goToEditPage(id: number) {
  router.visit(`/usermanagement/${id}/edit`);
}

function confirmDelete(user: {id: number, name: string}) {
  userToDelete.value = user;
  showDeleteDialog.value = true;
}

function cancelDelete() {
  showDeleteDialog.value = false;
  userToDelete.value = null;
}

function deleteUser() {
  if (!userToDelete.value) return;
  
  deletingId.value = userToDelete.value.id;
  showDeleteDialog.value = false;
  
  router.delete(`/usermanagement/${userToDelete.value.id}`, {
    onFinish: () => {
      deletingId.value = null;
      userToDelete.value = null;
    },
    onError: () => {
      deletingId.value = null;
    }
  });
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
              v-for="(user, index) in props.users.data"
              :key="user.id"
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
                    @click="confirmDelete({id: user.id, name: user.name})"
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

      <!-- Dialog Konfirmasi Hapus -->
      <Dialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Konfirmasi Hapus</DialogTitle>
            <DialogDescription>
              Apakah Anda yakin ingin menghapus pengguna <strong>{{ userToDelete?.name }}</strong>?<br>
              Tindakan ini tidak dapat dibatalkan.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter class="flex space-x-2 justify-end mt-4">
            <Button variant="outline" @click="cancelDelete">
              Batal
            </Button>
            <Button variant="destructive" @click="deleteUser">
              Ya, Hapus
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

    </div>
  </AppLayout>
</template>