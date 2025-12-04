<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, watch } from 'vue';

interface Bank {
    id: number;
    name: string;
    code: string;
    color: string;
    logo: string | null;
    logo_url: string | null;
    is_active: boolean;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    banks: {
        data: Bank[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: PaginationLink[];
    };
    filters: {
        search: string | null;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.banks.index'), { search: value }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Yönetim Paneli',
        href: route('admin.dashboard'),
    },
    {
        title: 'Bankalar',
        href: route('admin.banks.index'),
    },
];
</script>

<template>
    <Head title="Bankalar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                <h1 class="text-2xl font-bold">Bankalar</h1>
                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Banka ara..."
                            class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                        />
                    </div>

                    <Link :href="route('admin.banks.create')"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center justify-center">
                        Yeni Banka
                    </Link>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Responsive table wrapper -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Logo</th>
                                <th scope="col" class="px-6 py-3">Banka Adı</th>
                                <th scope="col" class="px-6 py-3">Kod</th>
                                <th scope="col" class="px-6 py-3">Renk</th>
                                <th scope="col" class="px-6 py-3">Durum</th>
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">İşlemler</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bank in banks.data" :key="bank.id"
                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                <td class="px-6 py-4">
                                    <div v-if="bank.logo_url" class="w-12 h-12 rounded-lg overflow-hidden">
                                        <img :src="bank.logo_url" :alt="bank.name" class="w-full h-full object-contain">
                                    </div>
                                    <div v-else 
                                        :style="{ backgroundColor: bank.color }"
                                        class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                                        {{ bank.name.charAt(0) }}
                                    </div>
                                </td>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ bank.name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ bank.code }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div :style="{ backgroundColor: bank.color }" class="w-8 h-8 rounded border border-gray-300"></div>
                                        <span class="text-xs font-mono">{{ bank.color }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="bank.is_active"
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        Aktif
                                    </span>
                                    <span v-else
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        Pasif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('admin.banks.edit', bank.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-colors">
                                        Düzenle
                                        </Link>
                                        <Link :href="route('admin.banks.destroy', bank.id)" method="delete" as="button"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors">
                                        Sil
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="banks.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-400">
                            Toplam <span class="font-medium">{{ banks.total }}</span> bankadan
                            <span class="font-medium">{{ (banks.current_page - 1) * banks.per_page + 1 }}</span> -
                            <span class="font-medium">{{ Math.min(banks.current_page * banks.per_page, banks.total) }}</span>
                            arası gösteriliyor
                        </div>

                        <nav class="flex items-center gap-1">
                            <Link
                                v-for="(link, index) in banks.links"
                                :key="index"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                    link.active
                                        ? 'bg-blue-500 text-white'
                                        : link.url
                                            ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                            : 'bg-gray-50 text-gray-400 cursor-not-allowed dark:bg-gray-900 dark:text-gray-600'
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
