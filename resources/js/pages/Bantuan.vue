<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'; 
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import { usePage, useForm } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import 'primeicons/primeicons.css'





const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
]

interface User {
  id: number
  name: string
  role: string 
}

const auth = usePage().props.auth as { user: User }
const isPD = computed(() => auth.user?.role === 'perangkat_daerah')
const isAdmin = computed(() => auth.user?.role === 'admin' || auth.user?.role === 'operator')

const props = defineProps<{
  bantuans: Array<{
    id: number
    judul: string
    created_at: string
    status: number
    faqs: Array<{
      balasan: string
    }>
  }>
}>()


const searchQuery = ref('')
const columnFilterOpen = ref(false) 
const columns = ref({
  no: true,
  judul: true,
  tanggal_dibuat: true,
  status: true,
  aksi: true
})


const filteredBantuans = computed(() => {
  return props.bantuans.filter((b) => {
    const matchSearch = b.judul.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchSearch
  })
})

function formatDate(date: string) {
  const options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }
  return new Date(date).toLocaleDateString('id-ID', options) 
}


function toggleColumnFilter() {
  columnFilterOpen.value = !columnFilterOpen.value
}


const filterRef = ref<HTMLElement | null>(null);

function closeFilterOnClickOutside(event: MouseEvent) {
  if (filterRef.value && !filterRef.value.contains(event.target as Node)) {
    columnFilterOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener('mousedown', closeFilterOnClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('mousedown', closeFilterOnClickOutside);
});

const updateStatusToDiproses = (bantuanId: number) => {
  if (isAdmin.value) {
    form.post(route('bantuan.updateStatusToDiproses', bantuanId), {
      onSuccess: () => {
        Inertia.get(route('bantuan.chat', bantuanId));
      }
    });
  } else {
    Inertia.get(route('bantuan.chat', bantuanId));
  }
}

const form = useForm({
  balasan: '',
  file: null as File | null,
});
</script>

<template>
  <Head title="Bantuan" />

  <AppLayout :breadcrumbs="breadcrumbs">
  
      <!-- Filter Kolom -->
      <div class="mb-2 flex justify-start">
        <button @click="toggleColumnFilter" class=" px-4 ml-3 mt-2 border border-gray-300 px-7 py-0,5 rounded-md">Filter</button>
        <div 
          v-if="columnFilterOpen" 
          ref="filterRef" 
          class="absolute bg-white border border-gray-300 rounded-md shadow-lg mt-2 font-serif"
        >
          <label class="block px-4 py-2">
            <input type="checkbox" v-model="columns.judul" /> Judul
          </label>
          <label class="block px-4 py-2">
            <input type="checkbox" v-model="columns.tanggal_dibuat" /> Tanggal Dibuat
          </label>
          <label class="block px-4 py-2">
            <input type="checkbox" v-model="columns.status" /> Status
          </label>
          <label class="block px-4 py-2">
            <input type="checkbox" v-model="columns.aksi" /> Aksi
          </label>
        </div>
      </div>

      <div class="flex flex-col gap-2 p-1">
        <!-- Flex container untuk search dan add button -->
        <div class="flex justify-between items-center mb-2 ml-2">
          <!-- Kolom Pencarian -->
          <div class="flex-1 mr-2 ">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari berdasarkan Judul..."
              class="w-full border border-gray-300 px-2 py-1 rounded-md w-xs"
            />
          </div>

          <!-- Tombol Tambah -->
          <Link 
          v-if="isPD" 
          :href="route('bantuan.create')" 
          class="pi pi-plus h-full px-4 py-2 bg-green-600 text-base text-white rounded-md hover:bg-green-700"
        >
          Tambah
        </Link>
        </div>

        <div class="overflow-x-auto rounded-lg shadow border border-gray-200 ml-2 mr-2">
          <table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow">
            <thead class="bg-gray-100">
              <tr>
                <th v-if="columns.no" class="px-4 py-2 text-center text-sm font-semibold text-gray-700">No</th>
                <th v-if="columns.judul" class="px-4 py-2 text-center text-sm font-semibold text-gray-700 w-[50%]">Judul</th>
                <th v-if="columns.tanggal_dibuat" class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Tanggal Dibuat</th>
                <th v-if="columns.status" class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Status</th>
                <th v-if="columns.aksi" class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="(b, index) in filteredBantuans" :key="b.id">
                <td v-if="columns.no" class="px-4 py-2 text-center text-sm text-gray-700">{{ index + 1 }}</td>
                <td v-if="columns.judul" class="px-4 py-2 text-justify text-sm text-gray-800">{{ b.judul }}</td>
                <td v-if="columns.tanggal_dibuat" class="px-4 py-2 text-center text-sm text-gray-600">{{ formatDate(b.created_at) }}</td>
                <td v-if="columns.status" class="px-4 py-2 text-center text-sm text-gray-600">
                  {{ b.status === 0 ? 'Diterima' : b.status === 1 ? 'Diproses' : 'Selesai' }}
                </td>
                <td v-if="columns.aksi" class="px-4 py-2 text-center text-sm text-gray-700 space-x-2">
                  <Link 
                    :href="route('bantuan.chat', b.id)"
                    @click.prevent="updateStatusToDiproses(b.id)"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 pi pi-comments"
                  >
                    Chat
                  </Link>
                  <Link 
                  v-if="isAdmin"
                  :href="route('bantuan.destroy', b.id)" method="delete" as="button" 
                  class="pi pi-trash px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                  Hapus
                </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </AppLayout>
</template>
