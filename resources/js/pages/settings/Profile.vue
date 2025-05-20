<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import type { BreadcrumbItem, SharedData } from '@/types';

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
  user_detail?: {
    alamat: string;
    nip: string;
    no_hp: string;
    jenis_kelamin: string;
  };
}

defineProps<Props>();

const page = usePage<SharedData>();
const user = page.props.auth.user;

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Detail Profile',
    href: '/settings/profile',
  },
];

const form = useForm({
  name: user.name,
  email: user.email,
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
    <Head title="Detail Profile" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="Detail Profile" description="Update informasi profil dan tambahan Anda" />

        <form @submit.prevent="submit" class="space-y-6">
          <!-- Name -->
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="form.name" required autocomplete="name" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

<!-- Email (dikunci) -->
          <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input
              id="email"
              type="email"
              v-model="form.email"
              readonly
              class="bg-muted cursor-not-allowed"
              autocomplete="username"
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>
          
          <!-- Verifikasi Email -->
          <div v-if="mustVerifyEmail && !user.email_verified_at">
            <p class="text-sm text-muted-foreground">
              Email Anda belum diverifikasi.
              <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="text-foreground underline underline-offset-4 hover:underline"
              >
                Klik di sini untuk mengirim ulang email verifikasi.
              </Link>
            </p>

            <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
              Link verifikasi baru telah dikirim ke email Anda.
            </div>
          </div>

          <!-- Alamat -->
          <div class="grid gap-2">
            <Label for="alamat">Alamat</Label>
            <Input id="alamat" v-model="form.alamat" />
            <InputError class="mt-2" :message="form.errors.alamat" />
          </div>

          <!-- NIP -->
          <div class="grid gap-2">
            <Label for="nip">NIP</Label>
            <Input id="nip" v-model="form.nip" />
            <InputError class="mt-2" :message="form.errors.nip" />
          </div>

          <!-- No HP -->
          <div class="grid gap-2">
            <Label for="no_hp">No HP</Label>
            <Input id="no_hp" v-model="form.no_hp" />
            <InputError class="mt-2" :message="form.errors.no_hp" />
          </div>

          <!-- Jenis Kelamin -->
          <div class="grid gap-2">
            <Label for="jenis_kelamin">Jenis Kelamin</Label>
            <select
              id="jenis_kelamin"
              v-model="form.jenis_kelamin"
              class="border rounded p-2 text-sm"
            >
              <option value="">Pilih Jenis Kelamin</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
            <InputError class="mt-2" :message="form.errors.jenis_kelamin" />
          </div>
          <!-- Submit -->
          <div class="flex items-center gap-4">
            <Button :disabled="form.processing">Simpan</Button>
            <Transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0"
            >
              <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Tersimpan.</p>
            </Transition>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
