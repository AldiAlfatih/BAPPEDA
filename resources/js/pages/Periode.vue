<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { Inertia, Method } from '@inertiajs/inertia'

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
const statusMessage = ref('')

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
  if (status === 1) return 'Buka'
  if (status === 0) return 'Tutup'
  return 'Selesai'
}

function canShowSelesai(p: typeof props.periode[number]) {
  if (p.status !== 0) return false

  // Urutkan semua periode berdasarkan id tahap (atau pakai urutan lain jika ada)
  const sorted = [...filteredperiode.value].sort((a, b) => a.id - b.id)
  const currentIndex = sorted.findIndex(item => item.id === p.id)

  // Jika tahap pertama, langsung izinkan
  if (currentIndex === 0) return true

  // Cek apakah tahap sebelumnya sudah selesai
  const prev = sorted[currentIndex - 1]
  return prev && prev.status === 2
}

function markAsSelesai(id: number) {
  const item = statusList.value.find((i) => i.id === id)
  if (!item) return

  if (confirm('Apakah Anda yakin ingin menandai periode ini sebagai selesai?')) {
    Inertia.put(`/periode/${id}/status`, { status: 2 }, {
      onSuccess: () => {
        item.status = 2
      },
      onError: () => {
        alert('Gagal mengubah status ke selesai.')
      },
      preserveScroll: true
    })
  }
}

const allSelesai = computed(() => {
  return periodeData.value.every(p => p.status === 2)
})


function editItem(id: number) {
  Inertia.visit(`/periode/${id}/edit`)
}

const searchQuery = ref('')
const yearFilter = ref<number>(props.tahuns.reduce((max, tahun) => tahun.id > max ? tahun.id : max, 0));

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

const isPeriodExists = computed(() => {
  return periodeData.value.some((p) => p.tahun.id === yearFilter.value)
})

const isGenerating = ref(false)
function generatePeriode() {
  if (confirm('Apakah Anda ingin membuat otomatis periode untuk tahun berjalan?')) {
    isGenerating.value = true
    Inertia.visit('/periode/generate', {
      method: 'post' as Method,
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

function handleLanjutTahun() {
  if (confirm('Lanjutkan ke tahun berikutnya dan buat periode baru?')) {
    Inertia.post('/periode/lanjutkanKeTahunBerikutnya', {}, {
      onSuccess: (page) => {
        const newYear = page.props.newYear as string | undefined;
        if (newYear) {
          statusMessage.value = `Tahun ${newYear} berhasil ditambahkan!`
        } else {
          statusMessage.value = 'Tahun baru berhasil ditambahkan!'
        }
        Inertia.reload({ preserveScroll: true });
      },
      onError: () => {
        statusMessage.value = 'Gagal menambahkan tahun.'
      }
    })
  }
}
</script>

<template>
  <Head title="Periode"/>
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
          v-if="!isPeriodExists"
          >
          {{ isGenerating ? 'Memproses...' : 'Buat Periode' }}
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
              <td class="px-4 py-2 text-sm text-gray-600">{{ p.tahap?.tahap || '-' }}</td>
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
                  v-if="p.status !== 2"
                  @click="toggleStatus(p.id)"
                  class="text-sm text-blue-600 hover:text-blue-700"
                >
                  {{ p.status === 1 ? 'Tutup' : 'Buka' }}
                </button>

                <button
                  v-if="p.status !== 2"
                  @click="editItem(p.id)"
                  class="text-sm text-green-600 hover:text-green-700 ml-2"
                >
                  Edit
                </button>

                <button
                  v-if="canShowSelesai(p)"
                  @click="markAsSelesai(p.id)"
                  class="text-sm text-yellow-600 hover:text-yellow-700 ml-2"
                >
                  Selesai
                </button>



                <span v-if="p.status === 2" class="text-gray-500 text-sm italic">Sudah selesai</span>
              </td>


            </tr>
          </tbody>
        </table>
        <div v-if="yearFilter && allSelesai" class="mt-4 flex justify-end">
          <button
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            @click="handleLanjutTahun"
          >
            Mulai Periode Baru
          </button>
        </div>
      </div>
      <div v-if="statusMessage" class="mt-4 text-sm text-green-600">
        {{ statusMessage }}
      </div>
    </div>
  </AppLayout>
</template>
