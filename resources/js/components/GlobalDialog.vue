<script setup lang="ts">
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { dialogState } from '@/services/notificationService';
</script>

<template>
    <ConfirmDialog
        :open="dialogState.open"
        :title="dialogState.title"
        :message="dialogState.message"
        :cancelText="dialogState.cancelText"
        :confirmText="dialogState.confirmText"
        :confirmClass="dialogState.confirmClass"
        @update:open="dialogState.open = $event"
        @confirm="dialogState.onConfirm && dialogState.onConfirm()"
        @cancel="dialogState.onCancel && dialogState.onCancel()"
    />
</template>
