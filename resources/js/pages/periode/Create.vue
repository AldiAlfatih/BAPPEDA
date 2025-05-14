<script setup lang="ts">
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { Inertia } from '@inertiajs/inertia'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Periode', href: '/periode' },
]

const props = defineProps<{
  periode?: Array<{
    id: number
    tanggal_mulai: string
    tanggal_selesai: string
    status: number
    tahap?: {
      id: number
      tahap: string
    }
    tahun: {
      id: number
      tahun: string
    }
  }>
  tahuns: Array<{ id: number; tahun: string }>
}>()

const periodeData = computed(() => props.periode ?? [])

const statusList = ref(periodeData.value.map((item) => ({ id: item.id, status: item.status })))

function toggleStatus(id: number) {
  const item = statusList.value.find((i) => i.id === id)
  if (!item) return

  const newStatus = item.status === 1 ? 0 : 1

  Inertia.put(`/periode/${id}/status`, { status: newStatus }, {
    onSuccess: () => {
      item.status = newStatus
    },
    onError: () => {
      alert('Gagal mengubah status.')
    }
  })
}

function getStatusLabel(status: number) {
  return status === 1 ? 'Buka' : 'Tutup'
}

function editItem(id: number) {
  Inertia.visit(`/periode/${id}/edit`)
}

const searchQuery = ref('')
const yearFilter = ref<number | null>(null)

const filteredperiode = computed(() => {
  return periodeData.value.filter((p) => {
    const matchesSearch = p.tahap?.tahap.toLowerCase().includes(searchQuery.value.toLowerCase()) ?? true
    const matchesYear = yearFilter.value ? p.tahun.id === yearFilter.value : true
    return matchesSearch && matchesYear
  })
})

function formatDate(date: string) {
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(date).toLocaleDateString('id-ID', options)
}

const years = computed(() => props.tahuns)

function deleteItem(id: number) {
  if (confirm('Apakah Anda yakin ingin menghapus periode ini?')) {
    Inertia.delete(`/periode/${id}`)
  }
}

// === ✅ Tambahan untuk tombol generate
const isGenerating = ref(false)
function generatePeriode() {
  if (confirm('Apakah Anda ingin membuat otomatis periode untuk tahun berjalan?')) {
    isGenerating.value = true
    Inertia.visit('/periode/generate', {
      method: 'post',
      preserveScroll: true,
      data: { tahun: yearFilter.value }, // Mengirimkan tahun yang dipilih
      onFinish: () => {
        isGenerating.value = false
      },
      onError: () => {
        alert('Gagal membuat periode.')
      }
    })
  }
}
</script>

<template>
  <Head title="Periode" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-2 p-1">
      <div class="flex justify-between items-center mb-2">
        <div class="ml-4">
          <select v-model="yearFilter" class="border border-gray-300 px-2 py-1 rounded-md">
            <option value="" disabled selected>Pilih Tahun</option>
            <option v-for="year in years" :key="year.id" :value="year.id">{{ year.tahun }}</option>
          </select>
        </div>
        <button
          @click="generatePeriode"
          :disabled="isGenerating"
          class="pi pi-plus h-xs px-2 py-1 bg-blue-600 text-sm text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
        >
          {{ isGenerating ? 'Memproses...' : 'Generate Periode Otomatis' }}
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">No</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tahap</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Mulai</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Berakhir</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="filteredperiode.length === 0">
              <td colspan="6" class="px-4 py-2 text-center text-gray-500">Tidak ada data periode tersedia</td>
            </tr>
            <tr v-for="(p, index) in filteredperiode" :key="p.id">
              <td class="px-4 py-2 text-sm text-gray-600">{{ index + 1 }}</td>
              <td class="px-4 py-2 text-sm text-gray-600">{{ p.tahap.tahap }}</td>
              <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(p.tanggal_mulai) }}</td>
              <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(p.tanggal_selesai) }}</td>
              <td class="px-4 py-2">
                <span
                  :class="{
                    'text-green-600': p.status === 1,
                    'text-red-600': p.status === 0
                  }"
                >
                  {{ getStatusLabel(p.status) }}
                </span>
              </td>
              <td class="px-4 py-2">
                <button
                  @click="toggleStatus(p.id)"
                  class="text-sm text-blue-600 hover:text-blue-700"
                >
                  {{ p.status === 1 ? 'Tutup' : 'Buka' }}
                </button>
                <button
                  @click="editItem(p.id)"
                  class="text-sm text-green-600 hover:text-green-700 ml-2"
                >
                  Edit
                </button>
                <button
                  @click="deleteItem(p.id)"
                  class="text-sm text-red-600 hover:text-red-700 ml-2"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
