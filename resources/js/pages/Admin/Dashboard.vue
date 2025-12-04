<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, reactive } from 'vue';
import { Users, Eye, UserPlus, Settings, RefreshCw, Database, Mail, HardDrive } from 'lucide-vue-next';
import SimpleChart from '@/components/SimpleChart.vue';

// Props
const props = defineProps<{
    stats: {
        total_users: number;
        total_vcard_visits: number;
        monthly_new_users: number;
    };
    charts: {
        last_7_days_visits: Array<{ date: string, count: number }>;
        last_30_days_visits: Array<{ date: string, count: number }>;
    };
    recent_users: Array<{
        id: number;
        name: string;
        email: string;
        created_at: string;
        role: string;
    }>;
    top_vcards: Array<{
        user_name: string;
        user_email: string;
        visit_count: number;
    }>;
    system_status: {
        cache_status: string;
        smtp_status: string;
        disk_usage: {
            total: string;
            used: string;
            free: string;
            usage_percent: number;
        } | null;
        last_backup: string;
    };
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Yönetim Paneli',
        href: '/admin/dashboard',
    },
];

// Loading states
const loading = reactive({
    clearCache: false
});

// Toast states
const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');

// Chart data
const activeChart = ref<'7days' | '30days'>('7days');

// Methods
const clearCache = async () => {
    loading.clearCache = true;

    try {
        await router.post(route('admin.dashboard.clear-cache'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                showSuccessToast();
            },
            onError: () => {
                showErrorToast('Cache temizlenirken hata oluştu');
            }
        });
    } finally {
        loading.clearCache = false;
    }
};

const showSuccessToast = () => {
    showSuccess.value = true;
    setTimeout(() => {
        showSuccess.value = false;
    }, 3000);
};

const showErrorToast = (message: string) => {
    errorMessage.value = message;
    showError.value = true;
    setTimeout(() => {
        showError.value = false;
    }, 3000);
};
</script>

<template>

    <Head title="Yönetim Paneli" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Hızlı Eylemler -->
            <div class="flex flex-wrap gap-4 mb-6">
                <button @click="$inertia.visit(route('admin.users.create'))"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <UserPlus class="w-4 h-4 mr-2" />
                    Yeni Kullanıcı Ekle
                </button>
                <button @click="$inertia.visit(route('admin.settings.index'))"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                    <Settings class="w-4 h-4 mr-2" />
                    Sistem Ayarları
                </button>
                <button @click="clearCache" :disabled="loading.clearCache"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                    <RefreshCw class="w-4 h-4 mr-2" :class="{ 'animate-spin': loading.clearCache }" />
                    <span v-if="loading.clearCache">Temizleniyor...</span>
                    <span v-else>Cache Temizle</span>
                </button>
            </div>

            <!-- Ana İstatistik Kartları -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <Users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Kullanıcı</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats.total_users }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <Eye class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">VCard Ziyaretleri</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{
                                props.stats.total_vcard_visits }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <UserPlus class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bu Ayki Yeni Kayıtlar</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats.monthly_new_users
                            }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistem Durumu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Database class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cache</span>
                        </div>
                        <span :class="{
                            'text-green-600 bg-green-100': props.system_status.cache_status === 'active',
                            'text-red-600 bg-red-100': props.system_status.cache_status === 'error',
                            'text-gray-600 bg-gray-100': props.system_status.cache_status === 'inactive'
                        }" class="px-2 py-1 text-xs font-medium rounded-full">
                            {{ props.system_status.cache_status === 'active' ? 'Aktif' :
                                props.system_status.cache_status === 'error' ? 'Hata' : 'Pasif' }}
                        </span>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Mail class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">SMTP</span>
                        </div>
                        <span :class="{
                            'text-green-600 bg-green-100': props.system_status.smtp_status === 'active',
                            'text-red-600 bg-red-100': props.system_status.smtp_status === 'error',
                            'text-gray-600 bg-gray-100': props.system_status.smtp_status === 'inactive'
                        }" class="px-2 py-1 text-xs font-medium rounded-full">
                            {{ props.system_status.smtp_status === 'active' ? 'Aktif' :
                                props.system_status.smtp_status === 'error' ? 'Hata' : 'Pasif' }}
                        </span>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <HardDrive class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Disk</span>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">
                            {{ props.system_status.disk_usage?.usage_percent || 0 }}%
                        </span>
                    </div>
                    <div v-if="props.system_status.disk_usage" class="mt-2">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <span>{{ props.system_status.disk_usage.used }}</span>
                            <span>{{ props.system_status.disk_usage.total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                :style="`width: ${props.system_status.disk_usage.usage_percent}%`"></div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Son Backup</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ props.system_status.last_backup }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ziyaret Grafiği -->
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">VCard Ziyaret İstatistikleri</h2>
                    <div class="flex gap-2">
                        <button @click="activeChart = '7days'"
                            :class="activeChart === '7days' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            class="px-3 py-1 text-sm rounded-md transition-colors">
                            Son 7 Gün
                        </button>
                        <button @click="activeChart = '30days'"
                            :class="activeChart === '30days' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            class="px-3 py-1 text-sm rounded-md transition-colors">
                            Son 30 Gün
                        </button>
                    </div>
                </div>

                <!-- XY Düzleminde Grafik -->
                <div class="w-full overflow-x-auto">
                    <SimpleChart v-if="activeChart === '7days'" :data="props.charts.last_7_days_visits || []" title=""
                        :chart-width="800" :chart-height="300" bar-color="#3B82F6" line-color="#10B981"
                        :show-line="false" date-format="day-month"
                        empty-message="Son 7 günde ziyaret verisi bulunmuyor">
                        <template #empty-icon>
                            <Eye class="w-12 h-12 mx-auto mb-3 opacity-50 text-gray-400 dark:text-gray-500" />
                        </template>
                    </SimpleChart>

                    <SimpleChart v-else :data="props.charts.last_30_days_visits || []" title="" :chart-width="800"
                        :chart-height="300" bar-color="#10B981" line-color="#3B82F6" :show-line="true"
                        date-format="short" :show-x-labels="false"
                        empty-message="Son 30 günde ziyaret verisi bulunmuyor">
                        <template #empty-icon>
                            <Eye class="w-12 h-12 mx-auto mb-3 opacity-50 text-gray-400 dark:text-gray-500" />
                        </template>
                    </SimpleChart>
                </div>
            </div>

            <!-- İki Kolon: Son Kullanıcılar ve En Çok Ziyaret Edilen VCard'lar -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Son Kayıt Olan Kullanıcılar -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Son Kayıt Olan Kullanıcılar
                    </h2>
                    <div class="space-y-3">
                        <div v-for="user in props.recent_users" :key="user.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.created_at }}</div>
                                <span :class="{
                                    'bg-red-100 text-red-800': user.role === 'admin',
                                    'bg-blue-100 text-blue-800': user.role === 'user'
                                }" class="inline-block px-2 py-1 text-xs font-medium rounded-full mt-1">
                                    {{ user.role === 'admin' ? 'Admin' : 'Kullanıcı' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- En Çok Ziyaret Edilen VCard'lar -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">En Çok Ziyaret Edilen VCard'lar
                    </h2>
                    <div class="space-y-3">
                        <div v-for="(vcard, index) in props.top_vcards" :key="index"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    {{ index + 1 }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ vcard.user_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ vcard.user_email }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">{{ vcard.visit_count }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">ziyaret</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Toast -->
        <div v-if="showSuccess"
            class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300">
            <p class="font-medium">✓ İşlem başarıyla tamamlandı!</p>
        </div>

        <!-- Error Toast -->
        <div v-if="showError"
            class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300">
            <p class="font-medium">✗ {{ errorMessage }}</p>
        </div>
    </AppLayout>
</template>
