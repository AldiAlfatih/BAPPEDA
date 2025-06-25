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
    rencana_awal?: Record<string, number>;
    parsial_data?: Record<string, number>;
    budget_change_data?: Record<string, number>;
    is_parsial_enabled?: boolean;
    is_budget_change_enabled?: boolean;
}

interface Props {
    user?: {
        id: number;
        name: string;
        email: string;
        skpd?: Array<{
            id: number;
            nama_skpd: string;
            kode_organisasi: string;
        }>;
        nama_dinas?: string;
        operator_name?: string;
        kepala_name?: string;
        kode_organisasi?: string;
    };
    skpdTugas?: Array<{
        id: number;
        kode_nomenklatur: {
            id: number;
            nomenklatur: string;
            jenis_nomenklatur: number;
        };
    }>;
    urusanList?: Array<{
        id: number;
        nomenklatur: string;
    }>;
    bidangUrusanList?: Array<{
        id: number;
        nomenklatur: string;
        urusan_id: number;
    }>;
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
    };
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
                dak?: number;
                dak_peruntukan?: number;
                dak_fisik?: number;
                dak_non_fisik?: number;
                blud?: number;
                rencana_awal?: number;
                parsial?: number;
                total?: number;
            } | {
                rencana_awal: Record<string, number>;
                parsial: Record<string, number>;
                [key: string]: any;
            };
            is_parsial_enabled?: boolean;
        }
    >;
    selectedPeriodeId?: number | null;
    periodeRencanaFallback?: {
        id: number;
        tahap: {
            id: number;
            tahap: string;
        };
        tahun: {
            id: number;
            tahun: string;
        };
    } | null;
    isParsialMode?: boolean;
    isBudgetChangeMode?: boolean;
    pageTitle?: string;
}

const props = defineProps<Props>();

// Create a reactive array for anggaran items
const anggaranItems = ref<AnggaranItem[]>([]);

// Ref untuk status periode dan pesan error
const errorMessage = ref('');

// Add reactive state for selected period
const selectedPeriodeId = ref<number | null>(props.selectedPeriodeId || null);

// Helper function to get the skpdId safely
const getSkpdId = computed(() => {
    if (props.user?.skpd && Array.isArray(props.user.skpd) && props.user.skpd.length > 0) {
        return props.user.skpd[0].id;
    }
    return null;
});

// PERBAIKAN: Computed untuk cek apakah periode aktif (berbeda untuk setiap mode)
const isPeriodeRencanaAktif = computed(() => {
    if (isBudgetChangeMode.value) {
        // In budget change mode, check for active Triwulan 4 period
        return props.periodeAktif && props.periodeAktif.length > 0 && 
               props.periodeAktif.some(p => p.tahap?.tahap === 'Triwulan 4');
    } else if (isParsialMode.value) {
        // In parsial mode, check for active triwulan periods
        return props.periodeAktif && props.periodeAktif.length > 0;
    }
    // In normal mode, check for active rencana periods
    return props.periodeAktif && props.periodeAktif.length > 0;
});

// Computed to check if we're in parsial mode
const isParsialMode = computed(() => {
    return props.isParsialMode || false;
});

// Computed to check if we're in budget change mode
const isBudgetChangeMode = computed(() => {
    return props.isBudgetChangeMode || false;
});

// Computed for page title
const pageTitle = computed(() => {
    if (props.pageTitle) {
        return props.pageTitle;
    } else if (isBudgetChangeMode.value) {
        return 'Perubahan Anggaran - Triwulan 4';
    } else if (isParsialMode.value) {
        return 'Manajemen Anggaran Parsial';
    } else {
        return 'Manajemen Anggaran';
    }
});

// PERBAIKAN: Computed untuk informasi periode yang ditampilkan
const currentPeriodeInfo = computed(() => {
    if (selectedPeriodeId.value && props.semuaPeriodeAktif) {
        const selectedPeriode = props.semuaPeriodeAktif.find(p => p.id === selectedPeriodeId.value);
        if (selectedPeriode) {
            return {
                id: selectedPeriode.id,
                tahap: selectedPeriode.tahap.tahap,
                tahun: selectedPeriode.tahun.tahun,
                isActive: props.periodeAktif?.some(p => p.id === selectedPeriode.id) || false
            };
        }
    }
    
    if (isPeriodeRencanaAktif.value) {
        const activePeriode = props.periodeAktif![0];
        return {
            id: activePeriode.id,
            tahap: activePeriode.tahap.tahap,
            tahun: activePeriode.tahun.tahun,
            isActive: true
        };
    }
    
    if (props.periodeRencanaFallback) {
        return {
            id: props.periodeRencanaFallback.id,
            tahap: props.periodeRencanaFallback.tahap.tahap,
            tahun: props.periodeRencanaFallback.tahun.tahun,
            isActive: false
        };
    }
    
    return null;
});

// Initialize with the active period if available, or the selected period, or fallback period
onMounted(() => {
    if (props.selectedPeriodeId) {
        selectedPeriodeId.value = props.selectedPeriodeId;
    } else if (isPeriodeRencanaAktif.value) {
        selectedPeriodeId.value = props.periodeAktif![0].id;
    } else if (props.periodeRencanaFallback) {
        selectedPeriodeId.value = props.periodeRencanaFallback.id;
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
                // Handle different modes: normal, parsial, and budget change
                if ((isParsialMode.value || isBudgetChangeMode.value) && lastData.values && typeof lastData.values === 'object' && 'rencana_awal' in lastData.values) {
                    // Parsial or Budget Change mode with structured data
                    const structuredData = lastData.values as { 
                        rencana_awal: Record<string, number>; 
                        parsial: Record<string, number>;
                        budget_change?: Record<string, number>;
                    };
                    console.log(`Task ${task.id} - Structured data (${isBudgetChangeMode.value ? 'Budget Change' : 'Parsial'} mode):`, structuredData);
                    
                    // Determine which values to use for input fields based on mode
                    let inputValues;
                    if (isBudgetChangeMode.value) {
                        // In budget change mode, use budget_change values for input
                        inputValues = structuredData.budget_change || {};
                    } else {
                        // In parsial mode, use parsial values for input
                        inputValues = structuredData.parsial || {};
                    }
                    
                    return {
                        id: task.id,
                        kode: (task.kode_nomenklatur as any).nomor_kode || task.id.toString(),
                        jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
                        sumber_anggaran: {
                            dak: lastData.sumber_anggaran.dak,
                            dak_peruntukan: lastData.sumber_anggaran.dak_peruntukan,
                            dak_fisik: lastData.sumber_anggaran.dak_fisik,
                            dak_non_fisik: lastData.sumber_anggaran.dak_non_fisik,
                            blud: lastData.sumber_anggaran.blud,
                        },
                        // Use appropriate values for input fields based on mode
                        dak: inputValues.dak || 0,
                        dak_peruntukan: inputValues.dak_peruntukan || 0,
                        dak_fisik: inputValues.dak_fisik || 0,
                        dak_non_fisik: inputValues.dak_non_fisik || 0,
                        blud: inputValues.blud || 0,
                        // Store all data for display
                        rencana_awal: structuredData.rencana_awal || {},
                        parsial_data: structuredData.parsial || {},
                        budget_change_data: structuredData.budget_change || {},
                        is_parsial_enabled: (lastData as any).is_parsial_enabled || false,
                        is_budget_change_enabled: (lastData as any).is_budget_change_enabled || false,
                    };
                } else if (lastData.values && typeof lastData.values === 'object' && 'dak' in lastData.values) {
                    // Normal mode with simple data structure
                    const simpleData = lastData.values as { dak?: number; dak_peruntukan?: number; dak_fisik?: number; dak_non_fisik?: number; blud?: number };
                    console.log(`Task ${task.id} - Normal mode data:`, simpleData);
                    
                    return {
                        id: task.id,
                        kode: (task.kode_nomenklatur as any).nomor_kode || task.id.toString(),
                        jenis_nomenklatur: task.kode_nomenklatur.nomenklatur,
                        sumber_anggaran: {
                            dak: lastData.sumber_anggaran.dak,
                            dak_peruntukan: lastData.sumber_anggaran.dak_peruntukan,
                            dak_fisik: lastData.sumber_anggaran.dak_fisik,
                            dak_non_fisik: lastData.sumber_anggaran.dak_non_fisik,
                            blud: lastData.sumber_anggaran.blud,
                        },
                        dak: simpleData.dak || 0,
                        dak_peruntukan: simpleData.dak_peruntukan || 0,
                        dak_fisik: simpleData.dak_fisik || 0,
                        dak_non_fisik: simpleData.dak_non_fisik || 0,
                        blud: simpleData.blud || 0,
                    };
                } else {
                    console.log(`Task ${task.id} - No matching data structure, lastData:`, lastData);
                }
            }
            
            // Fallback: use empty data if no saved data exists
            return {
                id: task.id,
                kode: (task.kode_nomenklatur as any).nomor_kode || task.id.toString(),
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
                // Initialize data structures for different modes
                rencana_awal: (isParsialMode.value || isBudgetChangeMode.value) ? {} : undefined,
                parsial_data: isBudgetChangeMode.value ? {} : undefined,
                budget_change_data: isBudgetChangeMode.value ? {} : undefined,
                is_parsial_enabled: false,
                is_budget_change_enabled: isBudgetChangeMode.value,
            };
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
    if (!isPeriodeRencanaAktif.value) {
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

// Helper function to calculate rencana awal total
const calculateRencanaAwalTotal = (rencanaAwal: Record<string, number> | undefined): number => {
    if (!rencanaAwal) return 0;
    return Object.values(rencanaAwal).reduce((sum, val) => sum + (val || 0), 0);
};

// Helper function to calculate grand total (parsial + rencana awal)
const calculateGrandTotal = (item: AnggaranItem): number => {
    const parsialTotal = calculateTotal(item);
    const rencanaAwalTotal = calculateRencanaAwalTotal(item.rencana_awal);
    return parsialTotal + rencanaAwalTotal;
};

// Helper function to calculate parsial total
const calculateParsialTotal = (parsialData: Record<string, number> | undefined): number => {
    if (!parsialData) return 0;
    return Object.values(parsialData).reduce((sum, val) => sum + (val || 0), 0);
};

// Helper function to calculate budget change grand total (rencana awal + parsial + budget change)
const calculateBudgetChangeGrandTotal = (item: AnggaranItem): number => {
    const budgetChangeTotal = calculateTotal(item);
    const rencanaAwalTotal = calculateRencanaAwalTotal(item.rencana_awal);
    const parsialTotal = calculateParsialTotal(item.parsial_data);
    return rencanaAwalTotal + parsialTotal + budgetChangeTotal;
};

const handleInputChange = (item: AnggaranItem, field: keyof AnggaranItem, event: Event) => {
    if (!isPeriodeRencanaAktif.value) {
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
    console.log('isParsialMode:', isParsialMode.value);
    console.log('isPeriodeRencanaAktif:', isPeriodeRencanaAktif.value);
    console.log('props.periodeAktif:', props.periodeAktif);
    console.log('selectedPeriodeId:', selectedPeriodeId.value);

    // Periksa apakah periode sedang aktif
    if (!isPeriodeRencanaAktif.value) {
        const modeText = isParsialMode.value ? 'triwulan' : 'rencana';
        alert(`Periode ${modeText} belum dibuka. Sumber dana tidak dapat diisi sampai periode dibuka.`);
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

    // Choose the appropriate save endpoint based on mode
    let saveEndpoint;
    if (isBudgetChangeMode.value) {
        saveEndpoint = '/manajemenanggaran/save-budget-change';
    } else if (isParsialMode.value) {
        saveEndpoint = '/manajemenanggaran/save-parsial';
    } else {
        saveEndpoint = '/rencana-awal-anggaran-save';
    }

    console.log(`Sending POST request to ${saveEndpoint}`);
    
    // Gunakan Inertia router untuk mengirim data ke server
    router.post(saveEndpoint, dataToSave, {
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

            // Show success message with additional info for different modes
            let modeText;
            if (isBudgetChangeMode.value) {
                modeText = 'perubahan anggaran';
            } else if (isParsialMode.value) {
                modeText = 'parsial';
            } else {
                modeText = 'rencana awal';
            }
            
            if (isBudgetChangeMode.value) {
                alert(`✅ Data ${modeText} berhasil disimpan ke database!\n\n🔥 Perubahan anggaran untuk Triwulan 4 telah berhasil disimpan.`);
            } else if (isParsialMode.value) {
                const viewDetail = confirm(`✅ Data ${modeText} berhasil disimpan ke database!\n\n📊 Data parsial sekarang tersedia di halaman Rencana Awal.\n\nApakah Anda ingin melihat detail Rencana Awal sekarang?`);
                if (viewDetail) {
                    goToSubkegiatanDetail(item.id);
                    return; // Don't reload current page if navigating away
                }
            } else {
                alert(`✅ Data ${modeText} berhasil disimpan ke database!`);
            }

            // Reload data untuk konsistensi tanpa menampilkan alert atau JSON
            const skpdId = getSkpdId.value;
            if (skpdId) {
                const reloadUrl = isParsialMode.value 
                    ? `/manajemenanggaran/${skpdId}/parsial`
                    : `/manajemenanggaran/${skpdId}`;
                    
                router.visit(reloadUrl, {
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
        let itemTotal = calculateTotal(item);
        
        // In budget change mode, add rencana awal and parsial values to the total
        if (isBudgetChangeMode.value) {
            const rencanaAwalTotal = calculateRencanaAwalTotal(item.rencana_awal);
            const parsialTotal = calculateParsialTotal(item.parsial_data);
            itemTotal = rencanaAwalTotal + parsialTotal + itemTotal; // Total semua (rencana + parsial + perubahan)
        }
        // In parsial mode, add rencana awal values to the total
        else if (isParsialMode.value && item.rencana_awal) {
            const rencanaAwalTotal = Object.values(item.rencana_awal).reduce((sum, val) => sum + (val || 0), 0);
            itemTotal += rencanaAwalTotal;
        }
        
        return total + itemTotal;
    }, 0);
});

// Computed untuk mendapatkan jumlah sub kegiatan yang sudah diisi
const jumlahSubKegiatanDiisi = computed(() => {
    return anggaranItems.value.filter((item) => {
        let itemTotal = calculateTotal(item);
        
        // In budget change mode, also check rencana awal and parsial values
        if (isBudgetChangeMode.value) {
            const rencanaAwalTotal = calculateRencanaAwalTotal(item.rencana_awal);
            const parsialTotal = calculateParsialTotal(item.parsial_data);
            itemTotal = rencanaAwalTotal + parsialTotal + itemTotal;
        }
        // In parsial mode, also check rencana awal values
        else if (isParsialMode.value && item.rencana_awal) {
            const rencanaAwalTotal = Object.values(item.rencana_awal).reduce((sum, val) => sum + (val || 0), 0);
            itemTotal += rencanaAwalTotal;
        }
        
        return itemTotal > 0;
    }).length;
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
    if (isBudgetChangeMode.value) {
        if (isPeriodeRencanaAktif.value) {
            return `Mode Perubahan Anggaran aktif! Periode Triwulan 4 sedang dibuka untuk tahun ${props.periodeAktif?.[0]?.tahun?.tahun || ''}. Anda dapat melakukan perubahan anggaran.`;
        } else {
            return 'Periode Triwulan 4 belum dibuka atau sudah ditutup. Perubahan anggaran tidak dapat dilakukan.';
        }
    } else if (isParsialMode.value) {
        if (isPeriodeRencanaAktif.value) {
            return `Periode triwulan sedang dibuka untuk tahun ${props.periodeAktif?.[0]?.tahun?.tahun || ''}. Anda dapat mengisi pagu parsial.`;
        } else {
            const currentInfo = currentPeriodeInfo.value;
            if (currentInfo) {
                return `Menampilkan data periode "${currentInfo.tahap}" tahun ${currentInfo.tahun} (periode sudah ditutup). Pagu parsial tidak dapat diedit.`;
            } else {
                return 'Tidak ada periode triwulan yang tersedia untuk pagu parsial. Silakan hubungi administrator.';
            }
        }
    } else {
        if (isPeriodeRencanaAktif.value) {
            return `Periode "Rencana" sedang dibuka untuk tahun ${props.periodeAktif?.[0]?.tahun?.tahun || ''}. Anda dapat mengisi sumber dana.`;
        } else {
            const currentInfo = currentPeriodeInfo.value;
            if (currentInfo) {
                if (currentInfo.isActive) {
                    return `Periode "${currentInfo.tahap}" sedang aktif untuk tahun ${currentInfo.tahun}. Sumber dana hanya dapat diisi pada periode Rencana.`;
                } else {
                    return `Menampilkan data periode "${currentInfo.tahap}" tahun ${currentInfo.tahun} (periode sudah ditutup). Sumber dana tidak dapat diedit.`;
                }
            } else {
                return 'Tidak ada periode yang tersedia saat ini. Silakan hubungi administrator.';
            }
        }
    }
});

// PERBAIKAN: Computed untuk pesan informasi data yang ditampilkan
const dataDisplayInfo = computed(() => {
    const currentInfo = currentPeriodeInfo.value;
    if (!currentInfo) {
        return 'Tidak ada data yang dapat ditampilkan';
    }
    
    if (currentInfo.isActive) {
        return `Data periode aktif: ${currentInfo.tahap} ${currentInfo.tahun}`;
    } else {
        return `Data periode yang ditutup: ${currentInfo.tahap} ${currentInfo.tahun}`;
    }
});

// Add a new method to handle going to Rencana Awal detail
const goToRencanaAwal = () => {
    // Navigate to Rencana Awal detail page
    const skpdId = getSkpdId.value;
    if (skpdId) {
        // If there's a specific subkegiatan, we can navigate to its detail
        const firstSubkegiatan = props.skpdTugas?.find(task => task.kode_nomenklatur.jenis_nomenklatur === 4);
        if (firstSubkegiatan) {
            router.visit(`/monitoring/rencanaawal/${firstSubkegiatan.id}`, {
                preserveState: false
            });
        } else {
            // Fallback to main rencana awal page for this user
            router.visit(`/rencana-awal/${skpdId}`, {
                preserveState: false
            });
        }
    }
};

// Add a new method to handle going to Rencana Awal detail for specific subkegiatan
const goToSubkegiatanDetail = (subkegiatanId: number) => {
    // Navigate to Rencana Awal detail page for the specific subkegiatan
    router.visit(`/monitoring/rencanaawal/${subkegiatanId}`, {
        preserveState: false
    });
};
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
                            <h1 class="text-xl font-bold text-gray-600">{{ pageTitle }}</h1>
                            <p class="text-sm text-gray-500">
                                <span v-if="isBudgetChangeMode">🔥 Mode khusus: Perubahan anggaran Triwulan 4</span>
                                <span v-else-if="isParsialMode">Kelola dan monitor pagu parsial perangkat daerah</span>
                                <span v-else>Kelola dan monitor anggaran perangkat daerah</span>
                            </p>
                        </div>
                    </div>

                    <!-- Add period selector -->
                </div>
            </div>

            <!-- PERBAIKAN: Tambahkan informasi data yang sedang ditampilkan -->
            <div v-if="currentPeriodeInfo" class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-2">
                        <h4 class="font-medium text-blue-800">{{ dataDisplayInfo }}</h4>
                        <p class="text-sm text-blue-600">{{ currentPeriodeInfo.isActive ? 'Data dapat diedit' : 'Data hanya dapat dilihat (periode sudah ditutup)' }}</p>
                    </div>
                </div>
            </div>

            <!-- Status Period Card - Diperkecil -->
            <div class="overflow-hidden rounded-lg shadow-md" 
                :class="{
                    'bg-red-600': isBudgetChangeMode && isPeriodeRencanaAktif,
                    'bg-green-600': !isBudgetChangeMode && isPeriodeRencanaAktif,
                    'border-gray-200 bg-gray-100': !isPeriodeRencanaAktif
                }">
                <div class="p-3 text-white">
                    <div class="flex items-center">
                        <div class="mr-3 flex-shrink-0">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                :class="isPeriodeRencanaAktif ? 'text-white' : 'text-red-600'"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                v-if="isPeriodeRencanaAktif"
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
                                :class="isPeriodeRencanaAktif ? 'text-white' : 'text-red-600'"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                v-else
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 :class="isPeriodeRencanaAktif ? 'font-semibold text-white' : 'font-semibold text-red-700'">Status Periode</h3>
                            <p :class="isPeriodeRencanaAktif ? 'text-white/90' : 'text-red-600'">{{ periodeMessage }}</p>
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

                    <!-- Navigation button to Rencana Awal when in parsial mode -->
                </div>
            </div>

            <!-- Budget Table - Diperkecil -->
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-semibold text-gray-600">
                        <span v-if="isBudgetChangeMode">🔥 Detail Perubahan Anggaran Sub Kegiatan</span>
                        <span v-else-if="isParsialMode">Detail Anggaran Sub Kegiatan (Rencana Awal + Parsial)</span>
                        <span v-else>Detail Anggaran Sub Kegiatan</span>
                    </h2>
                    <p v-if="isBudgetChangeMode" class="mt-1 text-sm text-red-600">
                        📊 Data rencana awal dan parsial tetap ditampilkan, form input untuk perubahan anggaran Triwulan 4
                    </p>
                    <p v-else-if="isParsialMode" class="mt-1 text-sm text-gray-500">
                        Data rencana awal tetap ditampilkan, form input untuk menambahkan pagu parsial
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Kegiatan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber Anggaran</th>
                                
                                <!-- Dynamic column headers based on mode -->
                                <template v-if="isBudgetChangeMode">
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAU</div>
                                        <div class="text-[9px] text-red-500">Awal | Parsial | Perubahan</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAK Peruntukan</div>
                                        <div class="text-[9px] text-red-500">Awal | Parsial | Perubahan</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAK Fisik</div>
                                        <div class="text-[9px] text-red-500">Awal | Parsial | Perubahan</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAK Non Fisik</div>
                                        <div class="text-[9px] text-red-500">Awal | Parsial | Perubahan</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>BLUD</div>
                                        <div class="text-[9px] text-red-500">Awal | Parsial | Perubahan</div>
                                    </th>
                                </template>
                                <template v-else-if="isParsialMode">
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAU</div>
                                        <div class="text-[10px] text-gray-400">Awal | Parsial</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAK Peruntukan</div>
                                        <div class="text-[10px] text-gray-400">Awal | Parsial</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAK Fisik</div>
                                        <div class="text-[10px] text-gray-400">Awal | Parsial</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>DAK Non Fisik</div>
                                        <div class="text-[10px] text-gray-400">Awal | Parsial</div>
                                    </th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        <div>BLUD</div>
                                        <div class="text-[10px] text-gray-400">Awal | Parsial</div>
                                    </th>
                                </template>
                                <template v-else>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAU</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Peruntukan</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Fisik</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DAK Non Fisik</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">BLUD</th>
                                </template>
                                
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
                                                :disabled="!isPeriodeRencanaAktif"
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
                                                :disabled="!isPeriodeRencanaAktif"
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
                                                :disabled="!isPeriodeRencanaAktif"
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
                                                :disabled="!isPeriodeRencanaAktif"
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
                                                :disabled="!isPeriodeRencanaAktif"
                                            />
                                            BLUD
                                            <span v-if="item.sumber_anggaran.blud" class="ml-1 text-green-600">✓</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- DAU Amount - Conditional display based on mode -->
                                <td class="px-2 py-3 text-center">
                                    <template v-if="isBudgetChangeMode">
                                        <div class="space-y-1">
                                            <!-- Rencana Awal (Read-only) -->
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak || 0) }}
                                                </div>
                                            </div>
                                            <!-- Parsial (Read-only) -->
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <div class="font-medium text-green-600">
                                                    {{ formatCurrency(item.parsial_data?.dak || 0) }}
                                                </div>
                                            </div>
                                            <!-- Perubahan (Editable) -->
                                            <div class="text-xs">
                                                <div class="text-red-600 font-medium">Perubahan:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak"
                                                    @input="(e) => handleInputChange(item, 'dak', e)"
                                                    :disabled="!item.sumber_anggaran.dak || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-red-300 px-1 py-0.5 text-right text-xs focus:border-red-500"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="isParsialMode">
                                        <div class="space-y-2">
                                            <!-- Rencana Awal (Read-only) -->
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak || 0) }}
                                                </div>
                                            </div>
                                            <!-- Parsial (Editable) -->
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak"
                                                    @input="(e) => handleInputChange(item, 'dak', e)"
                                                    :disabled="!item.sumber_anggaran.dak || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-gray-300 px-1 py-0.5 text-right text-xs"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <input
                                            type="number"
                                            :value="item.dak"
                                            @input="(e) => handleInputChange(item, 'dak', e)"
                                            :disabled="!item.sumber_anggaran.dak || !isPeriodeRencanaAktif"
                                            min="0"
                                            class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                            :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak || !isPeriodeRencanaAktif }"
                                        />
                                        <div v-if="item.dak > 0" class="mt-1 text-xs text-green-600">
                                            {{ formatCurrency(item.dak) }}
                                        </div>
                                    </template>
                                </td>

                                <!-- DAK Peruntukan Amount -->
                                <td class="px-2 py-3 text-center">
                                    <template v-if="isBudgetChangeMode">
                                        <div class="space-y-1">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak_peruntukan || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <div class="font-medium text-green-600">
                                                    {{ formatCurrency(item.parsial_data?.dak_peruntukan || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-red-600 font-medium">Perubahan:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak_peruntukan"
                                                    @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                                                    :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-red-300 px-1 py-0.5 text-right text-xs focus:border-red-500"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_peruntukan || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="isParsialMode">
                                        <div class="space-y-2">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak_peruntukan || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak_peruntukan"
                                                    @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                                                    :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-gray-300 px-1 py-0.5 text-right text-xs"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_peruntukan || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <input
                                            type="number"
                                            :value="item.dak_peruntukan"
                                            @input="(e) => handleInputChange(item, 'dak_peruntukan', e)"
                                            :disabled="!item.sumber_anggaran.dak_peruntukan || !isPeriodeRencanaAktif"
                                            min="0"
                                            class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                            :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_peruntukan || !isPeriodeRencanaAktif }"
                                        />
                                        <div v-if="item.dak_peruntukan > 0" class="mt-1 text-xs text-green-600">
                                            {{ formatCurrency(item.dak_peruntukan) }}
                                        </div>
                                    </template>
                                </td>

                                <!-- DAK Fisik Amount -->
                                <td class="px-2 py-3 text-center">
                                    <template v-if="isBudgetChangeMode">
                                        <div class="space-y-1">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak_fisik || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <div class="font-medium text-green-600">
                                                    {{ formatCurrency(item.parsial_data?.dak_fisik || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-red-600 font-medium">Perubahan:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak_fisik"
                                                    @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                                                    :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-red-300 px-1 py-0.5 text-right text-xs focus:border-red-500"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_fisik || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="isParsialMode">
                                        <div class="space-y-2">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak_fisik || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak_fisik"
                                                    @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                                                    :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-gray-300 px-1 py-0.5 text-right text-xs"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_fisik || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <input
                                            type="number"
                                            :value="item.dak_fisik"
                                            @input="(e) => handleInputChange(item, 'dak_fisik', e)"
                                            :disabled="!item.sumber_anggaran.dak_fisik || !isPeriodeRencanaAktif"
                                            min="0"
                                            class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                            :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_fisik || !isPeriodeRencanaAktif }"
                                        />
                                        <div v-if="item.dak_fisik > 0" class="mt-1 text-xs text-green-600">
                                            {{ formatCurrency(item.dak_fisik) }}
                                        </div>
                                    </template>
                                </td>

                                <!-- DAK Non Fisik Amount -->
                                <td class="px-2 py-3 text-center">
                                    <template v-if="isBudgetChangeMode">
                                        <div class="space-y-1">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak_non_fisik || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <div class="font-medium text-green-600">
                                                    {{ formatCurrency(item.parsial_data?.dak_non_fisik || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-red-600 font-medium">Perubahan:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak_non_fisik"
                                                    @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                                                    :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-red-300 px-1 py-0.5 text-right text-xs focus:border-red-500"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_non_fisik || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="isParsialMode">
                                        <div class="space-y-2">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.dak_non_fisik || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <input
                                                    type="number"
                                                    :value="item.dak_non_fisik"
                                                    @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                                                    :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-gray-300 px-1 py-0.5 text-right text-xs"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_non_fisik || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <input
                                            type="number"
                                            :value="item.dak_non_fisik"
                                            @input="(e) => handleInputChange(item, 'dak_non_fisik', e)"
                                            :disabled="!item.sumber_anggaran.dak_non_fisik || !isPeriodeRencanaAktif"
                                            min="0"
                                            class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                            :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.dak_non_fisik || !isPeriodeRencanaAktif }"
                                        />
                                        <div v-if="item.dak_non_fisik > 0" class="mt-1 text-xs text-green-600">
                                            {{ formatCurrency(item.dak_non_fisik) }}
                                        </div>
                                    </template>
                                </td>

                                <!-- BLUD Amount -->
                                <td class="px-2 py-3 text-center">
                                    <template v-if="isBudgetChangeMode">
                                        <div class="space-y-1">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.blud || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <div class="font-medium text-green-600">
                                                    {{ formatCurrency(item.parsial_data?.blud || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-red-600 font-medium">Perubahan:</div>
                                                <input
                                                    type="number"
                                                    :value="item.blud"
                                                    @input="(e) => handleInputChange(item, 'blud', e)"
                                                    :disabled="!item.sumber_anggaran.blud || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-red-300 px-1 py-0.5 text-right text-xs focus:border-red-500"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.blud || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="isParsialMode">
                                        <div class="space-y-2">
                                            <div class="text-xs">
                                                <div class="text-gray-500">Rencana Awal:</div>
                                                <div class="font-medium text-blue-600">
                                                    {{ formatCurrency(item.rencana_awal?.blud || 0) }}
                                                </div>
                                            </div>
                                            <div class="text-xs">
                                                <div class="text-gray-500">Parsial:</div>
                                                <input
                                                    type="number"
                                                    :value="item.blud"
                                                    @input="(e) => handleInputChange(item, 'blud', e)"
                                                    :disabled="!item.sumber_anggaran.blud || !isPeriodeRencanaAktif"
                                                    min="0"
                                                    class="h-6 w-20 rounded border border-gray-300 px-1 py-0.5 text-right text-xs"
                                                    :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.blud || !isPeriodeRencanaAktif }"
                                                />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <input
                                            type="number"
                                            :value="item.blud"
                                            @input="(e) => handleInputChange(item, 'blud', e)"
                                            :disabled="!item.sumber_anggaran.blud || !isPeriodeRencanaAktif"
                                            min="0"
                                            class="h-8 w-24 rounded border border-gray-300 px-2 py-1 text-right text-xs"
                                            :class="{ 'cursor-not-allowed bg-gray-100': !item.sumber_anggaran.blud || !isPeriodeRencanaAktif }"
                                        />
                                        <div v-if="item.blud > 0" class="mt-1 text-xs text-green-600">
                                            {{ formatCurrency(item.blud) }}
                                        </div>
                                    </template>
                                </td>

                                <!-- Action Button -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="flex items-center gap-1">
                                            <button
                                                @click="saveItem(item)"
                                                :disabled="!isPeriodeRencanaAktif"
                                                :class="{
                                                    'bg-red-600 hover:bg-red-700': isBudgetChangeMode,
                                                    'bg-blue-600 hover:bg-blue-700': !isBudgetChangeMode
                                                }"
                                                class="rounded px-3 py-1 text-xs text-white transition-colors disabled:cursor-not-allowed disabled:bg-gray-400"
                                            >
                                                <span v-if="isBudgetChangeMode">💥 Simpan Perubahan</span>
                                                <span v-else-if="isParsialMode">Simpan Parsial</span>
                                                <span v-else>Simpan</span>
                                            </button>
                                            
                                            <!-- Binocular button for parsial mode to view detail -->
                                        </div>

                                        <div class="text-xs text-gray-600">
                                            <template v-if="isBudgetChangeMode">
                                                <div>Perubahan: <span class="font-bold text-red-600">{{ formatCurrency(calculateTotal(item)) }}</span></div>
                                                <div>Total All: <span class="font-bold text-purple-600">{{ formatCurrency(calculateBudgetChangeGrandTotal(item)) }}</span></div>
                                            </template>
                                            <template v-else-if="isParsialMode">
                                                <div>Parsial: <span class="font-bold text-green-600">{{ formatCurrency(calculateTotal(item)) }}</span></div>
                                                <div>Total: <span class="font-bold text-blue-600">{{ formatCurrency(calculateGrandTotal(item)) }}</span></div>
                                            </template>
                                            <template v-else>
                                                <div>Total: <span class="font-bold text-green-600">{{ formatCurrency(calculateTotal(item)) }}</span></div>
                                            </template>
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
