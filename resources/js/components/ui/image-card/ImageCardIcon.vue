<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

interface Props {
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="['flex size-10 shrink-0 items-center justify-center rounded-full bg-gray-100', props.class]">
        <svg
            class="size-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <rect
                width="18"
                height="18"
                x="3"
                y="3"
                rx="2"
                ry="2"
            />
            <circle
                cx="9"
                cy="9"
                r="2"
            />
            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
        </svg>
    </div>
</template>
