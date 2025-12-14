<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const site = computed(() => page.props.site as any)

// Baro iletişim bilgileri
const baroPhone = ref('05013512121')
const whatsappNumber = ref('905013512121') // WhatsApp numarası (başında + olmadan)
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
            <a :href="`tel:${baroPhone}`" class="inline-flex items-center gap-2 text-red-700 dark:text-red-400 font-medium hover:underline">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              {{ baroPhone }}
            </a>
          </p>
        </div>
      </section>
    </main>

    <footer class="relative border-t border-neutral-200 dark:border-neutral-800 py-8 bg-white dark:bg-black mt-12">
      <div class="max-w-5xl mx-auto px-6 text-center text-sm text-neutral-500">
        © {{ new Date().getFullYear() }} Adana Barosu. Tüm hakları saklıdır.
      </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a
      :href="`https://wa.me/${whatsappNumber}`"
      target="_blank"
      rel="noopener noreferrer"
      class="whatsapp-float"
      aria-label="WhatsApp ile iletişime geç"
    >
      <svg class="whatsapp-icon" viewBox="0 0 32 32" fill="currentColor">
        <path d="M16 0c-8.837 0-16 7.163-16 16 0 2.825 0.737 5.607 2.137 8.048l-2.137 7.952 7.933-2.127c2.42 1.37 5.173 2.127 8.067 2.127 8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 29.467c-2.482 0-4.908-0.646-7.07-1.87l-0.507-0.292-5.155 1.382 1.389-5.165-0.321-0.527c-1.294-2.144-1.977-4.623-1.977-7.195 0-7.444 6.056-13.5 13.5-13.5s13.5 6.056 13.5 13.5-6.056 13.667-13.359 13.667zM21.947 18.507c-0.282-0.141-1.673-0.827-1.931-0.921-0.259-0.094-0.448-0.141-0.636 0.141s-0.731 0.921-0.896 1.109c-0.165 0.188-0.329 0.212-0.612 0.071s-1.194-0.44-2.274-1.404c-0.841-0.75-1.408-1.677-1.573-1.959s-0.018-0.434 0.124-0.574c0.127-0.127 0.282-0.329 0.423-0.494s0.188-0.282 0.282-0.47c0.094-0.188 0.047-0.353-0.024-0.494s-0.636-1.532-0.871-2.098c-0.229-0.551-0.462-0.476-0.636-0.485-0.165-0.009-0.353-0.009-0.541-0.009s-0.494 0.071-0.753 0.353c-0.259 0.282-0.99 0.968-0.99 2.361s1.014 2.739 1.155 2.927c0.141 0.188 1.993 3.044 4.829 4.269 0.675 0.291 1.202 0.465 1.613 0.595 0.678 0.215 1.295 0.185 1.782 0.112 0.544-0.081 1.673-0.684 1.908-1.344s0.235-1.226 0.165-1.344c-0.071-0.118-0.259-0.188-0.541-0.329z"/>
      </svg>
      <div class="whatsapp-ring whatsapp-ring-1"></div>
      <div class="whatsapp-ring whatsapp-ring-2"></div>
    </a>
  </div>
</template>

<style scoped>
html {
  scroll-behavior: smooth;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* WhatsApp Floating Button */
.whatsapp-float {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 60px;
  height: 60px;
  background: #25d366;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
  z-index: 1000;
  transition: all 0.3s ease;
  cursor: pointer;
}

.whatsapp-float:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
}

.whatsapp-icon {
  width: 32px;
  height: 32px;
}

/* Animated rings */
.whatsapp-ring {
  position: absolute;
  border: 2px solid #25d366;
  border-radius: 50%;
  animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
}

.whatsapp-ring-1 {
  width: 60px;
  height: 60px;
}

.whatsapp-ring-2 {
  width: 60px;
  height: 60px;
  animation-delay: 0.75s;
}

@keyframes pulse-ring {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(1.8);
    opacity: 0;
  }
}

/* Mobile responsive */
@media (max-width: 640px) {
  .whatsapp-float {
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
  }

  .whatsapp-icon {
    width: 28px;
    height: 28px;
  }

  .whatsapp-ring-1,
  .whatsapp-ring-2 {
    width: 50px;
    height: 50px;
  }
}
</style>
