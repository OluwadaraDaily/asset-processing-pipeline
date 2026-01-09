<script setup lang="ts">
import ProcessImages from '@/components/image-processing/ProcessImages.vue';
import SetDimensions from '@/components/image-processing/SetDimensions.vue';
import AssetUpload from '@/components/shared/AssetUpload.vue';
import { Toaster } from '@/components/ui/sonner';
import { CHANNELS, EVENTS } from '@/config/events';
import UploadLayout from '@/layout/UploadLayout.vue';
import { useImageStore } from '@/stores/imageStore';
import { useEchoPublic } from '@laravel/echo-vue';
import { storeToRefs } from 'pinia';
import { onUnmounted, watch } from 'vue';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

defineOptions({
    layout: UploadLayout,
});

defineProps<{
    errors?: Record<string, string>;
    name?: string;
    quote?: {
        message: string;
        author: string;
    };
    auth?: any;
}>();

interface ImageTransformationEvent {
    uuid: string;
    originalFilename: string;
    path: string;
    status: string;
    url: string;
}

interface ImageTransformationFailureEvent {
    uuid: string;
    originalFilename: string;
    path: string;
    status: string;
    errorMessage: string;
}

const imageStore = useImageStore();

const { currentStage, images: imageStates } = storeToRefs(imageStore);

// Polling intervals for each image
const pollingIntervals = new Map<string, number>();
const POLLING_INTERVAL = 2000; // 2 seconds
const MAX_POLLING_TIME = 120000; // 2 minutes

// Function to poll image status
async function pollImageStatus(uuid: string, startTime: number) {
    try {
        const response = await fetch(`/api/images/${uuid}/status`);

        if (!response.ok) {
            throw new Error('Failed to fetch image status');
        }

        const data = await response.json();
        const state = imageStates.value.find((image) => image.uuid === uuid);

        if (!state) return;

        if (data.status === 'completed') {
            state.status = 'completed';
            state.downloadUrl = data.url;
            state.errorMessage = undefined;
            toast.success(`${data.original_filename} has been resized!`);
            stopPolling(uuid);
        } else if (data.status === 'failed') {
            state.status = 'error';
            state.errorMessage = data.error_message || 'Transformation failed';
            toast.error(`${data.original_filename} transformation failed. Please try again.`);
            stopPolling(uuid);
        } else if (Date.now() - startTime > MAX_POLLING_TIME) {
            // Timeout after 2 minutes
            state.status = 'error';
            state.errorMessage = 'Processing is taking longer than expected. Please try again.';
            toast.error(`${state.originalFilename} is taking too long to process.`);
            stopPolling(uuid);
        }
    } catch (error) {
        console.error('Error polling image status:', error);
    }
}

// Start polling for an image
function startPolling(uuid: string) {
    const startTime = Date.now();
    const intervalId = window.setInterval(() => {
        pollImageStatus(uuid, startTime);
    }, POLLING_INTERVAL);

    pollingIntervals.set(uuid, intervalId);
    // Poll immediately
    pollImageStatus(uuid, startTime);
}

// Stop polling for an image
function stopPolling(uuid: string) {
    const intervalId = pollingIntervals.get(uuid);
    if (intervalId) {
        clearInterval(intervalId);
        pollingIntervals.delete(uuid);
    }
}

// Watch for new images being uploaded and start polling
watch(
    imageStates,
    (newStates) => {
        newStates.forEach((state) => {
            if (state.status === 'uploading' || state.status === 'pending') {
                if (!pollingIntervals.has(state.uuid)) {
                    startPolling(state.uuid);
                }
            }
        });
    },
    { deep: true },
);

// Clean up intervals on unmount
onUnmounted(() => {
    pollingIntervals.forEach((intervalId) => clearInterval(intervalId));
    pollingIntervals.clear();
});

// Keep WebSocket support for future use (when Reverb is available)
try {
    useEchoPublic<ImageTransformationEvent>(CHANNELS.IMAGE_TRANSFORMATION, EVENTS.IMAGE_TRANSFORMATION_SUCCESS, (event) => {
        console.log('ImageTransformed event received:', event);
        toast.success(`${event.originalFilename} has been resized!`);
        const state = imageStates.value.find((image) => image.uuid === event.uuid);
        if (state) {
            state.status = 'completed';
            state.downloadUrl = event.url;
            state.errorMessage = undefined;
            stopPolling(event.uuid);
        }
    });

    useEchoPublic<ImageTransformationFailureEvent>(CHANNELS.IMAGE_TRANSFORMATION, EVENTS.IMAGE_TRANSFORMATION_FAILURE, (event) => {
        console.error('There has been an error =>', event);
        toast.error(`${event.originalFilename} transformation failed.Please try again.`);
        const state = imageStates.value.find((state) => state.uuid === event.uuid);
        if (state) {
            state.status = 'error';
            state.errorMessage = event.errorMessage;
            toast.error(`Failed to process ${event.originalFilename}`);
            stopPolling(event.uuid);
        }
    });
} catch {
    // Echo not configured, will rely on polling only
    console.log('WebSocket not available, using polling');
}
</script>

<template>
    <template v-if="currentStage === 'upload'">
        <AssetUpload />
    </template>
    <template v-if="currentStage === 'set-dimensions'">
        <SetDimensions />
    </template>
    <template v-if="currentStage === 'process'">
        <ProcessImages />
    </template>
    <Toaster
        position="top-right"
        :richColors="true"
    />
</template>
