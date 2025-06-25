<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import TabelListSKPD from '@/components/data/TabelListSKPD.vue';
import { Button } from '@/components/ui/button';
import {
  Plus,
  Pencil,
  Eye,
  Search,
  ChevronLeft,
  ChevronRight,
  ArrowUpDown,
  FileText,
  Info,
  Building2,
  User,  
  HandCoins,
} from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,

} from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      user_detail?: {
        nip?: string;
      } | null;
      nama_dinas: string | null;
      operator_name: string | null;
      kepala_name: string | null;
      kode_organisasi: string | null;
    }>;
  };
  enabledParsialUsers?: number[];
  triwulan4Aktif?: {
    id: number;
    tahap: {
      id: number;
      tahap: string;
    };
    tahun: {
      id: number;
      tahun: string;
    };
  } | null;
  isBudgetChangeAvailable?: boolean;
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Manajemen Anggaran', href: '/manajemenanggaran' },
];

// State for anggaran operations
const isProcessing = ref(false);

// Computed untuk mengecek apakah Triwulan 4 aktif
const isTriwulan4Active = computed(() => {
  return props.isBudgetChangeAvailable && props.triwulan4Aktif;
});

// Computed untuk pesan perubahan anggaran
const budgetChangeMessage = computed(() => {
  if (isTriwulan4Active.value) {
    return `Periode Triwulan 4 tahun ${props.triwulan4Aktif?.tahun?.tahun} sedang aktif. Klik tombol untuk mengaktifkan mode perubahan anggaran untuk semua perangkat daerah.`;
  }
  return 'Periode Triwulan 4 belum dibuka. Perubahan anggaran hanya dapat dilakukan pada periode Triwulan 4.';
});

function goToShowPage(id: number) {
  router.visit(route('manajemenanggaran.show', { id }));
}

async function handleAnggaranPerubahan() {
  if (!isTriwulan4Active.value) {
    alert('Periode Triwulan 4 belum dibuka. Perubahan anggaran hanya dapat dilakukan ketika periode Triwulan 4 aktif.');
    return;
  }

  isProcessing.value = true;
  
  try {
    const confirmed = confirm(
      `⚠️ KONFIRMASI PERUBAHAN ANGGARAN\n\n` +
      `Apakah Anda yakin ingin mengaktifkan mode perubahan anggaran untuk SEMUA perangkat daerah?\n\n` +
      `✅ Periode: Triwulan 4 Tahun ${props.triwulan4Aktif?.tahun?.tahun}\n` +
      `✅ Total Perangkat Daerah: ${props.users.data.length}\n\n` +
      `Mode ini akan memungkinkan semua perangkat daerah untuk melakukan perubahan anggaran pada periode Triwulan 4.\n\n` +
      `Klik OK untuk melanjutkan atau Cancel untuk membatalkan.`
    );

    if (!confirmed) {
      isProcessing.value = false;
      return;
    }

    // Call backend to enable budget change for all departments
    const response = await fetch('/manajemenanggaran/enable-budget-change-all', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        confirm: true
      })
    });

    const data = await response.json();

    if (data.success) {
      alert(
        `✅ MODE PERUBAHAN ANGGARAN BERHASIL DIAKTIFKAN!\n\n` +
        `${data.message}\n\n` +
        `📊 Total perangkat daerah yang dapat melakukan perubahan: ${data.enabled_count}\n\n` +
        `Sekarang Anda dapat menggunakan tombol 🔍 (Binoculars) untuk mengakses mode perubahan anggaran pada setiap perangkat daerah.`
      );
      
      // Refresh page to show updated state
      window.location.reload();
    } else {
      alert(`❌ Gagal mengaktifkan mode perubahan anggaran:\n\n${data.message}`);
    }

  } catch (error) {
    console.error('Error enabling budget change:', error);
    alert('❌ Terjadi kesalahan saat mengaktifkan mode perubahan anggaran. Silakan coba lagi.');
  } finally {
    isProcessing.value = false;
  }
}

</script>

<template>
  <Head title="Monitoring" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">
      <!-- Header dengan judul dan action -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manajemen Anggaran Perangkat Daerah</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data Manajemen Anggaran Perangkat Daerah</p>
        </div>
        <div>
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger asChild>
                <Button 
                  @click="handleAnggaranPerubahan"
                  :disabled="isProcessing || !isTriwulan4Active"
                  class="flex items-center gap-2 shadow-lg transition-all duration-300 transform"
                  :class="{
                    'opacity-50 cursor-not-allowed': isProcessing || !isTriwulan4Active,
                    'hover:scale-105 bg-orange-600 hover:bg-orange-700 text-white': isTriwulan4Active,
                    'bg-gray-400': !isTriwulan4Active
                  }"
                >
                  <HandCoins class="w-4 h-4"/>
                  {{ isProcessing ? 'Memproses...' : 'Perubahan Anggaran' }}
                  <span v-if="isTriwulan4Active" class="ml-1 inline-block w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <div class="text-sm">
                  <p v-if="isTriwulan4Active" class="text-green-600 font-medium">✅ Triwulan 4 Aktif</p>
                  <p v-else class="text-red-600 font-medium">❌ Triwulan 4 Tidak Aktif</p>
                  <p class="mt-1">{{ budgetChangeMessage }}</p>
                  <p v-if="isTriwulan4Active" class="mt-2 text-xs text-gray-600">
                    Klik untuk mengaktifkan mode perubahan anggaran<br/>
                    untuk semua perangkat daerah
                  </p>
                </div>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </div>
      </div>

      <!-- Status Card untuk Triwulan 4 -->
      <div v-if="isTriwulan4Active" class="rounded-lg border border-orange-200 bg-orange-50 p-4 shadow-md">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100">
              <HandCoins class="h-4 w-4 text-orange-600" />
            </div>
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-orange-800">Mode Perubahan Anggaran Tersedia</h3>
            <div class="mt-1 text-sm text-orange-700">
              <p>
                Periode <strong>Triwulan 4 Tahun {{ triwulan4Aktif?.tahun?.tahun }}</strong> sedang aktif. 
                Klik tombol "Perubahan Anggaran" di atas untuk mengaktifkan mode perubahan anggaran untuk semua perangkat daerah.
              </p>
            </div>
          </div>
          <div class="flex-shrink-0">
            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
              Tersedia
            </span>
          </div>
        </div>
      </div>

      <TabelListSKPD 
                url_detail="manajemenanggaran.show" 
                url_detail_partial="manajemenanggaran.show_partial" 
                url_budget_change="manajemenanggaran.budget-change"
                :users="users" 
                :showBinocularsButton="true"
                :enabledParsialUsers="enabledParsialUsers || []"
                :isBudgetChangeMode="!!isTriwulan4Active"
                :triwulan4Aktif="triwulan4Aktif"
              ></TabelListSKPD>
    </div>
  </AppLayout>
</template>
