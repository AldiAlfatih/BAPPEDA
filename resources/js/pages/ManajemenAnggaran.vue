<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'
import { type BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manajemen Anggaran',
    href: '/managementanggaran',
  },
]

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
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-[900px] table-auto border-collapse border border-gray-400">
                <thead>
                  <tr class="bg-gray-100">
                    <th class="border border-gray-400 px-4 py-2 text-left font-semibold">Kode</th>
                    <th class="border border-gray-400 px-4 py-2 text-left font-semibold w-xl">Jenis Nomenklatur</th>
                    <th class="border border-gray-400 px-4 py-2 text-center font-semibold w-10000">Sumber Anggaran</th>
                    <th class="border border-gray-400 px-4 py-2 text-right font-semibold w-800">DAK</th>
                    <th class="border border-gray-400 px-4 py-2 text-right font-semibold w-800">DAK Peruntukan</th>
                    <th class="border border-gray-400 px-4 py-2 text-right font-semibold w-800">DAK Fisik</th>
                    <th class="border border-gray-400 px-4 py-2 text-right font-semibold w-800">DAK Non Fisik</th>
                    <th class="border border-gray-400 px-4 py-2 text-right font-semibold w-800">BLUD</th>
                    <th class="border border-gray-400 px-4 py-2 text-center font-semibold w-800">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in anggaranData" :key="item.id" class="hover:bg-gray-50">
                    <td class="border border-gray-400 px-4 py-2">{{ item.kode }}</td>
                    <td class="border border-gray-400 px-4 py-2">{{ item.jenis_nomenklatur }}</td>
                    <td class="border border-gray-400 px-4 py-2">
                      <div class="space-y-1">
                        <label class="flex items-center space-x-2">
                          <input
                            type="checkbox"
                            v-model="item.sumber_anggaran.dak"
                            class="rounded cursor-pointer"
                          />
                          <span class="text-sm">DAK</span>
                        </label>
                        <label class="flex items-center space-x-2">
                          <input
                            type="checkbox"
                            v-model="item.sumber_anggaran.dak_peruntukan"
                            class="rounded cursor-pointer"
                          />
                          <span class="text-sm">DAK Peruntukan</span>
                        </label>
                        <label class="flex items-center space-x-2">
                          <input
                            type="checkbox"
                            v-model="item.sumber_anggaran.dak_fisik"
                            class="rounded cursor-pointer"
                          />
                          <span class="text-sm">DAK Fisik</span>
                        </label>
                        <label class="flex items-center space-x-2">
                          <input
                            type="checkbox"
                            v-model="item.sumber_anggaran.dak_non_fisik"
                            class="rounded cursor-pointer"
                          />
                          <span class="text-sm">DAK Non Fisik</span>
                        </label>
                        <label class="flex items-center space-x-2">
                          <input
                            type="checkbox"
                            v-model="item.sumber_anggaran.blud"
                            class="rounded cursor-pointer"
                          />
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
                      <div class="space-x-2">
                        <td class="border-gray-400 px-4 py-2 text-center">
                        <button
                            @click="saveItem(item)"
                            class="bg-green-600 hover:bg-green-800 text-white px-3 py-1 rounded text-sm"
                        >
                            Simpan
                        </button>
                        </td>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
