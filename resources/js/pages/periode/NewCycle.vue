<script setup lang="ts">
import { defineProps } from 'vue'

interface Tahap {
  id: number;
  tahap: string;
}

// Menerima props tahapList
const props = defineProps({
  tahapList: {
    type: Array as () => Tahap[],
    required: true
  }
})
</script>

<template>
  <div>
    <h2>Daftar Tahap Baru</h2>
    <ul>
      <li v-for="tahap in props.tahapList" :key="tahap.id">
        {{ tahap.tahap }}
      </li>
    </ul>
  </div>
</template>
