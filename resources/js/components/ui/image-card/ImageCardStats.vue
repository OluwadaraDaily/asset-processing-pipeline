<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

interface Props {
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="['flex items-center gap-4 text-sm text-gray-600', props.class]">
        <slot />
    </div>
</template>
