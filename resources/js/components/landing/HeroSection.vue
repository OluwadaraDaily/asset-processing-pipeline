<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Sparkles } from 'lucide-vue-next';
</script>

<template>
    <section class="container mx-auto px-4 py-20 text-center">
        <div class="mx-auto max-w-4xl">
            <div
                class="mb-6 inline-flex items-center gap-2 rounded-full bg-[#5a6b5e]/10 px-4 py-2 text-sm font-medium text-[#3a4a3e] dark:bg-[#5a6b5e]/20 dark:text-[#5a6b5e]"
            >
                <Sparkles class="h-4 w-4" />
                Transform Any Asset, Instantly
            </div>
            <h1 class="mb-6 text-5xl leading-tight font-bold text-gray-900 md:text-6xl lg:text-7xl dark:text-white">
                The Future of Asset
                <span class="bg-gradient-to-r from-[#5a6b5e] to-[#3a4a3e] bg-clip-text text-transparent"> Processing </span>
            </h1>
            <p class="mb-8 text-xl text-gray-600 dark:text-gray-400">
                Powerful cloud-based transformations for images, videos, and all your digital assets. Process, optimize, and deliver at scale with
                cutting-edge technology.
            </p>
            <div class="flex items-center justify-center gap-4">
                <Link
                    href="/image-resizer"
                    as="button"
                >
                    <Button
                        size="lg"
                        class="gap-2 bg-[#3a4a3e] hover:bg-[#5a6b5e]"
                    >
                        Try Image Resizer Free
                        <ArrowRight class="h-4 w-4" />
                    </Button>
                </Link>
                <Link
                    href="#products"
                    as="button"
                >
                    <Button
                        size="lg"
                        variant="outline"
                        class="border-[#3a4a3e] text-[#3a4a3e] hover:bg-[#3a4a3e]/10"
                    >
                        View All Products
                    </Button>
                </Link>
            </div>
            <div class="mt-8 flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex -space-x-2">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-[#5a6b5e] to-[#3a4a3e]"></div>
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-[#3a4a3e] to-[#2a3a2e]"></div>
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-[#6a7b6e] to-[#5a6b5e]"></div>
                </div>
                <p>Join 1,000+ users transforming their assets daily</p>
            </div>
        </div>

        <!-- Hero Image -->
        <div class="mx-auto mt-16 max-w-5xl">
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl transition-transform duration-300 hover:scale-[1.02] dark:border-gray-700 dark:bg-gray-800"
            >
                <img
                    src="https://images.unsplash.com/photo-1551650975-87deedd944c3?w=1200&h=600&fit=crop"
                    alt="Dashboard Preview"
                    class="h-auto w-full"
                />
            </div>
        </div>
    </section>
</template>
