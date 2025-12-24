<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

interface Props {
    label: string;
    date?: string;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="['rounded-lg bg-blue-50 p-3', props.class]">
        <h4 class="text-sm font-medium text-gray-900">
            {{ label }}<template v-if="date"> | {{ date }}</template>
        </h4>
    </div>
</template>
