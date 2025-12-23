<script setup lang="ts">
import ImageStateCard from '@/components/image-processing/ImageStateCard.vue';
import SetDimensions from '@/components/image-processing/SetDimensions.vue';
import AssetUpload from '@/components/shared/AssetUpload.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Toaster } from '@/components/ui/sonner';
import { CHANNELS, EVENTS } from '@/config/events';
import UploadLayout from '@/layout/UploadLayout.vue';
import { useImageStore } from '@/stores/imageStore';
import { useForm } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import { storeToRefs } from 'pinia';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

defineOptions({
    layout: UploadLayout,
});

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
    const state = imageStates.find((image) => image.uuid === event.uuid);
    if (state) {
        state.status = 'completed';
        state.downloadUrl = event.url;
    }
});

useEchoPublic<ImageTransformationFailureEvent>(CHANNELS.IMAGE_TRANSFORMATION, EVENTS.IMAGE_TRANSFORMATION_FAILURE, (event) => {
    console.log('There has been an error =>', event);
    toast.error(`${event.originalFilename} transformation failed.Please try again.`);
    const state = imageStates.value.get(event.uuid);
    if (state) {
        state.status = 'error';
        state.errorMessage = event.errorMessage;
        toast.error(`Failed to process ${event.originalFilename}`);
    }
});

const form = useForm({
    files: [] as File[],
    uuids: [] as string[],
    width: 100,
    height: 100,
});

// const resetForm = () => {
//     // Reset form data
//     form.files = [];
//     form.uuids = [];
//     form.width = 100;
//     form.height = 100;

//     // Reset the file input element in the DOM
//     const fileInput = document.getElementById('files') as HTMLInputElement;
//     if (fileInput) {
//         fileInput.value = '';
//     }
// };

// const submit = () => {
//     imageStates.value.forEach((state) => {
//         state.status = 'uploading';
//     });

//     form.post('/upload', {
//         forceFormData: true,
//         onSuccess: () => {
//             resetForm();
//             imageStates.value.forEach((state) => {
//                 state.status = 'processing';
//             });
//         },
//         onError: (errors) => {
//             for (const error of Object.values(errors)) {
//                 toast.error(`Upload failed: ${error}`);
//             }
//             imageStates.value.forEach((state) => {
//                 state.status = 'error';
//             });
//         },
//     });
// };

// const retryImageUpload = (uuid: string) => {
//     const state = imageStates.value.get(uuid);
//     if (!state || state.status !== 'error') return;

//     // Re-upload single file
//     const retryForm = useForm({
//         files: [state.file],
//         uuids: [uuid],
//         width: form.width,
//         height: form.height,
//     });

//     state.status = 'uploading';
//     state.errorMessage = undefined;

//     retryForm.post('/upload', {
//         forceFormData: true,
//         onSuccess: () => {
//             state.status = 'processing';
//         },
//         onError: () => {
//             state.status = 'error';
//             state.errorMessage = 'Retry failed';
//         },
//     });
// };

// const getStatusDisplay = (status: ImageState['status']) => {
//     return {
//         pending: 'Ready to upload',
//         uploading: 'Uploading...',
//         processing: 'Processing...',
//         completed: 'Completed',
//         error: 'Failed',
//     }[status];
// };

// const getStatusColor = (status: ImageState['status']) => {
//     return {
//         pending: 'bg-gray-200 text-gray-800',
//         uploading: 'bg-blue-100 text-blue-800',
//         processing: 'bg-yellow-100/50 text-yellow-800',
//         completed: 'bg-green-100 text-green-800',
//         error: 'bg-red-100/50 text-red-800',
//     }[status];
// };
</script>

<template>
    <template v-if="currentStage === 'upload'">
        <AssetUpload />
    </template>
    <template v-if="currentStage === 'set-dimensions'">
        <SetDimensions />
    </template>
    <template v-if="currentStage === 'process'">
        <div class="mx-auto flex h-full w-[60%] flex-col space-y-8 bg-red-500">
            <!-- Upload Form -->
            <div class="mt-20 items-center justify-center p-4">
                <h1 class="mb-8 text-3xl font-bold">Upload Form</h1>
                <form
                    class="space-y-5"
                    @submit.prevent="submit"
                >
                    <div class="flex items-center gap-5">
                        <div class="flex-1/2">
                            <Label>Width</Label>
                            <Input
                                v-model="form.width"
                                type="number"
                            />
                        </div>
                        <div class="flex-1/2">
                            <Label>Height</Label>
                            <Input
                                v-model="form.height"
                                type="number"
                            />
                        </div>
                    </div>
                    <div>
                        <Label>Files</Label>
                        <Input
                            type="file"
                            multiple
                            @change="handleFileChange"
                            name="files"
                            id="files"
                            accept="image/*"
                        />
                    </div>
                    <progress
                        v-if="form.progress"
                        :value="form.progress.percentage"
                        max="100"
                    >
                        {{ form.progress.percentage }}%
                    </progress>
                    <Button
                        type="submit"
                        :disabled="form.processing || imageStates.size === 0"
                    >
                        {{ form.processing ? 'Uploading...' : 'Upload Images' }}
                    </Button>
                </form>
            </div>

            <!-- Image previews with status -->
            <div
                class="pb-8"
                v-show="imageStates.size"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Image Previews</h2>
                    <Dialog>
                        <DialogTrigger as-child>
                            <Button
                                class="group self-end border border-red-500 bg-white text-red-500 hover:cursor-pointer hover:bg-red-500 hover:text-white"
                                :disabled="!imageStates.size"
                            >
                                Clear
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[425px]">
                            <DialogHeader>
                                <DialogTitle>Remove all images</DialogTitle>
                                <DialogDescription> There is no reversal after continuing with this action. </DialogDescription>
                            </DialogHeader>
                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button variant="outline"> Cancel </Button>
                                </DialogClose>
                                <DialogClose as-child>
                                    <Button
                                        type="button"
                                        class="bg-red-500 text-white hover:opacity-75"
                                        @click="removeAllImageStates"
                                    >
                                        Continue
                                    </Button>
                                </DialogClose>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>
                <div class="flex items-center gap-5">
                    <ImageStateCard
                        v-for="[uuid, state] in imageStates"
                        :key="uuid"
                        :image-state="state"
                        @retry="retryImageUpload"
                        @remove="removeImage"
                    />
                </div>
            </div>
        </div>
    </template>
    <Toaster
        position="top-right"
        :richColors="true"
    />
</template>
