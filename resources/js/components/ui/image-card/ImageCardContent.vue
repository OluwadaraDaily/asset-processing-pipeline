<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

interface Props {
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="['flex flex-col gap-4 p-4', props.class]">
        <slot />
    </div>
</template>
