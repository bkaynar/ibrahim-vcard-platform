<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { QrCode, ArrowRight, Check, Zap, Shield, Users, Globe, TrendingUp, Smartphone } from 'lucide-vue-next';

const page = usePage();
const site = computed(() => page.props.site as any);

const scrollY = ref(0);

const handleScroll = () => {
    scrollY.value = window.scrollY;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const features = [
    {
        title: 'QR Code Intelligence',
        description: 'Yapay zeka destekli QR kod sistemi. Anında paylaşım, gerçek zamanlı analitik.',
    },
    {
        title: 'Enterprise Security',
        description: 'Kurumsal seviye güvenlik protokolleri. SSL, 2FA, ISO 27001 sertifikalı.',
    },
    {
        title: 'Unified Platform',
        description: 'Tüm sosyal medya, firma ve ödeme bilgileriniz tek ekosistemde.',
    }
];

const stats = [
    { value: '150K+', label: 'Global Users' },
    { value: '99.99%', label: 'Uptime SLA' },
    { value: '24/7', label: 'Support' },
    { value: '256-bit', label: 'Encryption' }
];
</script>

<template>
    <Head :title="site.title">
        <meta name="description" :content="site.description" />
        <meta name="keywords" :content="site.keywords" />
        <meta name="language" :content="site.language" />
    </Head>

    <div class="relative bg-[#fafafa] dark:bg-[#0a0a0a] antialiased">
        <!-- Subtle Grid Pattern -->
        <div class="fixed inset-0 opacity-[0.02] pointer-events-none"
             style="background-image: linear-gradient(#000 1px, transparent 1px), linear-gradient(90deg, #000 1px, transparent 1px); background-size: 100px 100px;"></div>

        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-white/70 dark:bg-black/70 border-b border-neutral-200/20 dark:border-neutral-800/20">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12">
                <div class="flex items-center justify-between h-20">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-neutral-900 dark:bg-white rounded-lg flex items-center justify-center">
                            <QrCode class="w-4.5 h-4.5 text-white dark:text-black" strokeWidth={2.5} />
                        </div>
                        <span class="text-[15px] font-medium tracking-[-0.01em] text-neutral-900 dark:text-white">
                            {{ site.title }}
                        </span>
                    </div>
                    <div class="hidden lg:flex items-center space-x-10">
                        <a href="#features" class="text-[13px] font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">Features</a>
                        <a href="#enterprise" class="text-[13px] font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">Enterprise</a>
                        <a href="#security" class="text-[13px] font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">Security</a>
                    </div>
                    <div class="flex items-center space-x-3">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="px-5 py-2 bg-neutral-900 dark:bg-white text-white dark:text-black text-[13px] font-medium rounded-lg hover:opacity-90 transition-opacity">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="hidden sm:inline-block px-5 py-2 text-[13px] font-medium text-neutral-900 dark:text-white hover:text-neutral-600 dark:hover:text-neutral-400 transition-colors">
                                Sign in
                            </Link>
                            <Link :href="route('login')"
                                class="px-5 py-2 bg-neutral-900 dark:bg-white text-white dark:text-black text-[13px] font-medium rounded-lg hover:opacity-90 transition-opacity">
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <!-- Gradient Orb -->
            <div class="absolute top-40 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-neutral-900/5 dark:bg-white/5 rounded-full blur-[150px]"></div>

            <div class="relative max-w-[1400px] mx-auto px-8 lg:px-12">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Main Headline -->
                    <h1 class="text-[56px] leading-[1.1] lg:text-[84px] font-[650] tracking-[-0.03em] text-neutral-900 dark:text-white mb-8">
                        Enterprise digital
                        <br>
                        business cards.
                    </h1>

                    <!-- Subheadline -->
                    <p class="text-[19px] lg:text-[21px] font-normal text-neutral-600 dark:text-neutral-400 mb-12 max-w-2xl mx-auto leading-[1.6] tracking-[-0.01em]">
                        Ölçeklenebilir, güvenli, profesyonel. Kurumsal düzeyde dijital kartvizit
                        altyapısı ile markanızı güçlendirin.
                    </p>

                    <!-- CTA -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                        <Link :href="route('login')"
                            class="group inline-flex items-center justify-center px-7 py-3.5 bg-neutral-900 dark:bg-white text-white dark:text-black text-[15px] font-medium rounded-xl hover:opacity-90 transition-all min-w-[180px]">
                            Start Free Trial
                            <ArrowRight class="ml-2 w-4 h-4 group-hover:translate-x-0.5 transition-transform" strokeWidth={2.5} />
                        </Link>
                        <button class="inline-flex items-center justify-center px-7 py-3.5 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white text-[15px] font-medium rounded-xl border border-neutral-200 dark:border-neutral-800 hover:border-neutral-300 dark:hover:border-neutral-700 transition-all min-w-[180px]">
                            Book a Demo
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 pt-12 border-t border-neutral-200 dark:border-neutral-800">
                        <div v-for="stat in stats" :key="stat.label" class="text-center">
                            <div class="text-[32px] font-[650] tracking-[-0.02em] text-neutral-900 dark:text-white mb-1">{{ stat.value }}</div>
                            <div class="text-[13px] font-medium text-neutral-500 dark:text-neutral-500 uppercase tracking-wider">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Image Section -->
        <section class="relative py-20 lg:py-32">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12">
                <!-- Large Product Image -->
                <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-900 dark:to-neutral-800 p-12 lg:p-20"
                     :style="{ transform: `translateY(${scrollY * 0.1}px)` }">
                    <!-- Phone Mockup - More Professional -->
                    <div class="max-w-sm mx-auto">
                        <div class="relative aspect-[9/19.5] bg-neutral-900 rounded-[3.5rem] p-3 shadow-2xl shadow-neutral-900/30 dark:shadow-black/50">
                            <div class="w-full h-full bg-white dark:bg-neutral-950 rounded-[3rem] overflow-hidden relative">
                                <!-- Status Bar -->
                                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-7 bg-neutral-900 rounded-b-3xl"></div>

                                <!-- Content -->
                                <div class="pt-12 px-8 pb-8 h-full flex flex-col">
                                    <!-- Profile -->
                                    <div class="text-center mb-10">
                                        <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-neutral-900 dark:bg-white flex items-center justify-center">
                                            <span class="text-2xl font-semibold text-white dark:text-black">BK</span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-1">Burak Kaynar</h3>
                                        <p class="text-sm text-neutral-500">Chief Technology Officer</p>
                                    </div>

                                    <!-- Contact Grid -->
                                    <div class="space-y-3 mb-8">
                                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-4 border border-neutral-200 dark:border-neutral-800">
                                            <div class="text-[11px] font-medium text-neutral-500 mb-1 uppercase tracking-wider">Email</div>
                                            <div class="text-sm font-medium text-neutral-900 dark:text-white">burak@company.com</div>
                                        </div>
                                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-4 border border-neutral-200 dark:border-neutral-800">
                                            <div class="text-[11px] font-medium text-neutral-500 mb-1 uppercase tracking-wider">Phone</div>
                                            <div class="text-sm font-medium text-neutral-900 dark:text-white">+90 555 123 4567</div>
                                        </div>
                                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-4 border border-neutral-200 dark:border-neutral-800">
                                            <div class="text-[11px] font-medium text-neutral-500 mb-1 uppercase tracking-wider">Company</div>
                                            <div class="text-sm font-medium text-neutral-900 dark:text-white">TechCorp International</div>
                                        </div>
                                    </div>

                                    <!-- QR Code -->
                                    <div class="mt-auto bg-neutral-900 dark:bg-white rounded-2xl p-6 flex items-center justify-center">
                                        <QrCode class="w-20 h-20 text-white dark:text-black" strokeWidth={1.5} />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Info Cards -->
                    <div class="hidden lg:block absolute top-20 left-20 bg-white dark:bg-neutral-900 rounded-2xl p-6 shadow-xl border border-neutral-200 dark:border-neutral-800 max-w-[280px]">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-neutral-100 dark:bg-neutral-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                <Shield class="w-5 h-5 text-neutral-900 dark:text-white" strokeWidth={2} />
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral-900 dark:text-white mb-1">Enterprise Security</div>
                                <div class="text-xs text-neutral-500 leading-relaxed">256-bit encryption with ISO compliance</div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block absolute bottom-20 right-20 bg-white dark:bg-neutral-900 rounded-2xl p-6 shadow-xl border border-neutral-200 dark:border-neutral-800 max-w-[280px]">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-neutral-100 dark:bg-neutral-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                <TrendingUp class="w-5 h-5 text-neutral-900 dark:text-white" strokeWidth={2} />
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral-900 dark:text-white mb-1">Real-time Analytics</div>
                                <div class="text-xs text-neutral-500 leading-relaxed">Track engagement and measure impact</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative py-20 lg:py-32 bg-white dark:bg-black">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12">
                <div class="text-center mb-20">
                    <h2 class="text-[48px] lg:text-[64px] font-[650] tracking-[-0.03em] text-neutral-900 dark:text-white mb-6 leading-[1.1]">
                        Built for modern
                        <br>
                        enterprises.
                    </h2>
                    <p class="text-[19px] text-neutral-600 dark:text-neutral-400 max-w-2xl mx-auto tracking-[-0.01em]">
                        Kurumsal ihtiyaçlar için tasarlanmış, ölçeklenebilir altyapı.
                    </p>
                </div>

                <div class="grid lg:grid-cols-3 gap-6">
                    <div v-for="(feature, index) in features" :key="index"
                        class="group relative bg-neutral-50 dark:bg-neutral-900 rounded-3xl p-10 border border-neutral-200 dark:border-neutral-800 hover:border-neutral-300 dark:hover:border-neutral-700 transition-all">
                        <div class="w-12 h-12 bg-neutral-900 dark:bg-white rounded-xl flex items-center justify-center mb-8 group-hover:scale-105 transition-transform">
                            <Check class="w-6 h-6 text-white dark:text-black" strokeWidth={2.5} />
                        </div>
                        <h3 class="text-[22px] font-semibold text-neutral-900 dark:text-white mb-3 tracking-[-0.01em]">{{ feature.title }}</h3>
                        <p class="text-[15px] text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enterprise Section -->
        <section id="enterprise" class="relative py-20 lg:py-32">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-[48px] lg:text-[56px] font-[650] tracking-[-0.03em] text-neutral-900 dark:text-white mb-6 leading-[1.1]">
                            Kurumsal çözümler.
                            <br>
                            Küresel ölçek.
                        </h2>
                        <p class="text-[17px] text-neutral-600 dark:text-neutral-400 mb-8 leading-relaxed">
                            Fortune 500 şirketlerinin güvendiği altyapı. Sınırsız kullanıcı,
                            merkezi yönetim, detaylı analitik ve 7/24 premium destek.
                        </p>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center space-x-3">
                                <div class="w-5 h-5 bg-neutral-900 dark:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <Check class="w-3 h-3 text-white dark:text-black" strokeWidth={3} />
                                </div>
                                <span class="text-[15px] text-neutral-700 dark:text-neutral-300">Single Sign-On (SSO) integration</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-5 h-5 bg-neutral-900 dark:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <Check class="w-3 h-3 text-white dark:text-black" strokeWidth={3} />
                                </div>
                                <span class="text-[15px] text-neutral-700 dark:text-neutral-300">Advanced user management & roles</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-5 h-5 bg-neutral-900 dark:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <Check class="w-3 h-3 text-white dark:text-black" strokeWidth={3} />
                                </div>
                                <span class="text-[15px] text-neutral-700 dark:text-neutral-300">Custom branding & white-label options</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-5 h-5 bg-neutral-900 dark:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                    <Check class="w-3 h-3 text-white dark:text-black" strokeWidth={3} />
                                </div>
                                <span class="text-[15px] text-neutral-700 dark:text-neutral-300">99.99% uptime SLA guarantee</span>
                            </li>
                        </ul>
                        <Link :href="route('login')"
                            class="inline-flex items-center px-6 py-3 bg-neutral-900 dark:bg-white text-white dark:text-black text-[14px] font-medium rounded-xl hover:opacity-90 transition-opacity">
                            Contact Sales
                            <ArrowRight class="ml-2 w-4 h-4" strokeWidth={2.5} />
                        </Link>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-8 border border-neutral-200 dark:border-neutral-800">
                            <Users class="w-8 h-8 text-neutral-900 dark:text-white mb-4" strokeWidth={1.5} />
                            <div class="text-[32px] font-[650] text-neutral-900 dark:text-white mb-2">150K+</div>
                            <div class="text-[13px] text-neutral-500 font-medium">Active Users</div>
                        </div>
                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-8 border border-neutral-200 dark:border-neutral-800">
                            <Globe class="w-8 h-8 text-neutral-900 dark:text-white mb-4" strokeWidth={1.5} />
                            <div class="text-[32px] font-[650] text-neutral-900 dark:text-white mb-2">120+</div>
                            <div class="text-[13px] text-neutral-500 font-medium">Countries</div>
                        </div>
                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-8 border border-neutral-200 dark:border-neutral-800">
                            <Smartphone class="w-8 h-8 text-neutral-900 dark:text-white mb-4" strokeWidth={1.5} />
                            <div class="text-[32px] font-[650] text-neutral-900 dark:text-white mb-2">5M+</div>
                            <div class="text-[13px] text-neutral-500 font-medium">Scans/Month</div>
                        </div>
                        <div class="bg-neutral-100 dark:bg-neutral-900 rounded-2xl p-8 border border-neutral-200 dark:border-neutral-800">
                            <Zap class="w-8 h-8 text-neutral-900 dark:text-white mb-4" strokeWidth={1.5} />
                            <div class="text-[32px] font-[650] text-neutral-900 dark:text-white mb-2">< 50ms</div>
                            <div class="text-[13px] text-neutral-500 font-medium">Response Time</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Security Section -->
        <section id="security" class="relative py-20 lg:py-32 bg-neutral-900 dark:bg-white">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12 text-center">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-[48px] lg:text-[64px] font-[650] tracking-[-0.03em] text-white dark:text-neutral-900 mb-6 leading-[1.1]">
                        Security first.
                        <br>
                        Always.
                    </h2>
                    <p class="text-[17px] text-neutral-400 dark:text-neutral-600 mb-12 leading-relaxed">
                        Verileriniz askeri seviye şifreleme ile korunur. SOC 2 Type II, ISO 27001
                        ve GDPR uyumlu altyapı ile güvendesiniz.
                    </p>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-neutral-800 dark:bg-neutral-100 rounded-2xl p-6 border border-neutral-700 dark:border-neutral-200">
                            <div class="text-[15px] font-semibold text-white dark:text-neutral-900 mb-1">SOC 2</div>
                            <div class="text-[12px] text-neutral-400 dark:text-neutral-600">Type II Certified</div>
                        </div>
                        <div class="bg-neutral-800 dark:bg-neutral-100 rounded-2xl p-6 border border-neutral-700 dark:border-neutral-200">
                            <div class="text-[15px] font-semibold text-white dark:text-neutral-900 mb-1">ISO 27001</div>
                            <div class="text-[12px] text-neutral-400 dark:text-neutral-600">Certified</div>
                        </div>
                        <div class="bg-neutral-800 dark:bg-neutral-100 rounded-2xl p-6 border border-neutral-700 dark:border-neutral-200">
                            <div class="text-[15px] font-semibold text-white dark:text-neutral-900 mb-1">GDPR</div>
                            <div class="text-[12px] text-neutral-400 dark:text-neutral-600">Compliant</div>
                        </div>
                        <div class="bg-neutral-800 dark:bg-neutral-100 rounded-2xl p-6 border border-neutral-700 dark:border-neutral-200">
                            <div class="text-[15px] font-semibold text-white dark:text-neutral-900 mb-1">256-bit</div>
                            <div class="text-[12px] text-neutral-400 dark:text-neutral-600">Encryption</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="relative py-32">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12 text-center">
                <h2 class="text-[56px] lg:text-[72px] font-[650] tracking-[-0.03em] text-neutral-900 dark:text-white mb-8 leading-[1.1]">
                    Ready to transform
                    <br>
                    your business?
                </h2>
                <p class="text-[19px] text-neutral-600 dark:text-neutral-400 mb-12 max-w-2xl mx-auto">
                    Binlerce global şirketin tercih ettiği platforma siz de katılın.
                </p>
                <Link :href="route('login')"
                    class="inline-flex items-center px-8 py-4 bg-neutral-900 dark:bg-white text-white dark:text-black text-[15px] font-medium rounded-xl hover:opacity-90 transition-opacity">
                    Start Your Free Trial
                    <ArrowRight class="ml-2 w-4 h-4" strokeWidth={2.5} />
                </Link>
                <p class="text-[13px] text-neutral-500 mt-6">
                    No credit card required · 14-day free trial · Cancel anytime
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative border-t border-neutral-200 dark:border-neutral-800 py-12 bg-white dark:bg-black">
            <div class="max-w-[1400px] mx-auto px-8 lg:px-12">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center space-x-3 mb-6 md:mb-0">
                        <div class="w-8 h-8 bg-neutral-900 dark:bg-white rounded-lg flex items-center justify-center">
                            <QrCode class="w-4.5 h-4.5 text-white dark:text-black" strokeWidth={2.5} />
                        </div>
                        <span class="text-[15px] font-medium text-neutral-900 dark:text-white">{{ site.title }}</span>
                    </div>
                    <div class="text-center md:text-right">
                        <p class="text-[13px] text-neutral-500 mb-1">© {{ new Date().getFullYear() }} {{ site.title }}. All rights reserved.</p>
                        <p class="text-[12px] text-neutral-400">Enterprise-grade digital business card platform</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}

/* Use system font stack for premium look */
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
