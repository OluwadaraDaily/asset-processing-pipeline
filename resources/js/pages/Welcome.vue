<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { useEchoPublic } from '@laravel/echo-vue';

// Subscribe to a public channel and listen for an event
useEchoPublic(
  'image-transformations', // Channel name
  'ImageTransformed', // Event name (e.g., a Laravel event class name)
  (event) => {
    // Callback function when the event is received
    console.log('Event received:', event);
  }
);


const form = useForm({
    files: [] as File[],
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.files = Array.from(target.files);
    }
};

const submit = () => {
    form.post('/upload', {
        forceFormData: true,
    });
};
</script>

<template>
    <div class="my-8">
        <form class="space-y-5" @submit.prevent="submit">
            <Input type="file" multiple @change="handleFileChange" name="files" id="files" accept="image/*"/>
            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
            {{ form.progress.percentage }}%
        </progress>
            <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Uploading...' : 'Send' }}
            </Button>
        </form>
    </div>
    
</template>
