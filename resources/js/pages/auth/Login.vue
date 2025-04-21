<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
<<<<<<< HEAD
    <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
=======
    <AuthBase title="Login">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-black">
            {{ status }}
        </div>
        
        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2 text-black">
>>>>>>> bd447f1 (database pertama)
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

<<<<<<< HEAD
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                            Forgot password?
                        </TextLink>
=======
                <div class="grid gap-2 text-black">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
>>>>>>> bd447f1 (database pertama)
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

<<<<<<< HEAD
                <div class="flex items-center justify-between" :tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="4" />
=======
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center text-black ">
                        <Checkbox id="remember" v-model="form.remember"/>
>>>>>>> bd447f1 (database pertama)
                        <span>Remember me</span>
                    </Label>
                </div>

<<<<<<< HEAD
                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
=======
                <Button type="submit" class="w-full bg-green-900" :tabindex="4" :disabled="form.processing"\\\>
>>>>>>> bd447f1 (database pertama)
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>
<<<<<<< HEAD

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
=======
        </form>
    </AuthBase>
</template>
>>>>>>> bd447f1 (database pertama)
