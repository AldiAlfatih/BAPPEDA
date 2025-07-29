/**
 * Simple dialog utility that uses browser native confirm/alert
 * but can be easily replaced with custom UI components later
 */

export interface DialogOptions {
  title?: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
}

/**
 * Show a confirmation dialog
 * @param options Dialog options
 * @returns Promise resolving to true (confirmed) or false (cancelled)
 */
export function showConfirmation(options: DialogOptions): Promise<boolean> {
  const { message, title } = options;
  const displayMessage = title ? `${title}\n\n${message}` : message;
  return Promise.resolve(window.confirm(displayMessage));
}

/**
 * Show an alert dialog
 * @param message Message to display
 * @param title Optional title
 * @returns Promise resolving after user closes the dialog
 */
export function showAlert(message: string, title?: string): Promise<void> {
  const displayMessage = title ? `${title}\n\n${message}` : message;
  window.alert(displayMessage);
  return Promise.resolve();
}

/**
 * Show a success message
 * @param message Success message
 * @param title Optional title
 */
export function showSuccess(message: string, title: string = '✅ Berhasil'): Promise<void> {
  return showAlert(message, title);
}

/**
 * Show an error message
 * @param message Error message
 * @param title Optional title
 */
export function showError(message: string, title: string = '❌ Error'): Promise<void> {
  return showAlert(message, title);
}

/**
 * Show a confirmation dialog for deletion
 * @param message Message asking for confirmation
 * @param title Optional title
 */
export function showDeleteConfirmation(message: string, title: string = 'Konfirmasi Hapus'): Promise<boolean> {
  return showConfirmation({ message, title });
}
