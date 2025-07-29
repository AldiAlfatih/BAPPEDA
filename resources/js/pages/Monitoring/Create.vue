<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// Field utama pagu anggaran
const pokok = ref<number | null>(null);
const parsial = ref<number | null>(null);
const perubahan = ref<number | null>(null);
const sumberDana = ref('');

// Target tiap triwulan (array of objects)
type TargetTriwulan = {
    kinerjaFisik: number | null;
    keuangan: number | null;
};

const targetTriwulan = ref<TargetTriwulan[]>([
    { kinerjaFisik: null, keuangan: null },
    { kinerjaFisik: null, keuangan: null },
    { kinerjaFisik: null, keuangan: null },
    { kinerjaFisik: null, keuangan: null },
]);

const errors = ref<Record<string, string>>({});

function validate() {
    errors.value = {};

    if (pokok.value === null || pokok.value < 0) {
        errors.value.pokok = 'Pokok harus diisi dan tidak boleh negatif';
    }
    if (parsial.value === null || parsial.value < 0) {
        errors.value.parsial = 'Parsial harus diisi dan tidak boleh negatif';
    }
    if (perubahan.value === null || perubahan.value < 0) {
        errors.value.perubahan = 'Perubahan harus diisi dan tidak boleh negatif';
    }
    if (!sumberDana.value.trim()) {
        errors.value.sumberDana = 'Sumber Dana harus diisi';
    }
    targetTriwulan.value.forEach((t, idx) => {
        if (t.kinerjaFisik === null || t.kinerjaFisik < 0 || t.kinerjaFisik > 100) {
            errors.value[`kinerjaFisik_${idx}`] = 'Kinerja Fisik harus 0-100%';
        }
        if (t.keuangan === null || t.keuangan < 0) {
            errors.value[`keuangan_${idx}`] = 'Keuangan harus diisi dan tidak boleh negatif';
        }
    });

    return Object.keys(errors.value).length === 0;
}

async function submit() {
    if (!validate()) return;

    try {
        const payload = {
            pokok: pokok.value,
            parsial: parsial.value,
            perubahan: perubahan.value,
            sumber_dana: sumberDana.value,
            target_triwulan: targetTriwulan.value.map((t) => ({
                kinerja_fisik: t.kinerjaFisik,
                keuangan: t.keuangan,
            })),
        };

        await axios.post('/api/pagu-anggaran', payload);

        alert('Data pagu anggaran berhasil disimpan.');
        router.push('/pagu-anggaran');
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
        } else {
            alert('Terjadi kesalahan saat menyimpan data.');
        }
    }
}
</script>

<template>
    <Head title="Rencana kinerja" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl rounded bg-white p-4 shadow">
            <h2 class="mb-6 text-xl font-bold">Input Data Pagu Anggaran APBD</h2>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Bagian Pokok, Parsial, Perubahan -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="mb-1 block font-semibold">Pokok (Rp)</label>
                        <input type="number" v-model.number="pokok" min="0" class="input" />
                        <p v-if="errors.pokok" class="text-sm text-red-600">{{ errors.pokok }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block font-semibold">Parsial (Rp)</label>
                        <input type="number" v-model.number="parsial" min="0" class="input" />
                        <p v-if="errors.parsial" class="text-sm text-red-600">{{ errors.parsial }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block font-semibold">Perubahan (Rp)</label>
                        <input type="number" v-model.number="perubahan" min="0" class="input" />
                        <p v-if="errors.perubahan" class="text-sm text-red-600">{{ errors.perubahan }}</p>
                    </div>
                </div>

                <!-- Sumber Dana -->
                <div>
                    <label class="mb-1 block font-semibold">Sumber Dana</label>
                    <input type="text" v-model="sumberDana" class="input" />
                    <p v-if="errors.sumberDana" class="text-sm text-red-600">{{ errors.sumberDana }}</p>
                </div>

                <!-- Target Triwulan -->
                <div>
                    <h3 class="mb-2 font-semibold">Target Triwulan</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-3 py-1">Triwulan</th>
                                <th class="border border-gray-300 px-3 py-1">Kinerja Fisik (%)</th>
                                <th class="border border-gray-300 px-3 py-1">Keuangan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(t, idx) in targetTriwulan" :key="idx">
                                <td class="border border-gray-300 px-3 py-1">Triwulan {{ idx + 1 }}</td>
                                <td class="border border-gray-300 px-3 py-1">
                                    <input type="number" min="0" max="100" v-model.number="t.kinerjaFisik" class="w-full rounded border px-2 py-1" />
                                    <p v-if="errors[`kinerjaFisik_${idx}`]" class="text-xs text-red-600">
                                        {{ errors[`kinerjaFisik_${idx}`] }}
                                    </p>
                                </td>
                                <td class="border border-gray-300 px-3 py-1">
                                    <input type="number" min="0" v-model.number="t.keuangan" class="w-full rounded border px-2 py-1" />
                                    <p v-if="errors[`keuangan_${idx}`]" class="text-xs text-red-600">
                                        {{ errors[`keuangan_${idx}`] }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="rounded bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700">Simpan Data</button>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.input {
    width: 100%;
    border: 1px solid #cbd5e0;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
}
.input:focus {
    outline: none;
    border-color: #3182ce;
    box-shadow: 0 0 0 1px #3182ce;
}
</style>
