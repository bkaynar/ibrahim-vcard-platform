<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const site = computed(() => page.props.site as any)

// Baro iletişim bilgileri (placeholder). Gerçek değerleri siz güncelleyeceksiniz.
const baroEmail = ref('baro@adanabarosu.org.tr')
const baroPhone = ref('+905XXXXXXXXX')
</script>

<template>
  <Head :title="'Adana Barosu'">
    <meta name="description" :content="site.description" />
  </Head>

  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <header class="border-b border-red-100 dark:border-red-900 bg-white/60 dark:bg-black/60 backdrop-blur sticky top-0 z-40">
      <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="/main/adanabarosu.png" alt="Adana Barosu" class="w-10 h-10 object-contain" />
          <div class="text-lg font-semibold text-red-700">Adana Barosu</div>
        </div>
        <div class="flex items-center gap-3">
          <Link :href="route('login')" class="px-4 py-2 rounded-md bg-red-700 hover:bg-red-800 text-white text-sm">Giriş</Link>
        </div>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-16">
      <section class="text-center mb-12">
        <img src="/main/adanabarosu.png" alt="Adana Barosu" class="w-32 h-auto mx-auto mb-6" />
        <h1 class="text-3xl sm:text-4xl font-bold mb-4 text-red-700 dark:text-red-400">Adana Barosu Dijital Kart Başvurusu</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
          Adana Barosu'na bağlı avukatlar için dijital kimlik ve kart başvuruları Baro tarafından yönetilmektedir. Bu sayfa,
          başvuru hakkında temel bilgiler içerir. Başvuru, doğrulama ve onay süreçleri doğrudan Baro tarafından yürütülür.
        </p>
      </section>

      <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold mb-3">Kimler Başvurabilir?</h2>
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Sadece Adana Barosu'na kayıtlı ve ruhsatlı avukatlar başvurabilir.</p>
          <ul class="text-sm space-y-2 list-inside">
            <li>• Baro üyesi olmak</li>
            <li>• Geçerli ruhsat bilgileri</li>
            <li>• Başvuru sırasında istenen belgeleri sağlamak</li>
          </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold mb-3">Başvuru Süreci</h2>
          <ol class="text-sm list-decimal list-inside space-y-2">
            <li>Başvuru formunu doldurun ve gerekli belgeleri yükleyin.</li>
            <li>Baro doğrulama ve onay sürecini yürütür.</li>
            <li>Onay sonrası dijital kartınız yayımlanır ve yönetim panelinden erişilir.</li>
          </ol>
        </div>
      </section>

      <section class="mb-12">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 text-center">
          <h3 class="text-lg font-semibold mb-2">İletişim</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">Daha fazla bilgi için Baro ile iletişime geçin:</p>
          <p class="mt-3 text-sm">
            <a :href="`mailto:${baroEmail}`" class="text-red-700 dark:text-red-400 font-medium">{{ baroEmail }}</a>
            <span class="mx-2">·</span>
            <a :href="`tel:${baroPhone}`" class="text-red-700 dark:text-red-400 font-medium">{{ baroPhone }}</a>
          </p>
        </div>
      </section>

      <section class="text-center">
        <Link :href="route('login')" class="inline-flex items-center px-6 py-3 bg-red-700 hover:bg-red-800 text-white rounded-lg">Başvuruya Başla</Link>
      </section>
    </main>

    <footer class="relative border-t border-neutral-200 dark:border-neutral-800 py-8 bg-white dark:bg-black mt-12">
      <div class="max-w-5xl mx-auto px-6 text-center text-sm text-neutral-500">
        © {{ new Date().getFullYear() }} Adana Barosu. Tüm hakları saklıdır.
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
