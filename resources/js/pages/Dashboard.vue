<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Calendar } from '@/components/ui/calendar';
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { type Ref, ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import { Check, Circle, Dot } from 'lucide-vue-next';
import axios from 'axios';

const steps = [
  {
    step: 1,
    title: 'Your details',
    description: 'Provide your name and email',
  },
  {
    step: 2,
    title: 'Company details',
    description: 'A few details about your company',
  },
  {
    step: 3,
    title: 'Invite your team',
    description: 'Start collaborating with your team',
  },
];

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
        console.log(response.data);
        periodeBelumSelesai.value = response.data;
    } catch (error) {
        if (axios.isAxiosError(error) && error.response) {
            console.error('Error Response:', error.response.data);
        } else if (axios.isAxiosError(error) && error.request) {
            console.error('Error Request:', error.request);
        } else {
            console.error('Error Message:', (error as Error).message);
        }
    }
};

onMounted(() => {
  fetchPeriodeBelumSelesai();
});

function handleDateChange(newDate: DateValue) {
    console.log('Tanggal yang dipilih:', newDate);
}

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
const auth = page.props.auth as { user: { name: string; role?: string } };
const user = auth.user;
const userRole = auth.user?.role;

const breadcrumbs = [
  {
    title: 'DASHBOARD ' + (userRole ? userRole.replace('_', ' ').toUpperCase() : ''),
    href: '/dashboard'
  }
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
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
              </svg>
            </div>

            <div class="text-lg font-semibold">
              {{ user.name }}
            </div>

            <div class="text-sm text-gray-700">
              197305031999031007
            </div>

            <div class="mt-2 text-xs text-gray-500">
              Perangkat Daerah
            </div>
            <div class="text-sm font-bold">
              Bappeda
            </div>
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
                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
              />

              <StepperTrigger as-child>
                <Button
                  :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                  size="icon"
                  class="z-10 rounded-full shrink-0"
                  :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                >
                  <Check v-if="state === 'completed'" class="size-5" />
                  <Circle v-if="state === 'active'" />
                  <Dot v-if="state === 'inactive'" />
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

            <!-- JIKA TIDAK ADA PERIODE -->
            <div v-else class="mt-4 w-full rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm">
              <div class="flex items-start gap-3">
                <div class="mt-1 text-gray-400">
                  <!-- Icon check / info -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">
                    Tidak ada periode aktif saat ini.
                  </p>
                  <p class="mt-1 text-sm text-gray-500 italic">
                    Silakan pantau secara berkala untuk melihat update terbaru.
                  </p>
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

