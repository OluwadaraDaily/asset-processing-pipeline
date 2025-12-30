<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { getCurrentDate } from '@/utils';

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
    isSelected?: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    retry: [uuid: string];
    remove: [uuid: string];
    onSelect: [uuid: string];
    download: [uuid: string];
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

const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === 'Enter' || event.key === ' ') {
        emit('onSelect', props.imageState.uuid);
    }
};
</script>

<template>
    <div
        data-card
        tabindex="0"
        :class="[
            'flex w-full max-w-[315px] flex-col overflow-hidden rounded-3xl bg-white shadow-lg transition-all focus:outline-none',
            isSelected ? 'ring-2 ring-blue-500' : 'hover:ring-2 hover:ring-blue-500 focus:ring-2 focus:ring-gray-400',
        ]"
        @click.stop="$emit('onSelect', imageState.uuid)"
        @keydown.stop="handleKeyDown"
    >
        <!-- Image Section -->
        <div class="relative h-64 w-full">
            <img
                :src="imageState.previewUrl"
                class="h-full w-full object-cover"
                :alt="imageState.file.name"
            />
        </div>
        <div class="flex flex-col gap-4 p-4">
            <!-- Header Section -->
            <div class="flex items-start gap-2">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-gray-100">
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect
                            width="18"
                            height="18"
                            x="3"
                            y="3"
                            rx="2"
                            ry="2"
                        />
                        <circle
                            cx="9"
                            cy="9"
                            r="2"
                        />
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="truncate text-base font-semibold text-gray-900">{{ imageState.file.name }}</h3>
                    <div class="mt-1 flex items-center gap-4">
                        <p class="text-xs text-gray-500">{{ imageState.width }} × {{ imageState.height }}</p>
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                class="size-6"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="none"
                                    stroke="#2b7fff"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 12h14m-4 4l4-4m-4-4l4 4"
                                />
                            </svg>
                        </span>
                        <p class="rounded bg-gray-100 px-2 py-1 font-mono text-xs text-gray-700">
                            {{ imageState.targetWidth }} × {{ imageState.targetHeight }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tag Section -->
            <div class="rounded-lg bg-blue-50 p-3">
                <h4 class="text-sm font-medium text-gray-900">Images | Library | {{ getCurrentDate() }}</h4>
            </div>

            <!-- Icons Section -->
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-1.5">
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle
                            cx="12"
                            cy="12"
                            r="3"
                        />
                    </svg>
                    <span>0</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    <span>0</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="10"
                        />
                        <path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" />
                    </svg>
                    <span>0</span>
                </div>
            </div>

            <!-- State and Button Section -->
            <div class="flex items-center justify-between">
                <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                    :class="getStatusColor(imageState.status)"
                >
                    {{ getStatusDisplay(imageState.status) }}
                </span>
                <div class="flex gap-2">
                    <Button
                        v-if="imageState.status === 'completed' && imageState.downloadUrl"
                        class="border border-green-500 bg-white text-green-600 hover:bg-green-500 hover:text-white"
                        @click.stop="emit('download', imageState.uuid)"
                    >
                        Download
                    </Button>
                    <Button
                        v-if="imageState.status === 'error'"
                        class="border border-blue-500 bg-white text-blue-500 hover:bg-blue-500 hover:text-white"
                        @click.stop="emit('retry', imageState.uuid)"
                    >
                        Retry
                    </Button>
                    <Button
                        class="group border border-red-500 bg-white text-red-500 hover:bg-red-500 hover:text-white"
                        @click.stop="emit('remove', imageState.uuid)"
                    >
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Optional Error Message Section -->
            <p
                v-if="imageState.errorMessage"
                class="text-sm text-red-600"
            >
                {{ imageState.errorMessage }}
            </p>
        </div>
    </div>
</template>
