<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Link, useForm } from '@inertiajs/vue3';

defineProps<{
    errors?: Record<string, string>;
}>();

const form = useForm({
    email: '',
});

function submit() {
    form.post('/login', {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 dark:bg-gray-900">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Welcome back</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Sign in to your account with just your email</p>
            </div>

            <div class="mt-8 rounded-lg bg-white p-8 shadow dark:bg-gray-800">
                <form
                    @submit.prevent="submit"
                    class="space-y-6"
                >
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            placeholder="your.email@example.com"
                            :class="{ 'border-red-500': errors?.email }"
                        />
                        <p
                            v-if="errors?.email"
                            class="text-sm text-red-600"
                        >
                            {{ errors.email }}
                        </p>
                    </div>

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Signing in...' : 'Sign in' }}
                    </Button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <Link
                            href="/register"
                            class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
                        >
                            Create one
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
