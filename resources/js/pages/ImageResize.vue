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

useEchoPublic<ImageTransformationEvent>(CHANNELS.IMAGE_TRANSFORMATION, EVENTS.IMAGE_TRANSFORMATION_SUCCESS, (event) => {
    console.log('ImageTransformed event received:', event);
    toast.success(`${event.originalFilename} has been resized!`);
    const state = imageStates.value.find((image) => image.uuid === event.uuid);
    if (state) {
        state.status = 'completed';
        state.downloadUrl = event.url;
        state.errorMessage = undefined;
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
    }
});
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
