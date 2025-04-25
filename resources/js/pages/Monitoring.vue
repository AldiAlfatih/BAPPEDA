<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Eye } from 'lucide-vue-next';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
const invoices = [
  {
    invoice: '1',
    paymentStatus: 'KODE/URUSAN PEMERINTAHAN',
    paymentMethod: '5 UNSUR PENUNJANG PEMERINTAHAN ANGGARAN APBD',
  },
  {
    invoice: '2',
    paymentStatus: 'KODE ORGANISASI',
    paymentMethod: '5.01.5.05.0.00.25.0000',
  },
  {
    invoice: '3',
    paymentStatus: 'NO.DPA SKPD',
    paymentMethod: 'DPA/A.1/5.01.5.05.0.00.25.0000/001.2024',
  },
  {
    invoice: '4',
    paymentStatus: 'NAMA KEPALA SKPD',
    paymentMethod: 'ZULKARNAEN,ST.,M.Si',
  },
]

const activities = [
  {
    kode: '5.01.01.1.02',
    nama: 'Program Pelayanan Administrasi Perkantoran',
    pagu: 'Rp 1.250.000.000',
    sumber: 'APBD',

  },
  {
    kode: '5.01.02.1.03',
    nama: 'Kegiatan Penunjang Operasional',
    pagu: 'Rp 850.000.000',
    sumber: 'DAK',

  },
  {
    kode: '5.01.03.1.04',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },

  {
    kode: '5.01.03.1.05',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },
  {
    kode: '5.01.03.1.06',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },
  {
    kode: '5.01.03.1.07',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },
  {
    kode: '5.01.03.1.08',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },
  {
    kode: '5.01.03.1.09',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },
  {
    kode: '5.01.03.1.10',
    nama: 'Sub Kegiatan Pemeliharaan Gedung',
    pagu: 'Rp 500.000.000',
    sumber: 'APBN',

  },
]


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Monitoring',
        href: '/monitoring',
    },
];
</script>

<template>
    <Head title="Monitring" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <Table>
                    <TableBody>
                        <TableRow v-for="invoice in invoices" :key="invoice.invoice">
                        <TableCell class="py-1 leading-none font-medium">
                            {{ invoice.invoice }}
                        </TableCell>
                        <TableCell class="py-1 leading-none">
                            {{ invoice.paymentStatus }}
                        </TableCell>
                        <TableCell class="py-1 leading-none">
                            {{ invoice.paymentMethod }}
                        </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <div class="py-1 leading-none">
                    <Button size="xs" class="ml-auto flex items-center gap-1 px-2 py-1 text-sm">
                        <Plus class="w-4 h-4" />
                        Tambahkan
                    </Button>
                </div>
                <Table>
                    <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">
                        Kode
                        </TableHead>
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
                        <TableCell class="py-1 leading-none">{{ activity.target }}
                            <Button class="bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-4 rounded">
                                <Eye class="w-4 h-4" />
                                Lihat Target
                            </Button>
                            <Button class="ml-4 bg-amber-700 hover:bg-amber-800 text-white text-xs px-2 py-4 rounded">
                                <Pencil class="w-4 h-4" />
                                Edit
                            </Button>
                        </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

        </div>
    </AppLayout>
</template>