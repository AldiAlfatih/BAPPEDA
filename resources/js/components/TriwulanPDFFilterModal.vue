<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Download PDF {{ triwulanName }} - Pengaturan & Filter</DialogTitle>
                <DialogDescription>
                    Atur pengaturan PDF dan pilih sumber dana yang ingin ditampilkan dalam PDF {{ triwulanName }}
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6">
                <!-- Loading State -->
                <div v-if="loading" class="flex items-center justify-center py-8">
                    <div class="flex items-center space-x-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                        <span class="text-sm text-gray-600">Memuat data sumber dana...</span>
                    </div>
                </div>

                <!-- PDF Settings -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Pengaturan PDF</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Paper Size -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kertas</label>
                            <select v-model="pdfSettings.paper_size" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="A4">A4</option>
                                <option value="A3">A3</option>
                                <option value="Letter">Letter</option>
                            </select>
                        </div>

                        <!-- Orientation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Orientasi</label>
                            <select v-model="pdfSettings.orientation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>
                    </div>

                    <!-- Margins -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Margin (mm)</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Atas</label>
                                <input v-model.number="pdfSettings.margin_top" type="number" min="10" max="50" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Kanan</label>
                                <input v-model.number="pdfSettings.margin_right" type="number" min="10" max="50" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Bawah</label>
                                <input v-model.number="pdfSettings.margin_bottom" type="number" min="10" max="50" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Kiri</label>
                                <input v-model.number="pdfSettings.margin_left" type="number" min="10" max="50" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Penandatangan</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
                            <input v-model="signatureSettings.tempat" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input v-model="signatureSettings.tanggal" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input v-model="signatureSettings.nama" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <input v-model="signatureSettings.jabatan" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input v-model="signatureSettings.nip" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Sumber Dana Filter -->
                <div v-if="!loading" class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Filter Sumber Dana</h3>
                    
                    <!-- No Data State -->
                    <div v-if="availableSumberDana.length === 0" class="text-center py-6">
                        <p class="text-gray-600">Tidak ada data sumber dana untuk {{ triwulanName }}</p>
                    </div>

                    <!-- Filter Options -->
                    <div v-else class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900">Pilih Sumber Dana:</h4>
                            <div class="flex space-x-2">
                                <button
                                    @click="selectAll"
                                    class="text-xs text-blue-600 hover:text-blue-800"
                                >
                                    Pilih Semua
                                </button>
                                <span class="text-gray-300">|</span>
                                <button
                                    @click="selectNone"
                                    class="text-xs text-gray-600 hover:text-gray-800"
                                >
                                    Batal Pilih
                                </button>
                            </div>
                        </div>

                        <div class="max-h-40 overflow-y-auto space-y-2 border rounded-lg p-3 bg-gray-50">
                            <label
                                v-for="sumberDana in availableSumberDana"
                                :key="sumberDana.id"
                                class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded"
                            >
                                <input
                                    type="checkbox"
                                    :value="sumberDana.id"
                                    v-model="selectedSumberDanaIds"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700">{{ sumberDana.nama }}</span>
                                <span class="text-xs text-gray-500 ml-auto">
                                    ({{ sumberDana.count }} {{ sumberDana.count === 1 ? 'item' : 'items' }})
                                </span>
                            </label>
                        </div>

                        <!-- Selection Summary -->
                        <div v-if="selectedSumberDanaIds.length > 0" class="bg-blue-50 p-3 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>{{ selectedSumberDanaIds.length }}</strong> sumber dana dipilih dari 
                                <strong>{{ availableSumberDana.length }}</strong> yang tersedia
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter class="flex justify-between pt-4 border-t">
                <Button variant="outline" @click="closeModal">
                    Batal
                </Button>
                <Button 
                    @click="downloadPDF" 
                    :disabled="selectedSumberDanaIds.length === 0 || loading || !isFormValid"
                    class="bg-red-600 hover:bg-red-700"
                >
                    <svg v-if="downloading" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ downloading ? 'Membuat PDF...' : 'Download PDF' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface SumberDana {
    id: number;
    nama: string;
    count: number;
}

interface Props {
    open: boolean;
    tid: number;
    tugasId: number;
    triwulanName: string;
    monitoringTargets: Array<any>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value)
});

const loading = ref(false);
const downloading = ref(false);
const selectedSumberDanaIds = ref<number[]>([]);

// PDF Settings with default values
const pdfSettings = ref({
    paper_size: 'A4',
    orientation: 'landscape',
    margin_top: 20,
    margin_right: 20,
    margin_bottom: 20,
    margin_left: 20,
});

// Signature Settings with default values
const signatureSettings = ref({
    tempat: 'Batam',
    tanggal: new Date().toISOString().split('T')[0],
    nama: 'Kepala SKPD',
    jabatan: 'Kepala Satuan Kerja Perangkat Daerah',
    nip: '000000000000000000'
});

// Extract unique sumber dana from monitoring targets
const availableSumberDana = computed<SumberDana[]>(() => {
    if (!props.monitoringTargets || props.monitoringTargets.length === 0) {
        return [];
    }

    // Group by sumber_anggaran_id and count occurrences
    const sumberDanaMap = new Map<number, { nama: string; count: number }>();
    
    props.monitoringTargets.forEach(target => {
        const id = target.sumber_anggaran_id;
        const nama = target.sumber_anggaran_nama || `Sumber Dana ${id}`;
        
        if (sumberDanaMap.has(id)) {
            sumberDanaMap.get(id)!.count++;
        } else {
            sumberDanaMap.set(id, { nama, count: 1 });
        }
    });

    // Convert to array and sort by name
    return Array.from(sumberDanaMap.entries())
        .map(([id, data]) => ({
            id,
            nama: data.nama,
            count: data.count
        }))
        .sort((a, b) => a.nama.localeCompare(b.nama));
});

// Form validation
const isFormValid = computed(() => {
    return signatureSettings.value.tempat.trim().length > 0 &&
           signatureSettings.value.tanggal.length > 0 &&
           signatureSettings.value.nama.trim().length > 0 &&
           signatureSettings.value.jabatan.trim().length > 0 &&
           signatureSettings.value.nip.trim().length > 0;
});

// Auto-select all when modal opens
watch(() => props.open, (newValue) => {
    if (newValue && availableSumberDana.value.length > 0) {
        selectAll();
    }
});

const selectAll = () => {
    selectedSumberDanaIds.value = availableSumberDana.value.map(item => item.id);
};

const selectNone = () => {
    selectedSumberDanaIds.value = [];
};

const closeModal = () => {
    isOpen.value = false;
    selectedSumberDanaIds.value = [];
};

const downloadPDF = async () => {
    if (selectedSumberDanaIds.value.length === 0) {
        alert('Pilih minimal satu sumber dana!');
        return;
    }

    if (!isFormValid.value) {
        alert('Mohon lengkapi semua field penandatangan!');
        return;
    }

    try {
        downloading.value = true;

        // Prepare form data for PDF generation
        const formData = new FormData();
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }

        // Add selected sumber dana IDs
        selectedSumberDanaIds.value.forEach(id => {
            formData.append('sumber_dana_ids[]', id.toString());
        });

        // Add PDF settings
        formData.append('mode', 'filtered');
        formData.append('paper_size', pdfSettings.value.paper_size);
        formData.append('orientation', pdfSettings.value.orientation);
        formData.append('margin_top', pdfSettings.value.margin_top.toString());
        formData.append('margin_right', pdfSettings.value.margin_right.toString());
        formData.append('margin_bottom', pdfSettings.value.margin_bottom.toString());
        formData.append('margin_left', pdfSettings.value.margin_left.toString());

        // Add signature data
        formData.append('penandatangan_1_tempat', signatureSettings.value.tempat);
        formData.append('penandatangan_1_tanggal', signatureSettings.value.tanggal);
        formData.append('penandatangan_1_nama', signatureSettings.value.nama);
        formData.append('penandatangan_1_jabatan', signatureSettings.value.jabatan);
        formData.append('penandatangan_1_nip', signatureSettings.value.nip);

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pdf/triwulan/${props.tid}/${props.tugasId}/filtered`;
        form.target = '_blank';
        form.style.display = 'none';

        // Add all form data as hidden inputs
        for (const [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value.toString();
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        // Close modal after successful submission
        setTimeout(() => {
            closeModal();
        }, 1000);

    } catch (error) {
        console.error('Error generating filtered PDF:', error);
        alert('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.');
    } finally {
        downloading.value = false;
    }
};
</script> 