import { ref } from 'vue';

interface DialogConfig {
  title?: string;
  message: string;
  open: boolean;
  cancelText?: string;
  confirmText?: string;
  confirmClass?: string;
  onConfirm?: () => void;
  onCancel?: () => void;
}

// Default dialog state
const dialogState = ref<DialogConfig>({
  title: 'Konfirmasi',
  message: '',
  open: false,
  cancelText: 'Batal',
  confirmText: 'OK',
  confirmClass: 'bg-blue-600 text-white hover:bg-blue-700',
});

/**
 * Show a confirmation dialog
 */
function showConfirmDialog(config: Omit<DialogConfig, 'open'>) {
  dialogState.value = {
    ...dialogState.value,
    ...config,
    open: true,
  };

  return new Promise<boolean>((resolve) => {
    dialogState.value.onConfirm = () => {
      if (config.onConfirm) config.onConfirm();
      resolve(true);
    };
    dialogState.value.onCancel = () => {
      if (config.onCancel) config.onCancel();
      resolve(false);
    };
  });
}

/**
 * Show an alert dialog
 */
function showAlert(message: string, title = 'Notifikasi', confirmText = 'OK') {
  return showConfirmDialog({
    title,
    message,
    confirmText,
    cancelText: '',
    confirmClass: 'bg-blue-600 text-white hover:bg-blue-700',
  });
}

/**
 * Show a success dialog
 */
function showSuccess(message: string, title = 'Sukses') {
  return showConfirmDialog({
    title,
    message,
    confirmText: 'OK',
    cancelText: '',
    confirmClass: 'bg-green-600 text-white hover:bg-green-700',
  });
}

/**
 * Show an error dialog
 */
function showError(message: string, title = 'Error') {
  return showConfirmDialog({
    title,
    message,
    confirmText: 'OK',
    cancelText: '',
    confirmClass: 'bg-red-600 text-white hover:bg-red-700',
  });
}

/**
 * Show a delete confirmation dialog
 */
function showDeleteConfirm(message: string, title = 'Konfirmasi Hapus') {
  return showConfirmDialog({
    title,
    message,
    confirmText: 'Hapus',
    cancelText: 'Batal',
    confirmClass: 'bg-red-600 text-white hover:bg-red-700',
  });
}

export {
    dialogState, showAlert, showConfirmDialog, showDeleteConfirm, showError, showSuccess
};

