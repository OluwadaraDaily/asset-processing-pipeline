<script setup lang="ts">
import MultiFormatNavigation from '@/components/shared/MultiFormNavigation.vue';
import UserDropdown from '@/components/shared/UserDropdown.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex h-screen flex-col">
        <nav class="sticky top-0 z-10 flex w-full items-center justify-between border-b border-gray-200 bg-white px-6 py-4 shadow-sm">
            <Link
                href="/"
                class="text-xl font-bold text-gray-900 hover:text-gray-700"
            >
                Image Resizer
            </Link>
            <UserDropdown />
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
