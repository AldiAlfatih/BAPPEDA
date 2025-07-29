<template>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-center">
                    <th rowspan="3" class="border px-2 py-1 align-middle">KODE</th>
                    <th rowspan="3" class="border px-2 py-1 align-middle">BIDANG URUSAN/PROGRAM/KEGIATAN/SUB KEGIATAN</th>
                    <th colspan="3" class="border bg-amber-50 px-2 py-1">PAGU ANGGARAN APBD</th>
                    <th rowspan="3" class="border bg-amber-50 px-2 py-1 align-middle">SUMBER DANA</th>
                    <th colspan="8" class="border bg-blue-50 px-2 py-1">TARGET</th>
                    <th rowspan="3" class="border bg-gray-50 px-2 py-1 align-middle">AKSI</th>
                </tr>
                <tr class="bg-gray-50 text-center">
                    <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">POKOK (RP)</th>
                    <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">PARSIAL (RP)</th>
                    <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">PERUBAHAN (RP)</th>
                    <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 1</th>
                    <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 2</th>
                    <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 3</th>
                    <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 4</th>
                </tr>
                <tr class="bg-gray-50 text-center">
                    <th class="border bg-blue-50 px-2 py-1">KINERJA FISIK (%)</th>
                    <th class="border bg-blue-50 px-2 py-1">KEUANGAN (RP)</th>
                    <th class="border bg-blue-50 px-2 py-1">KINERJA FISIK (%)</th>
                    <th class="border bg-blue-50 px-2 py-1">KEUANGAN (RP)</th>
                    <th class="border bg-blue-50 px-2 py-1">KINERJA FISIK (%)</th>
                    <th class="border bg-blue-50 px-2 py-1">KEUANGAN (RP)</th>
                    <th class="border bg-blue-50 px-2 py-1">KINERJA FISIK (%)</th>
                    <th class="border bg-blue-50 px-2 py-1">KEUANGAN (RP)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Bidang Urusan Level -->
                <template v-for="bidangUrusan in hierarchicalData" :key="`bidang-${bidangUrusan.id}`">
                    <tr
                        class="cursor-pointer border-b border-l-4 border-blue-600 bg-blue-50 font-bold hover:bg-blue-100"
                        @click="toggleExpand('bidang', bidangUrusan.id)"
                    >
                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                            <div class="flex items-center justify-center gap-2">
                                <ChevronRight v-if="!isExpanded('bidang', bidangUrusan.id)" class="h-4 w-4" />
                                <ChevronDown v-else class="h-4 w-4" />
                                {{ bidangUrusan.kode }}
                            </div>
                        </td>
                        <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                            <div class="line-clamp-3 font-bold">{{ bidangUrusan.nama }}</div>
                        </td>
                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm font-bold">
                            {{ formatCurrency(bidangUrusan.totals.pokok) }}
                        </td>
                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm font-bold">
                            {{ formatCurrency(bidangUrusan.totals.parsial) }}
                        </td>
                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm font-bold">
                            {{ formatCurrency(bidangUrusan.totals.perubahan) }}
                        </td>
                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                            <span class="text-gray-400">-</span>
                        </td>
                        <!-- Target columns for Bidang Urusan -->
                        <template v-for="i in 4" :key="`bidang-target-${i}`">
                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                {{ bidangUrusan.totals.targets[i - 1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                            </td>
                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                {{ formatCurrency(bidangUrusan.totals.targets[i - 1]?.keuangan || 0) }}
                            </td>
                        </template>
                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                            <span class="text-gray-400">-</span>
                        </td>
                    </tr>

                    <!-- Program Level -->
                    <template v-if="isExpanded('bidang', bidangUrusan.id)">
                        <template v-for="program in bidangUrusan.programs" :key="`program-${program.id}`">
                            <tr
                                class="cursor-pointer border-b border-l-4 border-green-500 bg-green-50 font-semibold hover:bg-green-100"
                                @click="toggleExpand('program', program.id)"
                            >
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                    <div class="ml-4 flex items-center justify-center gap-2">
                                        <ChevronRight v-if="!isExpanded('program', program.id)" class="h-4 w-4" />
                                        <ChevronDown v-else class="h-4 w-4" />
                                        {{ program.kode }}
                                    </div>
                                </td>
                                <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                    <div class="ml-4 line-clamp-3">{{ program.nama }}</div>
                                </td>
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ formatCurrency(program.totals.pokok) }}
                                </td>
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ formatCurrency(program.totals.parsial) }}
                                </td>
                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                    {{ formatCurrency(program.totals.perubahan) }}
                                </td>
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                    <span class="text-gray-400">-</span>
                                </td>
                                <!-- Target columns for Program -->
                                <template v-for="i in 4" :key="`program-target-${i}`">
                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                        {{ program.totals.targets[i - 1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                    </td>
                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                        {{ formatCurrency(program.totals.targets[i - 1]?.keuangan || 0) }}
                                    </td>
                                </template>
                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                    <span class="text-gray-400">-</span>
                                </td>
                            </tr>

                            <!-- Kegiatan Level -->
                            <template v-if="isExpanded('program', program.id)">
                                <template v-for="kegiatan in program.kegiatans" :key="`kegiatan-${kegiatan.id}`">
                                    <tr
                                        class="cursor-pointer border-b border-l-4 border-orange-400 bg-orange-50 font-semibold hover:bg-orange-100"
                                        @click="toggleExpand('kegiatan', kegiatan.id)"
                                    >
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                            <div class="ml-8 flex items-center justify-center gap-2">
                                                <ChevronRight v-if="!isExpanded('kegiatan', kegiatan.id)" class="h-4 w-4" />
                                                <ChevronDown v-else class="h-4 w-4" />
                                                {{ kegiatan.kode }}
                                            </div>
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                            <div class="ml-8 line-clamp-3">{{ kegiatan.nama }}</div>
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{ formatCurrency(kegiatan.totals.pokok) }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{ formatCurrency(kegiatan.totals.parsial) }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                            {{ formatCurrency(kegiatan.totals.perubahan) }}
                                        </td>
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            <span class="text-gray-400">-</span>
                                        </td>
                                        <!-- Target columns for Kegiatan -->
                                        <template v-for="i in 4" :key="`kegiatan-target-${i}`">
                                            <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                {{ kegiatan.totals.targets[i - 1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                            </td>
                                            <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                {{ formatCurrency(kegiatan.totals.targets[i - 1]?.keuangan || 0) }}
                                            </td>
                                        </template>
                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                            <span class="text-gray-400">-</span>
                                        </td>
                                    </tr>

                                    <!-- Sub Kegiatan Level -->
                                    <template v-if="isExpanded('kegiatan', kegiatan.id)">
                                        <template v-for="subKegiatan in kegiatan.subKegiatans" :key="`subkegiatan-${subKegiatan.id}`">
                                            <tr
                                                class="cursor-pointer border-b border-l-4 border-yellow-400 bg-yellow-50 hover:bg-yellow-100"
                                                @click="toggleExpand('subkegiatan', subKegiatan.id)"
                                            >
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                                    <div class="ml-12 flex items-center justify-center gap-2">
                                                        <ChevronRight v-if="!isExpanded('subkegiatan', subKegiatan.id)" class="h-4 w-4" />
                                                        <ChevronDown v-else class="h-4 w-4" />
                                                        {{ subKegiatan.kode }}
                                                    </div>
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                                    <div class="ml-12 line-clamp-3">{{ subKegiatan.nama }}</div>
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                    {{ formatCurrency(subKegiatan.totals.pokok) }}
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                    {{ formatCurrency(subKegiatan.totals.parsial) }}
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                    {{ formatCurrency(subKegiatan.totals.perubahan) }}
                                                </td>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <span class="text-gray-400">-</span>
                                                </td>
                                                <!-- Target columns for Sub Kegiatan -->
                                                <template v-for="i in 4" :key="`subkegiatan-target-${i}`">
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                        {{ subKegiatan.totals.targets[i - 1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ formatCurrency(subKegiatan.totals.targets[i - 1]?.keuangan || 0) }}
                                                    </td>
                                                </template>
                                                <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                    <Button size="sm" variant="outline" @click.stop="editSubKegiatan(subKegiatan)"> Edit </Button>
                                                </td>
                                            </tr>

                                            <!-- Detail Sumber Dana Level -->
                                            <template v-if="isExpanded('subkegiatan', subKegiatan.id)">
                                                <tr v-for="detail in subKegiatan.details" :key="`detail-${detail.id}`" class="border-b bg-gray-50">
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle font-mono text-sm">
                                                        <div class="ml-16 text-gray-600">{{ detail.sumberDana }}</div>
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 align-middle text-sm">
                                                        <div class="ml-16 text-gray-600">{{ detail.sumberDana }}</div>
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ formatCurrency(detail.pokok) }}
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ formatCurrency(detail.parsial) }}
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                        {{ formatCurrency(detail.perubahan) }}
                                                    </td>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                        <div class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                                            {{ detail.sumberDana }}
                                                        </div>
                                                    </td>
                                                    <!-- Target columns for Detail -->
                                                    <template v-for="i in 4" :key="`detail-target-${i}`">
                                                        <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                            {{ detail.targets[i - 1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%
                                                        </td>
                                                        <td class="border-r border-gray-200 px-3 py-3 text-right align-middle text-sm">
                                                            {{ formatCurrency(detail.targets[i - 1]?.keuangan || 0) }}
                                                        </td>
                                                    </template>
                                                    <td class="border-r border-gray-200 px-3 py-3 text-center align-middle text-sm">
                                                        <Button size="sm" @click="editDetail(detail)"> Edit </Button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </template>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    data: any[];
}

const props = defineProps<Props>();
const emit = defineEmits(['edit-subkegiatan', 'edit-detail']);

// State untuk expand/collapse
const expandedItems = ref<Set<string>>(new Set());

// Helper functions
const toggleExpand = (type: string, id: number) => {
    const key = `${type}-${id}`;
    if (expandedItems.value.has(key)) {
        expandedItems.value.delete(key);
    } else {
        expandedItems.value.add(key);
    }
};

const isExpanded = (type: string, id: number) => {
    return expandedItems.value.has(`${type}-${id}`);
};

const formatCurrency = (amount: number) => {
    return `Rp ${amount.toLocaleString('id-ID')}`;
};

// Transform data menjadi struktur hirarki
const hierarchicalData = computed(() => {
    const result: any[] = [];
    const bidangUrusanMap = new Map();
    const programMap = new Map();
    const kegiatanMap = new Map();
    const subKegiatanMap = new Map();

    // Proses setiap item data
    props.data.forEach((item) => {
        const bidangUrusan = item.bidangUrusan;
        const program = item.program;
        const kegiatan = item.kegiatan;
        const subKegiatan = item.subKegiatan;

        // Buat atau update bidang urusan
        if (!bidangUrusanMap.has(bidangUrusan.id)) {
            bidangUrusanMap.set(bidangUrusan.id, {
                id: bidangUrusan.id,
                kode: bidangUrusan.kode_nomenklatur.nomor_kode,
                nama: bidangUrusan.kode_nomenklatur.nomenklatur,
                programs: [],
                totals: {
                    pokok: 0,
                    parsial: 0,
                    perubahan: 0,
                    targets: [
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                    ],
                },
            });
        }

        // Buat atau update program
        const programKey = `${bidangUrusan.id}-${program.id}`;
        if (!programMap.has(programKey)) {
            const programData = {
                id: program.id,
                kode: program.kode_nomenklatur.nomor_kode,
                nama: program.kode_nomenklatur.nomenklatur,
                kegiatans: [],
                totals: {
                    pokok: 0,
                    parsial: 0,
                    perubahan: 0,
                    targets: [
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                    ],
                },
            };
            programMap.set(programKey, programData);
            bidangUrusanMap.get(bidangUrusan.id).programs.push(programData);
        }

        // Buat atau update kegiatan
        const kegiatanKey = `${program.id}-${kegiatan.id}`;
        if (!kegiatanMap.has(kegiatanKey)) {
            const kegiatanData = {
                id: kegiatan.id,
                kode: kegiatan.kode_nomenklatur.nomor_kode,
                nama: kegiatan.kode_nomenklatur.nomenklatur,
                subKegiatans: [],
                totals: {
                    pokok: 0,
                    parsial: 0,
                    perubahan: 0,
                    targets: [
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                    ],
                },
            };
            kegiatanMap.set(kegiatanKey, kegiatanData);
            programMap.get(programKey).kegiatans.push(kegiatanData);
        }

        // Buat atau update sub kegiatan
        const subKegiatanKey = `${kegiatan.id}-${subKegiatan.id}`;
        if (!subKegiatanMap.has(subKegiatanKey)) {
            const subKegiatanData = {
                id: subKegiatan.id,
                kode: subKegiatan.kode_nomenklatur.nomor_kode,
                nama: subKegiatan.kode_nomenklatur.nomenklatur,
                details: [],
                totals: {
                    pokok: 0,
                    parsial: 0,
                    perubahan: 0,
                    targets: [
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                        { kinerja_fisik: 0, keuangan: 0 },
                    ],
                },
            };
            subKegiatanMap.set(subKegiatanKey, subKegiatanData);
            kegiatanMap.get(kegiatanKey).subKegiatans.push(subKegiatanData);
        }

        // Tambahkan detail sumber dana
        const subKegiatanData = subKegiatanMap.get(subKegiatanKey);
        subKegiatanData.details.push({
            id: `${item.subKegiatan.id}-${item.sumberDana}`,
            sumberDana: item.sumberDana,
            pokok: item.pokok,
            parsial: item.parsial,
            perubahan: item.perubahan,
            targets: item.normalizedTargets,
        });

        // Update totals untuk semua level
        const updateTotals = (target: any, source: any) => {
            target.pokok += source.pokok;
            target.parsial += source.parsial;
            target.perubahan += source.perubahan;
            source.normalizedTargets.forEach((t: any, index: number) => {
                target.targets[index].kinerja_fisik += t.kinerja_fisik;
                target.targets[index].keuangan += t.keuangan;
            });
        };

        updateTotals(subKegiatanData.totals, item);
        updateTotals(kegiatanMap.get(kegiatanKey).totals, item);
        updateTotals(programMap.get(programKey).totals, item);
        updateTotals(bidangUrusanMap.get(bidangUrusan.id).totals, item);
    });

    return Array.from(bidangUrusanMap.values());
});

const editSubKegiatan = (subKegiatan: any) => {
    emit('edit-subkegiatan', subKegiatan);
};

const editDetail = (detail: any) => {
    emit('edit-detail', detail);
};
</script>
