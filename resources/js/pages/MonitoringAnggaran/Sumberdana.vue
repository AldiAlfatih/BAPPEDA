<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'
import { type BreadcrumbItem } from '@/types'



const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Anggaran', href: '/manajemenanggaran' },
    { title: 'Manajemen Anggaran Detail' , href: '' }
];

interface AnggaranItem {
  id: number
  kode: string
  jenis_nomenklatur: string
  sumber_anggaran: {
    dak: boolean
    dak_peruntukan: boolean
    dak_fisik: boolean
    dak_non_fisik: boolean
    blud: boolean
  }
  dak: number
  dak_peruntukan: number
  dak_fisik: number
  dak_non_fisik: number
  blud: number
}

const props = defineProps<{
    user: {
        id: number;
        name: string;
        user_detail?: {
        nip?: string;
        } | null;
        skpd: {
            id: number;
            nama_skpd: string;
            nama_dinas: string;
            no_dpa: string;
            kode_organisasi: string;
        } | null;
    };
}>();

const anggaranData = ref<AnggaranItem[]>([
  {
    id: 1,
    kode: '1.01.01.001',
    jenis_nomenklatur: 'Pendidikan',
    sumber_anggaran: {
      dak: false,
      dak_peruntukan: true,
      dak_fisik: false,
      dak_non_fisik: true,
      blud: false,
    },
    dak: 0,
    dak_peruntukan: 1000000,
    dak_fisik: 0,
    dak_non_fisik: 100000,
    blud: 0,
  },
  {
    id: 2,
    kode: '1.01.01.002',
    jenis_nomenklatur: 'Bappeda',
    sumber_anggaran: {
      dak: true,
      dak_peruntukan: false,
      dak_fisik: true,
      dak_non_fisik: false,
      blud: true,
    },
    dak: 1000000,
    dak_peruntukan: 0,
    dak_fisik: 1000000,
    dak_non_fisik: 0,
    blud: 100000,
  },
])

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('id-ID').format(value)
}

function getUserNip(user: { user_detail?: { nip?: string } | null; nip?: string }): string {
  if (user.user_detail && typeof user.user_detail.nip === 'string' && user.user_detail.nip.trim() !== '') {
    return user.user_detail.nip;
  }

  if (typeof user.nip === 'string' && user.nip.trim() !== '') {
    return user.nip;
  }

  return '-';
}

const editItem = (item: AnggaranItem) => {
  // jika perlu fungsi edit
}

const deleteItem = (id: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
    anggaranData.value = anggaranData.value.filter((item) => item.id !== id)
  }
}
const saveItem = (item: AnggaranItem) => {
  // Simpan perubahan, misal kirim ke server atau proses lain
  alert(`Data untuk kode ${item.kode} disimpan!`)
  // Contoh: console.log(item)
}


</script>

<template>
  <Head title="Manajemen Anggaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="flex flex-col gap-6 p-6 bg-gray-50">
            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-600">Detail Perangkat Daerah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Nama SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.nama_dinas || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kode Organisasi</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.kode_organisasi || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">No NIP</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ getUserNip(user) || 'Tidak tersedia' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Kepala SKPD</h3>
                        <p class="text-lg font-semibold text-gray-500">{{ user.skpd?.nama_skpd || 'Tidak tersedia' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-lg border border-gray-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                    <div class="flex items-center">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Manajemen Anggaran</h3>
                    </div>
                </div>
                <div class="max-w-xxl mx-auto overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-[1500px] table-auto border-collapse border border-gray-400">
                    <thead>
                    <tr class="bg-gray-100 text-center">
                        <th class="border border-gray-400 px-4 py-2 font-semibold">Kode</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[600px]">Jenis Nomenklatur</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[250px]">Sumber Anggaran</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[200px]">DAK</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[200px]">DAK Peruntukan</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[200px]">DAK Fisik</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[200px]">DAK Non Fisik</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[200px]">BLUD</th>
                        <th class="border border-gray-400 px-4 py-2 font-semibold w-[100px]">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in anggaranData" :key="item.id" class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-4 py-2">{{ item.kode }}</td>
                        <td class="border border-gray-400 px-4 py-2">{{ item.jenis_nomenklatur }}</td>
                        <td class="border border-gray-400 px-4 py-2">
                        <div class="space-y-1">
                            <label class="flex items-center space-x-2">
                            <input type="checkbox" v-model="item.sumber_anggaran.dak" class="rounded cursor-pointer" />
                            <span class="text-sm">DAK</span>
                            </label>
                            <label class="flex items-center space-x-2">
                            <input type="checkbox" v-model="item.sumber_anggaran.dak_peruntukan" class="rounded cursor-pointer" />
                            <span class="text-sm">DAK Peruntukan</span>
                            </label>
                            <label class="flex items-center space-x-2">
                            <input type="checkbox" v-model="item.sumber_anggaran.dak_fisik" class="rounded cursor-pointer" />
                            <span class="text-sm">DAK Fisik</span>
                            </label>
                            <label class="flex items-center space-x-2">
                            <input type="checkbox" v-model="item.sumber_anggaran.dak_non_fisik" class="rounded cursor-pointer" />
                            <span class="text-sm">DAK Non Fisik</span>
                            </label>
                            <label class="flex items-center space-x-2">
                            <input type="checkbox" v-model="item.sumber_anggaran.blud" class="rounded cursor-pointer" />
                            <span class="text-sm">BLUD</span>
                            </label>
                        </div>
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-right">
                        <input
                            type="number"
                            v-model.number="item.dak"
                            :disabled="!item.sumber_anggaran.dak"
                            min="0"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-right"
                        />
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-right">
                        <input
                            type="number"
                            v-model.number="item.dak_peruntukan"
                            :disabled="!item.sumber_anggaran.dak_peruntukan"
                            min="0"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-right"
                        />
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-right">
                        <input
                            type="number"
                            v-model.number="item.dak_fisik"
                            :disabled="!item.sumber_anggaran.dak_fisik"
                            min="0"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-right"
                        />
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-right">
                        <input
                            type="number"
                            v-model.number="item.dak_non_fisik"
                            :disabled="!item.sumber_anggaran.dak_non_fisik"
                            min="0"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-right"
                        />
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-right">
                        <input
                            type="number"
                            v-model.number="item.blud"
                            :disabled="!item.sumber_anggaran.blud"
                            min="0"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-right"
                        />
                        </td>
                        <td class="border border-gray-400 px-4 py-2 text-center">
                        <button
                            @click="saveItem(item)"
                            class="bg-green-600 hover:bg-green-800 text-white px-3 py-1 rounded text-sm"
                        >
                            Simpan
                        </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>

            </div>
          </div>
        </div>

  </AppLayout>
</template>
