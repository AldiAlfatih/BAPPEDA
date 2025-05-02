<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps<{
  bantuans: Array<{ id: number; judul: string }>
}>()

const form = useForm({
  bantuan_id: '',
  deskripsi: '',
  file: null as File | null,
  tgl: '',
})

function submit() {
  form.post('/bantuanfaq', {
    forceFormData: true,
  })
}
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Tambah FAQ</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Bantuan</label>
        <select v-model="form.bantuan_id" class="border rounded w-full p-2">
          <option v-for="b in bantuans" :key="b.id" :value="b.id">{{ b.judul }}</option>
        </select>
      </div>
      <div>
        <label>Deskripsi</label>
        <textarea v-model="form.deskripsi" class="border w-full p-2 rounded" rows="4"></textarea>
      </div>
      <div>
        <label>File (optional)</label>
        <input type="file" @change="e => form.file = e.target.files?.[0] || null" />
      </div>
      <div>
        <label>Tanggal</label>
        <input type="date" v-model="form.tgl" class="border rounded p-2 w-full" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
  </div>
</template>
