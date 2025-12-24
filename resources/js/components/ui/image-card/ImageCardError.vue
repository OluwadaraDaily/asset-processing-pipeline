<script setup lang="ts">
import type { HTMLAttributes} from 'vue';

interface Props {
    message: string;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <p :class="['text-sm text-red-600', props.class]">
        {{ message }}
    </p>
</template>
