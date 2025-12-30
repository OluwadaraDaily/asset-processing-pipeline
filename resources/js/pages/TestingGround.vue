<script setup lang="ts">
import ImageStateCard from '@/components/image-processing/ImageStateCard.vue';

const imageUrl =
    'https://images.unsplash.com/photo-1545048702-79362596cdc9?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';

// const imageUrl = 'http://example.test/storage/images/test/WU6odNVSrxZZBrPfka0RSZ8W2BJolb5UEUAoEFb3.png';

// Test states
const imageStates = [
    {
        uuid: 'test-pending',
        file: new File([''], 'pending-image.png', { type: 'image/png' }),
        previewUrl: imageUrl,
        status: 'pending' as const,
        width: 1920,
        height: 1080,
        targetWidth: 800,
        targetHeight: 600,
    },
    {
        uuid: 'test-uploading',
        file: new File([''], 'uploading-image.png', { type: 'image/png' }),
        previewUrl: imageUrl,
        status: 'uploading' as const,
        width: 1920,
        height: 1080,
        targetWidth: 800,
        targetHeight: 600,
    },
    {
        uuid: 'test-processing',
        file: new File([''], 'processing-image.png', { type: 'image/png' }),
        previewUrl: imageUrl,
        status: 'processing' as const,
        width: 1920,
        height: 1080,
        targetWidth: 800,
        targetHeight: 600,
    },
    {
        uuid: 'test-completed',
        file: new File([''], 'completed-image.png', { type: 'image/png' }),
        previewUrl: imageUrl,
        status: 'completed' as const,
        width: 1920,
        height: 1080,
        targetWidth: 800,
        targetHeight: 600,
        downloadUrl: imageUrl,
    },
    {
        uuid: 'test-error',
        file: new File([''], 'error-image.png', { type: 'image/png' }),
        previewUrl: imageUrl,
        status: 'error' as const,
        width: 1920,
        height: 1080,
        targetWidth: 800,
        targetHeight: 600,
        errorMessage: 'Failed to process image. Please try again.',
    },
];
</script>

<template>
    <div class="p-8">
        <h1 class="mb-8 text-3xl font-bold">Image State Card Testing</h1>
        <div class="flex flex-wrap gap-6">
            <ImageStateCard
                v-for="state in imageStates"
                :key="state.uuid"
                :image-state="state"
            />
        </div>
    </div>
</template>
