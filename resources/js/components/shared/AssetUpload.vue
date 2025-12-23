<script setup lang="ts">
import { useImageStore } from '@/stores/imageStore';
import { formatFileSize } from '@/utils';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '../ui/button';

const imageStore = useImageStore();

const fileInputRef = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const MAX_IMAGES = 5;

const handleFileUploadSectionClick = () => {
    fileInputRef.value?.click();
};

const handleDragOver = (e: DragEvent) => {
    e.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = (e: DragEvent) => {
    e.preventDefault();
    isDragging.value = false;
};

const handleDrop = (e: DragEvent) => {
    e.preventDefault();
    isDragging.value = false;

    const files = e.dataTransfer?.files;
    if (files && files.length > 0) {
        console.log('Dropped files:', files);

        if (fileInputRef.value) {
            fileInputRef.value.files = files;
            fileInputRef.value.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        fileInputRef.value?.click();
    }
};

const handleFileChange = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;

    if (files && files.length > MAX_IMAGES) {
        toast.error('Maximum number of images that can be uploaded at once is 5.');
        return;
    }

    if (files && files.length > 0) {
        const fileArray = Array.from(files);
        await imageStore.setImages(fileArray);
        console.log('Files selected:', fileArray);
    }
};

const proceedToNextStage = () => {
    // validate current stage
    if (imageStore.images.length === 0) {
        toast.error('No images have been uploaded. Please try again');
        return;
    }

    if (imageStore.images.length > MAX_IMAGES) {
        toast.error('Maximum number of images that can be uploaded at once is 5.');
        fileInputRef.value = null;
        return;
    }

    imageStore.moveToNextStage();
};
</script>

<template>
    <div>
        <div class="flex min-h-full items-center justify-center bg-[#F5F6FA]">
            <div class="mx-auto my-8 w-[90%] space-y-8 rounded-xl p-4 shadow-[0_0_15px_rgba(0,0,0,0.1)] lg:w-[650px]">
                <h1 class="text-2xl font-medium">Add images</h1>
                <div
                    class="flex w-full flex-col items-center gap-5 rounded-lg border border-dashed py-12 transition-colors hover:cursor-pointer focus:outline-none focus-visible:border-blue-500 focus-visible:bg-blue-500/10 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 active:border-blue-500 active:bg-blue-500/10"
                    :class="isDragging ? 'border-blue-500 bg-blue-500/10' : 'border-black hover:border-blue-500 hover:bg-blue-500/10'"
                    tabindex="0"
                    @click="handleFileUploadSectionClick"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @drop="handleDrop"
                    @keydown="handleKeyDown"
                >
                    <svg
                        class="size-16"
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
                                d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"
                            />
                            <path d="M14 2v5a1 1 0 0 0 1 1h5m-8 4v6m3-3l-3-3l-3 3" />
                        </g>
                    </svg>
                    <p class="font-semibold">Drag and Drop or <span class="text-blue-500">Choose file</span> to uplooad</p>
                    <p class="text-gray-500">Supported formats: PNG, JPG, JPEG</p>
                    <input
                        ref="fileInputRef"
                        type="file"
                        multiple
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                    />
                </div>
                <div
                    v-if="imageStore.images.length"
                    class="space-y-4"
                >
                    <div
                        v-for="image in imageStore.images"
                        :key="image.uuid"
                        class="relative w-full rounded-lg border-2 border-gray-300 p-6"
                    >
                        <Button
                            @click="imageStore.removeImage(image.uuid)"
                            class="absolute top-2 right-2 bg-transparent p-0.5 text-black hover:cursor-pointer hover:bg-transparent"
                            aria-label="Remove image"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-4"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18 6L6 18M6 6l12 12"
                                />
                            </svg>
                        </Button>
                        <div class="flex items-center gap-6">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border-2 border-gray-300">
                                <img
                                    :src="image.previewUrl"
                                    :alt="image.file.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ image.file.name }}</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm text-gray-500">{{ formatFileSize(image.file.size) }}</p>
                                    <span class="text-gray-400">•</span>
                                    <p class="text-sm text-gray-500">{{ image.width }}px × {{ image.height }}px</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <Button @click="proceedToNextStage">Proceed</Button>
                </div>
            </div>
        </div>
    </div>
</template>
