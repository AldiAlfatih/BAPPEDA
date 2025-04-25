<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { usePage } from '@inertiajs/vue3';
import { Calendar } from '@/components/ui/calendar';
import { type DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { type Ref, ref } from 'vue';
import { Button } from '@/components/ui/button';

import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import { Check, Circle, Dot,} from 'lucide-vue-next';

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
]


const page = usePage();
const userRole = page.props.auth.user?.role;

const user = usePage().props.auth.user;

const breadcrumbs = [
  { title: 'DASHBOARD ' + (userRole ? userRole.replace('_', ' ').toUpperCase() : '') }
];

const value = ref(today(getLocalTimeZone())) as Ref<DateValue>

// const breadcrumbs: BreadcrumbItem[] = [
//     {
//         title: 'Dashboard',
//         href: '/dashboard',
//     },
// ];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">  
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="flex h-full w-full flex-col items-center justify-center rounded-xl text-center shadow">
                    <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900">
                    Selamat datang di e-Monev
                    </div>

                    <div class="flex flex-col items-center p-4">
                    <!-- Avatar placeholder -->
                    <div class="mb-4 mt-2 flex h-20 w-20 items-center justify-center rounded-full bg-gray-300">
                        <svg class="h-10 w-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                        </svg>
                    </div>

                    <!-- Nama -->
                    <div class="text-lg font-semibold">
                        {{ user.name }}
                    </div>

                    <!-- NIP -->
                    <div class="text-sm text-gray-700">
                        197305031999031007
                    </div>

                    <!-- Instansi -->
                    <div class="mt-2 text-xs text-gray-500">
                        Perangkat Daerah
                    </div>
                    <div class="text-sm font-bold">
                        Bappeda
                    </div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="w-full rounded-t-xl bg-emerald-100 py-2 text-sm font-medium text-emerald-900 text-center ">
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
                </div>
            </div> 
            <div class="flex h-full w-full flex-col items-start justify-start rounded-xl p-4 text-left shadow">
                <Calendar v-model="value" :weekday-format="'short'" class="rounded-md border" />
            </div>
        </div>
    </AppLayout>
</template>