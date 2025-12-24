<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    ImageCard,
    ImageCardActions,
    ImageCardContent,
    ImageCardDimensions,
    ImageCardError,
    ImageCardFooter,
    ImageCardHeader,
    ImageCardIcon,
    ImageCardMetadata,
    ImageCardPreview,
    ImageCardStat,
    ImageCardStats,
    ImageCardStatus,
    ImageCardTitle,
} from '@/components/ui/image-card';
import { useImageStore } from '@/stores/imageStore';
import { getCurrentDate } from '@/utils';
import { useForm } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';

const imageStore = useImageStore();
const { images: imageStates } = storeToRefs(imageStore);

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
            );
        }
    });
};

const startNewBatch = () => {
    imageStore.resetWorkflow();
};

const submit = () => {
    const files: File[] = [];
    const uuids: string[] = [];
    const widths: number[] = [];
    const heights: number[] = [];
    const targetWidths: number[] = [];
    const targetHeights: number[] = [];

    imageStates.value.forEach((state) => {
        files.push(state.file);
        uuids.push(state.uuid);
        widths.push(state.width);
        heights.push(state.height);
        targetWidths.push(state.targetWidth);
        targetHeights.push(state.targetHeight);
    });

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
    <div class="mx-auto mt-8 flex h-full w-[90%] flex-col space-y-8">
        <div
            class="pb-8"
            v-if="imageStates.length"
        >
            <h2 class="mb-5 text-2xl font-bold">Image Previews</h2>
            <div class="flex items-center gap-5">
                <ImageCard
                    v-for="state in imageStates"
                    :key="state.uuid"
                    :uuid="state.uuid"
                >
                    <ImageCardPreview
                        :src="state.previewUrl"
                        :alt="state.file.name"
                    />

                    <ImageCardContent>
                        <ImageCardHeader>
                            <ImageCardIcon />
                            <ImageCardTitle>
                                <h3 class="truncate text-base font-semibold text-gray-900">{{ state.file.name }}</h3>
                                <ImageCardDimensions
                                    :original-width="state.width"
                                    :original-height="state.height"
                                    :target-width="state.targetWidth"
                                    :target-height="state.targetHeight"
                                />
                            </ImageCardTitle>
                        </ImageCardHeader>

                        <ImageCardMetadata
                            label="Images | Library"
                            :date="getCurrentDate()"
                        />

                        <ImageCardStats>
                            <ImageCardStat
                                icon="eye"
                                :value="0"
                            />
                            <ImageCardStat
                                icon="comment"
                                :value="0"
                            />
                            <ImageCardStat
                                icon="emoji"
                                :value="0"
                            />
                        </ImageCardStats>

                        <ImageCardFooter>
                            <ImageCardStatus :status="state.status" />

                            <ImageCardActions>
                                <Button
                                    v-if="state.status === 'completed' && state.downloadUrl"
                                    class="border border-green-500 bg-white text-green-600 hover:bg-green-500 hover:text-white"
                                    @click.stop="downloadImage(state.uuid)"
                                >
                                    Download
                                </Button>
                                <Button
                                    v-if="state.status === 'error'"
                                    class="border border-blue-500 bg-white text-blue-500 hover:bg-blue-500 hover:text-white"
                                    @click.stop="retryImageUpload(state.uuid)"
                                >
                                    Retry
                                </Button>
                                <Button
                                    class="group border border-red-500 bg-white text-red-500 hover:bg-red-500 hover:text-white"
                                    @click.stop="removeImage(state.uuid)"
                                >
                                    Delete
                                </Button>
                            </ImageCardActions>
                        </ImageCardFooter>

                        <ImageCardError
                            v-if="state.errorMessage"
                            :message="state.errorMessage"
                        />
                    </ImageCardContent>
                </ImageCard>
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
