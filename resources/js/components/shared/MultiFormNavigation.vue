<script setup lang="ts">
import { useImageStore } from '@/stores/imageStore';
import { ImageResizeStage } from '@/types/images';
import { storeToRefs } from 'pinia';

const { currentStage, isStageDone } = storeToRefs(useImageStore());

interface Stage {
    id: ImageResizeStage;
    label: string;
}

const stages: Stage[] = [
    { id: 'upload', label: 'Image Upload' },
    { id: 'set-dimensions', label: 'Set Dimensions' },
    { id: 'process', label: 'Process' },
];
</script>

<template>
    <div class="sticky top-0 z-10 bg-white p-4">
        <div class="mx-auto flex w-[90%] items-center justify-between gap-4">
            <template
                v-for="(stage, index) in stages"
                :key="stage.id"
            >
                <div
                    class="h-0.5 flex-1 bg-gray-300"
                    v-if="index !== 0"
                ></div>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full font-semibold"
                        :class="{
                            'bg-blue-500 text-white': currentStage === stage.id && !isStageDone[stage.id],
                            'bg-black text-white': isStageDone[stage.id],
                            'bg-gray-300 text-white': currentStage !== stage.id && !isStageDone[stage.id],
                        }"
                    >
                        <svg
                            v-if="isStageDone[stage.id]"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 6L9 17l-5-5"
                            />
                        </svg>
                        <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span
                        class="font-semibold whitespace-nowrap"
                        :class="{
                            'text-blue-500': currentStage === stage.id && !isStageDone[stage.id],
                            'text-black': isStageDone[stage.id],
                            'text-gray-300': currentStage !== stage.id && !isStageDone[stage.id],
                        }"
                    >
                        {{ stage.label }}
                    </span>
                </div>
            </template>
        </div>
    </div>
</template>
