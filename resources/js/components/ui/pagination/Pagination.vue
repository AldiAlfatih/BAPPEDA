<template>
  <div v-if="totalPages > 1" class="flex items-center justify-between">
    <!-- Info text -->
    <div class="text-sm text-gray-600">
      Menampilkan {{ startItem }} sampai {{ endItem }} dari {{ totalItems }} data
    </div>
    
    <!-- Pagination controls -->
    <div class="flex items-center gap-2">
      <!-- Previous button -->
      <Button
        variant="outline"
        size="sm"
        @click="goToPreviousPage"
        :disabled="currentPage === 1"
        class="flex items-center gap-1"
      >
        <ChevronLeft class="h-4 w-4" />
        <span class="hidden sm:inline">Sebelumnya</span>
      </Button>

      <!-- Page numbers (max 5 visible) -->
      <div class="hidden gap-1 sm:flex">
        <Button
          v-for="page in visiblePages"
          :key="page"
          :variant="page === currentPage ? 'default' : 'outline'"
          size="sm"
          @click="goToPage(page)"
          class="h-10 w-10"
        >
          {{ page }}
        </Button>
      </div>

      <!-- Mobile page indicator -->
      <div class="sm:hidden">
        <span class="text-sm">{{ currentPage }} / {{ totalPages }}</span>
      </div>

      <!-- Next button -->
      <Button
        variant="outline"
        size="sm"
        @click="goToNextPage"
        :disabled="currentPage === totalPages"
        class="flex items-center gap-1"
      >
        <span class="hidden sm:inline">Selanjutnya</span>
        <ChevronRight class="h-4 w-4" />
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

interface Props {
  currentPage: number
  totalPages: number
  totalItems: number
  itemsPerPage: number
}

interface Emits {
  (e: 'update:currentPage', page: number): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Computed properties
const startItem = computed(() => (props.currentPage - 1) * props.itemsPerPage + 1)
const endItem = computed(() => Math.min(props.currentPage * props.itemsPerPage, props.totalItems))

// Calculate visible page numbers (max 5 pages)
const visiblePages = computed(() => {
  const maxVisiblePages = 5
  const pages: number[] = []
  
  if (props.totalPages <= maxVisiblePages) {
    // If total pages is 5 or less, show all pages
    for (let i = 1; i <= props.totalPages; i++) {
      pages.push(i)
    }
  } else {
    // Calculate the range of pages to show
    let startPage = Math.max(1, props.currentPage - Math.floor(maxVisiblePages / 2))
    let endPage = Math.min(props.totalPages, startPage + maxVisiblePages - 1)
    
    // Adjust start page if we're near the end
    if (endPage - startPage + 1 < maxVisiblePages) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1)
    }
    
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i)
    }
  }
  
  return pages
})

// Methods
const goToPage = (page: number) => {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('update:currentPage', page)
  }
}

const goToPreviousPage = () => {
  if (props.currentPage > 1) {
    emit('update:currentPage', props.currentPage - 1)
  }
}

const goToNextPage = () => {
  if (props.currentPage < props.totalPages) {
    emit('update:currentPage', props.currentPage + 1)
  }
}
</script> 