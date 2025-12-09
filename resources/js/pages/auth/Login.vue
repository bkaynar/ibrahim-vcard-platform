<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
  status?: string;
  canResetPassword: boolean;
}>();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Giriş Yap" />

  <div class="min-h-screen flex flex-col md:flex-row">
    <!-- Sol taraf: Animasyon -->
    <div class="hidden md:flex md:w-1/2 items-center justify-center bg-gray-100 p-6">
      <div class="text-center">
        <div class="mt-4 text-2xl font-bold text-gray-800 dark:text-gray-100">Adana Barosu</div>
      </div>
    </div>

    <!-- Sağ taraf: Login formu -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-6">
      <div class="w-full max-w-md space-y-6">
        <h2 class="text-3xl font-bold text-center">Adana Barosu NFC Kartvizit Sistemi</h2>
        <p class="text-sm text-muted-foreground text-center">Devam etmek için bilgilerinizi giriniz</p>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- E-posta -->
          <div>
            <Label for="email">E-posta Adresi</Label>
            <Input
              id="email"
              type="email"
              v-model="form.email"
              placeholder="ornek@eposta.com"
              required
              autofocus
              autocomplete="email"
            />
            <InputError :message="form.errors.email" />
          </div>

          <!-- Şifre -->
          <div>
            <Label for="password">Şifre</Label>
            <Input
              id="password"
              type="password"
              v-model="form.password"
              placeholder="••••••••"
              required
              autocomplete="current-password"
            />
            <InputError :message="form.errors.password" />
          </div>

          <!-- Beni Hatırla + Şifreyi Unuttum -->
          <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2">
              <input type="checkbox" v-model="form.remember" class="rounded border-gray-300" />
              <span>Beni hatırla</span>
            </label>

            <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-600">
              Şifremi unuttum
            </TextLink>
          </div>

          <!-- Giriş Butonu -->
          <Button type="submit" class="w-full" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
            Giriş Yap
          </Button>
        </form>
      </div>
    </div>
  </div>
</template>
