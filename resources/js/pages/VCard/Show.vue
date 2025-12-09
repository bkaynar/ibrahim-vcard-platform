<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Mail, Phone, MapPin, Download, QrCode, X, Building2, Landmark, FileText, Copy, Check, Share2 } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import QRCode from 'qrcode';

// Toast notification
const showToast = ref(false);
const toastMessage = ref('');

const showNotification = (message: string) => {
    toastMessage.value = message;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

// IBAN kopyalama
const copiedIban = ref<string | null>(null);

const copyIban = async (iban: string) => {
    try {
        await navigator.clipboard.writeText(iban);
        copiedIban.value = iban;
        showNotification('IBAN kopyalandı! ✓');

        // 2 saniye sonra kopyalanan durumunu sıfırla
        setTimeout(() => {
            copiedIban.value = null;
        }, 2000);
    } catch (err) {
        console.error('IBAN kopyalanamadı:', err);
        showNotification('IBAN kopyalanamadı');
    }
};

/**
 * Telefon numarasını `tel:` linki için uluslararası formata çevirir.
 * Örnek: "05551234567" -> "+905551234567"
 * Gelen değer zaten "+905..." ise korunur.
 */
const formatTel = (raw?: string | null) => {
    if (!raw) return '';
    const trimmed = String(raw).trim();
    const digits = trimmed.replace(/\D/g, '');

    // Eğer kullanıcı '+' ile başlamışsa, sadece rakamları alıp tekrar '+' ekle
    if (trimmed.startsWith('+')) {
        return `+${digits}`;
    }

    // Eğer başında ülke kodu (90) varsa, + ekle
    if (digits.startsWith('90')) {
        return `+${digits}`;
    }

    // Eğer başında sıfır varsa, baştaki sıfırları kaldırıp +90 ekle
    if (digits.startsWith('0')) {
        const noZero = digits.replace(/^0+/, '');
        return `+90${noZero}`;
    }

    // Diğer durumlarda direkt +90 ekle
    return `+90${digits}`;
};

/**
 * Kullanıcı adını gösterim için normalize eder.
 * Eğer isim zaten "Av" veya "Av." ile başlıyorsa tekrar eklemez.
 */
const formatDisplayName = (raw?: string | null) => {
    if (!raw) return '';
    let name = String(raw).trim();
    // Başında birden fazla "Av" veya "Av." varsa temizle
    while (/^av\.?\s+/i.test(name)) {
        name = name.replace(/^av\.?\s+/i, '').trim();
    }
    return `Av. ${name}`;
};

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
        profile_photo_url: string;
        cover_photo_url: string;
        socials?: {
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
        // Yeni: birden fazla banka hesabı pivot tablodan gelecek
        userBanks?: Array<{
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
        // backward compat: tekil eski alan
        bank_info?: {
            bank_name?: string;
            bank_account_holder?: string;
            iban?: string;
            bank_branch?: string;
        };
        documents?: Array<{
            title: string;
            file_path?: string;
            file_url?: string;
        }>;
    };
}>()

const getSocialIcon = (platform: string) => {
    const icons = {
        facebook: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>`,
        twitter: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>`,
        instagram: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
        </svg>`,
        linkedin: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
        </svg>`,
        youtube: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
        </svg>`,
        tiktok: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
        </svg>`,
        github: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
        </svg>`,
        whatsapp: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.484 3.488"/>
        </svg>`,
        telegram: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
        </svg>`,
        discord: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0190 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z"/>
        </svg>`,
        website: `<svg viewBox="0 0 24 24" class="w-full h-full fill-current">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
        </svg>`
    };

    return icons[platform as keyof typeof icons] || icons.website;
}

const getSocialColor = (platform: string) => {
    switch (platform) {
        case 'facebook': return 'bg-blue-600 hover:bg-blue-700';
        case 'twitter': return 'bg-black hover:bg-gray-800';
        case 'instagram': return 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600';
        case 'linkedin': return 'bg-blue-700 hover:bg-blue-800';
        case 'youtube': return 'bg-red-600 hover:bg-red-700';
        case 'tiktok': return 'bg-black hover:bg-gray-800';
        case 'snapchat': return 'bg-yellow-400 hover:bg-yellow-500';
        case 'pinterest': return 'bg-red-500 hover:bg-red-600';
        case 'github': return 'bg-gray-800 hover:bg-gray-900';
        case 'behance': return 'bg-blue-500 hover:bg-blue-600';
        case 'dribbble': return 'bg-pink-500 hover:bg-pink-600';
        case 'twitch': return 'bg-purple-600 hover:bg-purple-700';
        case 'discord': return 'bg-indigo-600 hover:bg-indigo-700';
        case 'telegram': return 'bg-blue-500 hover:bg-blue-600';
        case 'whatsapp': return 'bg-green-500 hover:bg-green-600';
        case 'website': return 'bg-gray-600 hover:bg-gray-700';
        default: return 'bg-gray-600 hover:bg-gray-700';
    }
}

const getSocialLabel = (platform: string) => {
    switch (platform) {
        case 'facebook': return 'Facebook';
        case 'twitter': return 'X';
        case 'instagram': return 'Instagram';
        case 'linkedin': return 'LinkedIn';
        case 'youtube': return 'YouTube';
        case 'tiktok': return 'TikTok';
        case 'snapchat': return 'Snapchat';
        case 'pinterest': return 'Pinterest';
        case 'github': return 'GitHub';
        case 'behance': return 'Behance';
        case 'dribbble': return 'Dribbble';
        case 'twitch': return 'Twitch';
        case 'discord': return 'Discord';
        case 'telegram': return 'Telegram';
        case 'whatsapp': return 'WhatsApp';
        case 'website': return 'Website';
        default: return platform.charAt(0).toUpperCase() + platform.slice(1);
    }
}

const getSocialUrl = (platform: string, username: string) => {
    // Eğer username zaten tam URL ise, olduğu gibi döndür
    if (username.startsWith('http://') || username.startsWith('https://')) {
        return username;
    }

    // Platform'a göre özel URL oluştur
    switch (platform) {
        case 'facebook':
            return `https://facebook.com/${username.replace('@', '')}`;
        case 'twitter':
            return `https://x.com/${username.replace('@', '')}`;
        case 'instagram':
            return `https://instagram.com/${username.replace('@', '')}`;
        case 'linkedin':
            // LinkedIn için in/ prefix'i kontrol et
            const linkedinUsername = username.replace('@', '');
            if (linkedinUsername.startsWith('in/')) {
                return `https://linkedin.com/${linkedinUsername}`;
            }
            return `https://linkedin.com/in/${linkedinUsername}`;
        case 'youtube':
            return `https://youtube.com/@${username.replace('@', '')}`;
        case 'tiktok':
            return `https://tiktok.com/@${username.replace('@', '')}`;
        case 'snapchat':
            return `https://snapchat.com/add/${username.replace('@', '')}`;
        case 'pinterest':
            return `https://pinterest.com/${username.replace('@', '')}`;
        case 'github':
            return `https://github.com/${username.replace('@', '')}`;
        case 'behance':
            return `https://behance.net/${username.replace('@', '')}`;
        case 'dribbble':
            return `https://dribbble.com/${username.replace('@', '')}`;
        case 'twitch':
            return `https://twitch.tv/${username.replace('@', '')}`;
        case 'discord':
            // Discord için özel format
            return username.startsWith('discord.gg/') ? `https://${username}` : `https://discord.gg/${username}`;
        case 'telegram':
            return `https://t.me/${username.replace('@', '')}`;
        case 'whatsapp':
            // WhatsApp için telefon numarası formatı
            return `https://wa.me/${username.replace(/[^0-9]/g, '')}`;
        case 'website':
            // Website için http prefix'i kontrol et
            return username.startsWith('http') ? username : `https://${username}`;
        default:
            return username.startsWith('http') ? username : `https://${username}`;
    }
}

/**
 * VCard dosyası oluşturur ve indirir
 */
const downloadVCard = () => {
    // VCard formatını oluştur
    let vCardContent = 'BEGIN:VCARD\n';
    vCardContent += 'VERSION:3.0\n';
    vCardContent += `FN:${props.user.name}\n`;

    // Eğer email varsa ekle
    if (props.user.email) {
        vCardContent += `EMAIL:${props.user.email}\n`;
    }

    // Eğer telefon varsa ekle
    if (props.user.phone) {
        // Telefon numarasını uluslararası formata çevir (+ işaretini kaldır)
        const phone = props.user.phone.replace(/\s+/g, '').replace(/^\+/, '');
        vCardContent += `TEL;TYPE=CELL:+${phone}\n`;
    }

    // Eğer adres varsa ekle
    if (props.user.address) {
        vCardContent += `ADR:;;${props.user.address};;;;\n`;
    }

    // Profil URL'ini ekle
    const profileUrl = window.location.href;
    vCardContent += `URL:${profileUrl}\n`;

    // Sosyal medya bilgilerini ekle
    if (props.user.socials) {
        for (const [platform, username] of Object.entries(props.user.socials)) {
            if (username) {
                vCardContent += `X-SOCIALPROFILE;TYPE=${platform.toUpperCase()}:${getSocialUrl(platform, username)}\n`;
            }
        }
    }

    // Eğer pivot tablodan gelen birden fazla banka hesabı varsa, her birini ekle
    if (props.user.userBanks && Array.isArray(props.user.userBanks) && props.user.userBanks.length > 0) {
        props.user.userBanks.forEach((account: any) => {
            if (account && account.iban) {
                // Basit bir custom alan olarak IBAN ve banka bilgisi ekle
                vCardContent += `X-IBAN;BANK=${account.bank_name}:${account.iban}\n`;
                if (account.account_holder) {
                    vCardContent += `X-ACCOUNT-HOLDER:${account.account_holder}\n`;
                }
            }
        });
    } else if (props.user.bank_info && props.user.bank_info.iban) {
        // Geriye dönük uyumluluk: tekil bank_info varsa onu da ekle
        vCardContent += `X-IBAN;BANK=${props.user.bank_info.bank_name}:${props.user.bank_info.iban}\n`;
    }

    // VCard kapanış
    vCardContent += 'END:VCARD';

    // Dosyayı oluştur ve indir
    const blob = new Blob([vCardContent], { type: 'text/vcard' });
    const url = window.URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${props.user.name}.vcf`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// QR kod için değişkenler
const showQrModal = ref(false);
const qrCodeDataUrl = ref('');

/**
 * QR kod modalını açar ve QR kodu oluşturur
 */
const openQrModal = async () => {
    try {
        // Mevcut URL'i al ve QR kod oluştur
        const currentUrl = window.location.href;
        qrCodeDataUrl.value = await QRCode.toDataURL(currentUrl, {
            width: 300,
            margin: 2,
            color: {
                dark: '#333333',
                light: '#ffffff'
            }
        });
        showQrModal.value = true;
    } catch (err) {
        console.error('QR kod oluşturulamadı:', err);
    }
};

/**
 * QR kod modalını kapatır
 */
const closeQrModal = () => {
    showQrModal.value = false;
};

// Company modal
const showCompanyModal = ref(false);
const openCompanyModal = () => {
    showCompanyModal.value = true;
};
const closeCompanyModal = () => {
    showCompanyModal.value = false;
};

// Bank modal
const showBankModal = ref(false);
const selectedBank = ref<any>(null);
const openBankModal = (account: any) => {
    selectedBank.value = account;
    showBankModal.value = true;
};
const closeBankModal = () => {
    showBankModal.value = false;
    selectedBank.value = null;
};

// Documents modal
const showDocumentsModal = ref(false);
const openDocumentsModal = () => {
    showDocumentsModal.value = true;
};
const closeDocumentsModal = () => {
    showDocumentsModal.value = false;
};

/**
 * Banka adına göre logo rengini döndürür
 */
const getBankColor = (bankName: string | undefined) => {
    if (!bankName) return 'bg-gray-500';

    const name = bankName.toLowerCase();
    if (name.includes('ziraat')) return 'bg-green-600';
    if (name.includes('halk')) return 'bg-blue-600';
    if (name.includes('vakıf')) return 'bg-orange-600';
    if (name.includes('garanti')) return 'bg-green-700';
    if (name.includes('yapı kredi') || name.includes('yapıkredi')) return 'bg-blue-700';
    if (name.includes('akbank')) return 'bg-red-600';
    if (name.includes('işbank') || name.includes('iş bankası')) return 'bg-blue-800';
    if (name.includes('denizbank')) return 'bg-green-500';
    if (name.includes('teb')) return 'bg-indigo-600';
    if (name.includes('ing')) return 'bg-orange-500';
    if (name.includes('kuveyt')) return 'bg-emerald-700';
    if (name.includes('albaraka')) return 'bg-teal-600';

    return 'bg-gray-500';
};

/**
 * Banka adının kısaltmasını döndürür
 */
const getBankInitials = (bankName: string | undefined) => {
    if (!bankName) return 'B';

    const name = bankName.toLowerCase();
    if (name.includes('ziraat')) return 'Z';
    if (name.includes('halk')) return 'H';
    if (name.includes('vakıf')) return 'V';
    if (name.includes('garanti')) return 'G';
    if (name.includes('yapı kredi') || name.includes('yapıkredi')) return 'YK';
    if (name.includes('akbank')) return 'A';
    if (name.includes('işbank') || name.includes('iş bankası')) return 'İ';
    if (name.includes('denizbank')) return 'D';
    if (name.includes('teb')) return 'T';
    if (name.includes('ing')) return 'ING';
    if (name.includes('kuveyt')) return 'K';
    if (name.includes('albaraka')) return 'AB';

    return 'B';
};

/**
 * QR kodunu indirir
 */
const downloadQrCode = () => {
    if (!qrCodeDataUrl.value) return;

    const link = document.createElement('a');
    link.href = qrCodeDataUrl.value;
    link.download = `${props.user.username}-qr-code.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showNotification('QR kod indirildi! ✓');
};

/**
 * QR kodunu paylaşır
 */
const shareQrCode = async () => {
    try {
        // Web Share API destekleniyor mu kontrol et
        if (navigator.share) {
            // QR kodu blob'a çevir
            const response = await fetch(qrCodeDataUrl.value);
            const blob = await response.blob();
            const file = new File([blob], `${props.user.username}-qr-code.png`, { type: 'image/png' });

            await navigator.share({
                title: `${props.user.name} - VCard QR Kod`,
                text: `${props.user.name} profilini görüntülemek için QR kodu tarayın`,
                files: [file],
                url: window.location.href
            });

            showNotification('QR kod paylaşıldı! ✓');
        } else {
            // Web Share API desteklenmiyorsa, URL'i kopyala
            await navigator.clipboard.writeText(window.location.href);
            showNotification('Profil linki kopyalandı! ✓');
        }
    } catch (err) {
        // Kullanıcı iptal ettiyse veya hata olduysa
        if (err instanceof Error && err.name !== 'AbortError') {
            console.error('Paylaşım hatası:', err);
            // Fallback: URL'i kopyala
            try {
                await navigator.clipboard.writeText(window.location.href);
                showNotification('Profil linki kopyalandı! ✓');
            } catch (clipboardErr) {
                showNotification('Paylaşım başarısız oldu');
            }
        }
    }
};

/**
 * Harita uygulamasını açar (iOS: Apple Maps, Android/Web: Google Maps)
 */
const openMaps = () => {
    if (!props.user.address) return;

    const address = encodeURIComponent(props.user.address);
    const userAgent = navigator.userAgent || navigator.vendor;

    // iOS cihaz kontrolü
    const isIOS = /iPad|iPhone|iPod/.test(userAgent) && !(window as any).MSStream;

    if (isIOS) {
        // iOS için Apple Maps
        window.location.href = `maps://maps.apple.com/?q=${address}`;
    } else {
        // Android ve Web için Google Maps
        window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
    }
};

// Sayfa yüklendiğinde QR kodu hazırla
onMounted(() => {
    // Önceden QR kodu hazırlayalım
    const currentUrl = window.location.href;
    QRCode.toDataURL(currentUrl, {
        width: 300,
        margin: 2,
        color: {
            dark: '#333333',
            light: '#ffffff'
        }
    }).then(url => {
        qrCodeDataUrl.value = url;
    }).catch(err => {
        console.error('QR kod oluşturulamadı:', err);
    });
});
</script>

<template>

    <Head :title="formatDisplayName(props.user.name)" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-950 dark:via-gray-900 dark:to-slate-900">
        <div class="max-w-3xl mx-auto py-6 sm:py-12 px-4">
            <!-- VCard -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700/50 hover:shadow-3xl transition-all duration-300">
                <!-- Cover Photo removed per design -->

                <!-- Profile Section -->
                <div class="relative px-6 sm:px-10 pb-10">
                    <!-- Profile Photo -->
                    <div class="flex justify-center mb-12 mt-12 ">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-300"></div>
                            <div class="relative w-36 h-36 sm:w-40 sm:h-40 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 shadow-xl">
                                <img :src="props.user.profile_photo_url" :alt="`${formatDisplayName(props.user.name)} profil fotoğrafı`"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="text-center mb-10">
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-3">
                            {{ formatDisplayName(props.user.name) }}
                        </h1>
                        <p v-if="props.user.bio" class="text-gray-600 dark:text-gray-300 mt-5 leading-relaxed max-w-2xl mx-auto text-base">
                            {{ props.user.bio }}
                        </p>

                        <!-- Butonlar -->
                        <div class="flex flex-wrap justify-center gap-4 mt-8">
                            <!-- VCard İndirme Butonu -->
                            <button @click="downloadVCard"
                                class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-semibold">
                                <Download class="w-5 h-5 mr-2 group-hover:animate-bounce" />
                                Rehbere Ekle
                            </button>

                            <!-- QR Kod Butonu -->
                            <button @click="openQrModal"
                                class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-semibold">
                                <QrCode class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" />
                                QR Kod
                            </button>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-3 gap-4 max-w-md mx-auto mb-4">
                        <a v-if="props.user.email" :href="`mailto:${props.user.email}`"
                            class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                            <div class="p-3 rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 bg-gradient-to-r from-blue-500 to-indigo-600">
                                <Mail class="w-5 h-5 text-white" />
                            </div>
                            <span class="text-xs text-gray-700 dark:text-gray-300 mt-2.5 text-center font-semibold leading-tight">
                                E-posta
                            </span>
                        </a>

                        <a v-if="props.user.phone" :href="`tel:${formatTel(props.user.phone)}`"
                            class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                            <div class="p-3 rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 bg-gradient-to-r from-green-500 to-emerald-600">
                                <Phone class="w-5 h-5 text-white" />
                            </div>
                            <span class="text-xs text-gray-700 dark:text-gray-300 mt-2.5 text-center font-semibold leading-tight">
                                Telefon
                            </span>
                        </a>

                        <a v-if="props.user.address" @click="openMaps" href="javascript:void(0)"
                            class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30 cursor-pointer">
                            <div class="p-3 rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 bg-gradient-to-r from-purple-500 to-pink-600">
                                <MapPin class="w-5 h-5 text-white" />
                            </div>
                            <span class="text-xs text-gray-700 dark:text-gray-300 mt-2.5 text-center font-semibold leading-tight">
                                Konum
                            </span>
                        </a>
                    </div>

                    <!-- Social Media Links -->
                    <div v-if="props.user.socials && Object.entries(props.user.socials).some(([, url]) => url)">

                        <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                            <template v-for="[platform, username] in Object.entries(props.user.socials || {})"
                                :key="platform">
                                <a v-if="username" :href="getSocialUrl(platform, username)" target="_blank"
                                    class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                                    <div
                                        :class="`p-3 rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 ${getSocialColor(platform)}`">
                                        <div class="w-5 h-5 text-white" v-html="getSocialIcon(platform)"></div>
                                    </div>
                                    <span
                                        class="text-xs text-gray-700 dark:text-gray-300 mt-2.5 text-center font-semibold leading-tight">
                                        {{ getSocialLabel(platform) }}
                                    </span>
                                </a>
                            </template>

                            <!-- Firma Bilgisi İkonu -->
                            <button v-if="props.user.company_info && (props.user.company_info.company_name || props.user.company_info.tax_office || props.user.company_info.tax_number || props.user.company_info.company_address)"
                                @click="openCompanyModal"
                                class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                                <div class="p-3 rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 bg-gradient-to-r from-blue-600 to-indigo-700">
                                    <Building2 class="w-5 h-5 text-white" />
                                </div>
                                <span class="text-xs text-gray-700 dark:text-gray-300 mt-2.5 text-center font-semibold leading-tight">
                                    Firma
                                </span>
                            </button>

                            <!-- Banka Bilgisi İkonları (pivot'tan gelen userBanks) -->
                            <template v-if="props.user.userBanks && props.user.userBanks.length > 0">
                                <template v-for="(account, idx) in props.user.userBanks" :key="`bank-${idx}`">
                                    <button v-if="account && account.iban" @click="openBankModal(account)"
                                        class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                                        <div class="flex flex-col items-center">
                                            <div class="flex items-center justify-center p-0 rounded-xl transition-all duration-300">
                                                <template v-if="account.bank_logo_url">
                                                    <img :src="account.bank_logo_url" :alt="account.bank_name" class="w-8 h-8 object-contain mx-auto">
                                                </template>
                                                <template v-else>
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-sm font-semibold text-gray-700">{{ getBankInitials(account.bank_name) }}</span>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <div class="text-xs text-gray-700 dark:text-gray-300 font-semibold">{{ account.bank_name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 break-all">IBAN</div>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                            </template>
                            <template v-else>
                                <!-- Geriye dönük: tekil bank_info varsa göster -->
                                <button v-if="props.user.bank_info && props.user.bank_info.iban"
                                    @click="openBankModal(props.user.bank_info)"
                                    class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                                    <div class="flex flex-col items-center">
                                        <div class="flex items-center justify-center p-0 rounded-xl transition-all duration-300">
                                            <template v-if="props.user.bank_info.bank_name">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-semibold text-gray-700">{{ getBankInitials(props.user.bank_info.bank_name) }}</span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mt-2 text-center">
                                            <div class="text-xs text-gray-700 dark:text-gray-300 font-semibold">{{ props.user.bank_info.bank_name || 'Banka' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 break-all">IBAN</div>
                                        </div>
                                    </div>
                                </button>
                            </template>

                            <!-- Dökümanlar İkonu -->
                            <button v-if="props.user.documents && props.user.documents.length > 0"
                                @click="openDocumentsModal"
                                class="group flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 rounded-2xl hover:shadow-lg transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600/30">
                                <div class="p-3 rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 bg-gradient-to-r from-orange-500 to-red-600">
                                    <FileText class="w-5 h-5 text-white" />
                                </div>
                                <span class="text-xs text-gray-700 dark:text-gray-300 mt-2.5 text-center font-semibold leading-tight">
                                    Dökümanlar
                                </span>
                            </button>
                        </div>
                    </div>
                    <!-- Company Modal -->
                    <transition name="modal">
                        <div v-if="showCompanyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fadeIn overflow-y-auto">
                            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeCompanyModal"></div>
                            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden max-w-md w-full z-10 transform animate-scaleIn border border-gray-200 dark:border-gray-700">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                            Firma Bilgisi
                                        </h3>
                                        <button @click="closeCompanyModal" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                            <X class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                        </button>
                                    </div>

                                    <div v-if="props.user.company_info" class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Firma Adı</div>
                                            <div class="font-semibold">{{ props.user.company_info.company_name || '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Unvan</div>
                                            <div class="font-semibold">{{ props.user.company_info.company_title || '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Vergi Dairesi</div>
                                            <div class="font-semibold">{{ props.user.company_info.tax_office || '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Vergi No</div>
                                            <div class="font-semibold">{{ props.user.company_info.tax_number || '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Adres</div>
                                            <div class="font-semibold break-words">{{ props.user.company_info.company_address || '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- Bank Modal -->
                    <transition name="modal">
                        <div v-if="showBankModal && selectedBank" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fadeIn overflow-y-auto">
                            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeBankModal"></div>
                            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden max-w-md w-full z-10 transform animate-scaleIn border border-gray-200 dark:border-gray-700">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                            Banka Bilgisi
                                        </h3>
                                        <button @click="closeBankModal" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                            <X class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                        </button>
                                    </div>

                                    <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                        <!-- Banka Logosu -->
                                        <div v-if="selectedBank.bank_logo_url" class="flex justify-center mb-4">
                                            <img :src="selectedBank.bank_logo_url" :alt="selectedBank.bank_name" class="w-20 h-20 object-contain">
                                        </div>

                                        <!-- Banka Adı -->
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Banka Adı</div>
                                            <div class="font-semibold">{{ selectedBank.bank_name || '-' }}</div>
                                        </div>

                                        <!-- IBAN -->
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">IBAN</div>
                                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 border border-gray-200 dark:border-gray-600">
                                                <div class="font-mono text-sm break-all">{{ selectedBank.iban || '-' }}</div>
                                                <button v-if="selectedBank.iban" @click="copyIban(selectedBank.iban)"
                                                    class="ml-2 p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors flex-shrink-0">
                                                    <Check v-if="copiedIban === selectedBank.iban" class="w-4 h-4 text-green-600" />
                                                    <Copy v-else class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Hesap Sahibi -->
                                        <div v-if="selectedBank.account_holder">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Hesap Sahibi</div>
                                            <div class="font-semibold">{{ selectedBank.account_holder }}</div>
                                        </div>

                                        <!-- Şube -->
                                        <div v-if="selectedBank.branch || selectedBank.bank_branch">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Şube</div>
                                            <div class="font-semibold">{{ selectedBank.branch || selectedBank.bank_branch || '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- QR Code Modal -->
                    <transition name="modal">
                        <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fadeIn overflow-y-auto">
                            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeQrModal"></div>
                            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden max-w-md w-full z-10 transform animate-scaleIn border border-gray-200 dark:border-gray-700">
                                <div class="p-8">
                                    <div class="flex justify-between items-center mb-6">
                                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                            QR Kod
                                        </h3>
                                        <button @click="closeQrModal"
                                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                            <X class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                        </button>
                                    </div>
                                    <div class="flex justify-center mb-6 p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl">
                                        <img v-if="qrCodeDataUrl" :src="qrCodeDataUrl" alt="QR Kodu"
                                            class="w-64 h-64 object-contain">
                                    </div>
                                    <p class="text-center text-gray-600 dark:text-gray-400 text-sm mb-6">
                                        Bu QR kodu taratarak profilimi görüntüleyebilirsiniz
                                    </p>
                                    <div class="flex flex-col gap-3">
                                        <button @click="downloadQrCode"
                                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold">
                                            <Download class="w-5 h-5 mr-2" />
                                            QR Kodu İndir
                                        </button>
                                        <button @click="shareQrCode"
                                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold">
                                            <Share2 class="w-5 h-5 mr-2" />
                                            QR Kodu Paylaş
                                        </button>
                                        <button @click="downloadVCard"
                                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg font-semibold">
                                            <Download class="w-5 h-5 mr-2" />
                                            VCard'ı İndir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- Documents Modal -->
                    <transition name="modal">
                        <div v-if="showDocumentsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fadeIn overflow-y-auto">
                            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeDocumentsModal"></div>
                            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden max-w-md w-full z-10 transform animate-scaleIn border border-gray-200 dark:border-gray-700">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                            Dökümanlar
                                        </h3>
                                        <button @click="closeDocumentsModal" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                            <X class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                        </button>
                                    </div>

                                    <div v-if="props.user.documents && props.user.documents.length > 0" class="space-y-3">
                                        <div v-for="(doc, index) in props.user.documents" :key="index"
                                            class="flex items-center justify-between p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-300">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <div class="p-2 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg">
                                                    <FileText class="w-5 h-5 text-white" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-semibold text-gray-900 dark:text-white truncate">{{ doc.title }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Döküman</div>
                                                </div>
                                            </div>
                                            <a v-if="doc.file_url" :href="doc.file_url" target="_blank"
                                                class="ml-2 p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex-shrink-0">
                                                <Download class="w-4 h-4" />
                                            </a>
                                        </div>
                                    </div>

                                    <div v-else class="text-center text-gray-500 dark:text-gray-400 py-8">
                                        Henüz döküman eklenmemiş
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- Footer -->
                    <div class="text-center mt-12 pt-8 border-t border-gray-200 dark:border-gray-700/50">
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                            {{ formatDisplayName(props.user.name) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}

.animate-scaleIn {
    animation: scaleIn 0.3s ease-out;
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
