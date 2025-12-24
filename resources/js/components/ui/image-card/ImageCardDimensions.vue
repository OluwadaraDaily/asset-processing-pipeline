<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

interface Props {
    originalWidth: number;
    originalHeight: number;
    targetWidth: number;
    targetHeight: number;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="['mt-1 flex items-center gap-4', props.class]">
        <p class="text-xs text-gray-500">{{ originalWidth }} × {{ originalHeight }}</p>
        <span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                class="size-6"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="#2b7fff"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 12h14m-4 4l4-4m-4-4l4 4"
                />
            </svg>
        </span>
        <p class="rounded bg-gray-100 px-2 py-1 font-mono text-xs text-gray-700">
            {{ targetWidth }} × {{ targetHeight }}
        </p>
    </div>
</template>
