<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
  DropdownMenuCheckboxItem,
} from '@/components/ui/dropdown-menu'

// Data dummy (nanti bisa dari API)
const bantuanList = ref([
  {
    id: '1',
    status: 'Selesai',
    namaPerangkatDaerah: 'Dinas Pendidikan',
    jenisBantuan: 'IT Support',
    deskripsi: 'Perbaikan jaringan internet',
    tanggal: '2025-04-20',
    screenshotUrl: "/images/logo-parepare.png" ,
  },
  {
    id: '2',
    status: 'Dalam Proses',
    namaPerangkatDaerah: 'Dinas Kesehatan',
    jenisBantuan: 'Pengadaan Alat',
    deskripsi: 'Permintaan laptop tambahan',
    tanggal: '2025-04-22',
    screenshotUrl: '/screenshots/screenshot2.png',
  },
])

const selectedRows = ref<string[]>([])
const search = ref('')
const showStatus = ref(true)
const showNama = ref(true)
const showJenisBantuan = ref(true)
const showDeskripsi = ref(true)
const showTanggal = ref(true)

// Untuk filter berdasarkan Nama Perangkat Daerah
const filteredBantuanList = computed(() => {
  if (!search.value) return bantuanList.value
  return bantuanList.value.filter((b) =>
    b.namaPerangkatDaerah.toLowerCase().includes(search.value.toLowerCase())
  )
})

function toggleAllRows(checked: boolean) {
  if (checked) {
    selectedRows.value = bantuanList.value.map((b) => b.id)
  } else {
    selectedRows.value = []
  }
}

function toggleRow(id: string, checked: boolean) {
  if (checked) {
    if (!selectedRows.value.includes(id)) {
      selectedRows.value.push(id)
    }
  } else {
    selectedRows.value = selectedRows.value.filter((item) => item !== id)
  }
}

// Untuk melihat screenshot
function lihatScreenshot(url: string) {
  window.open(url, '_blank')
}
</script>

<template>
  <Head title="Bantuan" />

  <AppLayout>
    <div class="flex flex-col gap-4 p-4">
      <div class="relative min-h-[100vh] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
        <div class="space-y-4">
          <div class="flex items-center justify-between gap-2">
            <Input v-model="search" placeholder="Cari Perangkat Daerah..." class="max-w-sm" />
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline">Columns</Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuCheckboxItem v-model:modelValue="showStatus">
                  Status
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem v-model:modelValue="showNama">
                  Nama Perangkat Daerah
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem v-model:modelValue="showJenisBantuan">
                  Jenis Bantuan
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem v-model:modelValue="showDeskripsi">
                  Deskripsi
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem v-model:modelValue="showTanggal">
                  Tanggal
                </DropdownMenuCheckboxItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>

          <div class="border rounded-lg">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>
                    <Checkbox
                      :modelValue="selectedRows.length === bantuanList.length"
                      :indeterminate="selectedRows.length > 0 && selectedRows.length < bantuanList.length"
                      @update:modelValue="toggleAllRows"
                    />
                  </TableHead>
                  <TableHead v-if="showStatus">Status</TableHead>
                  <TableHead v-if="showNama">Nama Perangkat Daerah</TableHead>
                  <TableHead v-if="showJenisBantuan">Jenis Bantuan</TableHead>
                  <TableHead v-if="showDeskripsi">Deskripsi</TableHead>
                  <TableHead v-if="showTanggal">Tanggal</TableHead>
                  <TableHead class="text-left" >Detail</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="bantuan in filteredBantuanList" :key="bantuan.id">
                  <TableCell>
                    <Checkbox
                      :modelValue="selectedRows.includes(bantuan.id)"
                      @update:modelValue="(checked) => toggleRow(bantuan.id, checked)"
                    />
                  </TableCell>
                  <TableCell v-if="showStatus">{{ bantuan.status }}</TableCell>
                  <TableCell v-if="showNama">{{ bantuan.namaPerangkatDaerah }}</TableCell>
                  <TableCell v-if="showJenisBantuan">{{ bantuan.jenisBantuan }}</TableCell>
                  <TableCell v-if="showDeskripsi">{{ bantuan.deskripsi }}</TableCell>
                  <TableCell v-if="showTanggal">{{ bantuan.tanggal }}</TableCell>
                  <TableCell class="text-left">
                    <Button size="sm" variant="outline" @click="lihatScreenshot(bantuan.screenshotUrl)">
                      Lihat Screenshot
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <div class="text-sm text-muted-foreground">
            {{ selectedRows.length }} dari {{ bantuanList.length }} data terpilih.
          </div>

          <div class="flex justify-end space-x-2">
            <Button variant="outline" disabled>Previous</Button>
            <Button variant="outline" disabled>Next</Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>