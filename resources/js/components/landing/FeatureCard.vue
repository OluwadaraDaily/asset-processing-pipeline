<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    icon: Component;
    title: string;
    description: string;
    color: 'green' | 'teal' | 'emerald' | 'lime';
}>();

const colorClasses = {
    green: 'bg-[#5a6b5e]/10 dark:bg-[#5a6b5e]/20 text-[#3a4a3e] dark:text-[#5a6b5e]',
    teal: 'bg-[#3a4a3e]/10 dark:bg-[#3a4a3e]/20 text-[#2a3a2e] dark:text-[#3a4a3e]',
    emerald: 'bg-[#6a7b6e]/10 dark:bg-[#6a7b6e]/20 text-[#5a6b5e] dark:text-[#6a7b6e]',
    lime: 'bg-[#4a5a4e]/10 dark:bg-[#4a5a4e]/20 text-[#3a4a3e] dark:text-[#4a5a4e]',
};
</script>

<template>
    <div
        class="group rounded-xl border border-gray-200 bg-gray-50 p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800"
    >
        <div
            class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg transition-transform duration-300 group-hover:scale-110"
            :class="colorClasses[color]"
        >
            <component
                :is="icon"
                class="h-6 w-6"
            />
        </div>
        <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
        <p class="text-gray-600 dark:text-gray-400">
            {{ description }}
        </p>
    </div>
</template>
