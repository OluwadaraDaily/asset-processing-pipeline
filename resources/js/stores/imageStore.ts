import { generateUUID } from '@/utils';
import { defineStore } from 'pinia';
import { ref } from 'vue';

interface ImageDimensions {
    width: number;
    height: number;
}

export interface ImageState {
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

export type ImageResizeStage = 'upload' | 'set-dimensions' | 'process';

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

export const useImageStore = defineStore('image-store', () => {
    const images = ref<ImageState[]>([]);
    const currentStage = ref<ImageResizeStage>('upload');
    const isStageDone = ref<Record<ImageResizeStage, boolean>>({
        upload: false,
        'set-dimensions': false,
        process: false,
    });

    const setImages = async (newImages: File[]) => {
        const imageStates: ImageState[] = [];

        for (const file of newImages) {
            const uuid = generateUUID();

            try {
                const dimensions = await getImageDimensions(file);

                imageStates.push({
                    uuid,
                    file,
                    previewUrl: URL.createObjectURL(file),
                    status: 'pending',
                    width: dimensions.width,
                    height: dimensions.height,
                    targetWidth: 100,
                    targetHeight: 100,
                });
            } catch (error) {
                console.error(`Failed to get dimensions for ${file.name}:`, error);

                imageStates.push({
                    uuid,
                    file,
                    previewUrl: URL.createObjectURL(file),
                    status: 'error',
                    width: 0,
                    height: 0,
                    targetWidth: 100,
                    targetHeight: 100,
                    errorMessage: 'Failed to read image dimensions',
                });
            }
        }

        images.value = imageStates;
    };

    const clearImages = () => {
        images.value.forEach((image) => URL.revokeObjectURL(image.previewUrl));
        images.value = [];
    };

    const removeImage = (uuid: string) => {
        const index = images.value.findIndex((img) => img.uuid === uuid);

        if (index !== -1) {
            URL.revokeObjectURL(images.value[index].previewUrl);
            images.value.splice(index, 1);
        }
    };

    const updateImageDimensions = (uuid: string, width: number, height: number) => {
        const image = images.value.find((img) => img.uuid === uuid);
        if (image) {
            image.targetWidth = width;
            image.targetHeight = height;
        }
    };

    const moveToNextStage = () => {
        isStageDone.value[currentStage.value] = true;

        if (currentStage.value === 'upload') {
            currentStage.value = 'set-dimensions';
        } else if (currentStage.value === 'set-dimensions') {
            currentStage.value = 'process';
        }

        console.log('Current Stage =>', currentStage.value);
    };

    const moveToPreviousStage = () => {
        if (currentStage.value === 'process') {
            currentStage.value = 'set-dimensions';
        } else if (currentStage.value === 'set-dimensions') {
            currentStage.value = 'upload';
        }
    };

    return {
        images,
        currentStage,
        isStageDone,
        setImages,
        clearImages,
        removeImage,
        updateImageDimensions,
        moveToNextStage,
        moveToPreviousStage,
    };
});
