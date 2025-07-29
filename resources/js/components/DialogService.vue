<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

// Dialog state
interface DialogOptions {
  title: string;
  message: string;
  open: boolean;
  confirmText?: string;
  cancelText?: string;
  confirmClass?: string;
  onConfirm?: () => void;
  onCancel?: () => void;
  type?: 'confirm' | 'alert' | 'delete';
  showIcon?: boolean;
}

const dialogState = ref<DialogOptions>({
  title: '',
  message: '',
  open: false,
  confirmText: 'OK',
  cancelText: 'Batal',
  confirmClass: 'bg-red-600 text-white hover:bg-red-700',
  type: 'confirm',
  showIcon: true
});

// Expose functions to manage dialogs
const showDialog = (options: Omit<DialogOptions, 'open'>) => {
  dialogState.value = {
    ...dialogState.value,
    ...options,
    open: true
  };
  
  return new Promise<boolean>((resolve) => {
    dialogState.value.onConfirm = () => {
      hideDialog();
      resolve(true);
    };
    dialogState.value.onCancel = () => {
      hideDialog();
      resolve(false);
    };
  });
};

const hideDialog = () => {
  dialogState.value.open = false;
};

const onOpenChange = (value: boolean) => {
  if (!value) {
    hideDialog();
  }
};

// Dialog type helpers
const showConfirm = (title: string, message: string, confirmText = 'OK', cancelText = 'Batal') => {
  return showDialog({
    title,
    message,
    confirmText,
    cancelText,
    confirmClass: 'bg-blue-600 text-white hover:bg-blue-700',
    type: 'confirm',
    showIcon: false
  });
};

const showDelete = (title: string, message: string, confirmText = 'Hapus', cancelText = 'Batal') => {
  return showDialog({
    title,
    message,
    confirmText,
    cancelText,
    confirmClass: 'bg-red-600 text-white hover:bg-red-700',
    type: 'delete',
    showIcon: true
  });
};

const showAlert = (title: string, message: string, confirmText = 'OK') => {
  return showDialog({
    title,
    message,
    confirmText,
    cancelText: '',
    confirmClass: 'bg-blue-600 text-white hover:bg-blue-700',
    type: 'alert',
    showIcon: false
  });
};

// Make functions globally available
defineExpose({
  showConfirm,
  showDelete,
  showAlert,
  hideDialog
});
</script>

<template>
  <Dialog :open="dialogState.open" @update:open="onOpenChange">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ dialogState.title }}</DialogTitle>
        <DialogDescription>{{ dialogState.message }}</DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex justify-end gap-2 pt-4">
        <Button 
          v-if="dialogState.cancelText" 
          variant="outline" 
          @click="dialogState.onCancel && dialogState.onCancel()"
        >
          {{ dialogState.cancelText }}
        </Button>
        
        <Button 
          :class="dialogState.confirmClass"
          @click="dialogState.onConfirm && dialogState.onConfirm()"
        >
          <Trash2 v-if="dialogState.showIcon && dialogState.type === 'delete'" class="mr-2 h-4 w-4" />
          {{ dialogState.confirmText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template> 