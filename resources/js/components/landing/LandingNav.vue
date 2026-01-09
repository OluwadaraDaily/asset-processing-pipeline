<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { Zap } from 'lucide-vue-next';

defineProps<{
    auth?: any;
}>();
</script>

<template>
    <nav class="border-b border-[#3a4a3e] bg-white/80 backdrop-blur-sm dark:bg-gray-900/80">
        <div class="container mx-auto flex items-center justify-between px-4 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-[#6B8E23] to-[#556B2F]">
                    <Zap class="h-6 w-6 text-white" />
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white">TransformX</span>
            </div>
            <div class="flex items-center gap-4">
                <a
                    href="#"
                    class="text-gray-600 transition-colors hover:text-[#6B8E23] dark:text-gray-400 dark:hover:text-white"
                >
                    Home
                </a>
                <a
                    href="#features"
                    class="text-gray-600 transition-colors hover:text-[#6B8E23] dark:text-gray-400 dark:hover:text-white"
                >
                    Features
                </a>
                <a
                    href="#products"
                    class="text-gray-600 transition-colors hover:text-[#6B8E23] dark:text-gray-400 dark:hover:text-white"
                >
                    Products
                </a>
                <template v-if="auth?.user">
                    <Link
                        href="/image-resizer"
                        as="button"
                    >
                        <Button class="bg-[#6B8E23] hover:bg-[#556B2F]">Go to App</Button>
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="text-gray-600 transition-colors hover:text-[#6B8E23] dark:text-gray-400 dark:hover:text-white"
                    >
                        Sign In
                    </Link>
                    <Link
                        href="/register"
                        as="button"
                    >
                        <Button class="bg-[#6B8E23] hover:bg-[#556B2F]">Get Started</Button>
                    </Link>
                </template>
            </div>
        </div>
    </nav>
</template>
