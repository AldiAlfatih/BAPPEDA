<script setup lang="ts">
import { formatRupiah, formatPercent } from '@/utils/formatters';

interface Props {
  item: any;
  editingTargets: Record<string, any>;
  loadingRow: string | null;
  successRow: string | null;
  errorRow: string | null;
  getUniqueKey: (subKegiatan: any, sumberDana: string) => string;
}

interface Emits {
  (e: 'inputTarget', uniqueKey: string, idx: number, field: 'kinerja_fisik' | 'keuangan', value: any): void;
  (e: 'saveTargets', subKegiatan: any, sumberDana?: string): void;
  (e: 'deleteTargets', subKegiatan: any, sumberDana?: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const handleInputTarget = (uniqueKey: string, idx: number, field: 'kinerja_fisik' | 'keuangan', value: any) => {
  emit('inputTarget', uniqueKey, idx, field, value);
};

const handleSaveTargets = (subKegiatan: any, sumberDana?: string) => {
  emit('saveTargets', subKegiatan, sumberDana);
};

const handleDeleteTargets = (subKegiatan: any, sumberDana?: string) => {
  emit('deleteTargets', subKegiatan, sumberDana);
};
</script>

<template>
  <tr class="border hover:bg-blue-50 transition-all"
      :class="{
        'bg-green-50': successRow === item.subKegiatan.id,
        'bg-red-50': errorRow === item.subKegiatan.id,
        'opacity-75': loadingRow === item.subKegiatan.id
      }">
    <td class="p-2 border text-left">
      <div class="text-xs font-medium text-gray-500 bg-blue-100 px-2 py-1 rounded inline-block">
        {{ item.subKegiatan.kode_nomenklatur.nomor_kode }}
      </div>
    </td>
    <td class="p-2 border">{{ item.subKegiatan.kode_nomenklatur.nomenklatur }}</td>
    <td class="p-2 border text-right font-medium text-green-600">{{ item.pokok.toLocaleString('id-ID') }}</td>
    <td class="p-2 border text-right">{{ item.parsial.toLocaleString('id-ID') || '0' }}</td>
    <td class="p-2 border text-right">{{ item.perubahan.toLocaleString('id-ID') || '0' }}</td>
    <td class="p-2 border text-center">
      <div class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
        {{ item.sumberDana }}
      </div>
    </td>
    
    <!-- Target Triwulan 1-4, Fisik & Keuangan -->
    <!-- Triwulan 1 -->
    <td class="border px-1 py-1 text-center !bg-blue-50">
      <input
        type="text"
        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[0].kinerja_fisik)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 0, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="0.00%"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[0]?.kinerja_fisik">
        <span>Tersimpan: {{ item.normalizedTargets[0].kinerja_fisik.toFixed(2) }}%</span>
      </div>
    </td>
    <td class="border px-1 py-1 text-center !bg-green-50">
      <input
        type="text"
        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[0].keuangan)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 0, 'keuangan', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="Rp 0"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[0]?.keuangan">
        <span>Tersimpan: {{ Number(item.normalizedTargets[0].keuangan).toLocaleString('id-ID') }}</span>
      </div>
    </td>

    <!-- Triwulan 2 -->
    <td class="border px-1 py-1 text-center !bg-blue-50">
      <input
        type="text"
        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[1].kinerja_fisik)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 1, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="0.00%"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[1]?.kinerja_fisik">
        <span>Tersimpan: {{ item.normalizedTargets[1].kinerja_fisik.toFixed(2) }}%</span>
      </div>
    </td>
    <td class="border px-1 py-1 text-center !bg-green-50">
      <input
        type="text"
        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[1].keuangan)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 1, 'keuangan', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="Rp 0"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[1]?.keuangan">
        <span>Tersimpan: {{ Number(item.normalizedTargets[1].keuangan).toLocaleString('id-ID') }}</span>
      </div>
    </td>

    <!-- Triwulan 3 -->
    <td class="border px-1 py-1 text-center !bg-blue-50">
      <input
        type="text"
        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[2].kinerja_fisik)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 2, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="0.00%"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[2]?.kinerja_fisik">
        <span>Tersimpan: {{ item.normalizedTargets[2].kinerja_fisik.toFixed(2) }}%</span>
      </div>
    </td>
    <td class="border px-1 py-1 text-center !bg-green-50">
      <input
        type="text"
        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[2].keuangan)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 2, 'keuangan', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="Rp 0"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[2]?.keuangan">
        <span>Tersimpan: {{ Number(item.normalizedTargets[2].keuangan).toLocaleString('id-ID') }}</span>
      </div>
    </td>

    <!-- Triwulan 4 -->
    <td class="border px-1 py-1 text-center !bg-blue-50">
      <input
        type="text"
        class="w-16 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-blue-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatPercent(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[3].kinerja_fisik)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 3, 'kinerja_fisik', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="0.00%"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[3]?.kinerja_fisik">
        <span>Tersimpan: {{ item.normalizedTargets[3].kinerja_fisik.toFixed(2) }}%</span>
      </div>
    </td>
    <td class="border px-1 py-1 text-center !bg-green-50">
      <input
        type="text"
        class="w-20 px-1 py-0.5 border rounded text-xs transition-all focus:ring-2 focus:ring-blue-300 focus:border-blue-500 focus:outline-none !bg-green-50"
        :class="{
          'bg-green-100 border-green-400': successRow === item.subKegiatan.id,
          'bg-red-100 border-red-400': errorRow === item.subKegiatan.id,
          'border-gray-300': !successRow && !errorRow
        }"
        :value="formatRupiah(editingTargets[getUniqueKey(item.subKegiatan, item.sumberDana)]?.[3].keuangan)"
        @input="handleInputTarget(getUniqueKey(item.subKegiatan, item.sumberDana), 3, 'keuangan', ($event.target as HTMLInputElement)?.value)"
        :disabled="loadingRow === item.subKegiatan.id"
        placeholder="Rp 0"
      />
      <div class="text-xs mt-1 text-gray-500" v-if="item.normalizedTargets?.[3]?.keuangan">
        <span>Tersimpan: {{ Number(item.normalizedTargets[3].keuangan).toLocaleString('id-ID') }}</span>
      </div>
    </td>
    
    <!-- Kolom aksi -->
    <td class="p-2 border text-center w-40">
      <div class="flex flex-col gap-1 items-center">
        <div class="flex gap-1">
          <button
            class="px-3 py-1 bg-green-600 text-white rounded text-xs mr-1 flex items-center transition-all hover:bg-green-700"
            :class="{'opacity-50 cursor-not-allowed': loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)}"
            :disabled="loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)" 
            @click="handleSaveTargets(item.subKegiatan, item.sumberDana)"
          >
            <svg v-if="loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana) ? 'Menyimpan...' : 'Simpan' }}</span>
          </button>
          <button 
            class="px-3 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition-all" 
            :disabled="loadingRow === getUniqueKey(item.subKegiatan, item.sumberDana)" 
            @click="handleDeleteTargets(item.subKegiatan, item.sumberDana)"
          >
            Hapus
          </button>
        </div>
      </div>
    </td>
  </tr>
</template> 