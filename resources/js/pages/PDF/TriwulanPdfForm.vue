<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Calendar, Download, FileText, Settings } from 'lucide-vue-next';

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
}

const props = defineProps<Props>();

const form = useForm({
    penandatangan_1_tempat: 'Parepare',
    penandatangan_1_tanggal: new Date().toISOString().split('T')[0],
    penandatangan_1_nama: props.skpd.kepala_aktif?.user?.name || '',
    penandatangan_1_jabatan: 'Kepala SKPD',
    penandatangan_1_nip: props.skpd.kepala_aktif?.user?.user_detail?.nip || '',
    penandatangan_2_tempat: 'Parepare',
    penandatangan_2_tanggal: new Date().toISOString().split('T')[0],
    penandatangan_2_nama: props.skpd.tim_kerja_aktif?.operator?.name || '',
    penandatangan_2_jabatan: 'Operator',
    penandatangan_2_nip: props.skpd.tim_kerja_aktif?.operator?.user_detail?.nip || '',
    paper_size: 'A4',
    orientation: 'portrait',
    margin_top: 20,
    margin_right: 20,
    margin_bottom: 20,
    margin_left: 20,
});

const isLoading = ref(false);

const submitForm = async () => {
    isLoading.value = true;
    
    try {
        // First, submit the form data to generate the PDF
        const response = await fetch(route('pdf.triwulan.generate', { tid: props.tid, tugasId: props.tugas.id }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(form.data())
        });
        
        if (response.ok) {
            // Create blob from response
            const blob = await response.blob();
            
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${props.triwulanName}_${props.skpd.nama_skpd}_${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        } else {
            throw new Error('Failed to generate PDF');
        }
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.');
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
        <div class="container mx-auto py-6 px-4 max-w-4xl">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <FileText class="w-6 h-6 text-blue-600" />
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        Konfigurasi PDF {{ triwulanName }}
                    </h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ skpd.nama_skpd }} - {{ tugas.kodeNomenklatur?.nomor_kode || '-' }} {{ tugas.kodeNomenklatur?.nomenklatur || '-' }}
                </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">


                <!-- Penandatangan 1 -->
                <Card>
                    <CardHeader>
                        <CardTitle>Penandatangan Pertama</CardTitle>
                        <CardDescription>
                            Informasi pejabat yang akan menandatangani (biasanya Kepala SKPD)
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="penandatangan_1_tempat">Tempat</Label>
                                <Input 
                                    id="penandatangan_1_tempat"
                                    v-model="form.penandatangan_1_tempat"
                                    required
                                    placeholder="Nama kota/tempat"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_1_tempat" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.penandatangan_1_tempat }}
                                </div>
                            </div>
                            <div>
                                <Label for="penandatangan_1_tanggal">Tanggal</Label>
                                <Input 
                                    id="penandatangan_1_tanggal"
                                    type="date"
                                    v-model="form.penandatangan_1_tanggal"
                                    required
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_1_tanggal" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.penandatangan_1_tanggal }}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="penandatangan_1_nama">Nama Lengkap</Label>
                                <Input 
                                    id="penandatangan_1_nama"
                                    v-model="form.penandatangan_1_nama"
                                    required
                                    placeholder="Nama lengkap penandatangan"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_1_nama" class="text-red-500 text-sm mt-1">
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
                                <div v-if="form.errors.penandatangan_1_jabatan" class="text-red-500 text-sm mt-1">
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
                            <div v-if="form.errors.penandatangan_1_nip" class="text-red-500 text-sm mt-1">
                                {{ form.errors.penandatangan_1_nip }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Penandatangan 2 -->
                <Card>
                    <CardHeader>
                        <CardTitle>Penandatangan Kedua</CardTitle>
                        <CardDescription>
                            Informasi pejabat kedua yang akan menandatangani (biasanya Operator/Penyusun)
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="penandatangan_2_tempat">Tempat</Label>
                                <Input 
                                    id="penandatangan_2_tempat"
                                    v-model="form.penandatangan_2_tempat"
                                    required
                                    placeholder="Nama kota/tempat"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_2_tempat" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.penandatangan_2_tempat }}
                                </div>
                            </div>
                            <div>
                                <Label for="penandatangan_2_tanggal">Tanggal</Label>
                                <Input 
                                    id="penandatangan_2_tanggal"
                                    type="date"
                                    v-model="form.penandatangan_2_tanggal"
                                    required
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_2_tanggal" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.penandatangan_2_tanggal }}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="penandatangan_2_nama">Nama Lengkap</Label>
                                <Input 
                                    id="penandatangan_2_nama"
                                    v-model="form.penandatangan_2_nama"
                                    required
                                    placeholder="Nama lengkap penandatangan"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_2_nama" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.penandatangan_2_nama }}
                                </div>
                            </div>
                            <div>
                                <Label for="penandatangan_2_jabatan">Jabatan</Label>
                                <Input 
                                    id="penandatangan_2_jabatan"
                                    v-model="form.penandatangan_2_jabatan"
                                    required
                                    placeholder="Jabatan penandatangan"
                                    class="mt-1"
                                />
                                <div v-if="form.errors.penandatangan_2_jabatan" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.penandatangan_2_jabatan }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <Label for="penandatangan_2_nip">NIP</Label>
                            <Input 
                                id="penandatangan_2_nip"
                                v-model="form.penandatangan_2_nip"
                                required
                                placeholder="NIP penandatangan"
                                class="mt-1"
                            />
                            <div v-if="form.errors.penandatangan_2_nip" class="text-red-500 text-sm mt-1">
                                {{ form.errors.penandatangan_2_nip }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pengaturan PDF -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Settings class="w-5 h-5" />
                            Pengaturan PDF
                        </CardTitle>
                        <CardDescription>
                            Konfigurasi format dan ukuran dokumen PDF
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <Label for="margin_top">Margin Atas (mm)</Label>
                                <Input 
                                    id="margin_top"
                                    type="number"
                                    v-model="form.margin_top"
                                    min="0"
                                    max="50"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="margin_right">Margin Kanan (mm)</Label>
                                <Input 
                                    id="margin_right"
                                    type="number"
                                    v-model="form.margin_right"
                                    min="0"
                                    max="50"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="margin_bottom">Margin Bawah (mm)</Label>
                                <Input 
                                    id="margin_bottom"
                                    type="number"
                                    v-model="form.margin_bottom"
                                    min="0"
                                    max="50"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="margin_left">Margin Kiri (mm)</Label>
                                <Input 
                                    id="margin_left"
                                    type="number"
                                    v-model="form.margin_left"
                                    min="0"
                                    max="50"
                                    class="mt-1"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <Button 
                        type="button"
                        variant="outline"
                        @click="goBack"
                        class="w-full sm:w-auto"
                    >
                        Kembali
                    </Button>
                    
                    <Button 
                        type="submit"
                        :disabled="form.processing || isLoading"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700"
                    >
                        <Download class="w-4 h-4 mr-2" />
                        {{ isLoading ? 'Sedang Membuat PDF...' : `Download PDF ${triwulanName}` }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 