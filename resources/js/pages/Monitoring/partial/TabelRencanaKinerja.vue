<script setup lang="ts">
import SubkegiatanRow from './SubkegiatanRow.vue';

interface Props {
  bidangurusanTugas?: any[];
  programTugas?: any[];
  kegiatanTugas?: any[];
  formattedSubKegiatanData: any[];
  calculateBidangUrusan: Record<number, any>;
  calculateProgram: Record<number, any>;
  calculateKegiatan: Record<number, any>;
  editingTargets: Record<string, any>;
  loadingRow: string | null;
  successRow: string | null;
  errorRow: string | null;
  getUniqueKey: (subKegiatan: any, sumberDana: string) => string;
  onInputTarget: (uniqueKey: string, idx: number, field: 'kinerja_fisik' | 'keuangan', value: any) => void;
  saveTargets: (subKegiatan: any, sumberDana?: string) => Promise<void>;
  deleteTargets: (subKegiatan: any, sumberDana?: string) => Promise<void>;
}

const props = defineProps<Props>();
</script>

<template>
  <!-- Program table with targets -->
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-600">Detail Rencana Kinerja</h2>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border-collapse text-sm">
        <thead>
          <tr class="text-center bg-gray-50 border-b">
            <th rowspan="3" class="border px-2 py-1 align-middle">KODE</th>
            <th rowspan="3" class="border px-2 py-1 align-middle">BIDANG URUSAN/PROGRAM/KEGIATAN/SUB KEGIATAN</th>
            <th colspan="3" class="border px-2 py-1 bg-amber-50">PAGU ANGGARAN APBD</th>
            <th rowspan="3" class="border px-2 py-1 bg-amber-50 align-middle">SUMBER DANA</th>
            <th colspan="8" class="border px-2 py-1 bg-blue-50">TARGET</th>
            <th rowspan="3" class="border px-2 py-1 bg-gray-50 align-middle">AKSI</th>
          </tr>
          <tr class="text-center bg-gray-50">
            <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">POKOK (RP)</th>
            <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">PARSIAL (RP)</th>
            <th rowspan="2" class="border bg-amber-50 px-2 py-1 align-middle">PERUBAHAN (RP)</th>
            <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 1</th>
            <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 2</th>
            <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 3</th>
            <th colspan="2" class="border bg-blue-50 px-2 py-1">TRIWULAN 4</th>
          </tr>
          <tr class="text-center bg-blue-50">
            <!-- Triwulan 1 -->
            <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
            <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
            <!-- Triwulan 2 -->
            <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
            <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
            <!-- Triwulan 3 -->
            <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
            <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
            <!-- Triwulan 4 -->
            <th class="border px-2 py-1 text-xs bg-blue-100">KINERJA FISIK (%)</th>
            <th class="border px-2 py-1 text-xs bg-green-100">KEUANGAN (RP)</th>
          </tr>
        </thead>
        <tbody>
          <!-- Display bidang urusan from the selected urusan -->
          <template v-for="bidangUrusan in bidangurusanTugas" :key="bidangUrusan.id">
            <tr class="bg-blue-50 font-semibold hover:bg-blue-100">
              <td class="p-2 border text-left">{{ bidangUrusan.kode_nomenklatur.nomor_kode }}</td>
              <td class="p-2 border">{{ bidangUrusan.kode_nomenklatur.nomenklatur }}</td>
              <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.pokok?.toLocaleString('id-ID') || '0' }}</td>
              <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.parsial?.toLocaleString('id-ID') || '0' }}</td>
              <td class="p-2 border text-right">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.perubahan?.toLocaleString('id-ID') || '0' }}</td>
              <td class="p-2 border text-left">-</td>
              <!-- Triwulan 1 -->
              <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
              <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID') }}</td>
              <!-- Triwulan 2 -->
              <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
              <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID') }}</td>
              <!-- Triwulan 3 -->
              <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
              <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID') }}</td>
              <!-- Triwulan 4 -->
              <td class="p-2 border text-center">{{ calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
              <td class="p-2 border text-right">{{ (calculateBidangUrusan[bidangUrusan.kode_nomenklatur.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID') }}</td>
              <td></td>
            </tr>

            <!-- Display programs that belong to this bidang urusan -->
            <template v-for="program in programTugas?.filter(p => p.kode_nomenklatur.details[0]?.id_bidang_urusan === bidangUrusan.kode_nomenklatur.id)" :key="program.id">
              <tr class="border bg-gray-50 hover:bg-gray-100 font-medium">
                <td class="p-2 border text-left">{{ program.kode_nomenklatur.nomor_kode }}</td>
                <td class="p-2 border text-left">{{ program.kode_nomenklatur.nomenklatur }}</td>
                <td class="p-2 border text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.pokok?.toLocaleString('id-ID') || '0' }}</td>
                <td class="p-2 border text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.parsial?.toLocaleString('id-ID') || '0' }}</td>
                <td class="p-2 border text-right">{{ calculateProgram[program.kode_nomenklatur.id]?.perubahan?.toLocaleString('id-ID') || '0' }}</td>
                <td class="p-2 border text-center">{{ program.monitoring?.sumber_dana || '-' }}</td>
                <!-- Triwulan 1 -->
                <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                <!-- Triwulan 2 -->
                <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                <!-- Triwulan 3 -->
                <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                <!-- Triwulan 4 -->
                <td class="p-2 border text-center">{{ calculateProgram[program.kode_nomenklatur.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                <td class="p-2 border text-right">{{ (calculateProgram[program.kode_nomenklatur.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                <td></td>
              </tr>

              <!-- Display kegiatan for this program -->
              <template v-for="kegiatan in kegiatanTugas?.filter(k => k.kode_nomenklatur.details[0]?.id_program === program.kode_nomenklatur.id)" :key="kegiatan.id">
                <tr class="border hover:bg-gray-50">
                  <td class="p-2 border text-left">{{ kegiatan.kode_nomenklatur.nomor_kode }}</td>
                  <td class="p-2 border text-left">{{ kegiatan.kode_nomenklatur.nomenklatur }}</td>
                  <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.pokok?.toLocaleString('id-ID') || '0' }}</td>
                  <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.parsial?.toLocaleString('id-ID') || '0' }}</td>
                  <td class="p-2 border text-right">{{ calculateKegiatan[kegiatan.id]?.perubahan?.toLocaleString('id-ID') || '0' }}</td>
                  <td class="p-2 border text-left">{{ kegiatan.monitoring?.sumber_dana || '-' }}</td>
                  <!-- Triwulan 1 -->
                  <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[0]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                  <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[0]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                  <!-- Triwulan 2 -->
                  <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[1]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                  <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[1]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                  <!-- Triwulan 3 -->
                  <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[2]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                  <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[2]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                  <!-- Triwulan 4 -->
                  <td class="p-2 border text-center">{{ calculateKegiatan[kegiatan.id]?.targets?.[3]?.kinerja_fisik?.toFixed(2) || '0.00' }}%</td>
                  <td class="p-2 border text-right">{{ (calculateKegiatan[kegiatan.id]?.targets?.[3]?.keuangan || 0).toLocaleString('id-ID') }}</td>
                  <td></td>
                </tr>

                <!-- Display subkegiatan data for this kegiatan with funding details -->
                <template v-for="item in formattedSubKegiatanData.filter(sk => sk.kegiatan.id === kegiatan.id)" :key="item.id">
                  <SubkegiatanRow
                    :item="item"
                    :editing-targets="editingTargets"
                    :loading-row="loadingRow"
                    :success-row="successRow"
                    :error-row="errorRow"
                    :get-unique-key="getUniqueKey"
                    @input-target="onInputTarget"
                    @save-targets="saveTargets"
                    @delete-targets="deleteTargets"
                  />
                </template>
              </template>
            </template>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template> 