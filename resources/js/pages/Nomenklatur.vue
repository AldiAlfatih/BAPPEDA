<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Nomenklatur',
    href: '/nomenklatur',
  },
];

// Data state
const kodeNomenklatur = ref([]);
const isModalOpen = ref(false);
const isEditMode = ref(false);
const formData = ref({
  id: null,
  nomor_kode: '',
  nomenklatur: '',
  urusan: '',
  bidang_urusan: '',
  program: '',
  kegiatan: '',
  subkegiatan: '',
});

// Fetch data from API
const fetchKodeNomenklatur = async () => {
  try {
    const response = await axios.get('/api/NomenklaturController');
    kodeNomenklatur.value = response.data;
  } catch (error) {
    console.error('Error fetching data:', error);
  }
};

// Modal controls
const showCreateModal = () => {
  isModalOpen.value = true;
  isEditMode.value = false;
  resetForm();
};

const showEditModal = (kode) => {
  isModalOpen.value = true;
  isEditMode.value = true;
  formData.value = { ...kode };
};

const closeModal = () => {
  isModalOpen.value = false;
};

// Save data (add/update)
const saveKode = async () => {
  try {
    if (isEditMode.value) {
      await axios.put(`/api/NomenklaturController/${formData.value.id}`, formData.value);
    } else {
      await axios.post('/api/NomenklaturController', formData.value);
    }
    fetchKodeNomenklatur();
    closeModal();
  } catch (error) {
    console.error('Error saving data:', error);
  }
};

// Delete data
const deleteKode = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    try {
      await axios.delete(`/api/NomenklaturController/${id}`);
      fetchKodeNomenklatur();
    } catch (error) {
      console.error('Error deleting data:', error);
    }
  }
};

// Reset form
const resetForm = () => {
  formData.value = {
    id: null,
    nomor_kode: '',
    nomenklatur: '',
    urusan: '',
    bidang_urusan: '',
    program: '',
    kegiatan: '',
    subkegiatan: '',
  };
};

onMounted(() => {
  fetchKodeNomenklatur();
});
</script>

<template>
  <Head title="Nomenklatur" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="container p-4">
        <h1 class="mb-4 text-xl font-semibold">Daftar Kode Nomenklatur</h1>

        <!-- Button untuk membuka form tambah data -->
        <button @click="showCreateModal" class="bg-blue-500 text-white p-2 rounded-md mb-4">Tambah Kode Nomenklatur</button>

        <!-- Menampilkan tabel data kode nomenklatur -->
        <div v-if="kodeNomenklatur.length === 0" class="alert alert-info mb-4">
          Belum ada data kode nomenklatur.
        </div>
        <div v-else>
          <table class="table-auto w-full border-collapse">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Nomor Kode</th>
                <th class="px-4 py-2">Nomenklatur</th>
                <th class="px-4 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(kode, index) in kodeNomenklatur" :key="kode.id">
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2">{{ kode.nomor_kode }}</td>
                <td class="px-4 py-2">{{ kode.nomenklatur }}</td>
                <td class="px-4 py-2">
                  <button @click="showEditModal(kode)" class="bg-yellow-500 text-white p-2 rounded-md">Edit</button>
                  <button @click="deleteKode(kode.id)" class="bg-red-500 text-white p-2 rounded-md ml-2">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Modal untuk Create/Edit -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
          <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-lg font-semibold mb-4">{{ isEditMode ? 'Edit' : 'Tambah' }} Kode Nomenklatur</h2>
            <form @submit.prevent="saveKode">
              <div class="mb-4">
                <label for="nomor_kode" class="block text-sm font-medium">Nomor Kode</label>
                <input type="text" id="nomor_kode" v-model="formData.nomor_kode" class="border border-gray-300 rounded-md p-2 w-full" required />
              </div>
              <div class="mb-4">
                <label for="nomenklatur" class="block text-sm font-medium">Nomenklatur</label>
                <input type="text" id="nomenklatur" v-model="formData.nomenklatur" class="border border-gray-300 rounded-md p-2 w-full" />
              </div>
              <div class="mb-4">
                <label for="urusan" class="block text-sm font-medium">Urusan</label>
                <input type="text" id="urusan" v-model="formData.urusan" class="border border-gray-300 rounded-md p-2 w-full" />
              </div>
              <div class="mb-4">
                <label for="bidang_urusan" class="block text-sm font-medium">Bidang Urusan</label>
                <input type="text" id="bidang_urusan" v-model="formData.bidang_urusan" class="border border-gray-300 rounded-md p-2 w-full" />
              </div>
              <div class="mb-4">
                <label for="program" class="block text-sm font-medium">Program</label>
                <input type="text" id="program" v-model="formData.program" class="border border-gray-300 rounded-md p-2 w-full" />
              </div>
              <div class="mb-4">
                <label for="kegiatan" class="block text-sm font-medium">Kegiatan</label>
                <input type="text" id="kegiatan" v-model="formData.kegiatan" class="border border-gray-300 rounded-md p-2 w-full" />
              </div>
              <div class="mb-4">
                <label for="subkegiatan" class="block text-sm font-medium">Subkegiatan</label>
                <input type="text" id="subkegiatan" v-model="formData.subkegiatan" class="border border-gray-300 rounded-md p-2 w-full" />
              </div>
              <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">{{ isEditMode ? 'Perbarui' : 'Simpan' }}</button>
                <button type="button" @click="closeModal" class="bg-gray-500 text-white p-2 rounded-md">Tutup</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
}
.table-auto {
  width: 100%;
  border: 1px solid #ccc;
  border-collapse: collapse;
}
.table-auto th, .table-auto td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
}
.alert {
  padding: 10px;
  background-color: #f0f4f8;
  border: 1px solid #d3e2e8;
}
</style>