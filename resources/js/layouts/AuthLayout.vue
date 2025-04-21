<script setup lang="ts">
<<<<<<< HEAD
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';
=======
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';

>>>>>>> bd447f1 (database pertama)

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description">
        <slot />
    </AuthLayout>
</template>
