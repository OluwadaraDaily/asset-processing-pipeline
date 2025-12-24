<script setup lang="ts">
import { ImageState, useImageStore } from '@/stores/imageStore';
import { useForm } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import Label from '../ui/label/Label.vue';
import ImageStateCard from './ImageStateCard.vue';

const { images: imagesState } = storeToRefs(useImageStore());

// interface ImageDimensions {
//     uuid: string;
//     file: File;
//     width?: number;
//     height?: number;
// }

const form = useForm({
    files: [] as File[],
    uuids: [] as string[],
    width: 100,
    height: 100,
});

const selectedImage = ref<ImageState | null>(null);

const submit = (event: Event) => {
    console.log('EVENT ->', event);
};

const retryImageUpload = (uuid: string) => {
    console.log('retryImageUpload =>', uuid);
};

const removeImage = (uuid: string) => {
    console.log('removeImage =>', uuid);
};
const handleImageSelect = (uuid: string) => {
    console.log('handleImageSelect =>', uuid);
};
</script>

<template>
    <div class="flex h-full">
        <aside class="fixed left-0 h-full w-70 flex-shrink-0 border-r border-gray-200 shadow-md">
            <div class="mx-auto h-full w-[90%]">
                <div v-if="selectedImage">
                    <h1 class="mb-6 text-xl font-semibold">Resize Settings</h1>
                    <form
                        id="resize-form"
                        @submit.prevent="submit"
                    >
                        <div class="flex items-center gap-5">
                            <div class="flex-[50%]">
                                <Label>Width</Label>
                                <Input
                                    type="number"
                                    class="mt-0.5"
                                    name="width"
                                    :value="form.width"
                                    @input="form.width = Number($event.target.value)"
                                />
                            </div>
                            <div class="flex-[50%]">
                                <Label>Height</Label>
                                <Input
                                    type="number"
                                    class="mt-0.5"
                                    placeholder="100"
                                    name="height"
                                    :value="form.height"
                                    @input="form.height = Number($event.target.value)"
                                />
                            </div>
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
        <div class="ml-70 flex-1 overflow-y-auto">
            <div class="m-8 flex items-center gap-5">
                <ImageStateCard
                    v-for="state in imagesState"
                    :key="state.uuid"
                    :image-state="state"
                    @retry="retryImageUpload"
                    @remove="removeImage"
                    @onSelect="handleImageSelect"
                />
            </div>
            <div class="flex justify-end">
                <Button
                    type="submit"
                    form="resize-form"
                    class=""
                    >Proceed</Button
                >
            </div>
        </div>
    </div>
</template>
