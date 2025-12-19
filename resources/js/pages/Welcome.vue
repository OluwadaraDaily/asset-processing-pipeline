<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label'
import { useForm } from '@inertiajs/vue3';
import { onUnmounted, ref, watch } from 'vue';
import { useEchoPublic } from '@laravel/echo-vue';
import { generateUUID } from '@/utils';
import { CHANNELS, EVENTS } from '@/config/events';
import 'vue-sonner/style.css'
import { Toaster } from '@/components/ui/sonner'
import { toast } from 'vue-sonner'

interface ImageState {
    uuid: string;
    file: File;
    previewUrl: string;
    status: 'pending' | 'uploading' | 'processing' | 'completed';
    downloadUrl?: string;
}

interface ImageTransformationEvent {
    uuid: string;
    originalFileName: string;
    path: string;
    status: string;
    url: string;
}

const imageStates = ref<Map<string, ImageState>>(new Map());

useEchoPublic<ImageTransformationEvent>(
    CHANNELS.IMAGE_TRANSFORMATION,
    EVENTS.IMAGE_TRANSFORMATION,
    (event) => {
        console.log('ImageTransformed event received:', event);
        toast.success(`${event.originalFileName} has been resized!`)
        const state = imageStates.value.get(event.uuid);
        if (state) {
            state.status = 'completed';
            state.downloadUrl = event.url;
        }
    }
);

const form = useForm({
    files: [] as File[],
    uuids: [] as string[],
    width: 100,
    height: 100,
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        const newFiles = Array.from(target.files);

        imageStates.value.forEach(state => URL.revokeObjectURL(state.previewUrl));
        imageStates.value.clear();

        form.files = newFiles;
        form.uuids = newFiles.map(() => generateUUID());

        // Initialize state for each image
        newFiles.forEach((file, index) => {
            const uuid = form.uuids[index];
            imageStates.value.set(uuid, {
                uuid,
                file,
                previewUrl: URL.createObjectURL(file),
                status: 'pending',
            });
        });
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
}

const submit = () => {
    imageStates.value.forEach(state => {
        state.status = 'uploading';
    });

    form.post('/upload', {
        forceFormData: true,
        onSuccess: () => {
            resetForm()
            imageStates.value.forEach(state => {
                state.status = 'processing';
            });
        },
    });
};

const getStatusDisplay = (status: ImageState['status']) => {
    return {
        pending: 'Ready to upload',
        uploading: 'Uploading...',
        processing: 'Processing...',
        completed: 'Completed',
    }[status];
};

const getStatusColor = (status: ImageState['status']) => {
    return {
        pending: 'bg-gray-100 text-gray-800',
        uploading: 'bg-blue-100 text-blue-800',
        processing: 'bg-yellow-100/50 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
    }[status];
};

function handlePromiseClick() {
  toast.promise<{ name: string }>(
    () =>
      new Promise(resolve =>
        setTimeout(() => resolve({ name: 'Event' }), 2000),
      ),
    {
      loading: 'Loading...',
      success: (data: { name: string }) => `${data.name} has been created`,
      error: 'Error',
    },
  )
}

onUnmounted(() => {
    imageStates.value.forEach(state => URL.revokeObjectURL(state.previewUrl));
});
</script>

<template>
    <div class="space-y-8 h-full flex flex-col w-[60%] mx-auto">
        <div class="p-4 justify-center items-center mt-20">
            <h1 class="text-3xl font-bold mb-8">Upload Form</h1>
            <form class="space-y-5" @submit.prevent="submit">
                <div class="flex items-center gap-5">
                    <div class="flex-1/2">
                        <Label>Width</Label>
                        <Input v-model="form.width" type="number"/>
                    </div>
                    <div class="flex-1/2">
                        <Label>Height</Label>
                        <Input v-model="form.height" type="number"/>
                    </div>
                </div>
                <div>
                    <Label>Files</Label>
                    <Input type="file" multiple @change="handleFileChange" name="files" id="files" accept="image/*"/>

                </div>
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>
                <Button type="submit" :disabled="form.processing || imageStates.size === 0">
                    {{ form.processing ? 'Uploading...' : 'Upload Images' }}
                </Button>
            </form>
        </div>

        <!-- Image previews with status -->
        <div class="flex flex-col gap-5 pb-10">
            <div
                v-for="[uuid, state] in imageStates"
                :key="uuid"
                class="p-4 flex justify-between items-center bg-black/5 rounded-xl"
            >
                <div class="flex items-center gap-4">
                    <div class="h-15 w-15 rounded-md border border-black overflow-hidden">
                        <img
                            :src="state.previewUrl"
                            class="h-full w-full object-cover"
                            :alt="state.file.name"
                        />
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="font-medium">{{ state.file.name }}</p>
                        <span
                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full w-max"
                            :class="getStatusColor(state.status)"
                        >
                            {{ getStatusDisplay(state.status) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button
                        v-if="state.status === 'completed' && state.downloadUrl"
                        :as="'a'"
                        :href="state.downloadUrl"
                        :download="state.file.name"
                        target="_blank"
                    >
                        Download
                    </Button>
                </div>
            </div>
        </div>
    </div>
    <Toaster />
</template>
