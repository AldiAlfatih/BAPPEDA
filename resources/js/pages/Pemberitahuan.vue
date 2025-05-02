<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps<{
  bantuan: {
    id: number,
    judul: string
  }
}>()

const form = useForm({
  deskripsi: '',
  file: null as File | null,
  tgl: '',
})

function submit() {
  form.post(`/bantuan/${props.bantuan.id}/faq`, {
    forceFormData: true,
  })
}
</script>

<template>
  <Head title="Tambah FAQ Bantuan" />
  <AppLayout>
    <div class="p-4 max-w-xl mx-auto space-y-4">
      <h1 class="text-2xl font-bold">Tambah FAQ untuk: {{ bantuan.judul }}</h1>

      <div>
        <label class="block mb-1">Deskripsi</label>
        <Textarea v-model="form.deskripsi" placeholder="Tuliskan penjelasan atau solusi..." />
        <p v-if="form.errors.deskripsi" class="text-red-500 text-sm">{{ form.errors.deskripsi }}</p>
      </div>

      <div>
        <label class="block mb-1">Upload File</label>
        <Input type="file" @change="e => form.file = e.target.files?.[0] ?? null" />
        <p v-if="form.errors.file" class="text-red-500 text-sm">{{ form.errors.file }}</p>
      </div>

      <div>
        <label class="block mb-1">Tanggal</label>
        <Input type="date" v-model="form.tgl" />
        <p v-if="form.errors.tgl" class="text-red-500 text-sm">{{ form.errors.tgl }}</p>
      </div>

      <Button @click="submit" :disabled="form.processing">Simpan</Button>
    </div>
  </AppLayout>
</template>
