<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    ImageCard,
    ImageCardContent,
    ImageCardDimensions,
    ImageCardHeader,
    ImageCardIcon,
    ImageCardMetadata,
    ImageCardPreview,
    ImageCardTitle,
} from '@/components/ui/image-card';
import { Input } from '@/components/ui/input';
import Label from '@/components/ui/label/Label.vue';
import { useImageStore } from '@/stores/imageStore';
import { ImageState } from '@/types/images';
import { getCurrentDate } from '@/utils';
import { useForm } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';

const imageStore = useImageStore();

const { images } = storeToRefs(imageStore);

const selectedImage = ref<ImageState | null>(null);

const form = useForm({
    files: [] as File[],
    uuids: [] as string[],
    width: 100,
    height: 100,
});

const handleImageSelect = (uuid: string) => {
    if (selectedImage.value?.uuid === uuid) {
        selectedImage.value = null;
        return;
    }
    const image = images.value.find((image) => image.uuid === uuid);
    if (image) {
        selectedImage.value = image;
        form.width = image.targetWidth;
        form.height = image.targetHeight;
    }
};

const setDimension = (uuid: string) => {
    const width = form.width;
    const height = form.height;

    imageStore.updateImageDimensions(uuid, width, height);

    selectedImage.value = null;
};

const handleClickOutside = (event: MouseEvent) => {
    const clickedElement = event.target as HTMLElement;
    const isCard = clickedElement.closest('[data-card]') !== null;

    if (!isCard) {
        selectedImage.value = null;
    }
};

const proceedToNextStage = () => {
    imageStore.moveToNextStage();
};
</script>

<template>
    <div class="mx-auto mt-8 flex h-full w-[90%]">
        <aside class="fixed left-0 h-full w-70 flex-shrink-0 border-r border-gray-200 shadow-md">
            <div class="mx-auto h-full w-[90%]">
                <div
                    v-if="selectedImage"
                    class="mt-8"
                >
                    <h1 class="mb-6 text-xl font-semibold">Resize Settings</h1>
                    <form
                        id="resize-form"
                        @submit.prevent="setDimension(selectedImage!.uuid)"
                    >
                        <div class="flex items-center gap-5">
                            <div class="flex-[50%]">
                                <Label>Width</Label>
                                <Input
                                    type="number"
                                    class="mt-1"
                                    name="width"
                                    v-model.number="form.width"
                                />
                            </div>
                            <div class="flex-[50%]">
                                <Label>Height</Label>
                                <Input
                                    type="number"
                                    class="mt-1"
                                    placeholder="100"
                                    name="height"
                                    v-model.number="form.height"
                                />
                            </div>
                        </div>
                        <div class="mt-5 flex justify-end">
                            <Button
                                type="submit"
                                class="w-full"
                            >
                                Set Dimension
                            </Button>
                        </div>
                    </form>
                </div>
                <div
                    v-else
                    class="flex h-full items-center justify-center"
                >
                    <p class="text-gray-500">Select an image to update its dimensions</p>
                </div>
            </div>
        </aside>
        <div
            class="deselect-area ml-70 flex-1 overflow-y-auto"
            @click="handleClickOutside"
        >
            <div class="m-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <ImageCard
                    v-for="state in images"
                    :key="state.uuid"
                    :uuid="state.uuid"
                    :selectable="true"
                    :selected="selectedImage?.uuid === state.uuid"
                    @select="handleImageSelect"
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
                    </ImageCardContent>
                </ImageCard>
            </div>
            <div class="flex justify-end">
                <Button @click.prevent="proceedToNextStage"> Proceed </Button>
            </div>
        </div>
    </div>
</template>
