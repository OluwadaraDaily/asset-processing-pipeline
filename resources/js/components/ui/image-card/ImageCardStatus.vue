<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

type Status = 'pending' | 'uploading' | 'processing' | 'completed' | 'error';

interface Props {
    status: Status;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();

const statusDisplay: Record<Status, string> = {
    pending: 'Ready to upload',
    uploading: 'Uploading...',
    processing: 'Processing...',
    completed: 'Completed',
    error: 'Failed',
};

const statusColor: Record<Status, string> = {
    pending: 'bg-gray-200 text-gray-800',
    uploading: 'bg-blue-100 text-blue-800',
    processing: 'bg-yellow-100/50 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    error: 'bg-red-100/50 text-red-800',
};
</script>

<template>
    <span :class="['inline-flex items-center rounded-full px-3 py-1 text-xs font-medium', statusColor[status], props.class]">
        {{ statusDisplay[status] }}
    </span>
</template>
