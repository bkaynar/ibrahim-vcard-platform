<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { BarChart3, Clock, Eye, TrendingUp, Users, Calendar, Globe } from 'lucide-vue-next';
import SimpleChart from '@/components/SimpleChart.vue';

// Props
const props = defineProps<{
    stats: {
        total_visits: number;
        today_visits: number;
        weekly_visits: number;
        monthly_visits: number;
    };
    charts: {
        last_30_days_visits: Array<{ date: string; day: string; count: number }>;
        hourly_visits: Array<{ hour: number; label: string; count: number }>;
        popular_hours: Array<{ hour: string; count: number }>;
    };
    all_visits: {
        data: Array<{
            id: number;
            ip_address: string;
            visited_at: string;
            user_agent: string;
            location: string;
        }>;
        links?: any;
        meta?: {
            last_page?: number;
            current_page?: number;
            total?: number;
        };
    };
    profile_url: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kullanıcı Paneli',
        href: route('dashboard'),
    },
    {
        title: 'VCard İstatistikleri',
        href: route('user.statistics'),
    },
];

// Chart data preparation
const chartData30Days = props.charts.last_30_days_visits.map(item => ({
    date: item.date,
    label: item.day,
    value: item.count
}));

const chartDataHourly = props.charts.hourly_visits.map(item => ({
    date: item.hour.toString(),
    label: item.label,
    value: item.count
}));
</script>

<template>

    <Head title="VCard İstatistikleri" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        VCard İstatistikleri
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        VCard profilinizin detaylı ziyaret analizi
                    </p>
                </div>
                <a :href="profile_url" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <Globe class="w-4 h-4 mr-2" />
                    VCard'ımı Görüntüle
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Toplam Ziyaret -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Toplam Ziyaret
                            </p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ props.stats.total_visits.toLocaleString() }}
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                            <Eye class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <!-- Bugün -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Bugün
                            </p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ props.stats.today_visits }}
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                            <Calendar class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </div>

                <!-- Bu Hafta -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Bu Hafta
                            </p>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                {{ props.stats.weekly_visits }}
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                            <TrendingUp class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>

                <!-- Bu Ay -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Bu Ay
                            </p>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                                {{ props.stats.monthly_visits }}
                            </p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                            <BarChart3 class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="flex flex-col gap-6">
                <!-- Son 30 Gün Grafiği -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <TrendingUp class="w-5 h-5 mr-2" />
                        Son 30 Gün Ziyaret Trendi
                    </h3>
                    <SimpleChart :data="chartData30Days" type="line" color="#3B82F6" :height="250" />
                </div>
                <!-- Saatlik Ziyaret Dağılımı -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <Clock class="w-5 h-5 mr-2" />
                        Bugün Saatlik Ziyaret Dağılımı
                    </h3>
                    <SimpleChart :data="chartDataHourly" type="bar" color="#10B981" :height="250" />
                </div>
            </div>

            <!-- Popular Hours & Recent Visits -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- En Popüler Saatler -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <Clock class="w-5 h-5 mr-2" />
                        En Popüler Saatler
                    </h3>
                    <div class="space-y-3">
                        <div v-for="hour in props.charts.popular_hours" :key="hour.hour"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="font-medium text-gray-900 dark:text-white">{{ hour.hour }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ hour.count }} ziyaret</span>
                        </div>
                        <div v-if="props.charts.popular_hours.length === 0" class="text-center py-4 text-gray-500">
                            Henüz veri yok
                        </div>
                    </div>
                </div>

                <!-- Tüm Ziyaretler Tablosu -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <Users class="w-5 h-5 mr-2" />
                        Son Ziyaretler
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left p-3 font-medium text-gray-900 dark:text-white">IP Adresi</th>
                                    <th class="text-left p-3 font-medium text-gray-900 dark:text-white">Tarih</th>
                                    <th class="text-left p-3 font-medium text-gray-900 dark:text-white">Tarayıcı</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="visit in (props.all_visits?.data || [])" :key="visit.id"
                                    class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="p-3 text-gray-900 dark:text-white font-mono">{{ visit.ip_address }}</td>
                                    <td class="p-3 text-gray-600 dark:text-gray-400">{{ visit.visited_at }}</td>
                                    <td class="p-3 text-gray-600 dark:text-gray-400 truncate max-w-xs">
                                        {{ visit.user_agent }}
                                    </td>
                                </tr>
                                <tr v-if="!props.all_visits?.data || props.all_visits.data.length === 0">
                                    <td colspan="3" class="p-6 text-center text-gray-500">
                                        Henüz ziyaret kaydı yok
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (if needed) -->
                    <div v-if="props.all_visits?.meta?.last_page && props.all_visits.meta.last_page > 1"
                        class="mt-4 flex justify-center">
                        <div class="flex space-x-2">
                            <!-- Pagination links would go here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
