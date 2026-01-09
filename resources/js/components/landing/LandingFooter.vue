<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Zap } from 'lucide-vue-next';
</script>

<template>
    <footer class="border-t border-gray-200 bg-white py-12 dark:border-gray-800 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-[#5a6b5e] to-[#3a4a3e]">
                            <Zap class="h-5 w-5 text-white" />
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">TransformX</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Transform any asset, instantly. Built for the modern web.</p>
                </div>
                <div>
                    <h4 class="mb-4 font-semibold text-gray-900 dark:text-white">Product</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>
                            <a
                                href="#features"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                Features
                            </a>
                        </li>
                        <li>
                            <a
                                href="#products"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                Products
                            </a>
                        </li>
                        <li>
                            <Link
                                href="/image-resizer"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                Image Resizer
                            </Link>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-semibold text-gray-900 dark:text-white">Company</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>
                            <a
                                href="#"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                About
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                Contact
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-semibold text-gray-900 dark:text-white">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>
                            <a
                                href="#"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                Privacy
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="transition-colors hover:text-[#3a4a3e] dark:hover:text-white"
                            >
                                Terms
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-8 text-center text-sm text-gray-600 dark:border-gray-800 dark:text-gray-400">
                <p>2026 TransformX. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>
