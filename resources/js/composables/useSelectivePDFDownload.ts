import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import ActivityLogger from '@/services/activityLogger';

interface SumberDanaOption {
    id: number;
    nama: string;
    totalSubKegiatan: number;
    totalPagu: number;
    totalRealisasi: number;
    persentaseRealisasi: number;
}

interface PreviewData {
    totalSubKegiatan: number;
    totalPagu: number;
    totalRealisasi: number;
    avgRealisasi: number;
    selectedSourceNames: string[];
}

interface SelectivePDFState {
    showDialog: boolean;
    selectionMode: 'single' | 'multiple' | 'compare' | 'all';
    selectedSingleSource: number | null;
    selectedMultipleSources: number[];
    selectedCompareSources: number[];
    availableSources: SumberDanaOption[];
    previewData: PreviewData | null;
    showPreview: boolean;
    isLoading: boolean;
    error: string | null;
}

export function useSelectivePDFDownload(tid: number, tugasId: number, triwulanName: string) {
    const state = reactive<SelectivePDFState>({
        showDialog: false,
        selectionMode: 'single',
        selectedSingleSource: null,
        selectedMultipleSources: [],
        selectedCompareSources: [],
        availableSources: [],
        previewData: null,
        showPreview: false,
        isLoading: false,
        error: null,
    });

    // Computed properties
    const selectedSourceIds = computed(() => {
        switch (state.selectionMode) {
            case 'single':
                return state.selectedSingleSource ? [state.selectedSingleSource] : [];
            case 'multiple':
                return state.selectedMultipleSources;
            case 'compare':
                return state.selectedCompareSources;
            case 'all':
                return state.availableSources.map(s => s.id);
            default:
                return [];
        }
    });

    const selectedSourceNames = computed(() => {
        const ids = selectedSourceIds.value;
        return state.availableSources
            .filter(source => ids.includes(source.id))
            .map(source => source.nama);
    });

    const canDownload = computed(() => {
        return selectedSourceIds.value.length > 0;
    });

    const isValidSelection = computed(() => {
        const count = selectedSourceIds.value.length;
        switch (state.selectionMode) {
            case 'single':
                return count === 1;
            case 'multiple':
                return count >= 2 && count <= 5;
            case 'compare':
                return count >= 2 && count <= 3;
            case 'all':
                return true;
            default:
                return false;
        }
    });

    // Methods
    const loadAvailableSources = async () => {
        try {
            state.isLoading = true;
            state.error = null;
            
            const response = await fetch(`/api/triwulan/${tid}/tugas/${tugasId}/sumber-dana`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load available sources');
            }

            const data = await response.json();
            state.availableSources = data.available_sources || [];
        } catch (error) {
            console.error('Failed to load available sources:', error);
            state.error = 'Gagal memuat data sumber dana. Silakan coba lagi.';
        } finally {
            state.isLoading = false;
        }
    };

    const generatePreview = async () => {
        try {
            state.isLoading = true;
            state.error = null;
            
            if (!isValidSelection.value) {
                state.error = 'Seleksi sumber dana tidak valid untuk mode yang dipilih.';
                return;
            }

            const selectedIds = selectedSourceIds.value;
            
            const response = await fetch(`/api/triwulan/${tid}/tugas/${tugasId}/preview`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    sumber_dana_ids: selectedIds,
                    mode: state.selectionMode,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to generate preview');
            }

            const data = await response.json();
            state.previewData = {
                totalSubKegiatan: data.total_subkegiatan,
                totalPagu: data.total_pagu,
                totalRealisasi: data.total_realisasi,
                avgRealisasi: data.avg_realisasi,
                selectedSourceNames: selectedSourceNames.value,
            };
            state.showPreview = true;
        } catch (error) {
            console.error('Failed to generate preview:', error);
            state.error = 'Gagal membuat preview. Silakan coba lagi.';
        } finally {
            state.isLoading = false;
        }
    };

    const downloadSelectivePDF = async () => {
        try {
            state.isLoading = true;
            state.error = null;

            if (!isValidSelection.value) {
                state.error = 'Seleksi sumber dana tidak valid untuk mode yang dipilih.';
                return;
            }

            const selectedIds = selectedSourceIds.value;
            
            // Generate PDF via form submission for file download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/pdf/triwulan/${tid}/tugas/${tugasId}/selective`;
            form.target = '_blank';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            form.appendChild(csrfInput);

            // Add sumber dana IDs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'sumber_dana_ids[]';
                input.value = id.toString();
                form.appendChild(input);
            });

            // Add mode
            const modeInput = document.createElement('input');
            modeInput.type = 'hidden';
            modeInput.name = 'mode';
            modeInput.value = state.selectionMode;
            form.appendChild(modeInput);

            // Add default PDF settings
            const defaultSettings = {
                paper_size: 'A4',
                orientation: 'landscape',
                margin_top: 20,
                margin_right: 20,
                margin_bottom: 20,
                margin_left: 20,
                penandatangan_1_tempat: 'Kota/Kabupaten',
                penandatangan_1_tanggal: new Date().toISOString().split('T')[0],
                penandatangan_1_nama: 'Nama Pejabat',
                penandatangan_1_jabatan: 'Jabatan Pejabat',
                penandatangan_1_nip: 'NIP Pejabat',
            };

            Object.entries(defaultSettings).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value.toString();
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Log activity
            await ActivityLogger.logPDFDownload(`${triwulanName} Selective`, {
                tugas_id: tugasId,
                triwulan_id: tid,
                selected_sources: selectedIds,
                selected_source_names: selectedSourceNames.value,
                mode: state.selectionMode,
                total_sources: selectedIds.length,
            });

            // Close dialog
            state.showDialog = false;
            resetState();
        } catch (error) {
            console.error('Failed to download PDF:', error);
            state.error = 'Gagal mengunduh PDF. Silakan coba lagi.';
        } finally {
            state.isLoading = false;
        }
    };

    const resetState = () => {
        state.selectedSingleSource = null;
        state.selectedMultipleSources = [];
        state.selectedCompareSources = [];
        state.previewData = null;
        state.showPreview = false;
        state.error = null;
        state.selectionMode = 'single';
    };

    const openDialog = async () => {
        resetState();
        state.showDialog = true;
        await loadAvailableSources();
    };

    const closeDialog = () => {
        state.showDialog = false;
        resetState();
    };

    const switchMode = (mode: SelectivePDFState['selectionMode']) => {
        state.selectionMode = mode;
        resetState();
        state.selectionMode = mode; // Keep the mode after reset
        state.showPreview = false;
    };

    return {
        state,
        selectedSourceIds,
        selectedSourceNames,
        canDownload,
        isValidSelection,
        loadAvailableSources,
        generatePreview,
        downloadSelectivePDF,
        openDialog,
        closeDialog,
        switchMode,
        resetState,
    };
} 