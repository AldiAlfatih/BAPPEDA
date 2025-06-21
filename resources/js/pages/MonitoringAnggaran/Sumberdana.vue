<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Anggaran', href: '/manajemenanggaran' },
    { title: 'Kelola Anggaran', href: '/manajemenanggaran/sumberdana' },
];

interface AnggaranItem {
    id: number;
    kode: string;
    jenis_nomenklatur: string;
    sumber_anggaran: {
        dak: boolean;
        dak_peruntukan: boolean;
        dak_fisik: boolean;
        dak_non_fisik: boolean;
        blud: boolean;
    };
    dak: number;
    dak_peruntukan: number;
    dak_fisik: number;
    dak_non_fisik: number;
    blud: number;
}

const props = defineProps<{
    user: {
        id?: number;
        name?: string;
        email?: string;
        skpd:
            | {
                  id: number;
                  nama_skpd: string;
                  nama_dinas: string;
                  no_dpa: string;
                  kode_organisasi: string;
              }[]
            | null;
        nama_dinas?: string;
        operator_name?: string;
        kepala_name?: string;
        kode_organisasi?: string;
    };
    skpdTugas?: Array<{
        id: number;
        kode_nomenklatur: {
            id: number;
            nomor_kode: string;
            nomenklatur: string;
            jenis_nomenklatur: number;
        };
    }>;
    urusanList?: Array<any>;
    bidangUrusanList?: Array<any>;
    programList?: Array<any>;
    kegiatanList?: Array<any>;
    subkegiatanList?: Array<any>;
    periodeAktif?: Array<{
        id: number;
        tahap: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    }>;
    tahunAktif?: {
        id: number;
        tahun: string;
    } | null;
    semuaPeriodeAktif?: Array<{
        id: number;
        tahap: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    }>;
    dataAnggaranTerakhir?: Record<
        number,
        {
            sumber_anggaran: {
                dak: boolean;
                dak_peruntukan: boolean;
                dak_fisik: boolean;
                dak_non_fisik: boolean;
                blud: boolean;
            };
            values: {
                dak: number;
                dak_peruntukan: number;
                dak_fisik: number;
                dak_non_fisik: number;
                blud: number;
            };
        }
    >;
}>();

// Create a reactive array for anggaran items
const anggaranItems = ref<AnggaranItem[]>([]);

// Ref untuk status periode dan pesan error
const errorMessage = ref('');

// Add reactive state for selected period
const selectedPeriodeId = ref<number | null>(null);

// Helper function to get the skpdId safely
const getSkpdId = computed(() => {
    if (props.user?.skpd && Array.isArray(props.user.skpd) && props.user.skpd.length > 0) {
        return props.user.skpd[0].id;
    }
    return null;
});

// Computed untuk cek apakah periode aktif
const isPeriodeAktif = computed(() => props.periodeAktif && props.periodeAktif.length > 0);

// Initialize with the active period if available
onMounted(() => {
    console.log('Props received:', {
        skpdTugas: props.skpdTugas,
        dataAnggaranTerakhir: props.dataAnggaranTerakhir,
    });

    if (props.periodeAktif && props.periodeAktif.length > 0) {
        selectedPeriodeId.value = props.periodeAktif[0].id;
    } else if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
        selectedPeriodeId.value = props.semuaPeriodeAktif[0].id;
    }

    if (props.skpdTugas?.length) {
        console.log('Processing skpdTugas:', props.skpdTugas);

        // Filter only sub-kegiatan items (jenis_nomenklatur = 4)
        const subKegiatanTasks = props.skpdTugas.filter((task) => {
            const isSubKegiatan = task.kode_nomenklatur.jenis_nomenklatur === 4;
            if (!isSubKegiatan) {
                console.log('Skipping non-subkegiatan:', task);
            }
            return isSubKegiatan;
        });

        console.log('Filtered subKegiatanTasks:', subKegiatanTasks);

        anggaranItems.value = subKegiatanTasks.map((task) => {
            // Cek apakah ada data terakhir untuk tugas ini
            const lastData = props.dataAnggaranTerakhir?.[task.id];
            console.log(`Processing task ${task.id}, lastData:`, lastData);

            if (lastData) {
                // Gunakan data terakhir yang sudah pernah disimpan
                return {
                    id: task.id,
                    kode: task.kode_nomenklatur.nomor_kode,
                    jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
                    sumber_anggaran: {
                        dak: lastData.sumber_anggaran.dak,
                        dak_peruntukan: lastData.sumber_anggaran.dak_peruntukan,
                        dak_fisik: lastData.sumber_anggaran.dak_fisik,
                        dak_non_fisik: lastData.sumber_anggaran.dak_non_fisik,
                        blud: lastData.sumber_anggaran.blud,
                    },
                    dak: lastData.values.dak,
                    dak_peruntukan: lastData.values.dak_peruntukan,
                    dak_fisik: lastData.values.dak_fisik,
                    dak_non_fisik: lastData.values.dak_non_fisik,
                    blud: lastData.values.blud,
                };
            } else {
                // Gunakan data kosong jika belum ada data yang disimpan
                return {
                    id: task.id,
                    kode: task.kode_nomenklatur.nomor_kode,
                    jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
                    sumber_anggaran: {
                        dak: false,
                        dak_peruntukan: false,
                        dak_fisik: false,
                        dak_non_fisik: false,
                        blud: false,
                    },
                    dak: 0,
                    dak_peruntukan: 0,
                    dak_fisik: 0,
                    dak_non_fisik: 0,
                    blud: 0,
                };
            }
        });

        console.log('Initialized anggaranItems:', anggaranItems.value);
    } else {
        console.log('No skpdTugas data available');
    }
});

// Use the reactive array for the table
const anggaranData = computed<AnggaranItem[]>(() => anggaranItems.value);

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID').format(value);
};

const editItem = (item: AnggaranItem) => {
    // jika perlu fungsi edit
};

type SumberAnggaranKey = keyof AnggaranItem['sumber_anggaran'];

const countSelectedSources = (sumberAnggaran: AnggaranItem['sumber_anggaran']) => {
    return Object.values(sumberAnggaran).filter((value) => value === true).length;
};

// Handler for checkbox changes
const handleSumberAnggaranChange = (item: AnggaranItem, key: SumberAnggaranKey, event: Event) => {
    // Periksa apakah periode sedang aktif
    if (!isPeriodeAktif.value) {
        alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
        if (event.target instanceof HTMLInputElement) {
            event.target.checked = false;
        }
        return;
    }

    const target = event.target as HTMLInputElement;
    if (target) {
        const checked = target.checked;

        item.sumber_anggaran[key] = checked;

        // Reset the corresponding value if unchecked
        if (!checked) {
            item[key] = 0;
        }
    }
};

const calculateTotal = (item: AnggaranItem): number => {
    return item.dak + item.dak_peruntukan + item.dak_fisik + item.dak_non_fisik + item.blud;
};

const handleInputChange = (item: AnggaranItem, field: keyof AnggaranItem, event: Event) => {
    if (!isPeriodeAktif.value) {
        alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
        return;
    }

    const target = event.target;
    if (!(target instanceof HTMLInputElement)) return;

    const value = parseInt(target.value) || 0;

    if (field in item) {
        const numericField = field as keyof Pick<AnggaranItem, 'dak' | 'dak_peruntukan' | 'dak_fisik' | 'dak_non_fisik' | 'blud'>;
        item[numericField] = value;
    }
};

const saveItem = (item: AnggaranItem) => {
    console.log('=== SAVE ITEM DEBUG ===');
    console.log('Item to save:', item);
    console.log('isPeriodeAktif:', isPeriodeAktif.value);
    console.log('props.periodeAktif:', props.periodeAktif);
    console.log('selectedPeriodeId:', selectedPeriodeId.value);

    // Periksa apakah periode sedang aktif
    if (!isPeriodeAktif.value) {
        alert('Periode belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.');
        return;
    }

    // Validate that at least one funding source is selected
    const selectedCount = countSelectedSources(item.sumber_anggaran);
    console.log('Selected sources count:', selectedCount);

    if (selectedCount === 0) {
        alert('Pilih minimal satu sumber anggaran terlebih dahulu!');
        return;
    }

    const total = calculateTotal(item);
    console.log('Total calculated:', total);

    // Get the active period ID
    const periodeId = selectedPeriodeId.value || props.periodeAktif?.[0]?.id;
    console.log('Periode ID to use:', periodeId);

    if (!periodeId) {
        alert('Tidak ada periode aktif yang dipilih!');
        return;
    }

    // Siapkan data untuk disimpan ke database
    const dataToSave = {
        skpd_tugas_id: item.id,
        periode_id: periodeId,
        sumber_anggaran: item.sumber_anggaran,
        values: {
            dak: item.dak,
            dak_peruntukan: item.dak_peruntukan,
            dak_fisik: item.dak_fisik,
            dak_non_fisik: item.dak_non_fisik,
            blud: item.blud,
        },
    };

    console.log('Data to save:', dataToSave);

    // Gunakan Inertia router untuk mengirim data ke server
    console.log('Sending POST request to /rencana-awal-anggaran-save');
    router.post('/rencana-awal-anggaran-save', dataToSave, {
        onSuccess: (response) => {
            console.log('✅ SUCCESS - Server response:', response);
            console.log('Data berhasil disimpan ke database');

            // Update UI silently without displaying any JSON data
            const updatedItem = anggaranItems.value.find((i) => i.id === item.id);
            if (updatedItem) {
                updatedItem.sumber_anggaran = { ...item.sumber_anggaran };
                updatedItem.dak = item.dak;
                updatedItem.dak_peruntukan = item.dak_peruntukan;
                updatedItem.dak_fisik = item.dak_fisik;
                updatedItem.dak_non_fisik = item.dak_non_fisik;
                updatedItem.blud = item.blud;

                // Untuk memastikan perubahan dirender dengan benar
                anggaranItems.value = [...anggaranItems.value];
            }

            // Show success message
            alert('✅ Data berhasil disimpan ke database!');

            // Reload data untuk konsistensi tanpa menampilkan alert atau JSON
            const skpdId = getSkpdId.value;
            if (skpdId) {
                router.visit(`/manajemenanggaran/${skpdId}`, {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['dataAnggaranTerakhir'],
                });
            }
        },
        onError: (errors) => {
            console.log('❌ ERROR - Server errors:', errors);
            errorMessage.value = Object.values(errors).join('\n');
            alert('❌ Terjadi kesalahan saat menyimpan data: ' + errorMessage.value);
            console.error('Full error details:', errors);
        },
        onBefore: () => {
            console.log('🚀 Starting request...');
        },
        onFinish: () => {
            console.log('🏁 Request finished');
        },
    });
};

// Computed untuk mendapatkan total anggaran dari semua sub kegiatan
const totalSeluruhAnggaran = computed(() => {
    return anggaranItems.value.reduce((total, item) => {
        return total + calculateTotal(item);
    }, 0);
});

// Computed untuk mendapatkan jumlah sub kegiatan yang sudah diisi
const jumlahSubKegiatanDiisi = computed(() => {
    return anggaranItems.value.filter((item) => calculateTotal(item) > 0).length;
});

// Computed untuk mendapatkan jumlah total sub kegiatan
const totalSubKegiatan = computed(() => {
    return anggaranItems.value.length;
});

// Handler for period change
const handlePeriodeChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newPeriodeId = target.value ? parseInt(target.value) : null;

    if (selectedPeriodeId.value !== newPeriodeId) {
        selectedPeriodeId.value = newPeriodeId;

        // Reload data with the new period
        const skpdId = getSkpdId.value;
        if (skpdId) {
            router.visit(`/manajemenanggaran/${skpdId}?periode_id=${newPeriodeId || ''}`, {
                preserveState: true,
                only: ['dataAnggaranTerakhir'],
            });
        }
    }
};

// Computed untuk pesan status periode
const periodeMessage = computed(() => {
    if (isPeriodeAktif.value) {
        return `Periode "Rencana" sedang dibuka untuk tahun ${props.periodeAktif?.[0]?.tahun?.tahun || ''}. Anda dapat mengisi sumber dana.`;
    } else {
        if (props.semuaPeriodeAktif && props.semuaPeriodeAktif.length > 0) {
            const periodeList = props.semuaPeriodeAktif.map((p) => p.tahap.tahap).join(', ');
            return `Periode yang sedang aktif: ${periodeList}. Sumber dana hanya dapat diisi pada periode Rencana.`;
        } else {
            return 'Tidak ada periode yang aktif saat ini. Sumber dana tidak dapat diisi sampai periode Rencana dibuka.';
        }
    }
});
</script>

<template>
    <Head title="Manajemen Anggaran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <!-- Header Section - Diperkecil -->
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-3 rounded-full bg-blue-100 p-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-600">Manajemen Anggaran</h1>
                            <p class="text-sm text-gray-500">Kelola dan monitor anggaran perangkat daerah</p>
                        </div>
                    </div>

                    <!-- Add period selector -->
                    <div class="flex items-center">
                        <label for="periode-selector" class="mr-2 font-medium text-gray-700">Pilih Periode:</label>
                        <select
                            id="periode-selector"
                            class="rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            @change="handlePeriodeChange"
                            :value="selectedPeriodeId"
                        >
                            <option value="">Semua Periode</option>
                            <option
                                v-for="periode in props.semuaPeriodeAktif"
                                :key="periode.id"
                                :value="periode.id"
                                :selected="periode.id === selectedPeriodeId"
                            >
                                {{ periode.tahap.tahap }} - {{ periode.tahun.tahun }}
                            </option>
                        </select>

                        <div class="ml-4 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                            <span class="text-xs font-medium text-gray-500">Tahun Anggaran</span>
                            <div class="text-center text-lg font-bold text-blue-600">{{ props.tahunAktif?.tahun || 'Belum ada' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Period Card - Diperkecil -->
            <div class="overflow-hidden rounded-lg shadow-md" :class="isPeriodeAktif ? 'bg-green-600' : 'border-gray-200 bg-gray-100'">
                <div class="p-3 text-white">
                    <div class="flex items-center">
                        <div class="mr-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                :class="isPeriodeAktif ? 'text-white' : 'text-red-600'"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                v-if="isPeriodeAktif"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                :class="isPeriodeAktif ? 'text-white' : 'text-red-600'"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                v-else
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 :class="isPeriodeAktif ? 'font-semibold text-white' : 'font-semibold text-red-700'">Status Periode</h3>
                            <p :class="isPeriodeAktif ? 'text-white/90' : 'text-red-600'">{{ periodeMessage }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-lg">
                <div class="mb-2 flex items-center">
                    <h2 class="mb-2 text-lg font-semibold text-gray-600">Informasi Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Perangkat Daerah</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.user?.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.user?.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 text-sm font-medium text-gray-500">Nama Operator</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ props.user?.operator_name || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <h3 class="mb-1 text-xs font-medium text-emerald-700">Total Anggaran</h3>
                        <p class="text-lg font-bold text-green-600">Rp {{ formatCurrency(totalSeluruhAnggaran) }}</p>
                        <p class="mt-1 text-xs text-gray-600">{{ jumlahSubKegiatanDiisi }} dari {{ totalSubKegiatan }} sub kegiatan</p>
                    </div>
                </div>
            </div>

            <!-- Budget Table - Diperkecil -->
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-semibold text-gray-600">Detail Anggaran Sub Kegiatan</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Kegiatan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber Anggaran</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAU</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Peruntukan</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Fisik</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Non Fisik</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">BLUD</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="(item, index) in anggaranData"
                                :key="item.id"
                                :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                                class="hover:bg-blue-50"
                            >
                                <!-- Kode -->
                                <td class="px-4 py-3">
                                    <div class="inline-block rounded bg-blue-100 px-2 py-1 text-xs font-medium text-gray-500">
                                        {{ item.kode }}
                                    </div>
                                </td>

                                <!-- Sub Kegiatan -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-500">{{ item.jenis_nomenklatur }}</div>
                                </td>

                                <!-- Sumber Anggaran -->
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        <label class="flex items-center text-xs">
                                            <input
                                                type="checkbox"
                                                :checked="item.sumber_anggaran.dak"
                                                @change="(e) => handleSumberAnggaranChange(item, 'dak', e)"
                                                class="mr-2 h-3 w-3 rounded border-gray-300 text-indigo-600"
                                                :disabled="!isPeriodeAktif"
                                            />
                                            DAU
                                            <span v-if="item.sumber_anggaran.dak" class="ml-1 text-green-600">✓</span>
                                        </label>

                                        <label class="flex items-center text-xs">
                                            <input
                                                type="checkbox"
                                                :checked="item.sumber_anggaran.dak_peruntukan"
                                                @change="(e) => handleSumberAnggaranChange(item, 'dak_peruntukan', e)"
                                                class="mr-2 h-3 w-3 rounded border-gray-300 text-indigo-600"
                                                :disabled="!isPeriodeAktif"
                                            />
                                            DAK Peruntukan
                                            <span v-if="item.sumber_anggaran.dak_peruntukan" class="ml-1 text-green-600">✓</span>
                                        </label>

                                        <label class="flex items-center text-xs">
                                            <input
                                                type="checkbox"
                                                :checked="item.sumber_anggaran.dak_fisik"
                                                @change="(e) => handleSumberAnggaranChange(item, 'dak_fisik', e)"
                                                class="mr-2 h-3 w-3 rounded border-gray-300 text-indigo-600"
                                                :disabled="!isPeriodeAktif"
                                            />
                                            DAK Fisik
                                            <span v-if="item.sumber_anggaran.dak_fisik" class="ml-1 text-green-600">✓</span>
                                        </label>

                                        <label class="flex items-center text-xs">
                                            <input
                                                type="checkbox"
                                                :checked="item.sumber_anggaran.dak_non_fisik"
                                                @change="(e) => handleSumberAnggaranChange(item, 'dak_non_fisik', e)"
                                                class="mr-2 h-3 w-3 rounded border-gray-300 text-indigo-600"
                                                :disabled="!isPeriodeAktif"
                                            />
                                            DAK Non Fisik
                                            <span v-if="item.sumber_anggaran.dak_non_fisik" class="ml-1 text-green-600">✓</span>
                                        </label>

                                        <label class="flex items-center text-xs">
                                            <input
                                                type="checkbox"
                                                :checked="item.sumber_anggaran.blud"
                                                @change="(e) => handleSumberAnggaranChange(item, 'blud', e)"
                                                class="mr-2 h-3 w-3 rounded border-gray-300 text-indigo-600"
                                                :disabled="!isPeriodeAktif"
                                            />
                                            BLUD
                                            <span v-if="item.sumber_anggaran.blud" class="ml-1 text-green-600">✓</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- DAK Amount -->
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="number"
                                        :value="item.dak"
                                        @input="(e) => handleInputChange(item, 'dak', e)"
                                        :disabled="!item.sumber_anggaran.dak || !isPeriodeAktif"
                                        min="0"
                                        class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                        :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak || !isPeriodeAktif }"
                                    />
                                    <div v-if="item.dak > 0" class="mt-1 text-xs text-green-600">
                                        {{ formatCurrency(item.dak) }}
                                    </div>
                                </td>

                                <!-- DAK Peruntukan Amount -->
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="number"
                                        :value="item.dak_peruntukan"
                                        @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                                        :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif"
                                        min="0"
                                        class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                        :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_peruntukan || !isPeriodeAktif }"
                                    />
                                    <div v-if="item.dak_peruntukan > 0" class="mt-1 text-xs text-green-600">
                                        {{ formatCurrency(item.dak_peruntukan) }}
                                    </div>
                                </td>

                                <!-- DAK Fisik Amount -->
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="number"
                                        :value="item.dak_fisik"
                                        @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                                        :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeAktif"
                                        min="0"
                                        class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                        :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_fisik || !isPeriodeAktif }"
                                    />
                                    <div v-if="item.dak_fisik > 0" class="mt-1 text-xs text-green-600">
                                        {{ formatCurrency(item.dak_fisik) }}
                                    </div>
                                </td>

                                <!-- DAK Non Fisik Amount -->
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="number"
                                        :value="item.dak_non_fisik"
                                        @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                                        :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif"
                                        min="0"
                                        class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                        :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_non_fisik || !isPeriodeAktif }"
                                    />
                                    <div v-if="item.dak_non_fisik > 0" class="mt-1 text-xs text-green-600">
                                        {{ formatCurrency(item.dak_non_fisik) }}
                                    </div>
                                </td>

                                <!-- BLUD Amount -->
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="number"
                                        :value="item.blud"
                                        @input="(e) => handleInputChange(item, 'blud', e)"
                                        :disabled="!item.sumber_anggaran.blud || !isPeriodeAktif"
                                        min="0"
                                        class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                        :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.blud || !isPeriodeAktif }"
                                    />
                                    <div v-if="item.blud > 0" class="mt-1 text-xs text-green-600">
                                        {{ formatCurrency(item.blud) }}
                                    </div>
                                </td>

                                <!-- Action Button -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col items-center space-y-2">
                                        <button
                                            @click="saveItem(item)"
                                            :disabled="!isPeriodeAktif"
                                            class="rounded bg-blue-600 px-3 py-1 text-xs text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-400"
                                        >
                                            Simpan
                                        </button>

                                        <div class="text-xs text-gray-600">
                                            Total: <span class="font-bold text-green-600">{{ formatCurrency(calculateTotal(item)) }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Additional custom styles if needed */
.hover\:bg-blue-50:hover {
    transition: background-color 0.2s ease-in-out;
}

input[type='number'] {
    -moz-appearance: textfield;
}

input[type='number']::-webkit-outer-spin-button,
input[type='number']::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Checkbox styling */
input[type='checkbox']:checked {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

input[type='checkbox']:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Button disabled state */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Table responsive */
@media (max-width: 768px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
}
</style>
