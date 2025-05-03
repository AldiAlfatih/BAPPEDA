<template>
    <div>
      <select v-bind="$attrs" v-model="modelValue" class="w-full p-2 border rounded">
        <option v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </div>
</template>
    
<script setup lang="ts">
    import { defineProps, defineEmits } from 'vue';
    
    // Define the Option type
    interface Option {
      label: string;
      value: string | number;
    }
  
    const props = defineProps({
      modelValue: {
        type: [String, Number],
        required: true,
      },
      options: {
        type: Array as () => Option[], // Specify the type of the options array
        required: true,
      },
    });
    
    const emit = defineEmits(['update:modelValue']);
</script>
  