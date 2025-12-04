<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, computed, onMounted } from 'vue';
import { UserPen, User, Phone, Mail, Key, MapPin, FileText, ImageIcon, Eye, EyeOff, Upload, X, Plus, Share2 } from 'lucide-vue-next';

// Props - Backend'den gelen user verisi
interface Props {
    user: {
        id: number;
        name: string;
        username: string;
        email: string;
        phone?: string;
        address?: string;
        bio?: string;
        profile_photo?: string;
        cover_photo?: string;
        socials?: Record<string, string>;
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name || '',
    username: props.user.username || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    phone: props.user.phone || '',
    address: props.user.address || '',
    bio: props.user.bio || '',
    profile_photo: null as File | null,
    cover_photo: null as File | null,
    socials: [] as Array<{ platform: string, username: string }>,
});

// Sosyal medya platformları
const socialPlatforms = [
    { value: 'instagram', label: 'Instagram', icon: '📷' },
    { value: 'twitter', label: 'Twitter (X)', icon: '🐦' },
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
];

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
        title: `${props.user.name} - Düzenle`,
        href: route('admin.users.edit', props.user.id),
    },
];

const profilePhotoPreview = ref<string | null>(null);
const coverPhotoPreview = ref<string | null>(null);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
const dragOverProfile = ref(false);
const dragOverCover = ref(false);

// Form validation states
const isFormValid = computed(() => {
    return form.name && form.username && form.email;
});

const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return { score: 0, text: '', color: '' };

    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const levels = [
        { score: 0, text: '', color: '' },
        { score: 1, text: 'Çok Zayıf', color: 'bg-red-500' },
        { score: 2, text: 'Zayıf', color: 'bg-orange-500' },
        { score: 3, text: 'Orta', color: 'bg-yellow-500' },
        { score: 4, text: 'Güçlü', color: 'bg-blue-500' },
        { score: 5, text: 'Çok Güçlü', color: 'bg-green-500' }
    ];

    return levels[score];
});

// Existing photos preview
onMounted(() => {
    if (props.user.profile_photo) {
        profilePhotoPreview.value = `/storage/${props.user.profile_photo}`;
    }
    if (props.user.cover_photo) {
        coverPhotoPreview.value = `/storage/${props.user.cover_photo}`;
    }

    // Convert socials object to array
    if (props.user.socials) {
        form.socials = Object.entries(props.user.socials).map(([platform, username]) => ({
            platform,
            username
        }));
    }
});

const handleProfilePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        if (file.type.startsWith('image/')) {
            form.profile_photo = file;
            profilePhotoPreview.value = URL.createObjectURL(file);
        }
    }
};

const handleCoverPhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        if (file.type.startsWith('image/')) {
            form.cover_photo = file;
            coverPhotoPreview.value = URL.createObjectURL(file);
        }
    }
};

// Drag & Drop handlers
const handleProfileDragOver = (event: DragEvent) => {
    event.preventDefault();
    dragOverProfile.value = true;
};

const handleProfileDragLeave = () => {
    dragOverProfile.value = false;
};

const handleProfileDrop = (event: DragEvent) => {
    event.preventDefault();
    dragOverProfile.value = false;
    const files = event.dataTransfer?.files;
    if (files && files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
            form.profile_photo = file;
            profilePhotoPreview.value = URL.createObjectURL(file);
        }
    }
};

const handleCoverDragOver = (event: DragEvent) => {
    event.preventDefault();
    dragOverCover.value = true;
};

const handleCoverDragLeave = () => {
    dragOverCover.value = false;
};

const handleCoverDrop = (event: DragEvent) => {
    event.preventDefault();
    dragOverCover.value = false;
    const files = event.dataTransfer?.files;
    if (files && files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
            form.cover_photo = file;
            coverPhotoPreview.value = URL.createObjectURL(file);
        }
    }
};

const removeProfilePhoto = () => {
    form.profile_photo = null;
    profilePhotoPreview.value = props.user.profile_photo ? `/storage/${props.user.profile_photo}` : null;
};

const removeCoverPhoto = () => {
    form.cover_photo = null;
    coverPhotoPreview.value = props.user.cover_photo ? `/storage/${props.user.cover_photo}` : null;
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

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

const submit = () => {
    // _method parametresini form'a ekle
    form.transform((data) => ({
        ...data,
        _method: 'PUT'
    })).post(route('admin.users.update', props.user.id), {
        forceFormData: true,
        onSuccess: () => {
            // Başarılı olursa users sayfasına yönlendir
        },
        onError: (errors) => {
            console.error('Form gönderim hatası:', errors);
        }
    });
};
</script>

<template>

    <Head :title="`${user.name} - Düzenle`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl shadow-lg">
                        <UserPen class="h-6 w-6 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Kullanıcı Düzenle
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ user.name }} kullanıcısının bilgilerini düzenleyin
                        </p>
                    </div>
                </div>
                <Link :href="route('admin.users.index')"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Geri Dön
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Progress Indicator -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <span>Form Tamamlanma</span>
                        <span>{{Math.round(Object.values(form.data()).filter(val => val !== '' && val !== null).length
                            / Object.keys(form.data()).length * 100)}}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-teal-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: Math.round(Object.values(form.data()).filter(val => val !== '' && val !== null).length / Object.keys(form.data()).length * 100) + '%' }">
                        </div>
                    </div>
                </div>

                <!-- Temel Bilgiler -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <User class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" />
                            Temel Bilgiler
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Kullanıcının temel kimlik bilgilerini düzenleyin
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Ad Soyad -->
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ad Soyad *
                                </label>
                                <div class="relative">
                                    <input v-model="form.name" type="text" id="name"
                                        placeholder="Kullanıcının ad ve soyadını giriniz"
                                        class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.name,
                                            'border-green-300 focus:ring-green-500': form.name && !form.errors.name
                                        }">
                                    <div v-if="form.name && !form.errors.name"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="form.errors.name" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Kullanıcı Adı -->
                            <div class="space-y-2">
                                <label for="username"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Kullanıcı Adı *
                                </label>
                                <div class="relative">
                                    <input v-model="form.username" type="text" id="username"
                                        placeholder="Benzersiz kullanıcı adı giriniz"
                                        class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.username,
                                            'border-green-300 focus:ring-green-500': form.username && !form.errors.username
                                        }">
                                    <div v-if="form.username && !form.errors.username"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="form.errors.username" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.username }}
                                </div>
                            </div>

                            <!-- E-posta -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    E-posta Adresi *
                                </label>
                                <div class="relative">
                                    <Mail class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" />
                                    <input v-model="form.email" type="email" id="email" placeholder="email@example.com"
                                        class="block w-full pl-10 pr-10 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.email,
                                            'border-green-300 focus:ring-green-500': form.email && !form.errors.email
                                        }">
                                    <div v-if="form.email && !form.errors.email"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="form.errors.email" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Telefon -->
                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Telefon Numarası
                                </label>
                                <div class="relative">
                                    <Phone class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" />
                                    <input v-model="form.phone" type="tel" id="phone" placeholder="+90 5XX XXX XX XX"
                                        class="block w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.phone
                                        }">
                                </div>
                                <div v-if="form.errors.phone" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.phone }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Güvenlik -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <Key class="h-5 w-5 mr-2 text-red-600 dark:text-red-400" />
                            Güvenlik Bilgileri
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Şifre değiştirmek için yeni şifre giriniz (isteğe bağlı)
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Şifre -->
                            <div class="space-y-2">
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Yeni Şifre (isteğe bağlı)
                                </label>
                                <div class="relative">
                                    <Key class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" />
                                    <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                        id="password" placeholder="Yeni şifre belirleyin"
                                        class="block w-full pl-10 pr-12 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.password
                                        }">
                                    <button type="button" @click="togglePasswordVisibility"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <Eye v-if="!showPassword" class="h-5 w-5" />
                                        <EyeOff v-else class="h-5 w-5" />
                                    </button>
                                </div>
                                <!-- Password Strength Indicator -->
                                <div v-if="form.password" class="mt-2">
                                    <div
                                        class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        <span>Şifre Gücü</span>
                                        <span>{{ passwordStrength.text }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full transition-all duration-300"
                                            :class="passwordStrength.color"
                                            :style="{ width: (passwordStrength.score * 20) + '%' }"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        En az 8 karakter, büyük/küçük harf, rakam ve özel karakter içermelidir
                                    </div>
                                </div>
                                <div v-if="form.errors.password" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Şifre Tekrar -->
                            <div class="space-y-2">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Şifre Tekrar
                                </label>
                                <div class="relative">
                                    <Key class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" />
                                    <input v-model="form.password_confirmation"
                                        :type="showPasswordConfirmation ? 'text' : 'password'"
                                        id="password_confirmation" placeholder="Şifrenizi tekrar giriniz"
                                        class="block w-full pl-10 pr-12 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.password_confirmation && form.password !== form.password_confirmation,
                                            'border-green-300 focus:ring-green-500': form.password_confirmation && form.password === form.password_confirmation
                                        }">
                                    <button type="button" @click="togglePasswordConfirmationVisibility"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <Eye v-if="!showPasswordConfirmation" class="h-5 w-5" />
                                        <EyeOff v-else class="h-5 w-5" />
                                    </button>
                                </div>
                                <!-- Password Match Indicator -->
                                <div v-if="form.password_confirmation" class="mt-1">
                                    <div v-if="form.password === form.password_confirmation"
                                        class="text-sm text-green-600 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Şifreler eşleşiyor
                                    </div>
                                    <div v-else-if="form.password !== form.password_confirmation"
                                        class="text-sm text-red-600 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Şifreler eşleşmiyor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profil Detayları -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <User class="h-5 w-5 mr-2 text-green-600 dark:text-green-400" />
                            Profil Detayları
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Kullanıcının profil bilgilerini ve medya dosyalarını düzenleyin
                        </p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Adres ve Biyografi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Adres -->
                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Adres
                                </label>
                                <div class="relative">
                                    <MapPin class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <textarea v-model="form.address" id="address" rows="3"
                                        placeholder="Tam adres bilgilerini giriniz..."
                                        class="block w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.address
                                        }"></textarea>
                                </div>
                                <div v-if="form.errors.address" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <!-- Biyografi -->
                            <div class="space-y-2">
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Biyografi
                                </label>
                                <div class="relative">
                                    <FileText class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <textarea v-model="form.bio" id="bio" rows="3"
                                        placeholder="Kullanıcı hakkında kısa bir açıklama yazınız..."
                                        class="block w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                        :class="{
                                            'border-red-300 focus:ring-red-500': form.errors.bio
                                        }"></textarea>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ form.bio ? form.bio.length : 0 }}/500 karakter
                                </div>
                                <div v-if="form.errors.bio" class="text-sm text-red-600 flex items-center mt-1">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.bio }}
                                </div>
                            </div>
                        </div>

                        <!-- Medya Dosyaları -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Profil Fotoğrafı -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Profil Fotoğrafı
                                </label>
                                <div class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200"
                                    :class="[
                                        dragOverProfile ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600',
                                        'hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20'
                                    ]" @dragover="handleProfileDragOver" @dragleave="handleProfileDragLeave"
                                    @drop="handleProfileDrop">
                                    <div v-if="!profilePhotoPreview" class="space-y-3">
                                        <div
                                            class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                            <ImageIcon class="h-8 w-8 text-gray-400" />
                                        </div>
                                        <div>
                                            <label
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 cursor-pointer transition-colors duration-200">
                                                <Upload class="h-4 w-4 mr-2" />
                                                Dosya Seç
                                                <input @change="handleProfilePhotoChange" type="file" id="profile_photo"
                                                    class="sr-only" accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            veya dosyayı buraya sürükleyin
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            PNG, JPG, GIF - Max 2MB
                                        </p>
                                    </div>

                                    <div v-else class="space-y-3">
                                        <div class="relative inline-block">
                                            <img :src="profilePhotoPreview" alt="Profil Önizleme"
                                                class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg">
                                            <button @click="removeProfilePhoto" type="button"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors duration-200">
                                                <X class="h-3 w-3" />
                                            </button>
                                        </div>
                                        <div class="space-x-2">
                                            <label
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer transition-colors duration-200">
                                                Değiştir
                                                <input @change="handleProfilePhotoChange" type="file" class="sr-only"
                                                    accept="image/*">
                                            </label>
                                            <button @click="removeProfilePhoto" type="button"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors duration-200">
                                                Kaldır
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.profile_photo" class="text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.profile_photo }}
                                </div>
                            </div>

                            <!-- Kapak Fotoğrafı -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Kapak Fotoğrafı
                                </label>
                                <div class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200"
                                    :class="[
                                        dragOverCover ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600',
                                        'hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20'
                                    ]" @dragover="handleCoverDragOver" @dragleave="handleCoverDragLeave"
                                    @drop="handleCoverDrop">
                                    <div v-if="!coverPhotoPreview" class="space-y-3">
                                        <div
                                            class="mx-auto w-20 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <ImageIcon class="h-6 w-6 text-gray-400" />
                                        </div>
                                        <div>
                                            <label
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 cursor-pointer transition-colors duration-200">
                                                <Upload class="h-4 w-4 mr-2" />
                                                Dosya Seç
                                                <input @change="handleCoverPhotoChange" type="file" id="cover_photo"
                                                    class="sr-only" accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            veya dosyayı buraya sürükleyin
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            PNG, JPG, GIF - Max 5MB (1200x400 önerilir)
                                        </p>
                                    </div>

                                    <div v-else class="space-y-3">
                                        <div class="relative inline-block">
                                            <img :src="coverPhotoPreview" alt="Kapak Önizleme"
                                                class="w-full h-32 rounded-lg object-cover border-2 border-gray-200 dark:border-gray-600 shadow-md">
                                            <button @click="removeCoverPhoto" type="button"
                                                class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors duration-200">
                                                <X class="h-3 w-3" />
                                            </button>
                                        </div>
                                        <div class="space-x-2">
                                            <label
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer transition-colors duration-200">
                                                Değiştir
                                                <input @change="handleCoverPhotoChange" type="file" class="sr-only"
                                                    accept="image/*">
                                            </label>
                                            <button @click="removeCoverPhoto" type="button"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors duration-200">
                                                Kaldır
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.cover_photo" class="text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.cover_photo }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sosyal Medya -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <Share2 class="h-5 w-5 mr-2 text-purple-600 dark:text-purple-400" />
                            Sosyal Medya Hesapları
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Kullanıcının sosyal medya platformlarını düzenleyin
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
                                                <span class="text-lg">{{ getSocialIcon(social.platform) }}</span>
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
                                Kullanıcının sosyal medya hesaplarını ekleyerek profili daha zengin hale getirin
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Değişiklikler:</span>
                            Form güncellendiğinde otomatik olarak kaydedilecektir
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <Link :href="route('admin.users.index')"
                                class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 flex items-center justify-center font-medium border border-gray-300 dark:border-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            İptal Et
                            </Link>
                            <button type="submit" :disabled="form.processing || !isFormValid"
                                class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:from-green-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center font-medium shadow-md hover:shadow-lg transform hover:scale-105 disabled:transform-none">
                                <div v-if="form.processing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Güncelleniyor...
                                </div>
                                <div v-else class="flex items-center">
                                    <UserPen class="h-4 w-4 mr-2" />
                                    Kullanıcıyı Güncelle
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
