<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Camera, Upload, X, Save, User, Mail, Phone, MapPin, Plus, Share2, Building2, Landmark, FileText } from 'lucide-vue-next';
import { ref, computed } from 'vue';

// Props
const props = defineProps<{
    user: {
        id: number;
        name: string;
        username: string;
        registration_number?: string;
        email: string;
        phone?: string;
        address?: string;
        bio?: string;
        profile_photo?: string;
        cover_photo?: string;
        profile_photo_url: string;
        cover_photo_url: string;
        socials: {
            facebook?: string;
            twitter?: string;
            instagram?: string;
            linkedin?: string;
            website?: string;
        };
        company_info?: {
            company_name: string;
            company_title?: string;
            tax_office: string;
            tax_number: string;
            company_address: string;
        } | null;
        documents?: Array<{
            title: string;
            file_path?: string;
        }>;
    };
    banks: Array<{
        id: number;
        name: string;
        code: string;
        color: string;
        logo_url: string | null;
    }>;
    userBanks: Array<{
        bank_id: number;
        bank_name: string;
        bank_code: string;
        bank_color: string;
        bank_logo_url: string | null;
        iban: string | null;
        account_holder: string | null;
        branch: string | null;
        is_primary: boolean;
    }>;
}>()

// Sosyal medya platformları
const socialPlatforms = [
    { value: 'instagram', label: 'Instagram', icon: '📷' },
    { value: 'twitter', label: 'X (Twitter)', icon: '✖️' },
    { value: 'facebook', label: 'Facebook', icon: '📘' },
    { value: 'linkedin', label: 'LinkedIn', icon: '💼' },
    { value: 'youtube', label: 'YouTube', icon: '📺' },
    { value: 'tiktok', label: 'TikTok', icon: '🎵' },
    { value: 'snapchat', label: 'Snapchat', icon: '👻' },
    { value: 'pinterest', label: 'Pinterest', icon: '📌' },
    { value: 'github', label: 'GitHub', icon: '👨‍💻' },
    { value: 'behance', label: 'Behance', icon: '🎨' },
    { value: 'dribbble', label: 'Dribbble', icon: '🏀' },
    { value: 'twitch', label: 'Twitch', icon: '🎮' },
    { value: 'discord', label: 'Discord', icon: '🎯' },
    { value: 'telegram', label: 'Telegram', icon: '✈️' },
    { value: 'whatsapp', label: 'WhatsApp', icon: '💬' },
    { value: 'website', label: 'Website', icon: '🌐' },
];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kullanıcı Paneli',
        href: '/dashboard',
    },
    {
        title: 'Profil Düzenle',
        href: '/user/profile/edit',
    },
];

// Mevcut sosyal medya hesaplarını array formatına çevir
const existingSocials = computed(() => {
    const socials = [];
    if (props.user.socials) {
        for (const [platform, username] of Object.entries(props.user.socials)) {
            if (username && username.trim() !== '') {
                socials.push({ platform, username });
            }
        }
    }
    return socials;
});

// Form
const form = useForm({
    name: props.user.name,
    username: props.user.username,
    registration_number: props.user.registration_number || '',
    email: props.user.email,
    phone: props.user.phone || '',
    address: props.user.address || '',
    bio: props.user.bio || '',
    profile_photo: null as File | null,
    cover_photo: null as File | null,
    socials: existingSocials.value,
    company_info: props.user.company_info || { company_name: '', company_title: '', tax_office: '', tax_number: '', company_address: '' },
    bank_accounts: props.userBanks || [],
    documents: props.user.documents || [],
});

// Refs for file inputs
const profilePhotoInput = ref<HTMLInputElement>();
const coverPhotoInput = ref<HTMLInputElement>();

// Preview URLs
const profilePhotoPreview = ref<string | null>(null);
const coverPhotoPreview = ref<string | null>(null);

// Sosyal medya yönetimi
const addSocialMedia = () => {
    form.socials.push({ platform: '', username: '' });
};

const removeSocialMedia = (index: number) => {
    form.socials.splice(index, 1);
};

const updateSocialPlatform = (index: number, platform: string) => {
    form.socials[index].platform = platform;
};

const updateSocialUsername = (index: number, username: string) => {
    form.socials[index].username = username;
};

const getSocialIcon = (platform: string) => {
    const found = socialPlatforms.find(p => p.value === platform);
    return found ? found.icon : '🌐';
};

const getSocialLabel = (platform: string) => {
    const found = socialPlatforms.find(p => p.value === platform);
    return found ? found.label : platform;
};

// Banka bilgisi yönetimi
const addBankAccount = () => {
    form.bank_accounts.push({
        bank_id: 0,
        bank_name: '',
        bank_code: '',
        bank_color: '',
        bank_logo_url: null,
        iban: '',
        account_holder: '',
        branch: '',
        is_primary: false
    });
};

const removeBankAccount = (index: number) => {
    form.bank_accounts.splice(index, 1);
};

const updateBankSelection = (index: number, bankId: number) => {
    const selectedBank = props.banks.find(b => b.id === bankId);
    if (selectedBank) {
        form.bank_accounts[index].bank_id = selectedBank.id;
        form.bank_accounts[index].bank_name = selectedBank.name;
        form.bank_accounts[index].bank_code = selectedBank.code;
        form.bank_accounts[index].bank_color = selectedBank.color;
        form.bank_accounts[index].bank_logo_url = selectedBank.logo_url;
    }
};

// Doküman yönetimi
const addDocument = () => {
    form.documents.push({ title: '', file_path: '' });
};

const removeDocument = (index: number) => {
    form.documents.splice(index, 1);
};

const handleDocumentChange = (event: Event, index: number) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        // Dosyayı form.documents[index] içinde sakla
        (form.documents[index] as any).file = file;
    }
};

// Methods
const selectProfilePhoto = () => {
    profilePhotoInput.value?.click();
};

const selectCoverPhoto = () => {
    coverPhotoInput.value?.click();
};

const handleProfilePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.profile_photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePhotoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const handleCoverPhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.cover_photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            coverPhotoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeProfilePhoto = () => {
    form.profile_photo = null;
    profilePhotoPreview.value = null;
    if (profilePhotoInput.value) {
        profilePhotoInput.value.value = '';
    }
};

const removeCoverPhoto = () => {
    form.cover_photo = null;
    coverPhotoPreview.value = null;
    if (coverPhotoInput.value) {
        coverPhotoInput.value.value = '';
    }
};

// Computed values for phone preview
const previewProfilePhoto = computed(() => {
    return profilePhotoPreview.value || props.user.profile_photo_url;
});

const previewName = computed(() => {
    return form.name || props.user.name;
});

const previewTitle = computed(() => {
    return form.bio || props.user.bio || 'Kullanıcı';
});

const previewSocials = computed(() => {
    return form.socials.filter(social => social.platform && social.username);
});

const getSocialColor = (platform: string) => {
    const colors: { [key: string]: string } = {
        'instagram': 'bg-gradient-to-r from-purple-500 to-pink-500',
        'twitter': 'bg-blue-500',
        'facebook': 'bg-blue-600',
        'linkedin': 'bg-blue-700',
        'youtube': 'bg-red-600',
        'tiktok': 'bg-black',
        'snapchat': 'bg-yellow-400',
        'pinterest': 'bg-red-500',
        'github': 'bg-gray-800',
        'behance': 'bg-blue-500',
        'dribbble': 'bg-pink-500',
        'twitch': 'bg-purple-600',
        'discord': 'bg-indigo-600',
        'telegram': 'bg-blue-500',
        'whatsapp': 'bg-green-500',
        'website': 'bg-gray-600',
    };
    return colors[platform] || 'bg-gray-500';
};

const submit = () => {
    form.post(route('user.profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Profil Düzenle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl mx-auto p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Form Section - Sol Taraf -->
                <div class="lg:col-span-7">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Cover Photo Section -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="relative h-48 bg-gradient-to-r from-blue-500 to-purple-600">
                                <img v-if="coverPhotoPreview || props.user.cover_photo_url"
                                    :src="coverPhotoPreview || props.user.cover_photo_url"
                                    class="w-full h-full object-cover" alt="Kapak fotoğrafı">

                                <!-- Cover Photo Controls -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <div class="flex space-x-3">
                                        <button type="button" @click="selectCoverPhoto"
                                            class="px-4 py-2 bg-white bg-opacity-90 text-gray-800 rounded-lg hover:bg-opacity-100 transition-all flex items-center space-x-2">
                                            <Upload class="w-4 h-4" />
                                            <span>Kapak Değiştir</span>
                                        </button>
                                        <button v-if="coverPhotoPreview" type="button" @click="removeCoverPhoto"
                                            class="px-4 py-2 bg-red-500 bg-opacity-90 text-white rounded-lg hover:bg-opacity-100 transition-all flex items-center space-x-2">
                                            <X class="w-4 h-4" />
                                            <span>Kaldır</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Photo Section -->
                            <div class="relative px-8 pb-8">
                                <div class="flex justify-center -mt-16 mb-6">
                                    <div class="relative">
                                        <div
                                            class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden bg-gray-200 dark:bg-gray-700">
                                            <img :src="profilePhotoPreview || props.user.profile_photo_url"
                                                :alt="props.user.name" class="w-full h-full object-cover">
                                        </div>

                                        <!-- Profile Photo Controls -->
                                        <div
                                            class="absolute inset-0 rounded-full bg-black bg-opacity-30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                            <div class="flex space-x-2">
                                                <button type="button" @click="selectProfilePhoto"
                                                    class="p-2 bg-white bg-opacity-90 text-gray-800 rounded-full hover:bg-opacity-100 transition-all">
                                                    <Camera class="w-4 h-4" />
                                                </button>
                                                <button v-if="profilePhotoPreview" type="button"
                                                    @click="removeProfilePhoto"
                                                    class="p-2 bg-red-500 bg-opacity-90 text-white rounded-full hover:bg-opacity-100 transition-all">
                                                    <X class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                                <User class="w-5 h-5 mr-2" />
                                Kişisel Bilgiler
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Ad Soyad *
                                    </label>
                                    <input v-model="form.name" type="text" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name
                                        }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Kullanıcı Adı *
                                    </label>
                                    <input v-model="form.username" type="text" required disabled
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Kullanıcı adı değiştirilemez
                                    </p>
                                    <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{
                                        form.errors.username
                                        }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Sicil Numarası
                                    </label>
                                    <input v-model="form.registration_number" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                        placeholder="Örn: 12345">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Profil URL'iniz: /{{ form.registration_number || 'sicil-numarasi' }}
                                    </p>
                                    <div v-if="form.errors.registration_number" class="text-red-500 text-sm mt-1">{{
                                        form.errors.registration_number
                                        }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <Mail class="w-4 h-4 inline mr-1" />
                                        E-posta *
                                    </label>
                                    <input v-model="form.email" type="email" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email
                                        }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <Phone class="w-4 h-4 inline mr-1" />
                                        Telefon
                                    </label>
                                    <input v-model="form.phone" type="tel"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <MapPin class="w-4 h-4 inline mr-1" />
                                    Adres
                                </label>
                                <input v-model="form.address" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address
                                    }}
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Hakkımda
                                </label>
                                <textarea v-model="form.bio" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                    placeholder="Kendiniz hakkında kısa bir açıklama yazın..."></textarea>
                                <div v-if="form.errors.bio" class="text-red-500 text-sm mt-1">{{ form.errors.bio }}
                                </div>
                            </div>
                        </div>

                        <!-- Sosyal Medya -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                    <Share2 class="h-5 w-5 mr-2 text-purple-600 dark:text-purple-400" />
                                    Sosyal Medya Hesapları
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Sosyal medya platformlarınızı ekleyin ve yönetin
                                </p>
                            </div>
                            <div class="p-6">
                                <!-- Mevcut Sosyal Medya Hesapları -->
                                <div v-if="form.socials.length > 0" class="space-y-4 mb-6">
                                    <div v-for="(social, index) in form.socials" :key="index"
                                        class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <!-- Mobil ve masaüstü responsive layout -->
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                            <!-- Platform Seçimi -->
                                            <div class="w-full sm:w-auto sm:flex-shrink-0">
                                                <label
                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 sm:hidden">
                                                    Platform
                                                </label>
                                                <select :value="social.platform"
                                                    @change="updateSocialPlatform(index, ($event.target as HTMLSelectElement).value)"
                                                    class="block w-full sm:w-48 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                                    <option value="">Platform Seçin</option>
                                                    <option v-for="platform in socialPlatforms" :key="platform.value"
                                                        :value="platform.value">
                                                        {{ platform.icon }} {{ platform.label }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Kullanıcı Adı -->
                                            <div class="flex-grow">
                                                <label
                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 sm:hidden">
                                                    Kullanıcı Adı
                                                </label>
                                                <div class="relative">
                                                    <div v-if="social.platform"
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-10">
                                                        <span class="text-lg">{{ getSocialIcon(social.platform)
                                                            }}</span>
                                                    </div>
                                                    <input :value="social.username"
                                                        @input="updateSocialUsername(index, ($event.target as HTMLInputElement).value)"
                                                        type="text"
                                                        :placeholder="social.platform ? `${getSocialLabel(social.platform)} kullanıcı adınız` : 'Önce platform seçin'"
                                                        :class="[
                                                            'block w-full py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200',
                                                            social.platform ? 'pl-12 pr-4' : 'px-4'
                                                        ]" :disabled="!social.platform">
                                                </div>
                                            </div>

                                            <!-- Kaldır Butonu -->
                                            <div class="flex justify-end sm:flex-shrink-0">
                                                <button @click="removeSocialMedia(index)" type="button"
                                                    class="inline-flex items-center px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 text-sm font-medium">
                                                    <X class="h-4 w-4 mr-1" />
                                                    <span class="sm:hidden">Kaldır</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sosyal Medya Ekleme Butonu -->
                                <div class="flex items-center justify-center">
                                    <button @click="addSocialMedia" type="button"
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200 shadow-sm">
                                        <Plus class="h-4 w-4 mr-2" />
                                        Sosyal Medya Hesabı Ekle
                                    </button>
                                </div>

                                <!-- Açıklama -->
                                <div v-if="form.socials.length === 0" class="text-center py-8">
                                    <div
                                        class="mx-auto w-16 h-16 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center mb-4">
                                        <Share2 class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        Sosyal Medya Hesabı Yok
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Sosyal medya hesaplarınızı ekleyerek profilinizi daha zengin hale getirin
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Firma Bilgisi -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                    <Building2 class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" />
                                    Firma Bilgisi
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Firma bilgilerinizi ekleyin
                                </p>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Firma İsmi
                                        </label>
                                        <input v-model="form.company_info.company_name" type="text"
                                            placeholder="Lütfen Firma İsminizi Yazın"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Ünvan
                                        </label>
                                        <input v-model="form.company_info.company_title" type="text"
                                            placeholder="Örn: Kurucu Ortak & CEO"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Vergi Dairesi
                                        </label>
                                        <input v-model="form.company_info.tax_office" type="text"
                                            placeholder="Lütfen Vergi Dairenizi Yazın"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Vergi Numarası
                                        </label>
                                        <input v-model="form.company_info.tax_number" type="text"
                                            placeholder="Lütfen Vergi Numarası Yazın"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Firma Adresi
                                        </label>
                                        <input v-model="form.company_info.company_address" type="text"
                                            placeholder="Lütfen Firma Adresini Yazın"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banka Bilgileri -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                    <Landmark class="h-5 w-5 mr-2 text-green-600 dark:text-green-400" />
                                    Banka Bilgileri
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Banka hesap bilgilerinizi ekleyin ve yönetin
                                </p>
                            </div>
                            <div class="p-6">
                                <!-- Mevcut Banka Hesapları -->
                                <div v-if="form.bank_accounts.length > 0" class="space-y-4 mb-6">
                                    <div v-for="(account, index) in form.bank_accounts" :key="index"
                                        class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Banka Seçimi -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Banka Seçin *
                                                </label>
                                                <select
                                                    :value="account.bank_id"
                                                    @change="updateBankSelection(index, parseInt(($event.target as HTMLSelectElement).value))"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                                    <option value="0">Banka Seçin</option>
                                                    <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                                                        {{ bank.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Şube
                                                </label>
                                                <input v-model="account.branch" type="text"
                                                    placeholder="Örn: Kadıköy Şubesi"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Hesap Sahibi
                                                </label>
                                                <input v-model="account.account_holder" type="text"
                                                    placeholder="Lütfen Hesap Sahibi Ad Soyad Yazın"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    IBAN
                                                </label>
                                                <input v-model="account.iban" type="text"
                                                    placeholder="TR00 0000 0000 0000 0000 0000 00"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                            </div>
                                        </div>
                                        <div class="mt-3 flex items-center justify-between">
                                            <label class="flex items-center space-x-2">
                                                <input
                                                    v-model="account.is_primary"
                                                    type="checkbox"
                                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ana Hesap</span>
                                            </label>
                                            <button @click="removeBankAccount(index)" type="button"
                                                class="inline-flex items-center px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 text-sm font-medium">
                                                <X class="h-4 w-4 mr-1" />
                                                Kaldır
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Banka Ekleme Butonu -->
                                <div class="flex items-center justify-center">
                                    <button @click="addBankAccount" type="button"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm">
                                        <Plus class="h-4 w-4 mr-2" />
                                        Banka Hesabı Ekle
                                    </button>
                                </div>

                                <!-- Açıklama -->
                                <div v-if="form.bank_accounts.length === 0" class="text-center py-8">
                                    <div
                                        class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mb-4">
                                        <Landmark class="h-8 w-8 text-green-600 dark:text-green-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        Banka Hesabı Yok
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Banka hesap bilgilerinizi ekleyerek ödeme almayı kolaylaştırın
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Dokümanlar -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                    <FileText class="h-5 w-5 mr-2 text-orange-600 dark:text-orange-400" />
                                    Dokümanlar (PDF)
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    PDF dokümanlarınızı ekleyin ve yönetin
                                </p>
                            </div>
                            <div class="p-6">
                                <!-- Mevcut Dokümanlar -->
                                <div v-if="form.documents.length > 0" class="space-y-4 mb-6">
                                    <div v-for="(doc, index) in form.documents" :key="index"
                                        class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Başlık
                                                </label>
                                                <input v-model="doc.title" type="text" placeholder="Katalog"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    PDF Dosyası
                                                </label>
                                                <input type="file" accept="application/pdf"
                                                    @change="handleDocumentChange($event, index)"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                                <p v-if="doc.file_path" class="text-xs text-gray-500 mt-1">Mevcut: {{ doc.file_path }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-end">
                                            <button @click="removeDocument(index)" type="button"
                                                class="inline-flex items-center px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 text-sm font-medium">
                                                <X class="h-4 w-4 mr-1" />
                                                Kaldır
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Doküman Ekleme Butonu -->
                                <div class="flex items-center justify-center">
                                    <button @click="addDocument" type="button"
                                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors duration-200 shadow-sm">
                                        <Plus class="h-4 w-4 mr-2" />
                                        Doküman Ekle
                                    </button>
                                </div>

                                <!-- Açıklama -->
                                <div v-if="form.documents.length === 0" class="text-center py-8">
                                    <div
                                        class="mx-auto w-16 h-16 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center mb-4">
                                        <FileText class="h-8 w-8 text-orange-600 dark:text-orange-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        Doküman Yok
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Katalog, broşür gibi PDF dokümanlarınızı ekleyin
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                                <Save class="w-4 h-4 mr-2" />
                                <span v-if="form.processing">Kaydediliyor...</span>
                                <span v-else>Profili Kaydet</span>
                            </button>
                        </div>

                        <!-- Hidden File Inputs -->
                        <input ref="profilePhotoInput" type="file" class="hidden" accept="image/*"
                            @change="handleProfilePhotoChange">
                        <input ref="coverPhotoInput" type="file" class="hidden" accept="image/*"
                            @change="handleCoverPhotoChange">
                    </form>
                </div>

                <!-- Phone Preview Section - Sağ Taraf -->
                <div class="lg:col-span-5">
                    <div class="sticky top-6">
                        <div
                            class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 text-center">
                                📱 VCard Önizleme
                            </h3>

                            <!-- Phone Mockup -->
                            <div class="relative mx-auto flex flex-col items-center"
                                style="width: 300px; height: 600px;">
                                <div class="absolute inset-0 bg-gray-800 rounded-[3rem] p-2 shadow-2xl flex flex-col">
                                    <div
                                        class="w-full h-full bg-white rounded-[2.5rem] overflow-hidden relative flex flex-col">
                                        <div
                                            class="absolute top-0 left-1/2 transform -translate-x-1/2 w-32 h-6 bg-black rounded-b-2xl z-10">
                                        </div>
                                        <div class="flex flex-col h-full justify-between pt-8 px-6 pb-6">
                                            <!-- Profile Section -->
                                            <div class="text-center">
                                                <div class="relative mx-auto w-28 h-28 mb-4">
                                                    <img :src="previewProfilePhoto" :alt="previewName"
                                                        class="w-full h-full rounded-full object-cover border-4 border-white shadow-xl">
                                                    <div
                                                        class="absolute bottom-2 right-2 w-7 h-7 bg-black rounded-full flex items-center justify-center">
                                                        <div
                                                            class="w-4 h-4 bg-white rounded-full flex items-center justify-center">
                                                            <div class="w-2 h-2 bg-black rounded-full"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ previewName }}</h2>
                                                <p class="text-gray-600 text-sm">{{ previewTitle }}</p>
                                            </div>
                                            <!-- Social + Mail/Telefon/Konum Grid -->
                                            <div
                                                class="grid grid-cols-3 gap-4 px-2 my-6 max-h-44 overflow-y-auto justify-items-center">
                                                <!-- Social Icons -->
                                                <div v-for="social in previewSocials.slice(0, 6)" :key="social.platform"
                                                    class="flex flex-col items-center space-y-2">
                                                    <div :class="[
                                                        'w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg shadow-lg',
                                                        getSocialColor(social.platform)
                                                    ]">
                                                        {{ getSocialIcon(social.platform) }}
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-700 font-medium text-center leading-tight">
                                                        {{ getSocialLabel(social.platform) }}
                                                    </span>
                                                </div>
                                                <!-- Mail -->
                                                <div class="flex flex-col items-center space-y-2">
                                                    <div
                                                        class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg shadow-lg bg-gradient-to-r from-blue-500 to-blue-600">
                                                        <Mail class="w-6 h-6" />
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-700 font-medium text-center leading-tight">Mail</span>
                                                </div>
                                                <!-- Telefon -->
                                                <div class="flex flex-col items-center space-y-2">
                                                    <div
                                                        class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg shadow-lg bg-gradient-to-r from-green-500 to-green-600">
                                                        <Phone class="w-6 h-6" />
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-700 font-medium text-center leading-tight">Telefon</span>
                                                </div>
                                                <!-- Konum -->
                                                <div class="flex flex-col items-center space-y-2">
                                                    <div
                                                        class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg shadow-lg bg-gradient-to-r from-red-500 to-red-600">
                                                        <MapPin class="w-6 h-6" />
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-700 font-medium text-center leading-tight">Konum</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
