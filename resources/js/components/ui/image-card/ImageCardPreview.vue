<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

interface Props {
    src: string;
    alt: string;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="['relative h-64 w-full', props.class]">
        <img
            :src="src"
            :alt="alt"
            class="h-full w-full object-cover"
        />
    </div>
</template>
