<script setup>
import { defineProps } from 'vue'

// Menerima props tahapList
const props = defineProps({
  tahapList: Array
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
