<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Upload, Download, AlertCircle, CheckCircle, FileSpreadsheet } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Yönetim Paneli',
        href: route('admin.dashboard'),
    },
    {
        title: 'Kullanıcılar',
        href: route('admin.users.index'),
    },
    {
        title: 'Excel İçe Aktar',
        href: route('admin.users.import.form'),
    },
];

const form = useForm({
    file: null as File | null,
});

const fileInput = ref<HTMLInputElement>();
const fileName = ref<string>('');

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.file = file;
        fileName.value = file.name;
    }
};

const selectFile = () => {
    fileInput.value?.click();
};

const submit = () => {
    form.post(route('admin.users.import'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            fileName.value = '';
        },
    });
};

const downloadTemplate = () => {
    window.location.href = route('admin.users.import.template');
};

// Flash mesajları
const successMessage = computed(() => page.props.flash?.success);
const errorMessage = computed(() => page.props.flash?.error);
const importErrors = computed(() => page.props.flash?.import_errors);
</script>

<template>
    <Head title="Excel İçe Aktar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-4xl mx-auto">
            <!-- Başlık -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Excel'den Kullanıcı İçe Aktar</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Excel dosyası yükleyerek toplu kullanıcı ekleyebilirsiniz
                </p>
            </div>

            <!-- Başarı Mesajı -->
            <div v-if="successMessage" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-start">
                <CheckCircle class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" />
                <div class="flex-1">
                    <p class="text-green-800 dark:text-green-200">{{ successMessage }}</p>
                </div>
            </div>

            <!-- Hata Mesajı -->
            <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start">
                <AlertCircle class="w-5 h-5 text-red-600 dark:text-red-400 mr-3 flex-shrink-0 mt-0.5" />
                <div class="flex-1">
                    <p class="text-red-800 dark:text-red-200">{{ errorMessage }}</p>
                </div>
            </div>

            <!-- Detaylı Hatalar -->
            <div v-if="importErrors && importErrors.length > 0" class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-start mb-3">
                    <AlertCircle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h3 class="text-yellow-800 dark:text-yellow-200 font-semibold">Aşağıdaki hatalar oluştu: ({{ importErrors.length }} hata)</h3>
                    </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <ul class="list-disc list-inside space-y-1 ml-8">
                        <li v-for="(error, index) in importErrors" :key="index" class="text-yellow-700 dark:text-yellow-300 text-sm">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Şablon İndirme -->
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <div class="flex items-start">
                    <FileSpreadsheet class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-4 flex-shrink-0 mt-1" />
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Excel Şablonu</h3>
                        <p class="text-blue-700 dark:text-blue-300 mb-4">
                            Önce örnek Excel şablonunu indirin ve sütun yapısına uygun şekilde kullanıcı bilgilerini girin.
                        </p>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Desteklenen Sütun İsimleri:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <div>
                                    <p class="font-semibold mb-1">Bizim Şablon:</p>
                                    <ul class="space-y-1">
                                        <li><span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">sicil_no</span></li>
                                        <li><span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">ad</span></li>
                                        <li><span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">soyad</span></li>
                                        <li><span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">telefon</span></li>
                                        <li><span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">eposta</span></li>
                                    </ul>
                                </div>
                                <div>
                                    <p class="font-semibold mb-1">Alternatif İsimler:</p>
                                    <ul class="space-y-1">
                                        <li><span class="font-mono bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded text-xs">sicil_no</span></li>
                                        <li><span class="font-mono bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded text-xs">adi</span></li>
                                        <li><span class="font-mono bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded text-xs">soyadi</span></li>
                                        <li><span class="font-mono bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded text-xs">cep_telefonu</span></li>
                                        <li><span class="font-mono bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded text-xs">e_posta</span></li>
                                    </ul>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                                <span class="text-red-600 dark:text-red-400">*</span> Her iki sütun ismi de desteklenir<br>
                                <span class="text-red-600 dark:text-red-400">*</span> Veriler 2. satırdan itibaren girilmeli<br>
                                * Telefon ve e-posta opsiyoneldir (her ikisi de boş olabilir)<br>
                                * Varsayılan şifre: Sicil numarası (yoksa "12345678")
                            </p>
                        </div>
                        <button @click="downloadTemplate" type="button"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            <Download class="w-4 h-4 mr-2" />
                            Şablonu İndir (.xlsx)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <form @submit.prevent="submit">
                        <!-- Dosya Seçme Alanı -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Excel Dosyası Seç
                            </label>
                            <div class="flex items-center gap-4">
                                <button @click="selectFile" type="button"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors border border-gray-300 dark:border-gray-600">
                                    <Upload class="w-4 h-4 mr-2" />
                                    Dosya Seç
                                </button>
                                <span v-if="fileName" class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ fileName }}
                                </span>
                                <span v-else class="text-sm text-gray-400 dark:text-gray-500">
                                    Dosya seçilmedi
                                </span>
                            </div>
                            <input ref="fileInput" type="file" class="hidden" accept=".xlsx,.xls,.csv"
                                @change="handleFileChange">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Desteklenen formatlar: .xlsx, .xls, .csv (Max: 10MB)
                            </p>
                            <div v-if="form.errors.file" class="text-sm text-red-600 mt-1">
                                {{ form.errors.file }}
                            </div>
                        </div>

                        <!-- Dosya Bilgisi -->
                        <div v-if="form.file" class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <FileSpreadsheet class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" />
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ form.file.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ (form.file.size / 1024 / 1024).toFixed(2) }} MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-3">
                            <a :href="route('admin.users.index')"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                İptal
                            </a>
                            <button type="submit" :disabled="!form.file || form.processing"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                                <Upload class="w-4 h-4 mr-2" />
                                <span v-if="form.processing">İçe Aktarılıyor...</span>
                                <span v-else>İçe Aktar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bilgilendirme -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Önemli Notlar:</h4>
                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                    <li>Telefon ve e-posta opsiyoneldir (her ikisi de boş olabilir)</li>
                    <li>Aynı telefon, e-posta veya sicil numarası olan kayıtlar atlanır</li>
                    <li>Kullanıcı adı otomatik olarak ad-soyad'dan oluşturulur</li>
                    <li>Tüm kullanıcılara otomatik olarak "user" rolü atanır</li>
                    <li>İçe aktarma işlemi sırasında hatalar kaydedilir ve gösterilir</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
