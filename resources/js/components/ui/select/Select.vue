<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';
import type { Option } from '@/types';

const props = defineProps<{
  modelValue: number | null;
  options: Option[];
  id?: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: number): void;
}>();

function handleChange(event: Event) {
  const value = parseInt((event.target as HTMLSelectElement).value);
  emit('update:modelValue', value);
}
</script>

<template>
  <select
    :id="id"
    class="border rounded px-3 py-2"
    :value="modelValue ?? ''"
    @change="handleChange"
  >
    <option value="" disabled>Pilih jenis...</option>
    <option
      v-for="option in options"
      :key="option.value"
      :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>
