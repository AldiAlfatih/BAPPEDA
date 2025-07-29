<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  title?: string;
  message: string;
  open: boolean;
  cancelText?: string;
  confirmText?: string;
  confirmClass?: string;
  showIcon?: boolean;
}>();

const emits = defineEmits<{
  (e: 'update:open', value: boolean): void;
  (e: 'confirm'): void;
  (e: 'cancel'): void;
}>();

const isOpen = ref(props.open);

// Watch for external open changes
const onOpenChange = (value: boolean) => {
  isOpen.value = value;
  emits('update:open', value);
};

const handleCancel = () => {
  onOpenChange(false);
  emits('cancel');
};

const handleConfirm = () => {
  onOpenChange(false);
  emits('confirm');
};
</script>

<template>
  <Dialog :open="open" @update:open="onOpenChange">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ title || 'Konfirmasi' }}</DialogTitle>
        <DialogDescription>{{ message }}</DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex justify-end gap-2 pt-4">
        <Button variant="outline" @click="handleCancel">
          {{ cancelText || 'Batal' }}
        </Button>
        <Button 
          :class="confirmClass || 'bg-red-600 text-white hover:bg-red-700'" 
          @click="handleConfirm"
        >
          <Trash2 v-if="showIcon !== false" class="mr-2 h-4 w-4" />
          {{ confirmText || 'Hapus' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
