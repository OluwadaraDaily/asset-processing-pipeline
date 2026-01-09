<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import type { Component } from 'vue';

defineProps<{
    icon: Component;
    title: string;
    description: string;
    features: string[];
    available: boolean;
    href?: string;
}>();
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-2xl border bg-white p-8 shadow-lg transition-all duration-300 dark:bg-gray-800"
        :class="[available ? 'border-[#3a4a3e] hover:-translate-y-2 hover:shadow-2xl' : 'border-gray-200 opacity-75 dark:border-gray-700']"
    >
        <div
            class="absolute top-4 right-4 rounded-full px-3 py-1 text-xs font-semibold text-white"
            :class="available ? 'bg-[#3a4a3e]' : 'bg-gray-500'"
        >
            {{ available ? 'Available Now' : 'Coming Soon' }}
        </div>
        <div
            class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br transition-transform duration-300 group-hover:scale-110"
            :class="available ? 'from-[#5a6b5e] to-[#3a4a3e]' : 'from-gray-400 to-gray-500'"
        >
            <component
                :is="icon"
                class="h-8 w-8 text-white"
            />
        </div>
        <h3 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h3>
        <p class="mb-4 text-gray-600 dark:text-gray-400">
            {{ description }}
        </p>
        <ul class="mb-6 space-y-2 text-sm text-gray-600 dark:text-gray-400">
            <li
                v-for="(feature, index) in features"
                :key="index"
                class="flex items-center gap-2"
            >
                <div
                    class="h-1.5 w-1.5 rounded-full"
                    :class="available ? 'bg-[#3a4a3e]' : 'bg-gray-400'"
                ></div>
                {{ feature }}
            </li>
        </ul>
        <template v-if="available && href">
            <Link
                :href="href"
                as="button"
            >
                <Button class="w-full bg-[#3a4a3e] hover:bg-[#5a6b5e]">
                    Start Resizing
                    <ArrowRight class="ml-2 h-4 w-4" />
                </Button>
            </Link>
        </template>
        <template v-else>
            <Button
                class="w-full cursor-not-allowed"
                disabled
            >
                Coming Soon
            </Button>
        </template>
    </div>
</template>
