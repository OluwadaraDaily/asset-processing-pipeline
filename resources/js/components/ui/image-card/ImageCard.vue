<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed, provide } from 'vue';

interface Props {
    uuid?: string;
    selectable?: boolean;
    selected?: boolean;
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    selectable: false,
    selected: false,
});

const emit = defineEmits<{
    select: [uuid: string];
}>();

const handleClick = () => {
    if (props.selectable && props.uuid) {
        emit('select', props.uuid);
    }
};

const handleKeyDown = (event: KeyboardEvent) => {
    if (props.selectable && props.uuid && (event.key === 'Enter' || event.key === ' ')) {
        event.preventDefault();
        emit('select', props.uuid);
    }
};

// Provide context to child components
provide('imageCard', {
    uuid: computed(() => props.uuid),
    selected: computed(() => props.selected),
});
</script>

<template>
    <div
        data-card
        :tabindex="selectable ? 0 : undefined"
        :class="[
            'flex w-full max-w-[315px] flex-col overflow-hidden rounded-3xl bg-white shadow-lg transition-all',
            selectable && 'cursor-pointer focus:outline-none',
            selected ? 'ring-2 ring-blue-500' : selectable && 'hover:ring-2 hover:ring-blue-500 focus:ring-2 focus:ring-gray-400',
            props.class,
        ]"
        @click="handleClick"
        @keydown="handleKeyDown"
    >
        <slot />
    </div>
</template>
