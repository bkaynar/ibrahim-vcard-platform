<template>
    <AppLayout title="Sistem Ayarları">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Sistem Ayarları</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Site ayarlarını, SMTP yapılandırmasını ve
                    entegrasyonları yönetin</p>
            </div>

            <form @submit.prevent="updateSettings" class="space-y-8">
                <!-- Site Ayarları -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <Globe class="w-5 h-5 mr-2 text-blue-500" />
                            Site Ayarları
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Site Başlığı
                                </label>
                                <input v-model="form.site_title" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    placeholder="VCard Platform" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Dil
                                </label>
                                <select v-model="form.language"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="tr">Türkçe</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Site Açıklaması
                            </label>
                            <textarea v-model="form.site_description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="Site açıklamasını buraya yazın..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                SEO Anahtar Kelimeleri
                            </label>
                            <input v-model="form.site_keywords" type="text"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="vcard, dijital kartvizit, qr kod (virgülle ayırın)" />
                        </div>
                    </div>
                </div>

                <!-- SMTP Ayarları -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <Mail class="w-5 h-5 mr-2 text-green-500" />
                            SMTP Ayarları
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">SMTP'yi Etkinleştir</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">E-posta gönderimi için SMTP kullanın
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input v-model="form.smtp_enabled" type="checkbox" class="sr-only peer" />
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <div v-if="form.smtp_enabled" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        SMTP Host
                                    </label>
                                    <input v-model="form.smtp_host" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="smtp.gmail.com" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        SMTP Port
                                    </label>
                                    <input v-model="form.smtp_port" type="number"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="587" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Kullanıcı Adı
                                    </label>
                                    <input v-model="form.smtp_username" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="your-email@gmail.com" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Şifre
                                    </label>
                                    <input v-model="form.smtp_password" type="password"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="••••••••" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Şifreleme
                                    </label>
                                    <select v-model="form.smtp_encryption"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">Yok</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Gönderen E-posta
                                    </label>
                                    <input v-model="form.smtp_from_address" type="email"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="noreply@example.com" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Gönderen İsim
                                    </label>
                                    <input v-model="form.smtp_from_name" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="VCard Platform" />
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button @click="testSmtp" type="button" :disabled="loading.smtp"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                                    <Send class="w-4 h-4 mr-2" />
                                    <span v-if="loading.smtp">Test Ediliyor...</span>
                                    <span v-else>SMTP Test Et</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shopify Entegrasyonu -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <ShoppingBag class="w-5 h-5 mr-2 text-purple-500" />
                            Shopify Entegrasyonu
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Shopify'ı Etkinleştir</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Shopify mağazası ile entegrasyon</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input v-model="form.shopify_enabled" type="checkbox" class="sr-only peer" />
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <div v-if="form.shopify_enabled" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mağaza URL'si
                                    </label>
                                    <input v-model="form.shopify_store_url" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="your-store.myshopify.com" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Access Token
                                    </label>
                                    <input v-model="form.shopify_access_token" type="password"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        placeholder="••••••••" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Shopify Anahtar Kelimeleri
                                </label>
                                <textarea v-model="form.shopify_keywords" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    placeholder="ürün anahtar kelimelerini buraya yazın (her satıra bir tane)" />
                            </div>

                            <div class="flex justify-end">
                                <button @click="testShopify" type="button" :disabled="loading.shopify"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                                    <ShoppingBag class="w-4 h-4 mr-2" />
                                    <span v-if="loading.shopify">Test Ediliyor...</span>
                                    <span v-else>Shopify Test Et</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cache Temizleme -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <Settings class="w-5 h-5 mr-2 text-red-500" />
                            Sistem Yönetimi
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cache Temizle</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sistem önbelleğini temizle</p>
                            </div>
                            <button @click="clearCache" type="button" :disabled="loading.cache"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                                <RefreshCw class="w-4 h-4 mr-2" />
                                <span v-if="loading.cache">Temizleniyor...</span>
                                <span v-else>Cache Temizle</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="flex justify-end space-x-4">
                    <button @click="$inertia.visit(route('admin.dashboard'))" type="button"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                        İptal
                    </button>
                    <button type="submit" :disabled="loading.form"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                        <span v-if="loading.form">Kaydediliyor...</span>
                        <span v-else>Ayarları Kaydet</span>
                    </button>
                </div>
            </form>
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

<script>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import {
    Globe,
    Mail,
    ShoppingBag,
    Settings,
    Send,
    RefreshCw
} from 'lucide-vue-next'

export default {
    name: 'AdminSettingsIndex',
    components: {
        AppLayout,
        Globe,
        Mail,
        ShoppingBag,
        Settings,
        Send,
        RefreshCw
    },
    props: {
        settings: {
            type: Object,
            default: () => ({})
        }
    },
    setup(props) {

        // Reactive form data
        const form = reactive({
            // Site Ayarları
            site_title: props.settings?.site_title || 'VCard Platform',
            site_description: props.settings?.site_description || '',
            site_keywords: props.settings?.site_keywords || '',
            language: props.settings?.language || 'tr',

            // SMTP Ayarları
            smtp_enabled: props.settings?.smtp_enabled || false,
            smtp_host: props.settings?.smtp_host || '',
            smtp_port: props.settings?.smtp_port || 587,
            smtp_username: props.settings?.smtp_username || '',
            smtp_password: props.settings?.smtp_password || '',
            smtp_encryption: props.settings?.smtp_encryption || 'tls',
            smtp_from_address: props.settings?.smtp_from_address || '',
            smtp_from_name: props.settings?.smtp_from_name || '',

            // Shopify Ayarları
            shopify_enabled: props.settings?.shopify_enabled || false,
            shopify_store_url: props.settings?.shopify_store_url || '',
            shopify_access_token: props.settings?.shopify_access_token || '',
            shopify_keywords: props.settings?.shopify_keywords || ''
        })

        // Loading states
        const loading = reactive({
            form: false,
            smtp: false,
            shopify: false,
            cache: false
        })

        // Toast states
        const showSuccess = ref(false)
        const showError = ref(false)
        const errorMessage = ref('')

        // Methods
        const updateSettings = async () => {
            loading.form = true

            try {
                await router.post(route('admin.settings.update'), form, {
                    preserveScroll: true,
                    onSuccess: () => {
                        showSuccessToast()
                    },
                    onError: (errors) => {
                        showErrorToast('Ayarlar kaydedilirken bir hata oluştu')
                        console.error('Form errors:', errors)
                    }
                })
            } finally {
                loading.form = false
            }
        }

        const testSmtp = async () => {
            if (!form.smtp_enabled || !form.smtp_host) {
                showErrorToast('SMTP ayarlarını önce tamamlayın')
                return
            }

            loading.smtp = true

            try {
                await router.post(route('admin.settings.test-smtp'), {
                    smtp_host: form.smtp_host,
                    smtp_port: form.smtp_port,
                    smtp_username: form.smtp_username,
                    smtp_password: form.smtp_password,
                    smtp_encryption: form.smtp_encryption,
                    smtp_from_address: form.smtp_from_address,
                    smtp_from_name: form.smtp_from_name
                }, {
                    preserveScroll: true,
                    onSuccess: () => {
                        showSuccessToast()
                    },
                    onError: () => {
                        showErrorToast('SMTP test başarısız oldu')
                    }
                })
            } finally {
                loading.smtp = false
            }
        }

        const testShopify = async () => {
            if (!form.shopify_enabled || !form.shopify_store_url) {
                showErrorToast('Shopify ayarlarını önce tamamlayın')
                return
            }

            loading.shopify = true

            try {
                await router.post(route('admin.settings.test-shopify'), {
                    shopify_store_url: form.shopify_store_url,
                    shopify_access_token: form.shopify_access_token
                }, {
                    preserveScroll: true,
                    onSuccess: () => {
                        showSuccessToast()
                    },
                    onError: () => {
                        showErrorToast('Shopify bağlantısı başarısız oldu')
                    }
                })
            } finally {
                loading.shopify = false
            }
        }

        const clearCache = async () => {
            loading.cache = true

            try {
                await router.post(route('admin.settings.clear-cache'), {}, {
                    preserveScroll: true,
                    onSuccess: () => {
                        showSuccessToast()
                    },
                    onError: () => {
                        showErrorToast('Cache temizlenirken hata oluştu')
                    }
                })
            } finally {
                loading.cache = false
            }
        }

        const showSuccessToast = () => {
            showSuccess.value = true
            setTimeout(() => {
                showSuccess.value = false
            }, 3000)
        }

        const showErrorToast = (message) => {
            errorMessage.value = message
            showError.value = true
            setTimeout(() => {
                showError.value = false
            }, 3000)
        }

        return {
            form,
            loading,
            showSuccess,
            showError,
            errorMessage,
            updateSettings,
            testSmtp,
            testShopify,
            clearCache,
            showSuccessToast,
            showErrorToast
        }

    }
}
</script>
