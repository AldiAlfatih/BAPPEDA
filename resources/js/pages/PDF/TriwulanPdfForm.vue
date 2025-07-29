<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Calendar, Download, FileText, Filter, Settings } from 'lucide-vue-next';
import { ref, computed, reactive, watch, nextTick } from 'vue';

interface Props {
    tugas: {
        id: number;
        kodeNomenklatur?: {
            nomor_kode: string;
            nomenklatur: string;
        };
    };
    skpd: {
        id: number;
        nama_skpd: string;
        nama_dinas: string;
        kode_organisasi: string;
        no_dpa: string;
        kepala_aktif?: {
            user: {
                name: string;
                user_detail: {
                    nip: string;
                };
            };
        };
        tim_kerja_aktif?: {
            operator: {
                name: string;
                user_detail: {
                    nip: string;
                };
            };
        };
    };
    tid: number;
    triwulanName: string;
    availableTahun: number[];
    sumberDanaOptions: Array<{
        id: number;
        nama: string;
        count: number;
    }>;
}

const props = defineProps<Props>();

// Initialize form with proper defaults
const initialSumberDanaIds = props.sumberDanaOptions.map(item => item.id);

const form = useForm({
    tahun: props.availableTahun && props.availableTahun.length > 0 ? Math.max(...props.availableTahun) : new Date().getFullYear(),
    mode: 'all', // 'all' untuk semua data, 'filtered' untuk data terpilih
    sumber_dana_ids: initialSumberDanaIds, // Default semua terpilih
    penandatangan_1_tempat: 'Parepare',
    penandatangan_1_tanggal: new Date().toISOString().split('T')[0],
    penandatangan_1_nama: props.skpd.kepala_aktif?.user?.name || 'Andi Rahmat',
    penandatangan_1_jabatan: 'Kepala SKPD',
    penandatangan_1_nip: props.skpd.kepala_aktif?.user?.user_detail?.nip || '1987000003',
    paper_size: 'A4',
    orientation: 'landscape',
    margin_top: 20,
    margin_right: 20,
    margin_bottom: 20,
    margin_left: 20,
});

const isLoading = ref(false);

// Gunakan tahun yang tersedia dari backend
const availableYears = ref<number[]>(props.availableTahun);

// Computed property for status to ensure reactivity
const statusInfo = computed(() => {
    const selectedCount = form.sumber_dana_ids.length;
    const totalCount = props.sumberDanaOptions.length;
    
    if (selectedCount === totalCount) {
        return {
            text: '📊 Laporan Lengkap - Semua sumber dana akan ditampilkan',
            class: 'text-blue-700'
        };
    } else if (selectedCount > 0) {
        return {
            text: `🎯 Laporan Selektif - ${selectedCount} dari ${totalCount} sumber dana terpilih`,
            class: 'text-orange-700'
        };
    } else {
        return {
            text: '⚠️ Tidak ada sumber dana dipilih',
            class: 'text-red-700'
        };
    }
});

// Watch for changes in sumber_dana_ids
watch(() => form.sumber_dana_ids, (newVal, oldVal) => {
    // Form array updated
}, { deep: true });

// Create reactive checkbox states
const checkboxStates = reactive<Record<number, boolean>>({});

// Initialize checkbox states based on form data
props.sumberDanaOptions.forEach(option => {
    checkboxStates[option.id] = form.sumber_dana_ids.includes(option.id);
});

// Watch checkbox states and sync with form array
watch(checkboxStates, (newStates) => {
    // Update form.sumber_dana_ids based on checkbox states
    const selectedIds = Object.keys(newStates)
        .filter(key => newStates[Number(key)] === true)
        .map(key => Number(key));
    
    form.sumber_dana_ids = selectedIds;
}, { deep: true });

// Functions for sumber dana filter
const selectAllSumberDana = () => {
    // Update checkbox states
    props.sumberDanaOptions.forEach(option => {
        checkboxStates[option.id] = true;
    });
};

const selectNoneSumberDana = () => {
    // Update checkbox states
    props.sumberDanaOptions.forEach(option => {
        checkboxStates[option.id] = false;
    });
};

// toggleSumberDana is no longer needed as we use reactive checkbox states

const submitForm = async () => {
    isLoading.value = true;

    try {
        // Ensure sumber_dana_ids is an array and contains valid numbers
        const selectedIds = Array.isArray(form.sumber_dana_ids) ? form.sumber_dana_ids.filter(id => id != null && !isNaN(id)) : [];

        // Update form with cleaned data
        form.sumber_dana_ids = selectedIds;

        // Determine endpoint based on mode and selection
        const isFiltered = form.mode === 'filtered' || selectedIds.length < props.sumberDanaOptions.length;
        const endpoint = isFiltered 
            ? route('pdf.triwulan.filtered', { tid: props.tid, tugasId: props.tugas.id })
            : route('pdf.triwulan.generate', { tid: props.tid, tugasId: props.tugas.id });

        // Validate if filtered mode has at least one source selected
        if (isFiltered && selectedIds.length === 0) {
            alert('Pilih minimal satu sumber dana untuk PDF selektif!');
            isLoading.value = false;
            return;
        }

        // First, submit the form data to generate the PDF
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minutes timeout

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                Accept: 'application/json',
            },
            body: JSON.stringify(form.data()),
            signal: controller.signal,
        });

        clearTimeout(timeoutId);

        if (response.ok) {
            // Check if response is actually a PDF
            const contentType = response.headers.get('Content-Type');
            if (contentType && contentType.includes('application/pdf')) {
                // Create blob from response
                const blob = await response.blob();

                // Create download link with appropriate filename
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                
                // Generate filename based on mode
                const isFiltered = form.sumber_dana_ids.length < props.sumberDanaOptions.length;
                const modeText = isFiltered ? '_Selektif' : '_Lengkap';
                const cleanSkpdName = props.skpd.nama_skpd.replace(/[^a-zA-Z0-9]/g, '_');
                a.download = `${props.triwulanName}${modeText}_${cleanSkpdName}_${new Date().toISOString().split('T')[0]}.pdf`;
                
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                // Show success message
                alert('PDF berhasil diunduh!');
            } else {
                // Handle JSON error response
                const errorData = await response.json();
                throw new Error(errorData.error || 'Response bukan file PDF');
            }
        } else {
            // Handle HTTP error responses
            try {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP Error ${response.status}: ${response.statusText}`);
            } catch (jsonError) {
                throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
            }
        }
    } catch (error) {
        console.error('Error generating PDF:', error);
        
        if (error.name === 'AbortError') {
            alert('Timeout: Proses pembuatan PDF memakan waktu terlalu lama. Silakan coba lagi atau hubungi administrator.');
        } else if (error.message.includes('Failed to fetch')) {
            alert('Koneksi gagal. Pastikan koneksi internet Anda stabil dan coba lagi.');
        } else {
            alert(`Terjadi kesalahan saat membuat PDF: ${error.message}`);
        }
    } finally {
        isLoading.value = false;
    }
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="`Konfigurasi PDF ${triwulanName}`" />

    <AppLayout>
        <div class="container mx-auto max-w-4xl px-4 py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="mb-4 flex items-center gap-2">
                    <FileText class="h-6 w-6 text-blue-600" />
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Konfigurasi PDF {{ triwulanName }}</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ skpd.nama_skpd }} - {{ tugas.kodeNomenklatur?.nomor_kode || '-' }} - {{ tugas.kodeNomenklatur?.nomenklatur || '-' }}
                </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Tahun -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Periode Tahun
                        </CardTitle>
                        <CardDescription> Pilih tahun untuk data yang akan ditampilkan dalam laporan PDF </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div>
                            <Label for="tahun">Tahun</Label>
                            <Select v-model="form.tahun">
                                <SelectTrigger class="mt-1">
                                    <SelectValue placeholder="Pilih tahun" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="year in availableYears" :key="year" :value="year">
                                        {{ year }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.tahun" class="mt-1 text-sm text-red-500">
                                {{ form.errors.tahun }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Filter Sumber Dana -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="h-5 w-5" />
                            Filter Sumber Dana
                        </CardTitle>
                        <CardDescription>
                            Pilih sumber dana yang akan ditampilkan dalam laporan PDF. 
                            Jika semua dipilih, akan menampilkan laporan lengkap. 
                            Jika sebagian dipilih, akan menampilkan laporan selektif.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Tombol Select All/None -->
                        <div class="flex gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="selectAllSumberDana"
                                class="text-xs"
                            >
                                Pilih Semua
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="selectNoneSumberDana"
                                class="text-xs"
                            >
                                Hapus Semua
                            </Button>
                        </div>

                        <!-- Checkbox List Sumber Dana -->
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div
                                v-for="sumberDana in sumberDanaOptions"
                                :key="sumberDana.id"
                                class="flex items-center space-x-2 rounded-lg border p-3 hover:bg-gray-50"
                            >
                                <Checkbox
                                    :id="`sumber_dana_${sumberDana.id}`"
                                    v-model="checkboxStates[sumberDana.id]"
                                />
                                <label
                                    :for="`sumber_dana_${sumberDana.id}`"
                                    class="flex-1 cursor-pointer text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    {{ sumberDana.nama }}
                                </label>
                            </div>
                        </div>

                        <!-- Status Info -->
                        <div class="rounded-lg bg-blue-50 p-3">
                            <div class="text-sm">
                                <span class="font-medium">Status:</span>
                                <span :class="['ml-2', statusInfo.class]">
                                    {{ statusInfo.text }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Penandatangan -->
                <Card>
                    <CardHeader>
                        <CardTitle>Penandatangan</CardTitle>
                        <CardDescription> Informasi pejabat yang akan menandatangani (Kepala SKPD) </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label for="penandatangan_1_tempat">Tempat</Label>
                                <Input
                                    id="penandatangan_1_tempat"
                                    v-model="form.penandatangan_1_tempat"
                                    required
                                    placeholder="Nama kota/tempat"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_1_tempat" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.penandatangan_1_tempat }}
                                </div>
                            </div>
                            <div>
                                <Label for="penandatangan_1_tanggal">Tanggal</Label>
                                <Input id="penandatangan_1_tanggal" type="date" v-model="form.penandatangan_1_tanggal" required class="mt-1" />
                                <div v-if="form.errors.penandatangan_1_tanggal" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.penandatangan_1_tanggal }}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label for="penandatangan_1_nama">Nama Lengkap</Label>
                                <Input
                                    id="penandatangan_1_nama"
                                    v-model="form.penandatangan_1_nama"
                                    required
                                    placeholder="Nama lengkap penandatangan"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_1_nama" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.penandatangan_1_nama }}
                                </div>
                            </div>
                            <div>
                                <Label for="penandatangan_1_jabatan">Jabatan</Label>
                                <Input
                                    id="penandatangan_1_jabatan"
                                    v-model="form.penandatangan_1_jabatan"
                                    required
                                    placeholder="Jabatan penandatangan"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_1_jabatan" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.penandatangan_1_jabatan }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <Label for="penandatangan_1_nip">NIP</Label>
                            <Input
                                id="penandatangan_1_nip"
                                v-model="form.penandatangan_1_nip"
                                required
                                placeholder="NIP penandatangan"
                                class="mt-1"
                            />
                            <div v-if="form.errors.penandatangan_1_nip" class="mt-1 text-sm text-red-500">
                                {{ form.errors.penandatangan_1_nip }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pengaturan PDF -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Settings class="h-5 w-5" />
                            Pengaturan PDF
                        </CardTitle>
                        <CardDescription> Konfigurasi format dan ukuran dokumen PDF </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label for="paper_size">Ukuran Kertas</Label>
                                <Select v-model="form.paper_size">
                                    <SelectTrigger class="mt-1">
                                        <SelectValue placeholder="Pilih ukuran kertas" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="A4">A4</SelectItem>
                                        <SelectItem value="A3">A3</SelectItem>
                                        <SelectItem value="Letter">Letter</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <Label for="orientation">Orientasi</Label>
                                <Select v-model="form.orientation">
                                    <SelectTrigger class="mt-1">
                                        <SelectValue placeholder="Pilih orientasi" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="portrait">Portrait</SelectItem>
                                        <SelectItem value="landscape">Landscape</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <Separator />

                        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                            <div>
                                <Label for="margin_top">Margin Atas (mm)</Label>
                                <Input id="margin_top" type="number" v-model="form.margin_top" min="0" max="50" class="mt-1" />
                            </div>
                            <div>
                                <Label for="margin_right">Margin Kanan (mm)</Label>
                                <Input id="margin_right" type="number" v-model="form.margin_right" min="0" max="50" class="mt-1" />
                            </div>
                            <div>
                                <Label for="margin_bottom">Margin Bawah (mm)</Label>
                                <Input id="margin_bottom" type="number" v-model="form.margin_bottom" min="0" max="50" class="mt-1" />
                            </div>
                            <div>
                                <Label for="margin_left">Margin Kiri (mm)</Label>
                                <Input id="margin_left" type="number" v-model="form.margin_left" min="0" max="50" class="mt-1" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Action Buttons -->
                <div class="flex flex-col justify-between gap-4 sm:flex-row">
                    <Button type="button" variant="outline" @click="goBack" class="w-full sm:w-auto"> Kembali </Button>

                    <Button type="submit" :disabled="form.processing || isLoading" class="w-full bg-blue-600 hover:bg-blue-700 sm:w-auto">
                        <Download class="mr-2 h-4 w-4" />
                        {{ isLoading ? 'Sedang Membuat PDF...' : `Download PDF ${triwulanName}` }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
