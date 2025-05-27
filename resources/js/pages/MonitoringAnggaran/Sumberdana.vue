<script setup lang="ts">
import { ref } from 'vue'

const sumberAnggarans = [
  'DAK Fisik', 'DAK Nonfisik', 'BLUD', 'PAD', 'DAU', 'DBH', 'Lainnya'
]

const form = ref({
  kegiatan: '',
  pptk: '',
  tahun: new Date().getFullYear(),
  anggarans: sumberAnggarans.map(sa => ({
    nama: sa,
    selected: false,
    dana: null,
  }))
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Monitoring Anggaran</h1>

    <div class="mb-4">
      <label class="block font-semibold">Nama Kegiatan</label>
      <input v-model="form.kegiatan" class="border rounded p-2 w-full" placeholder="Nama kegiatan..." />
    </div>

    <div class="mb-4">
      <label class="block font-semibold">Nama PPTK</label>
      <input v-model="form.pptk" class="border rounded p-2 w-full" placeholder="Nama PPTK..." />
    </div>

    <div class="mb-4">
      <label class="block font-semibold">Tahun</label>
      <input type="number" v-model="form.tahun" class="border rounded p-2 w-full" />
    </div>

    <h2 class="text-lg font-semibold mt-6 mb-2">Sumber Anggaran</h2>
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="border px-2 py-1">#</th>
          <th class="border px-2 py-1">Sumber</th>
          <th class="border px-2 py-1">Pilih</th>
          <th class="border px-2 py-1">Dana (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in form.anggarans" :key="index">
          <td class="border px-2 py-1 text-center">{{ index + 1 }}</td>
          <td class="border px-2 py-1">{{ item.nama }}</td>
          <td class="border px-2 py-1 text-center">
            <input type="checkbox" v-model="item.selected" />
          </td>
          <td class="border px-2 py-1">
            <input v-if="item.selected" type="number" v-model="item.dana" class="w-full border rounded px-1 py-0.5" />
            <span v-else class="text-gray-400 italic">-</span>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="mt-6">
      <button @click="submitForm" class="bg-blue-600 text-white px-4 py-2 rounded">
        Simpan
      </button>
    </div>
  </div>
</template>

<script lang="ts">
function submitForm() {
  console.log('Kirim data:', form.value)
  // Gunakan Inertia.post atau axios.post ke endpoint Laravel jika sudah siap
}
</script>
