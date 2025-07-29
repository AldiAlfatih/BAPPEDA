<template>
    <Dialog v-model:open="props.open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-4xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    📄 Download PDF Selective - {{ props.triwulanName }}
                </DialogTitle>
                <DialogDescription>
                    Pilih sumber dana tertentu untuk laporan PDF yang lebih fokus dan relevan
                </DialogDescription>
            </DialogHeader>

            <!-- Error Alert -->
            <div v-if="selectivePDF.state.error" class="mb-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800">{{ selectivePDF.state.error }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div v-if="selectivePDF.state.isLoading" class="mb-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-blue-800">Memproses...</span>
                    </div>
                </div>
            </div>

            <!-- Selection Mode Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            v-for="mode in selectionModes"
                            :key="mode.value"
                            @click="selectivePDF.switchMode(mode.value)"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                selectivePDF.state.selectionMode === mode.value
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            {{ mode.icon }} {{ mode.label }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Single Mode -->
            <div v-if="selectivePDF.state.selectionMode === 'single'" class="mb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">🎯 Pilih Satu Sumber Dana</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="source in selectivePDF.state.availableSources"
                        :key="source.id"
                        @click="selectivePDF.state.selectedSingleSource = source.id"
                        :class="[
                            'border-2 rounded-lg p-4 cursor-pointer transition-all',
                            selectivePDF.state.selectedSingleSource === source.id
                                ? 'border-blue-500 bg-blue-50'
                                : 'border-gray-200 hover:border-gray-300'
                        ]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900">{{ source.nama }}</h5>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ source.totalSubKegiatan }} sub kegiatan
                                </p>
                                <p class="text-sm font-medium text-blue-600 mt-1">
                                    Total: {{ formatRupiah(source.totalPagu) }}
                                </p>
                                <p class="text-xs text-green-600 mt-1">
                                    Realisasi: {{ source.persentaseRealisasi.toFixed(1) }}%
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <div :class="[
                                    'w-4 h-4 rounded-full border-2',
                                    selectivePDF.state.selectedSingleSource === source.id
                                        ? 'bg-blue-500 border-blue-500'
                                        : 'border-gray-300'
                                ]"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multiple Mode -->
            <div v-else-if="selectivePDF.state.selectionMode === 'multiple'" class="mb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-2">📊 Pilih Multiple Sumber Dana</h4>
                <p class="text-sm text-gray-600 mb-4">Pilih 2-5 sumber dana untuk laporan gabungan</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="source in selectivePDF.state.availableSources"
                        :key="source.id"
                        @click="toggleMultipleSource(source.id)"
                        :class="[
                            'border-2 rounded-lg p-4 cursor-pointer transition-all',
                            selectivePDF.state.selectedMultipleSources.includes(source.id)
                                ? 'border-green-500 bg-green-50'
                                : 'border-gray-200 hover:border-gray-300',
                            selectivePDF.state.selectedMultipleSources.length >= 5 && 
                                !selectivePDF.state.selectedMultipleSources.includes(source.id)
                                ? 'opacity-50 cursor-not-allowed'
                                : ''
                        ]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900">{{ source.nama }}</h5>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ source.totalSubKegiatan }} sub kegiatan
                                </p>
                                <p class="text-sm font-medium text-green-600 mt-1">
                                    Total: {{ formatRupiah(source.totalPagu) }}
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    Realisasi: {{ source.persentaseRealisasi.toFixed(1) }}%
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <div :class="[
                                    'w-4 h-4 rounded border-2 flex items-center justify-center',
                                    selectivePDF.state.selectedMultipleSources.includes(source.id)
                                        ? 'bg-green-500 border-green-500'
                                        : 'border-gray-300'
                                ]">
                                    <svg v-if="selectivePDF.state.selectedMultipleSources.includes(source.id)" 
                                         class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compare Mode -->
            <div v-else-if="selectivePDF.state.selectionMode === 'compare'" class="mb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-2">⚖️ Pilih untuk Perbandingan</h4>
                <p class="text-sm text-gray-600 mb-4">Pilih 2-3 sumber dana untuk analisis komparatif</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        v-for="source in selectivePDF.state.availableSources"
                        :key="source.id"
                        @click="toggleCompareSource(source.id)"
                        :class="[
                            'border-2 rounded-lg p-4 cursor-pointer transition-all',
                            selectivePDF.state.selectedCompareSources.includes(source.id)
                                ? 'border-orange-500 bg-orange-50'
                                : 'border-gray-200 hover:border-gray-300',
                            selectivePDF.state.selectedCompareSources.length >= 3 && 
                                !selectivePDF.state.selectedCompareSources.includes(source.id)
                                ? 'opacity-50 cursor-not-allowed'
                                : ''
                        ]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900 text-sm">{{ source.nama }}</h5>
                                <div class="mt-2 space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span>Sub Kegiatan:</span>
                                        <span class="font-medium">{{ source.totalSubKegiatan }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Pagu:</span>
                                        <span class="font-medium text-blue-600">{{ formatRupiahShort(source.totalPagu) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Realisasi:</span>
                                        <span class="font-medium text-green-600">{{ source.persentaseRealisasi.toFixed(1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <div :class="[
                                    'w-4 h-4 rounded border-2 flex items-center justify-center',
                                    selectivePDF.state.selectedCompareSources.includes(source.id)
                                        ? 'bg-orange-500 border-orange-500'
                                        : 'border-gray-300'
                                ]">
                                    <svg v-if="selectivePDF.state.selectedCompareSources.includes(source.id)" 
                                         class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Mode -->
            <div v-else-if="selectivePDF.state.selectionMode === 'all'" class="mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-blue-900 mb-2">📋 Semua Sumber Dana</h4>
                    <p class="text-sm text-blue-700 mb-3">
                        Laporan akan mencakup semua sumber dana yang tersedia ({{ selectivePDF.state.availableSources.length }} sumber dana)
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="source in selectivePDF.state.availableSources"
                            :key="source.id"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                        >
                            {{ source.nama }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Preview Summary -->
            <div v-if="selectivePDF.state.showPreview && selectivePDF.state.previewData" class="mb-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-3">👁️ Preview Summary</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Sumber Dana:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ selectivePDF.state.previewData.selectedSourceNames.join(', ') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Sub Kegiatan:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ selectivePDF.state.previewData.totalSubKegiatan }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Pagu:</span>
                                <span class="text-sm font-medium text-blue-600">
                                    {{ formatRupiah(selectivePDF.state.previewData.totalPagu) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Avg Realisasi:</span>
                                <span class="text-sm font-medium text-green-600">
                                    {{ selectivePDF.state.previewData.avgRealisasi.toFixed(1) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-500">
                    <span v-if="selectivePDF.selectedSourceIds.value.length > 0">
                        {{ selectivePDF.selectedSourceIds.value.length }} sumber dana dipilih
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <Button variant="outline" @click="selectivePDF.closeDialog()">
                        Batal
                    </Button>
                    <Button 
                        v-if="!selectivePDF.state.showPreview"
                        @click="selectivePDF.generatePreview()"
                        :disabled="!selectivePDF.isValidSelection.value || selectivePDF.state.isLoading"
                        class="bg-gray-600 hover:bg-gray-700"
                    >
                        👁️ Preview Data
                    </Button>
                    <Button 
                        @click="selectivePDF.downloadSelectivePDF()"
                        :disabled="!selectivePDF.isValidSelection.value || selectivePDF.state.isLoading"
                        class="bg-blue-600 hover:bg-blue-700"
                    >
                        📥 Download PDF
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { useSelectivePDFDownload } from '@/composables/useSelectivePDFDownload';

interface Props {
    open: boolean;
    tid: number;
    tugasId: number;
    triwulanName: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

// Initialize selective PDF composable
const selectivePDF = useSelectivePDFDownload(props.tid, props.tugasId, props.triwulanName);

// Selection modes configuration
const selectionModes = [
    { value: 'single', label: 'Single Source', icon: '🎯' },
    { value: 'multiple', label: 'Multiple Sources', icon: '📊' },
    { value: 'compare', label: 'Compare Sources', icon: '⚖️' },
    { value: 'all', label: 'All Sources', icon: '📋' },
] as const;

// Helper functions
const formatRupiah = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatRupiahShort = (value: number): string => {
    if (value >= 1e9) {
        return `Rp ${(value / 1e9).toFixed(1)}M`;
    } else if (value >= 1e6) {
        return `Rp ${(value / 1e6).toFixed(1)}Jt`;
    }
    return formatRupiah(value);
};

const toggleMultipleSource = (sourceId: number) => {
    const index = selectivePDF.state.selectedMultipleSources.indexOf(sourceId);
    if (index > -1) {
        selectivePDF.state.selectedMultipleSources.splice(index, 1);
    } else if (selectivePDF.state.selectedMultipleSources.length < 5) {
        selectivePDF.state.selectedMultipleSources.push(sourceId);
    }
    selectivePDF.state.showPreview = false;
};

const toggleCompareSource = (sourceId: number) => {
    const index = selectivePDF.state.selectedCompareSources.indexOf(sourceId);
    if (index > -1) {
        selectivePDF.state.selectedCompareSources.splice(index, 1);
    } else if (selectivePDF.state.selectedCompareSources.length < 3) {
        selectivePDF.state.selectedCompareSources.push(sourceId);
    }
    selectivePDF.state.showPreview = false;
};

// Watch for dialog open state to load data
watch(() => props.open, (isOpen) => {
    if (isOpen) {
        selectivePDF.openDialog();
    }
});
</script> 