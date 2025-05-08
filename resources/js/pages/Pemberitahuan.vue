<template>
    <Head title="Dashboard" />
  
    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
          <!-- Table Placeholder -->
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-left">
              <thead>
                <tr>
                  <th class="px-4 py-2">KODE</th>
                  <th class="px-4 py-2">PROGRAM/ KEGIATAN/ SUB KEGIATAN</th>
                  <th class="px-4 py-2">POKOK (RP)</th>
                  <th class="px-4 py-2">PARSIAL (RP)</th>
                  <th class="px-4 py-2">PERUBAHAN (RP)</th>
                  <th class="px-4 py-2">SUMBER DANA</th>
                  <th class="px-4 py-2">KINERJA FISIK (%)</th>
                  <th class="px-4 py-2">TRIWULAN 1 KEUANGAN (RP)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2 bg-yellow-200">URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR</td>
                  <td class="px-4 py-2">216.551.782.886,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">180.512</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">180.512</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">1 01</td>
                  <td class="px-4 py-2 bg-yellow-200">URUSAN PEMERINTAHAN BIDANG PENDIDIKAN</td>
                  <td class="px-4 py-2">216.551.782.886,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">211.129.110.516,00</td>
                  <td class="px-4 py-2">180.512</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">180.512</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">1 01 01</td>
                  <td class="px-4 py-2 bg-green-200">PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA</td>
                  <td class="px-4 py-2">170.351.678.486,00</td>
                  <td class="px-4 py-2">164.871.072.942,00</td>
                  <td class="px-4 py-2">164.871.072.942,00</td>
                  <td class="px-4 py-2">35.71</td>
                  <td class="px-4 py-2">35.71</td>
                  <td class="px-4 py-2">168.645.000</td>
                </tr>
                <tr>
                  <td class="px-4 py-2">1 01 01 2.01</td>
                  <td class="px-4 py-2 bg-yellow-200">Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah</td>
                  <td class="px-4 py-2">6.300.000</td>
                  <td class="px-4 py-2">6.300.000</td>
                  <td class="px-4 py-2">6.300.000</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">100%</td>
                  <td class="px-4 py-2">6.300.000</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue';
  import { type BreadcrumbItem } from '@/types';
  import { Head } from '@inertiajs/vue3';
  
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
  ];
  </script>
  