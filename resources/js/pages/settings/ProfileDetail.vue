<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    skpd?: {
        nama_skpd: string;
        nama_operator: string;
        nama_dinas: string;
        no_dpa: string;
        kode_organisasi: string;
    };
    user_detail?: {
        alamat: string;
        nip: string;
        no_hp: string;
        jenis_kelamin: string;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profiledetail',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
    // Data SKPD
    nama_skpd: page.props.skpd?.nama_skpd ?? '',
    nama_operator: page.props.skpd?.nama_operator ?? '',
    nama_dinas: page.props.skpd?.nama_dinas ?? '',
    no_dpa: page.props.skpd?.no_dpa ?? '',
    kode_organisasi: page.props.skpd?.kode_organisasi ?? '',
    // User Detail
    alamat: page.props.user_detail?.alamat ?? '',
    nip: page.props.user_detail?.nip ?? '',
    no_hp: page.props.user_detail?.no_hp ?? '',
    jenis_kelamin: page.props.user_detail?.jenis_kelamin ?? '',
});

const submit = () => {
    form.put(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nama (bisa diubah) -->
                <div class="grid gap-2">
                    <Label for="name">Nama</Label>
                    <Input id="name" v-model="form.name" required autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email (terkunci) -->
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" v-model="form.email" readonly class="bg-gray-100 cursor-not-allowed" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- SKPD Fields (semua terkunci) -->
                <HeadingSmall title="Informasi SKPD" description="Data SKPD yang terkait dengan akun Anda" />

                <div class="grid gap-2">
                    <Label for="nama_skpd">Nama SKPD</Label>
                    <Input id="nama_skpd" v-model="form.nama_skpd" />
                    <InputError class="mt-2" :message="form.errors.nama_skpd" />
                </div>

                <div class="grid gap-2">
                    <Label for="nama_operator">Nama Operator</Label>
                    <Input id="nama_operator" v-model="form.nama_operator" readonly class="bg-gray-100 cursor-not-allowed" />
                    <InputError class="mt-2" :message="form.errors.nama_operator" />
                </div>

                <div class="grid gap-2">
                    <Label for="nama_dinas">Nama Dinas</Label>
                    <Input id="nama_dinas" v-model="form.nama_dinas" readonly class="bg-gray-100 cursor-not-allowed" />
                    <InputError class="mt-2" :message="form.errors.nama_dinas" />
                </div>

                <div class="grid gap-2">
                    <Label for="no_dpa">No DPA</Label>
                    <Input id="no_dpa" v-model="form.no_dpa" readonly class="bg-gray-100 cursor-not-allowed" />
                    <InputError class="mt-2" :message="form.errors.no_dpa" />
                </div>

                <div class="grid gap-2">
                    <Label for="kode_organisasi">Kode Organisasi</Label>
                    <Input id="kode_organisasi" v-model="form.kode_organisasi" readonly class="bg-gray-100 cursor-not-allowed" />
                    <InputError class="mt-2" :message="form.errors.kode_organisasi" />
                </div>

                <!-- User Detail Fields -->
                <HeadingSmall title="Informasi Pengguna" description="Data pribadi tambahan Anda" />

                <div class="grid gap-2">
                    <Label for="alamat">Alamat</Label>
                    <Input id="alamat" v-model="form.alamat" />
                    <InputError class="mt-2" :message="form.errors.alamat" />
                </div>

                <div class="grid gap-2">
                    <Label for="nip">NIP</Label>
                    <Input id="nip" v-model="form.nip" />
                    <InputError class="mt-2" :message="form.errors.nip" />
                </div>

                <div class="grid gap-2">
                    <Label for="no_hp">No HP</Label>
                    <Input id="no_hp" v-model="form.no_hp" />
                    <InputError class="mt-2" :message="form.errors.no_hp" />
                </div>

                <!-- Jenis Kelamin (terkunci) -->
                <div class="grid gap-2">
                    <Label for="jenis_kelamin">Jenis Kelamin</Label>
                    <select id="jenis_kelamin" v-model="form.jenis_kelamin" disabled class="input w-full rounded border px-2 py-2 bg-gray-100 cursor-not-allowed">
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.jenis_kelamin" />
                </div>


                    <!-- Email Verification -->
                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline underline-offset-4 hover:underline"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
