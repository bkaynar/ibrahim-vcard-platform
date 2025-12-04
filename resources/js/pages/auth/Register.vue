<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle, CheckCircle, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const usernameStatus = ref<'idle' | 'checking' | 'available' | 'taken'>('idle');
const usernameMessage = ref('');
let debounceTimeout: NodeJS.Timeout;

const checkUsername = async (username: string) => {
    if (!username || username.length < 3) {
        usernameStatus.value = 'idle';
        usernameMessage.value = '';
        return;
    }

    usernameStatus.value = 'checking';

    try {
        const response = await fetch(route('check.username'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ username }),
        });

        const data = await response.json();

        usernameStatus.value = data.available ? 'available' : 'taken';
        usernameMessage.value = data.message;
    } catch (error) {
        usernameStatus.value = 'idle';
        usernameMessage.value = '';
    }
};

watch(() => form.username, (newUsername) => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        checkUsername(newUsername);
    }, 500);
});

const submit = () => {
   //hiçbişey yapma
    return null;
}
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">

        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name"
                        v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <div class="relative">
                        <Input id="username" type="text" required :tabindex="2" autocomplete="username"
                            v-model="form.username" placeholder="Username"
                            :class="{
                                'border-green-500': usernameStatus === 'available',
                                'border-red-500': usernameStatus === 'taken'
                            }" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <LoaderCircle v-if="usernameStatus === 'checking'" class="h-4 w-4 animate-spin text-gray-400" />
                            <CheckCircle v-else-if="usernameStatus === 'available'" class="h-4 w-4 text-green-500" />
                            <XCircle v-else-if="usernameStatus === 'taken'" class="h-4 w-4 text-red-500" />
                        </div>
                    </div>
                    <div v-if="usernameMessage" :class="{
                        'text-green-600': usernameStatus === 'available',
                        'text-red-600': usernameStatus === 'taken'
                    }" class="text-sm">
                        {{ usernameMessage }}
                    </div>
                    <InputError :message="form.errors.username" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="3" autocomplete="email" v-model="form.email"
                        placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" required :tabindex="4" autocomplete="off" readonly
                        @focus="($event.target as HTMLInputElement).removeAttribute('readonly')"
                        @blur="($event.target as HTMLInputElement).setAttribute('readonly', '')" v-model="form.password"
                        placeholder="Password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input id="password_confirmation" type="password" required :tabindex="5" autocomplete="off" readonly
                        @focus="($event.target as HTMLInputElement).removeAttribute('readonly')"
                        @blur="($event.target as HTMLInputElement).setAttribute('readonly', '')"
                        v-model="form.password_confirmation" placeholder="Confirm password" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="6"
                    :disabled="form.processing || usernameStatus === 'taken' || usernameStatus === 'checking'">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="7">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
