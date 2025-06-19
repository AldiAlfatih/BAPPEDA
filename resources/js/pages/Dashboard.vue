<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Calendar } from '@/components/ui/calendar';
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { type Ref, ref, watch, onMounted, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import { Check, Circle, Dot } from 'lucide-vue-next';

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

const fetchPeriodeBelumSelesai = () => {
  fetch(route('periode.belum-selesai.data'))
    .then(response => response.json())
    .then(data => {
      periodeBelumSelesai.value = data.periode || [];
    })
    .catch(error => {
      console.error('Error fetching periode data:', error);
    });
};

onMounted(() => {
  fetchPeriodeBelumSelesai();
});

function formatTanggal(periode: Periode) {
  const tahap = periode.tahap?.tahap || 'Tahap tidak diketahui';
  const startDate = new Date(periode.tanggal_mulai);
  const endDate = new Date(periode.tanggal_selesai);

  const formattedStartDate = startDate.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
  const formattedEndDate = endDate.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  return `${tahap} dimulai pada ${formattedStartDate} dan selesai ${formattedEndDate}`;
}

const page = usePage();
const auth = page.props.auth as {
  user: {
    name: string;
    role?: string;
    nip?: string;
    dinas?: string;
  };
};

const user = auth.user;
const userNip = user.nip;
const userdinas = user.dinas;
const userRole = auth.user?.role;

const breadcrumbs = [
  {
    title: 'DASHBOARD ' + (userRole ? userRole.replace('_', ' ').toUpperCase() : ''),
    href: '/dashboard'
  }
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
    'Dinas Perhubungan meng-update laporan triwulan',
  ],
  '2025-05-24': [
    'Dinas Sosial menambah data penerima bantuan',
    'Dinas Pertanian mengirim laporan akhir',
  ],
  '2025-06-19': [
    'Dinas Pendidikan Telah mengisi rencana awal',
    'Dinas Kesehatan Telah Mengisi Triwulan 1',
    'Rumah Sakit A.Makkasau Telah Mengisi Triwulan 1',
    'Dinas PUPR Telah Mengisi Triwulan 1',
    'Dinas Perdagangan Telah Mengisi Triwulan 1',
    'Dinas PUPR Telah Mengisi Triwulan 3',
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

<style scoped>
.marquee-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #fff;
  padding: 0.5rem;
}

.marquee-box {
  position: relative;
  overflow: hidden;
  border-radius: 0.5rem;
  padding: 0.5rem;
  border: 1px solid #faf7d5cc;
  background-color: #fefce8cc;
  height: 2.5rem;
  width: 100%;
  max-width: 1240px;
  display: flex;
  align-items: center;
  justify-content: center;

  /* Efek melayang */
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  transition: box-shadow 0.3s ease;

  /* optional: hover effect */
  cursor: pointer;
}
.marquee-box:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
}


.marquee-text {
  white-space: nowrap;
  display: inline-block;
  animation: marquee 15s linear infinite;
  font-size: 2rem;
}

@keyframes marquee {
  0% { transform: translateX(100%); }
  100% { transform: translateX(-100%); }
}

</style>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- <div class="marquee-wrapper">
      <div class="marquee-box"> -->
        <!-- <div
          v-if="periodeBelumSelesai.length"
          class="marquee-text text-yellow-800 font-semibold"
        >
          📢 Periode Monitoring Sedang Berlangsung: {{ formatTanggal(periodeBelumSelesai[0]) }}
        </div>
        <div
          v-else
          class="marquee-text text-gray-500 italic"
        >
          📢 Tidak ada periode monitoring yang sedang berlangsung.
        </div> -->
        <!-- <div class="marquee-text text-orange-800 font-black">
          "Setiap langkah kecil hari ini adalah pijakan kuat menuju kesuksesan besar di masa depan. Ayo mulai dengan penuh semangat!"
        </div> -->
      </div>
    </div> -->
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Grid untuk Selamat Datang dan Progres Monitoring -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Kartu Selamat Datang -->
        <div class="md:col-span-1 flex h-full w-full flex-col items-center justify-center rounded-xl text-center shadow">
          <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900">
            Selamat datang di e-Monev
          </div>
          <div class="flex flex-col items-center p-7">
            <div class="mb-4 mt-2 flex h-20 w-20 items-center justify-center rounded-full bg-gray-300">
              <svg class="h-10 w-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
              </svg>
            </div>
            <div class="text-lg font-semibold">{{ user.name }}</div>
            <div class="text-sm text-gray-700">{{ userNip }}</div>
            <div class="mt-2 text-xs text-gray-500">{{ userRole }}</div>
            <div class="text-sm font-bold">{{ userdinas }}</div>
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
              v-slot="{}"
              class="relative flex w-full flex-col items-center justify-center"
              :step="step.step"
              :data-state="stepStates[index]"
            >
              <StepperSeparator
                v-if="step.step !== steps[steps.length - 1].step"
                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
              />
              <StepperTrigger as-child>
                <Button
                  :variant="stepStates[index] === 'completed' || stepStates[index] === 'active' ? 'default' : 'outline'"
                  size="icon"
                  class="z-10 rounded-full shrink-0"
                  :class="[stepStates[index] === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                >
                  <Check v-if="stepStates[index] === 'completed'" class="size-5" />
                  <Circle v-else-if="stepStates[index] === 'active'" />
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

          <!-- Periode Info -->
          <div v-if="periodeBelumSelesai.length" class="mt-4 w-full rounded-lg border border-yellow-300 bg-yellow-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="mt-1 text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-yellow-800">
                  Periode Monitoring Sedang Berlangsung
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
