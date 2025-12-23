<script setup lang="ts">
import MultiFormatNavigation from '@/components/shared/MultiFormNavigation.vue';
</script>

<template>
    <div class="flex h-screen flex-col">
        <nav class="sticky top-0 z-10 w-full border-b border-gray-200 bg-white px-6 py-4 shadow-sm">
            <h1 class="text-xl font-bold">Image Resizer</h1>
        </nav>

        <div class="flex flex-1 overflow-hidden">
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <MultiFormatNavigation />
                <slot />
            </main>
        </div>

        <footer class="sticky bottom-0 z-10 w-full border-t border-gray-200 bg-white px-6 py-4 text-center text-sm text-gray-600">
            © 2025 Image Resizer. All rights reserved.
        </footer>
    </div>
</template>
