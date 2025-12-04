<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

interface Bank {
    id: number;
    name: string;
    code: string;
    color: string;
    logo: string | null;
    logo_url: string | null;
    is_active: boolean;
}

interface Props {
    bank: Bank;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.bank.name,
    code: props.bank.code,
    color: props.bank.color,
    logo: null as File | null,
    is_active: props.bank.is_active,
    _method: 'PUT',
});

const submit = () => {
    form.post(route('admin.banks.update', props.bank.id), {
        forceFormData: true,
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Yönetim Paneli',
        href: route('admin.dashboard'),
    },
    {
        title: 'Bankalar',
        href: route('admin.banks.index'),
    },
    {
        title: 'Banka Düzenle',
        href: route('admin.banks.edit', props.bank.id),
    },
];
</script>

<template>
    <Head title="Banka Düzenle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold mb-6">Banka Düzenle</h2>

                    <!-- Mevcut Logo -->
                    <div v-if="bank.logo_url" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mevcut Logo
                        </label>
                        <img :src="bank.logo_url" :alt="bank.name" class="w-24 h-24 object-contain rounded-lg border border-gray-300">
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Banka Adı -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Banka Adı *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                placeholder="Örn: Ziraat Bankası"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Banka Kodu -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Banka Kodu *
                            </label>
                            <input
                                id="code"
                                v-model="form.code"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                placeholder="Örn: ziraat"
                            />
                            <p class="mt-1 text-xs text-gray-500">Küçük harf, rakam ve tire kullanın</p>
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                        </div>

                        <!-- Renk -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Banka Rengi *
                            </label>
                            <div class="flex gap-3 items-center">
                                <input
                                    id="color"
                                    v-model="form.color"
                                    type="color"
                                    class="h-10 w-20 rounded cursor-pointer"
                                />
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono"
                                    placeholder="#6B7280"
                                />
                            </div>
                            <p v-if="form.errors.color" class="mt-1 text-sm text-red-600">{{ form.errors.color }}</p>
                        </div>

                        <!-- Logo -->
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Yeni Logo
                            </label>
                            <input
                                id="logo"
                                type="file"
                                accept="image/*"
                                @change="(e) => form.logo = (e.target as HTMLInputElement).files?.[0] || null"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                            />
                            <p class="mt-1 text-xs text-gray-500">Değiştirmek istiyorsanız yeni logo seçin (PNG, JPG veya SVG, Max: 2MB)</p>
                            <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">{{ form.errors.logo }}</p>
                        </div>

                        <!-- Durum -->
                        <div class="flex items-center gap-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            />
                            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Aktif
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <Link
                                :href="route('admin.banks.index')"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                İptal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Güncelleniyor...</span>
                                <span v-else>Güncelle</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
