<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface ImageState {
    uuid: string;
    file: File;
    previewUrl: string;
    status: 'pending' | 'uploading' | 'processing' | 'completed' | 'error';
    width: number;
    height: number;
    targetWidth: number;
    targetHeight: number;
    downloadUrl?: string;
    errorMessage?: string;
}

interface Props {
    imageState: ImageState;
}

defineProps<Props>();

const emit = defineEmits<{
    retry: [uuid: string];
    remove: [uuid: string];
}>();

const getStatusDisplay = (status: ImageState['status']) => {
    return {
        pending: 'Ready to upload',
        uploading: 'Uploading...',
        processing: 'Processing...',
        completed: 'Completed',
        error: 'Failed',
    }[status];
};

const getStatusColor = (status: ImageState['status']) => {
    return {
        pending: 'bg-gray-200 text-gray-800',
        uploading: 'bg-blue-100 text-blue-800',
        processing: 'bg-yellow-100/50 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
        error: 'bg-red-100/50 text-red-800',
    }[status];
};
</script>

<template>
    <div class="flex h-[315px] w-[250px] flex-col rounded-[25px] bg-white shadow-md">
        <div class="h-[50%]">
            <img
                :src="imageState.previewUrl"
                class="h-full w-full rounded-tl-[25px] rounded-tr-[25px] object-cover"
                :alt="imageState.file.name"
            />
        </div>
        <div class="mx-auto mt-auto w-[90%] space-y-3 pb-4">
            <div class="flex items-center gap-3">
                <h3 class="truncate text-sm font-bold">{{ imageState.file.name }}</h3>
                <svg
                    class="size-5 shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                >
                    <g
                        fill="none"
                        stroke="#000"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                    >
                        <path
                            d="M3.85 8.62a4 4 0 0 1 4.78-4.77a4 4 0 0 1 6.74 0a4 4 0 0 1 4.78 4.78a4 4 0 0 1 0 6.74a4 4 0 0 1-4.77 4.78a4 4 0 0 1-6.75 0a4 4 0 0 1-4.78-4.77a4 4 0 0 1 0-6.76"
                        />
                        <path d="m9 12l2 2l4-4" />
                    </g>
                </svg>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-600">{{ imageState.width }}px × {{ imageState.height }}px</p>
                <span
                    class="inline-flex w-max items-center rounded-full px-2 py-1 text-[8px] font-medium"
                    :class="getStatusColor(imageState.status)"
                >
                    {{ getStatusDisplay(imageState.status) }}
                </span>
            </div>
            <div v-if="imageState.errorMessage">
                <p class="text-sm text-red-600">
                    {{ imageState.errorMessage }}
                </p>
            </div>
            <div class="flex justify-end gap-2">
                <Button
                    v-if="imageState.status === 'error'"
                    class="border border-blue-500 bg-white text-blue-500 hover:bg-blue-500 hover:text-white"
                    @click="() => emit('retry', imageState.uuid)"
                >
                    Retry
                </Button>
                <Button
                    class="group self-end border border-red-500 bg-white text-red-500 hover:cursor-pointer hover:bg-red-500 hover:text-white"
                    @click="() => emit('remove', imageState.uuid)"
                >
                    Delete
                </Button>
            </div>
        </div>
    </div>
</template>
