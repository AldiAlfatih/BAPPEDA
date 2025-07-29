<script setup lang="ts">
import TabelTugasPD from '@/components/data/TabelTugasPD.vue';
import TabelDetail from '@/components/Triwulan/TabelDetail.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    user: {
        id: number;
        nama_skpd: string;
    };
    skpd: {
        id: number;
        nama_dinas: string;
        nama_operator: string;
        nama_kepala_skpd?: string;
        nip_kepala_skpd?: string;
        nip_operator?: string;
        no_dpa: string;
        kode_organisasi: string;
        nama_skpd?: string;
        user?: {
            id: number;
            name: string;
            user_detail?: {
                nip?: string;
            } | null;
        };
    };
    urusanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number }[];
    bidangUrusanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; urusan_id: number }[];
    programList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; bidang_urusan_id: number }[];
    kegiatanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; program_id: number }[];
    subkegiatanList: { id: number; nomor_kode: string; nomenklatur: string; jenis_nomenklatur: number; kegiatan_id: number }[];
    skpdTugas: {
        id: number;
        kode_nomenklatur: {
            id: number;
            nomor_kode: string;
            nomenklatur: string;
            jenis_nomenklatur: number;
        };
    }[];
    tid: number;
    tahun: number;
    periode: any;
    triwulanName: string;
    errors?: Record<string, string>;
    flash?: {
        success?: string;
        error?: string;
        info?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: `Monitoring ${props.triwulanName}`, href: route('triwulan.index', { tid: props.tid }) },
    { title: `Monitoring Detail ${props.user?.nama_skpd || props.skpd.nama_dinas || props.skpd.nama_skpd || 'SKPD'}`, href: '#' },
];

function getUserNip(user: any): string {
    if (!user) return '-';

    if (user.user_detail && typeof user.user_detail.nip === 'string' && user.user_detail.nip.trim() !== '') {
        return user.user_detail.nip;
    }

    return '-';
}
</script>

<template>
    <Head :title="`Detail Perangkat Daerah - ${triwulanName}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Detail {{ triwulanName }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.nama_skpd || skpd.nama_dinas || skpd.nama_skpd || 'SKPD' }}</p>
                </div>
            </div>

            <!-- SKPD Info -->
            <TabelDetail :skpd="skpd" :triwulan-name="triwulanName" :tahun="tahun" />

            <!-- Tabel Tugas PD -->
            <TabelTugasPD
                :skpd="props.skpd"
                :skpd-tugas="props.skpdTugas"
                :urusan-list="props.urusanList"
                :bidang-urusan-list="props.bidangUrusanList"
                :program-list="props.programList"
                :kegiatan-list="props.kegiatanList"
                :subkegiatan-list="props.subkegiatanList"
                :tid="props.tid"
                :tahun="props.tahun"
            ></TabelTugasPD>
        </div>
    </AppLayout>
</template>
