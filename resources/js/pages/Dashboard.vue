<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Calendar } from '@/components/ui/calendar';
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date';
<<<<<<< HEAD
import { type Ref, ref, watch, onMounted, computed } from 'vue';
=======
<<<<<<< HEAD
import { type Ref, ref, watch, onMounted, computed } from 'vue';
=======
import { type Ref, ref, reactive, onMounted, watch } from 'vue';
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
import { Button } from '@/components/ui/button';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import { Check, Circle, Dot } from 'lucide-vue-next';
import axios from 'axios';

<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
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

>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
    if (axios.isAxiosError(error) && error.response) {
      console.error('Error Response:', error.response.data);
    } else if (axios.isAxiosError(error) && error.request) {
      console.error('Error Request:', error.request);
    } else {
      console.error('Error Message:', (error as Error).message);
    }
<<<<<<< HEAD
=======
=======
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
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
  }
};

onMounted(() => {
  fetchPeriodeBelumSelesai();
  fetchProgressStatus();
});

<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
function formatTanggal(periode: Periode) {
  const tahap = periode.tahap?.tahap || 'Tahap tidak diketahui';
  const startDate = new Date(periode.tanggal_mulai);
  const endDate = new Date(periode.tanggal_selesai);
=======
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
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114

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

const logByDate: Record<string, string[]> = {
  '2025-05-21': [
    'Dinas Kesehatan telah mengisi rencana awal',
    'Rumah Sakit A.Makkasau telah mengisi rencana awal',
    'Dinas PUPR telah mengisi rencana awal',
    'Dinas PUPR telah mengisi Triwulan 1',
    'Dinas PUPR telah mengisi Triwulan 2',
  ],
  '2025-05-22': [
    'Dinas Pendidikan Telah mengisi rencana awal',
    'Dinas Kesehatan Telah Mengisi Triwulan 1',
    'Rumah Sakit A.Makkasau Telah Mengisi Triwulan 1',
    'Dinas PUPR Telah Mengisi Triwulan 1',
    'Dinas Perdagangan Telah Mengisi Triwulan 1',
    'Dinas PUPR Telah Mengisi Triwulan 3',
  ],
  '2025-05-23': [
<<<<<<< HEAD
    'Dinas Pendidikan Telah mengisi rencana awal',
    'Dinas Kesehatan Telah Mengisi Triwulan 1',
    'Rumah Sakit A.Makkasau Telah Mengisi Triwulan 1',
    'Dinas PUPR Telah Mengisi Triwulan 1',
    'Dinas Perdagangan Telah Mengisi Triwulan 1',
    'Dinas PUPR Telah Mengisi Triwulan 3',
=======
    'Dinas Perhubungan meng-update laporan triwulan',
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
  ],
  '2025-05-24': [
    'Dinas Sosial menambah data penerima bantuan',
    'Dinas Pertanian mengirim laporan akhir',
  ],
  '2025-05-25': [
    'Dinas Lingkungan Hidup mengajukan proposal baru',
  ],
};

const logsForSelectedDate = ref<string[]>([]);

function handleDateChange(newDate: DateValue) {
  const yyyy = newDate.year;
  const mm = String(newDate.month).padStart(2, '0');
  const dd = String(newDate.day).padStart(2, '0');
  const selectedDateStr = `${yyyy}-${mm}-${dd}`;

  logsForSelectedDate.value = logByDate[selectedDateStr] || [];
}

watch(value, (newDate) => {
  handleDateChange(newDate);
}, { immediate: true });

const steps = computed(() => {
  const tahap = periodeBelumSelesai.value[0]?.tahap?.tahap?.toLowerCase() ?? '';
  let triwulanLabel = 'Triwulan';

  const triwulanMatch = tahap.match(/triwulan\s*(\d+)/i);
  if (triwulanMatch) {
    triwulanLabel = `Triwulan ${triwulanMatch[1]}`;
  }

  return [
    {
      step: 1,
      title: 'Rencana Awal',
      description: 'Mengisi rencana awal kegiatan',
    },
    {
      step: 2,
      title: triwulanLabel,
      description: `Progres pada ${triwulanLabel}`,
    },
    {
      step: 3,
      title: 'Evaluasi Akhir',
      description: 'Mengisi laporan dan evaluasi akhir',
    },
  ];
});

const stepStates = computed(() => {
  if (!periodeBelumSelesai.value.length) {
    return ['inactive', 'inactive', 'inactive'];
  }

  const tahap = periodeBelumSelesai.value[0]?.tahap?.tahap?.toLowerCase();

  if (tahap?.includes('rencana')) {
    return ['active', 'inactive', 'inactive'];
  }

  // Pindahkan evaluasi akhir ke atas agar menandai triwulan 4 sebagai completed
  if (tahap?.includes('evaluasi akhir')) {
    return ['completed', 'completed', 'active'];
  }

  if (
    tahap?.includes('triwulan 1') ||
    tahap?.includes('triwulan 2') ||
    tahap?.includes('triwulan 3')
    || tahap?.includes('triwulan 4')
  ) {
    return ['completed', 'active', 'inactive'];
  }

  // if (tahap?.includes('triwulan 4')) {
  //   return ['completed', 'completed', 'inactive'];
  // }

  // Fallback
  return ['inactive', 'inactive', 'inactive'];
});


</script>




<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Grid untuk Selamat Datang dan Progres Monitoring -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Kartu Selamat Datang -->
        <div class="md:col-span-1 flex h-full w-full flex-col items-center justify-center rounded-xl text-center shadow">
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
<<<<<<< HEAD
            <div class="text-lg font-semibold">{{ user.name }}</div>
            <div class="text-sm text-gray-700">197305031999031007</div>
=======
<<<<<<< HEAD
            <div class="text-lg font-semibold">{{ user.name }}</div>
            <div class="text-sm text-gray-700">197305031999031007</div>
=======

            <div class="text-lg font-semibold">{{ user.name }}</div>

            <div class="text-sm text-gray-700">197305031999031007</div>

>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
            <div class="mt-2 text-xs text-gray-500">Perangkat Daerah</div>
            <div class="text-sm font-bold">Bappeda</div>
          </div>
        </div>

        <!-- Stepper dan Info -->
        <div class="md:col-span-2">
          <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900 text-center">
            Progres Monitoring dan Evaluasi Bappeda
          </div>

          <!-- Stepper -->
          <Stepper class="flex w-full items-start gap-2 mt-6">
            <StepperItem
              v-for="(step, index) in steps"
              :key="step.step"
              v-slot="{ state }"
              class="relative flex w-full flex-col items-center justify-center"
              :step="step.step"
              :data-state="stepStates[index]"
            >
              <StepperSeparator
                v-if="step.step !== steps[steps.length - 1].step"
                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted"
                :class="{ 'bg-primary': step.completed }"
              />
              <StepperTrigger as-child>
                <Button
<<<<<<< HEAD
                  :variant="stepStates[index] === 'completed' || stepStates[index] === 'active' ? 'default' : 'outline'"
=======
<<<<<<< HEAD
                  :variant="stepStates[index] === 'completed' || stepStates[index] === 'active' ? 'default' : 'outline'"
=======
                  :variant="step.completed || state === 'active' ? 'default' : 'outline'"
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
                  size="icon"
                  class="z-10 rounded-full shrink-0"
                  :class="[stepStates[index] === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                >
<<<<<<< HEAD
                  <Check v-if="stepStates[index] === 'completed'" class="size-5" />
                  <Circle v-else-if="stepStates[index] === 'active'" />
=======
<<<<<<< HEAD
                  <Check v-if="stepStates[index] === 'completed'" class="size-5" />
                  <Circle v-else-if="stepStates[index] === 'active'" />
=======
                  <Check v-if="step.completed" class="size-5" />
                  <Circle v-else-if="state === 'active'" />
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
                  <Dot v-else />
                </Button>
              </StepperTrigger>
              <div class="mt-5 flex flex-col items-center text-center">
                <StepperTitle
                  :class="[stepStates[index] === 'active' && 'text-primary']"
                  class="text-sm font-semibold transition lg:text-base"
                >
                  {{ step.title }}
                </StepperTitle>
                <StepperDescription
                  :class="[stepStates[index] === 'active' && 'text-primary']"
                  class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
                >
                  {{ step.description }}
                </StepperDescription>
              </div>
            </StepperItem>
          </Stepper>

<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
          <!-- Periode Info -->
          <div v-if="periodeBelumSelesai.length" class="mt-4 w-full rounded-lg border border-yellow-300 bg-yellow-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="mt-1 text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
<<<<<<< HEAD
=======
=======
          <!-- JIKA ADA PERIODE -->
          <div v-if="periodeBelumSelesai.length" class="mt-4 w-full rounded-lg border border-yellow-300 bg-yellow-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="mt-1 text-yellow-600">
                <!-- Icon megaphone/alert -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-yellow-800">
                  Periode Monitoring Sedang Berlangsung
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
                  <span class="ml-2 inline-flex items-center rounded bg-yellow-200 px-2 py-0.5 text-xs font-medium text-yellow-800">
                    Aktif
                  </span>
                </p>
                <p class="mt-1 text-sm text-gray-800">
                  {{ formatTanggal(periodeBelumSelesai[0]) }}
                </p>
              </div>
            </div>  
          </div>

          <div v-else class="mt-4 w-full rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="mt-1 text-gray-400"></div>
              <div>
                <p class="text-sm text-gray-700 italic">
                  Tidak ada periode monitoring yang sedang berlangsung.
                </p>
              </div>
            </div>
          </div>
<<<<<<< HEAD
=======
=======
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
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
>>>>>>> 87f1bdf8678f48b801ea4328a66eef15bc59578c
        </div>
      </div>

      <!-- Kalender dan Log -->
      <div class="grid gap-4 md:grid-cols-3">
        <!-- Kalender -->
          <div class="md:col-span-1 flex max-w-full flex-1 items-center justify-center rounded-xl border p-4 shadow-sm">
            <Calendar
              v-model="value"
              :mode="'single'"
              :locale="'id-ID'"
              :max-date="today(getLocalTimeZone())"
              :show-week-numbers="false"
              class="text-xl p-6 scale-110"
            />
          </div>


        <!-- Log Aktivitas -->
        <div class="md:col-span-2 flex flex-col gap-2 rounded-xl border p-4 shadow-sm">
          <h2 class="text-lg font-medium">Log Aktivitas Tanggal {{ value.year }}-{{ String(value.month).padStart(2, '0') }}-{{ String(value.day).padStart(2, '0') }}</h2>
          <div v-if="logsForSelectedDate.length" class="flex flex-col gap-1 overflow-auto max-h-60">
            <div
              v-for="(log, i) in logsForSelectedDate"
              :key="i"
              class="flex items-start gap-2 rounded-md border border-gray-200 bg-gray-50 p-3 shadow-sm transition hover:shadow-md"
            >
              <div class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-sm text-gray-700">{{ log }}</p>
            </div>
          </div>
          <div v-else class="rounded-md border border-dashed border-gray-400 bg-gray-50 p-4 text-center text-sm text-gray-600 italic">
            Tidak ada aktivitas pada tanggal ini.
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
