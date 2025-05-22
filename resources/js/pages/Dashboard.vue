<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Calendar } from '@/components/ui/calendar';
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { type Ref, ref, reactive, onMounted, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import { Check, Circle, Dot } from 'lucide-vue-next';
import axios from 'axios';

// Menambahkan data kegiatan per tanggal
const kegiatanPerTanggal = reactive({
  '2025-05-21': [
    { kegiatan: 'Dinas Kesehatan mengisi rencana awal', deskripsi: 'Proses perencanaan awal untuk monitoring Bappeda' },
    { kegiatan: 'Dinas PUPR menyelesaikan triwulan 1', deskripsi: 'Proses pelaporan triwulan pertama' },
  ],
  // Tambahkan data kegiatan lainnya sesuai dengan tanggal yang relevan
});

const selectedKegiatan = ref([]);  // Menyimpan kegiatan yang dipilih

function handleDateChange(newDate: DateValue) {
  console.log('Tanggal yang dipilih:', newDate);

  // Format tanggal yang dipilih menjadi string
  const formattedDate = newDate.toLocaleDateString('id-ID');

  // Cari kegiatan berdasarkan tanggal yang dipilih
  selectedKegiatan.value = kegiatanPerTanggal[formattedDate] || [];
  console.log('Kegiatan untuk tanggal', formattedDate, ':', selectedKegiatan.value);
}

interface StepProgress {
  step: number;
  title: string;
  description: string;
  completed: boolean;
}

const steps = reactive<StepProgress[]>([
  {
    step: 1,
    title: 'Rencana Awal',
    description: 'Progres rencana Awal telah selesai',
    completed: false,
  },
  {
    step: 2,
    title: 'Triwulan',
    description: 'Progres triwulan telah selesai',
    completed: false,
  },
  {
    step: 3,
    title: 'Evaluasi Akhir',
    description: 'Progres Evaluasi telah selesai',
    completed: false,
  },
]);

interface Tahap {
  tahap?: string;
}

interface Periode {
  id: number;
  tahap?: Tahap;
  tanggal_mulai: string;
  tanggal_selesai: string;
}

const periodeBelumSelesai = ref<Periode[]>([]);

const fetchPeriodeBelumSelesai = async () => {
  try {
    const response = await axios.get('/periode-belum-selesai');
    periodeBelumSelesai.value = response.data;
  } catch (error) {
    console.error('Error fetching periode data:', error);
  }
};

const fetchProgressStatus = async () => {
  try {
    const response = await axios.get('/progress-status');
    const data = response.data;
    steps[0].completed = data.rencana_awal_selesai;
    steps[1].completed = data.triwulan_1_selesai;
    steps[2].completed = data.laporan_akhir_selesai;
  } catch (error) {
    console.error('Error fetching progress status:', error);
  }
};

onMounted(() => {
  fetchPeriodeBelumSelesai();
  fetchProgressStatus();
});

watch(
  periodeBelumSelesai,
  (newVal) => {
    if (newVal.length > 0) {
      // Cek apakah ada periode dengan tahap "Rencana Awal"
      const rencanaAwalDimulai = newVal.some((p) =>
        p.tahap?.tahap?.toLowerCase().includes('rencana awal')
      );
      // Jika ada, centang "Rencana Awal"
      steps[0].completed = rencanaAwalDimulai;

      // Cek apakah ada periode dengan tahap "Triwulan"
      const triwulanDimulai = newVal.some((p) =>
        p.tahap?.tahap?.toLowerCase().includes('triwulan')
      );
      // Jika ada, centang "Triwulan"
      steps[1].completed = triwulanDimulai;

      // Cek apakah ada periode dengan tahap "Evaluasi Akhir"
      const evaluasiAkhirDimulai = newVal.some((p) =>
        p.tahap?.tahap?.toLowerCase().includes('laporan akhir')
      );
      // Jika ada, centang "Evaluasi Akhir" serta "Rencana Awal" dan "Triwulan"
      steps[2].completed = evaluasiAkhirDimulai;
      if (evaluasiAkhirDimulai) {
        steps[0].completed = true; // Centang Rencana Awal jika Evaluasi Akhir dimulai
        steps[1].completed = true; // Centang Triwulan jika Evaluasi Akhir dimulai
      }

    } else {
      fetchProgressStatus(); // Ambil data progres jika tidak ada periode aktif
    }
  },
  { immediate: true }
);

const page = usePage();
const auth = page.props.auth as { user: { name: string; role?: string } };
const user = auth.user;
const userRole = auth.user?.role;

const breadcrumbs = [
  {
    title: 'DASHBOARD ' + (userRole ? userRole.replace('_', ' ').toUpperCase() : ''),
    href: '/dashboard',
  },
];

const value = ref(today(getLocalTimeZone())) as Ref<DateValue>;
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Kartu Selamat Datang -->
        <div class="flex h-full w-full flex-col items-center justify-center rounded-xl text-center shadow">
          <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900">
            Selamat datang di e-Monev
          </div>

          <div class="flex flex-col items-center p-4">
            <div class="mb-4 mt-2 flex h-20 w-20 items-center justify-center rounded-full bg-gray-300">
              <svg class="h-10 w-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"
                />
              </svg>
            </div>

            <div class="text-lg font-semibold">{{ user.name }}</div>

            <div class="text-sm text-gray-700">197305031999031007</div>

            <div class="mt-2 text-xs text-gray-500">Perangkat Daerah</div>
            <div class="text-sm font-bold">Bappeda</div>
          </div>
        </div>

        <!-- Stepper di kolom 2 dan 3 -->
        <div class="col-span-2">
          <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900 text-center">
            Progres Monitoring dan Evaluasi Bappeda
          </div>

          <Stepper class="flex w-full items-start gap-2 mt-6">
            <StepperItem
              v-for="step in steps"
              :key="step.step"
              v-slot="{ state }"
              class="relative flex w-full flex-col items-center justify-center"
              :step="step.step"
            >
              <StepperSeparator
                v-if="step.step !== steps[steps.length - 1].step"
                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted"
                :class="{ 'bg-primary': step.completed }"
              />

              <StepperTrigger as-child>
                <Button
                  :variant="step.completed || state === 'active' ? 'default' : 'outline'"
                  size="icon"
                  class="z-10 rounded-full shrink-0"
                  :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                >
                  <Check v-if="step.completed" class="size-5" />
                  <Circle v-else-if="state === 'active'" />
                  <Dot v-else />
                </Button>
              </StepperTrigger>

              <div class="mt-5 flex flex-col items-center text-center">
                <StepperTitle
                  :class="[state === 'active' && 'text-primary']"
                  class="text-sm font-semibold transition lg:text-base"
                >
                  {{ step.title }}
                </StepperTitle>
                <StepperDescription
                  :class="[state === 'active' && 'text-primary']"
                  class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
                >
                  {{ step.description }}
                </StepperDescription>
              </div>
            </StepperItem>
          </Stepper>

          <!-- JIKA ADA PERIODE -->
          <div v-if="periodeBelumSelesai.length" class="mt-4 w-full rounded-lg border border-yellow-300 bg-yellow-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="mt-1 text-yellow-600">
                <!-- Icon megaphone/alert -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-yellow-800">
                  Periode Monitoring Sedang Berlangsung
                  <span class="ml-2 inline-flex items-center rounded bg-yellow-200 px-2 py-0.5 text-xs font-medium text-yellow-800">Aktif</span>
                </p>
                <p class="mt-1 text-sm text-gray-800">{{ formatTanggal(periodeBelumSelesai[0]) }}</p>
              </div>
            </div>
          </div>

          <!-- JIKA TIDAK ADA PERIODE -->
          <div v-else class="mt-4 w-full rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="mt-1 text-gray-400">
                <!-- Icon check / info -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Tidak ada periode aktif saat ini.</p>
                <p class="mt-1 text-sm text-gray-500 italic">Silakan pantau secara berkala untuk melihat update terbaru.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Kalender + Notifikasi, satu kolom sama seperti Selamat Datang -->
        <div class="md:col-span-1">
          <div class="flex h-full w-full flex-col items-start justify-start rounded-xl p-4 text-left shadow">
            <Calendar
              v-model="value"
              :weekday-format="'short'"
              class="rounded-md border w-full"
              @change="handleDateChange"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
