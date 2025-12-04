<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Eye, Users, Calendar, TrendingUp, ExternalLink, Clock } from 'lucide-vue-next';
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
        last_7_days_visits: Array<{ date: string, count: number }>;
    };
    recent_visits: Array<{
        ip_address: string;
        visited_at: string;
        user_agent: string;
    }>;
    profile_url: string;
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kullanıcı Paneli',
        href: '/dashboard',
    },
];
</script>

<template>

    <Head title="Kullanıcı Paneli" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Hoş geldin mesajı -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white">
                <h1 class="text-2xl font-bold mb-2">Hoş geldiniz! 👋</h1>
                <p class="opacity-90">VCard istatistiklerinizi ve ziyaretçi verilerinizi buradan takip edebilirsiniz.
                </p>
            </div>

            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <Eye class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Ziyaret</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats.total_visits }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <Calendar class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bugünkü Ziyaret</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats.today_visits }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <TrendingUp class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Haftalık Ziyaret</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats.weekly_visits }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <Users class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Aylık Ziyaret</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats.monthly_visits }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hızlı Eylemler -->
            <div class="flex flex-wrap gap-4">
                <a :href="route('user.profile.edit')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <Users class="w-4 h-4 mr-2" />
                    Profilimi Düzenle
                </a>
                <a :href="props.profile_url" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <ExternalLink class="w-4 h-4 mr-2" />
                    VCard'ımı Görüntüle
                </a>
            </div>

            <!-- İki Kolon: Grafik ve Son Ziyaretler -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Son 7 Günlük Ziyaret Grafiği -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Son 7 Günlük Ziyaretler</h2>
                    <SimpleChart :data="props.charts.last_7_days_visits || []" title="" :chart-width="400"
                        :chart-height="200" bar-color="#3B82F6" date-format="day-month"
                        empty-message="Son 7 günde ziyaret bulunmuyor">
                        <template #empty-icon>
                            <Eye class="w-12 h-12 mx-auto mb-3 opacity-50 text-gray-400 dark:text-gray-500" />
                        </template>
                    </SimpleChart>
                </div>

                <!-- Son Ziyaretler -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Son Ziyaretler</h2>
                    <div class="space-y-3">
                        <div v-if="props.recent_visits && props.recent_visits.length > 0">
                            <div v-for="(visit, index) in props.recent_visits" :key="index"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                                        <Eye class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ visit.ip_address }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ visit.user_agent }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <Clock class="w-3 h-3 mr-1" />
                                        {{ visit.visited_at }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <Eye class="w-12 h-12 mx-auto mb-3 opacity-50 text-gray-400 dark:text-gray-500" />
                            <p class="text-gray-500 dark:text-gray-400">Henüz ziyaretçi bulunmuyor</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">VCard'ınızı paylaşarak ziyaretçi
                                kazanın</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
