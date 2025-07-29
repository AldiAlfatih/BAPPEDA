<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { Check, Circle, Dot } from 'lucide-vue-next';
import { computed, onMounted, type Ref, ref, watch } from 'vue';

// Props dari controller
interface Props {
    initialActivities?: ActivityLog[];
    initialDate?: string;
}

const props = withDefaults(defineProps<Props>(), {
    initialActivities: () => [],
    initialDate: ''
});

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
const periodeSelesai = ref<Periode[]>([]);

const fetchPeriodeBelumSelesai = () => {
    fetch(route('periode.belum-selesai.data'))
        .then((response) => response.json())
        .then((data) => {
            periodeBelumSelesai.value = data.periode || [];
        })
        .catch((error) => {
            console.error('Error fetching periode data:', error);
        });
};

const fetchPeriodeSelesai = () => {
    fetch('/api/periode/selesai')
        .then((response) => response.json())
        .then((data) => {
            periodeSelesai.value = data.periode || [];
        })
        .catch((error) => {
            console.error('Error fetching completed periode data:', error);
        });
};

onMounted(() => {
    fetchPeriodeBelumSelesai();
    fetchPeriodeSelesai();
    
    // Fetch activity logs for today only if we don't have initial data for today
    const today = value.value;
    const yyyy = today.year;
    const mm = String(today.month).padStart(2, '0');
    const dd = String(today.day).padStart(2, '0');
    const todayStr = `${yyyy}-${mm}-${dd}`;
    
    // Only fetch if initial date doesn't match today or no initial activities
    if (props.initialDate !== todayStr || !props.initialActivities?.length) {
        fetchActivityLogs(todayStr);
    }
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
        href: '/dashboard',
    },
];

const value = ref(today(getLocalTimeZone())) as Ref<DateValue>;

interface ActivityLog {
    id: number;
    user_name: string;
    description: string;
    activity_type: string;
    module: string;
    created_at: string;
    created_at_formatted: string;
    activity_data?: any;
}

const logsForSelectedDate = ref<ActivityLog[]>(props.initialActivities || []);
const isLoadingLogs = ref(false);

function handleDateChange(newDate: DateValue) {
    const yyyy = newDate.year;
    const mm = String(newDate.month).padStart(2, '0');
    const dd = String(newDate.day).padStart(2, '0');
    const selectedDateStr = `${yyyy}-${mm}-${dd}`;

    fetchActivityLogs(selectedDateStr);
}

async function fetchActivityLogs(date: string) {
    isLoadingLogs.value = true;
    try {
        const response = await fetch(`${route('dashboard.activities-by-date')}?date=${date}`);
        const data = await response.json();
        
        if (response.ok) {
            logsForSelectedDate.value = data.activities || [];
        } else {
            console.error('Error fetching activity logs:', data);
            logsForSelectedDate.value = [];
        }
    } catch (error) {
        console.error('Error fetching activity logs:', error);
        logsForSelectedDate.value = [];
    } finally {
        isLoadingLogs.value = false;
    }
}

watch(
    value,
    (newDate) => {
        handleDateChange(newDate);
    },
    { immediate: true },
);

const steps = computed(() => {
    return [
        {
            step: 1,
            title: 'Rencana Awal',
            description: 'Mengisi rencana awal kegiatan',
        },
        {
            step: 2,
            title: 'Triwulan 1',
            description: 'Progres pada Triwulan 1',
        },
        {
            step: 3,
            title: 'Triwulan 2',
            description: 'Progres pada Triwulan 2',
        },
        {
            step: 4,
            title: 'Triwulan 3',
            description: 'Progres pada Triwulan 3',
        },
        {
            step: 5,
            title: 'Triwulan 4',
            description: 'Progres pada Triwulan 4',
        },
    ];
});

const stepStates = computed(() => {
    // Jika ada periode yang sedang berlangsung
    if (periodeBelumSelesai.value.length) {
        const tahap = periodeBelumSelesai.value[0]?.tahap?.tahap?.toLowerCase();

        // Jika sedang periode rencana awal
        if (tahap?.includes('rencana')) {
            return ['active', 'inactive', 'inactive', 'inactive', 'inactive'];
        }

        // Jika sedang periode triwulan 1
        if (tahap?.includes('triwulan 1')) {
            return ['completed', 'active', 'inactive', 'inactive', 'inactive'];
        }

        // Jika sedang periode triwulan 2
        if (tahap?.includes('triwulan 2')) {
            return ['completed', 'completed', 'active', 'inactive', 'inactive'];
        }

        // Jika sedang periode triwulan 3
        if (tahap?.includes('triwulan 3')) {
            return ['completed', 'completed', 'completed', 'active', 'inactive'];
        }

        // Jika sedang periode triwulan 4
        if (tahap?.includes('triwulan 4')) {
            return ['completed', 'completed', 'completed', 'completed', 'active'];
        }
    }

    // Jika tidak ada periode aktif, cek periode yang sudah selesai
    if (periodeSelesai.value.length) {
        // Cari tahap terakhir yang selesai
        const tahapSelesai = periodeSelesai.value.map((p) => p.tahap?.tahap?.toLowerCase()).filter(Boolean);

        let rencanaSelesai = false;
        let triwulan1Selesai = false;
        let triwulan2Selesai = false;
        let triwulan3Selesai = false;
        let triwulan4Selesai = false;

        tahapSelesai.forEach((tahap) => {
            if (tahap?.includes('rencana')) rencanaSelesai = true;
            if (tahap?.includes('triwulan 1')) triwulan1Selesai = true;
            if (tahap?.includes('triwulan 2')) triwulan2Selesai = true;
            if (tahap?.includes('triwulan 3')) triwulan3Selesai = true;
            if (tahap?.includes('triwulan 4')) triwulan4Selesai = true;
        });

        return [
            rencanaSelesai ? 'completed' : 'inactive',
            triwulan1Selesai ? 'completed' : 'inactive',
            triwulan2Selesai ? 'completed' : 'inactive',
            triwulan3Selesai ? 'completed' : 'inactive',
            triwulan4Selesai ? 'completed' : 'inactive',
        ];
    }

    // Fallback - periode baru yang belum dimulai (tidak ada periode aktif atau selesai)
    return ['inactive', 'inactive', 'inactive', 'inactive', 'inactive'];
});

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Grid untuk Selamat Datang dan Progres Monitoring -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Kartu Selamat Datang -->
                <div class="flex h-full w-full flex-col items-center justify-center rounded-xl text-center shadow md:col-span-1">
                    <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900">Selamat datang di e-Monev</div>
                    <div class="flex flex-col items-center p-7">
                        <div class="mt-2 mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-300">
                            <svg class="h-10 w-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"
                                />
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
                    <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-center text-sm font-medium text-emerald-900">
                        Progres Monitoring dan Evaluasi Bappeda
                    </div>

                    <!-- Stepper -->
                    <Stepper class="mt-6 flex w-full items-start gap-2">
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
                                class="bg-black absolute top-5 right-[calc(-50%+10px)] left-[calc(50%+20px)] block h-0.5 shrink-0 rounded-full"
                            />
                            <StepperTrigger as-child>
                                <Button
                                    variant="default"
                                    size="icon"
                                    class="z-10 shrink-0 rounded-full bg-black text-white hover:bg-gray-800"
                                    :class="[stepStates[index] === 'active' && 'ring-black ring-offset-background ring-2 ring-offset-2']"
                                >
                                    <Check v-if="stepStates[index] === 'completed'" class="size-5 text-white" />
                                    <Circle v-else-if="stepStates[index] === 'active'" class="text-white" />
                                    <Dot v-else class="text-white" />
                                </Button>
                            </StepperTrigger>
                            <div class="mt-5 flex flex-col items-center text-center">
                                <StepperTitle
                                    class="text-sm font-semibold transition lg:text-base text-black"
                                >
                                    {{ step.title }}
                                </StepperTitle>
                                <StepperDescription
                                    class="text-black sr-only text-xs transition md:not-sr-only lg:text-sm"
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
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
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
                                <p class="text-sm text-gray-700 italic">Tidak ada periode monitoring yang sedang berlangsung.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kalender dan Log -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Kalender -->
                <div class="flex max-w-full flex-1 items-center justify-center rounded-xl border p-4 shadow-sm md:col-span-1">
                    <Calendar
                        v-model="value"
                        :mode="'single'"
                        :locale="'id-ID'"
                        :max-date="today(getLocalTimeZone())"
                        :show-week-numbers="false"
                        class="scale-110 p-6 text-xl"
                    />
                </div>

                <!-- Log Aktivitas -->
                <div class="flex flex-col gap-2 rounded-xl border p-4 shadow-sm md:col-span-2">
                    <h2 class="text-lg font-medium">
                        Log Aktivitas Tanggal {{ value.year }}-{{ String(value.month).padStart(2, '0') }}-{{ String(value.day).padStart(2, '0') }}
                        <span v-if="isLoadingLogs" class="ml-2 text-sm text-gray-500">(Loading...)</span>
                    </h2>
                    
                    <!-- Loading State -->
                    <div v-if="isLoadingLogs" class="flex items-center justify-center p-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
                    </div>
                    
                    <!-- Activity Logs -->
                    <div v-else-if="logsForSelectedDate.length" class="flex max-h-60 flex-col gap-1 overflow-auto">
                        <div
                            v-for="log in logsForSelectedDate"
                            :key="log.id"
                            class="flex items-start gap-2 rounded-md border border-gray-200 bg-gray-50 p-3 shadow-sm transition hover:shadow-md"
                        >
                            <div class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-orange-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">
                                    <span class="font-medium text-emerald-700">{{ log.user_name }}</span>
                                    {{ log.description }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ log.created_at }} • {{ log.module }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-else class="rounded-md border border-dashed border-gray-400 bg-gray-50 p-4 text-center text-sm text-gray-600 italic">
                        Tidak ada aktivitas pada tanggal ini.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
