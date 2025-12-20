<script setup lang="ts">
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
import { generateUUID } from '@/utils';
import { useForm } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import { onUnmounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

interface ImageState {
    uuid: string;
    file: File;
    previewUrl: string;
    status: 'pending' | 'uploading' | 'processing' | 'completed' | 'error';
    downloadUrl?: string;
    errorMessage?: string;
    width: number;
    height: number;
}

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

interface ImageDimensions {
    width: number;
    height: number;
}

const imageStates = ref<Map<string, ImageState>>(new Map());

useEchoPublic<ImageTransformationEvent>(CHANNELS.IMAGE_TRANSFORMATION, EVENTS.IMAGE_TRANSFORMATION_SUCCESS, (event) => {
    console.log('ImageTransformed event received:', event);
    toast.success(`${event.originalFilename} has been resized!`);
    const state = imageStates.value.get(event.uuid);
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

const getImageDimensions = (file: File): Promise<ImageDimensions> => {
    return new Promise((resolve, reject) => {
        const image = new Image();
        const url = URL.createObjectURL(file);

        image.src = url;

        image.onload = () => {
            URL.revokeObjectURL(url);
            resolve({
                width: image.width,
                height: image.height,
            });
        };

        image.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('Failed to load image'));
        };
    });
};

const form = useForm({
    files: [] as File[],
    uuids: [] as string[],
    width: 100,
    height: 100,
});

const handleFileChange = async (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        const newFiles = Array.from(target.files);

        imageStates.value.forEach((state) => URL.revokeObjectURL(state.previewUrl));
        imageStates.value.clear();

        form.files = newFiles;
        form.uuids = newFiles.map(() => generateUUID());

        for (let index = 0; index < newFiles.length; index++) {
            const file = newFiles[index];
            const uuid = form.uuids[index];

            try {
                const dimensions = await getImageDimensions(file);

                imageStates.value.set(uuid, {
                    uuid,
                    file,
                    previewUrl: URL.createObjectURL(file),
                    status: 'pending',
                    width: dimensions.width,
                    height: dimensions.height,
                });
            } catch (error) {
                console.error(`Failed to get dimensions for ${file.name}:`, error);

                imageStates.value.set(uuid, {
                    uuid,
                    file,
                    previewUrl: URL.createObjectURL(file),
                    status: 'error',
                    width: 0,
                    height: 0,
                    errorMessage: 'Failed to read image dimensions',
                });
            }
        }
    }
};

const resetForm = () => {
    // Reset form data
    form.files = [];
    form.uuids = [];
    form.width = 100;
    form.height = 100;

    // Reset the file input element in the DOM
    const fileInput = document.getElementById('files') as HTMLInputElement;
    if (fileInput) {
        fileInput.value = '';
    }
};

const submit = () => {
    imageStates.value.forEach((state) => {
        state.status = 'uploading';
    });

    form.post('/upload', {
        forceFormData: true,
        onSuccess: () => {
            resetForm();
            imageStates.value.forEach((state) => {
                state.status = 'processing';
            });
        },
        onError: (errors) => {
            for (const error of Object.values(errors)) {
                toast.error(`Upload failed: ${error}`);
            }
            imageStates.value.forEach((state) => {
                state.status = 'error';
            });
        },
    });
};

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

const removeImage = (uuid: string) => {
    imageStates.value.delete(uuid);
};

const removeAllImageStates = () => {
    imageStates.value = new Map();
    resetForm();
};

onUnmounted(() => {
    imageStates.value.forEach((state) => URL.revokeObjectURL(state.previewUrl));
});
</script>

<template>
    <div class="mx-auto flex h-full w-[60%] flex-col space-y-8">
        <!-- Upload Form -->
        <div class="mt-20 items-center justify-center p-4">
            <h1 class="mb-8 text-3xl font-bold">Upload Form</h1>
            <form class="space-y-5" @submit.prevent="submit">
                <div class="flex items-center gap-5">
                    <div class="flex-1/2">
                        <Label>Width</Label>
                        <Input v-model="form.width" type="number" />
                    </div>
                    <div class="flex-1/2">
                        <Label>Height</Label>
                        <Input v-model="form.height" type="number" />
                    </div>
                </div>
                <div>
                    <Label>Files</Label>
                    <Input type="file" multiple @change="handleFileChange" name="files" id="files" accept="image/*" />
                </div>
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">{{ form.progress.percentage }}%</progress>
                <Button type="submit" :disabled="form.processing || imageStates.size === 0">
                    {{ form.processing ? 'Uploading...' : 'Upload Images' }}
                </Button>
            </form>
        </div>

        <!-- Image previews with status -->
        <div class="pb-8" v-show="imageStates.size">
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
                                <Button type="button" class="bg-red-500 text-white hover:opacity-75" @click="removeAllImageStates"> Continue </Button>
                            </DialogClose>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
            <div class="flex items-center gap-5">
                <div v-for="[uuid, state] in imageStates" :key="uuid">
                    <div class="w-[250px] space-y-6 rounded-[25px] bg-white p-2 shadow-md">
                        <div class="">
                            <img :src="state.previewUrl" class="h-[250px] w-full rounded-[25px] object-cover" :alt="state.file.name" />
                        </div>
                        <div class="mx-auto w-[90%] space-y-3">
                            <div class="flex items-center gap-3">
                                <h3 class="truncate text-lg font-bold">{{ state.file.name }}</h3>
                                <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <g fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path
                                            d="M3.85 8.62a4 4 0 0 1 4.78-4.77a4 4 0 0 1 6.74 0a4 4 0 0 1 4.78 4.78a4 4 0 0 1 0 6.74a4 4 0 0 1-4.77 4.78a4 4 0 0 1-6.75 0a4 4 0 0 1-4.78-4.77a4 4 0 0 1 0-6.76"
                                        />
                                        <path d="m9 12l2 2l4-4" />
                                    </g>
                                </svg>
                            </div>
                            <div class="mb-5 flex items-center justify-between">
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-600">{{ state.width }}px × {{ state.height }}px</p>
                                    <span
                                        class="inline-flex w-max items-center rounded-full px-2 py-1 text-xs font-medium"
                                        :class="getStatusColor(state.status)"
                                    >
                                        {{ getStatusDisplay(state.status) }}
                                    </span>
                                    <p v-if="state.errorMessage" class="text-sm text-red-600">
                                        {{ state.errorMessage }}
                                    </p>
                                </div>
                                <div>
                                    <Button
                                        class="group self-end border border-red-500 bg-white text-red-500 hover:cursor-pointer hover:bg-red-500 hover:text-white"
                                        @click="() => removeImage(state.uuid)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path
                                                fill="none"
                                                class="stroke-red-500 group-hover:stroke-white"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 11v6m4-6v6m5-11v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                            />
                                        </svg>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Toaster />
</template>
