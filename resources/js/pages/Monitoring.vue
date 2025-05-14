<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import { BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const props = defineProps<{
  user: {
    name: string;
    skpd: {
      nama_skpd: string;
      no_dpa: string;
      kode_organisasi: string;
    } | null;
  } | null;
}>();

interface User {
  id: number
  name: string
  role: string 
}

const auth = usePage().props.auth as { user: User }
const isPD = computed(() => auth.user?.role === 'perangkat_daerah')
const isAdmin = computed(() => auth.user?.role === 'admin')

// Activities data
const activities = [
  { kode: '5.01.01.1.02', nama: 'Program Pelayanan Administrasi Perkantoran', pagu: 'Rp 1.250.000.000', sumber: 'APBD' },
  { kode: '5.01.02.1.03', nama: 'Kegiatan Penunjang Operasional', pagu: 'Rp 850.000.000', sumber: 'DAK' },
  { kode: '5.01.03.1.04', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
  { kode: '5.01.03.1.05', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
  { kode: '5.01.03.1.06', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
  { kode: '5.01.03.1.07', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
  { kode: '5.01.03.1.08', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
  { kode: '5.01.03.1.09', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
  { kode: '5.01.03.1.10', nama: 'Sub Kegiatan Pemeliharaan Gedung', pagu: 'Rp 500.000.000', sumber: 'APBN' },
];

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Monitoring', href: '/monitoring' }
];
</script>


<template>
  <Head title="Monitoring" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
        
        <!-- User Information -->
        <table v-if="isPD" class="min-w-full">
          <tbody>
            <tr>
              <td class="px-4">Nama</td>
              <td class="py-1 leading-none">{{ props.user?.name || '-' }}</td>
            </tr>
            <tr>
              <td class="px-4">Nama SKPD</td>
              <td class="py-1 leading-none">{{ props.user?.skpd?.nama_skpd || '-' }}</td>
            </tr>
            <tr>
              <td class="px-4">NO.DPA SKPD</td>
              <td class="py-1 leading-none">{{ props.user?.skpd?.no_dpa || '-' }}</td>
            </tr>
            <tr>
              <td class="px-4">Kode Organisasi</td>
              <td class="py-1 leading-none">{{ props.user?.skpd?.kode_organisasi || '-' }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Button to add more data -->
        <div v-if="isAdmin" class="py-1 leading-none">
          <Button size="sm" class="ml-auto flex items-center gap-1 px-2 py-1 text-sm">
            <Plus class="w-4 h-4" />
            Tambahkan
          </Button>
        </div>

        <!-- Table for Activities -->
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[100px]">Kode</TableHead>
              <TableHead>PROGRAM/KEGIATAN/ SUB KEGIATAN</TableHead>
              <TableHead>PAGU ANGGARAN APBD</TableHead>
              <TableHead>SUMBER DANA</TableHead>
              <TableHead>TARGET</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="activity in activities" :key="activity.kode">
              <TableCell class="py-1 leading-none font-medium">{{ activity.kode }}</TableCell>
              <TableCell class="py-1 leading-none">{{ activity.nama }}</TableCell>
              <TableCell class="py-1 leading-none">{{ activity.pagu }}</TableCell>
              <TableCell class="py-1 leading-none">{{ activity.sumber }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>

