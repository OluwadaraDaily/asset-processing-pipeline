<script setup lang="ts">
import FeatureCard from '@/components/landing/FeatureCard.vue';
import HeroSection from '@/components/landing/HeroSection.vue';
import LandingFooter from '@/components/landing/LandingFooter.vue';
import LandingNav from '@/components/landing/LandingNav.vue';
import ProductCard from '@/components/landing/ProductCard.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Download, FileType, Image as ImageIcon, Sparkles, Upload, Video, Zap } from 'lucide-vue-next';

defineOptions({
    layout: undefined,
});

defineProps<{
    auth?: any;
}>();
</script>

<template>
    <div class="scroll-smooth bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <LandingNav :auth="auth" />

        <HeroSection />

        <!-- Features Section -->
        <section
            id="features"
            class="bg-white py-20 dark:bg-gray-900"
        >
            <div class="container mx-auto px-4">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-gray-900 dark:text-white">Why Choose TransformX?</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Built for developers, designers, and content creators who demand excellence
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <FeatureCard
                        :icon="Zap"
                        title="Lightning Fast"
                        description="Process thousands of assets in seconds with our optimized infrastructure and parallel processing."
                        color="green"
                    />
                    <FeatureCard
                        :icon="Upload"
                        title="Batch Processing"
                        description="Upload and transform multiple files at once. Save time with intelligent batch operations."
                        color="teal"
                    />
                    <FeatureCard
                        :icon="Download"
                        title="Instant Download"
                        description="Get your transformed assets immediately. No waiting, no delays, just results."
                        color="emerald"
                    />
                    <FeatureCard
                        :icon="ImageIcon"
                        title="Format Flexibility"
                        description="Support for all major image formats: PNG, JPG, WebP, GIF, and more coming soon."
                        color="lime"
                    />
                    <FeatureCard
                        :icon="Sparkles"
                        title="Quality Preservation"
                        description="Advanced algorithms ensure your assets maintain the highest quality after transformation."
                        color="green"
                    />
                    <FeatureCard
                        :icon="Video"
                        title="More Coming Soon"
                        description="Video processing, format conversion, compression, and more transformations in the pipeline."
                        color="teal"
                    />
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section
            id="products"
            class="py-20"
        >
            <div class="container mx-auto px-4">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-gray-900 dark:text-white">Our Products</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">Specialized tools for every transformation need</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <ProductCard
                        :icon="ImageIcon"
                        title="Image Resizer"
                        description="Resize images to any dimension while maintaining quality. Perfect for web optimization and responsive design."
                        :features="['Batch processing', 'Multiple format support', 'Instant results']"
                        :available="true"
                        href="/image-resizer"
                    />
                    <ProductCard
                        :icon="Video"
                        title="Video Converter"
                        description="Convert videos between formats, compress, and optimize for any platform or device."
                        :features="['Format conversion', 'Video compression', 'Quality control']"
                        :available="false"
                    />
                    <ProductCard
                        :icon="FileType"
                        title="Format Converter"
                        description="Convert between any file format. Documents, images, videos, and more."
                        :features="['Universal conversion', 'Batch operations', 'Cloud processing']"
                        :available="false"
                    />
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-br from-[#5a6b5e] to-[#3a4a3e] py-20 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="mb-4 text-4xl font-bold">Ready to Transform Your Assets?</h2>
                <p class="mb-8 text-xl text-gray-100">Join thousands of users who trust TransformX for their asset processing needs</p>
                <div class="flex items-center justify-center gap-4">
                    <Link
                        href="/image-resizer"
                        as="button"
                    >
                        <Button
                            size="lg"
                            variant="secondary"
                            class="gap-2"
                        >
                            Get Started Free
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                    <Link
                        href="/register"
                        as="button"
                    >
                        <Button
                            size="lg"
                            variant="outline"
                            class="border-white bg-transparent text-white hover:bg-white hover:text-[#3a4a3e]"
                        >
                            Create Account
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <LandingFooter />
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}
</style>
