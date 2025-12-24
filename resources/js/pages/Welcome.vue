<script setup lang="ts">
import ImageStateCard from '@/components/image-processing/ImageStateCard.vue';
import SetDimensions from '@/components/image-processing/SetDimensions.vue';
import AssetUpload from '@/components/shared/AssetUpload.vue';
import { Button } from '@/components/ui/button';
import { Toaster } from '@/components/ui/sonner';
import { CHANNELS, EVENTS } from '@/config/events';
import UploadLayout from '@/layout/UploadLayout.vue';
import { useImageStore } from '@/stores/imageStore';
import { useForm } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import { storeToRefs } from 'pinia';
import { computed, watch } from 'vue';
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

const isProcessing = computed(() => {
    return imageStates.value.some((state) => state.status === 'uploading' || state.status === 'processing');
});

const allCompleted = computed(() => {
    return imageStates.value.length > 0 && imageStates.value.every((state) => state.status === 'completed');
});

watch(allCompleted, (completed) => {
    if (completed) {
        imageStore.isStageDone.process = true;
    }
});

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
    console.log('There has been an error =>', event);
    toast.error(`${event.originalFilename} transformation failed.Please try again.`);
    const state = imageStates.value.find((state) => state.uuid === event.uuid);
    if (state) {
        state.status = 'error';
        state.errorMessage = event.errorMessage;
        toast.error(`Failed to process ${event.originalFilename}`);
    }
});

const retryImageUpload = (uuid: string) => {
    console.log('retryImageUpload =>', uuid);
    // TODO: Implement retry logic
};

const removeImage = (uuid: string) => {
    imageStore.removeImage(uuid);
};

const downloadImage = (uuid: string) => {
    const state = imageStates.value.find((img) => img.uuid === uuid);
    if (state?.downloadUrl) {
        const link = document.createElement('a');
        link.href = state.downloadUrl;
        link.download = state.file.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

const downloadAll = () => {
    imageStates.value.forEach((state) => {
        if (state.status === 'completed' && state.downloadUrl) {
            setTimeout(
                () => {
                    const link = document.createElement('a');
                    link.href = state.downloadUrl!;
                    link.download = state.file.name;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                100 * imageStates.value.indexOf(state),
            ); // Stagger downloads
        }
    });
};

const startNewBatch = () => {
    imageStore.resetWorkflow();
};

const submit = () => {
    // Prepare the data to send
    const files: File[] = [];
    const uuids: string[] = [];
    const widths: number[] = [];
    const heights: number[] = [];
    const targetWidths: number[] = [];
    const targetHeights: number[] = [];

    // Collect data from all images
    imageStates.value.forEach((state) => {
        files.push(state.file);
        uuids.push(state.uuid);
        widths.push(state.width);
        heights.push(state.height);
        targetWidths.push(state.targetWidth);
        targetHeights.push(state.targetHeight);
    });

    // Create a form with the data
    const uploadForm = useForm({
        files,
        uuids,
        widths,
        heights,
        targetWidths,
        targetHeights,
    });

    imageStates.value.forEach((state) => {
        state.status = 'uploading';
    });

    uploadForm.post('/upload', {
        forceFormData: true,
        onSuccess: () => {
            console.log('Upload successful');
            imageStates.value.forEach((state) => {
                state.status = 'processing';
            });
        },
        onError: (errors) => {
            console.error('Upload errors:', errors);
            for (const error of Object.values(errors)) {
                toast.error(`Upload failed: ${error}`);
            }
            imageStates.value.forEach((state) => {
                state.status = 'error';
                state.errorMessage = 'Upload failed';
            });
        },
    });
};
</script>

<template>
    <template v-if="currentStage === 'upload'">
        <AssetUpload />
    </template>
    <template v-if="currentStage === 'set-dimensions'">
        <SetDimensions />
    </template>
    <template v-if="currentStage === 'process'">
        <div class="mx-auto mt-8 flex h-full w-[90%] flex-col space-y-8">
            <!-- Image previews with status -->
            <div
                class="pb-8"
                v-if="imageStates.length"
            >
                <h2 class="mb-5 text-2xl font-bold">Image Previews</h2>
                <div class="flex items-center gap-5">
                    <ImageStateCard
                        v-for="state in imageStates"
                        :key="state.uuid"
                        :image-state="state"
                        @retry="retryImageUpload"
                        @remove="removeImage"
                        @download="downloadImage"
                    />
                </div>
            </div>
            <div class="flex items-center justify-end gap-4">
                <template v-if="allCompleted">
                    <Button
                        @click="startNewBatch"
                        class="border border-blue-600 bg-white text-blue-600 hover:bg-blue-600 hover:text-white"
                    >
                        Start New Batch
                    </Button>
                    <Button
                        @click="downloadAll"
                        class="border border-green-600 bg-green-600 text-white hover:bg-green-700"
                    >
                        Download All
                    </Button>
                </template>
                <Button
                    v-else
                    @click="submit"
                    :loading="isProcessing"
                    :disabled="!imageStates.length || isProcessing"
                >
                    Process images
                </Button>
            </div>
        </div>
    </template>
    <Toaster
        position="top-right"
        :richColors="true"
    />
</template>
