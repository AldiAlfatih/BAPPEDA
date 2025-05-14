<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

const page = usePage();
const tahunAktif = computed(() => page.props.tahun_aktif as number);
const selectedTahun = ref(tahunAktif.value);

// Daftar tahun (bisa disesuaikan)
const currentYear = new Date().getFullYear();
const daftarTahun = Array.from({ length: 5 }, (_, i) => currentYear - 2 + i);

// Kirim ke backend
function setTahunAktif() {
    router.post('/set-tahun-aktif', {
        tahun: selectedTahun.value,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <header class="flex h-16 items-center justify-between border-b px-6">
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs?.length">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Filter Tahun -->
        <!-- <div class="flex items-center">
            <select
                v-model="selectedTahun"
                @change="setTahunAktif"
                class="border rounded px-2 py-1 text-sm"
            >
                <option v-for="tahun in daftarTahun" :key="tahun" :value="tahun">
                    {{ tahun }}
                </option>
            </select>
        </div> -->
    </header>
</template>
