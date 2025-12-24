<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

type IconType = 'eye' | 'comment' | 'emoji';

interface Props {
    icon: IconType;
    value: number;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();

const iconPaths: Record<IconType, string> = {
    eye: 'M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z',
    comment: 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z',
    emoji: 'M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01',
};

const circleForIcon: Record<IconType, { cx: number; cy: number; r: number } | null> = {
    eye: { cx: 12, cy: 12, r: 3 },
    comment: null,
    emoji: { cx: 12, cy: 12, r: 10 },
};
</script>

<template>
    <div :class="['flex items-center gap-1.5', props.class]">
        <svg
            class="size-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <circle
                v-if="circleForIcon[icon]"
                :cx="circleForIcon[icon]!.cx"
                :cy="circleForIcon[icon]!.cy"
                :r="circleForIcon[icon]!.r"
            />
            <path :d="iconPaths[icon]" />
        </svg>
        <span>{{ value }}</span>
    </div>
</template>
