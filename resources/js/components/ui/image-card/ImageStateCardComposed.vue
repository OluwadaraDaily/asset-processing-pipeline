<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { getCurrentDate } from '@/utils';
import {
    ImageCard,
    ImageCardActions,
    ImageCardContent,
    ImageCardDimensions,
    ImageCardError,
    ImageCardFooter,
    ImageCardHeader,
    ImageCardIcon,
    ImageCardMetadata,
    ImageCardPreview,
    ImageCardStat,
    ImageCardStats,
    ImageCardStatus,
    ImageCardTitle,
} from './index';

interface ImageState {
    uuid: string;
    file: File;
    previewUrl: string;
    status: 'pending' | 'uploading' | 'processing' | 'completed' | 'error';
    width: number;
    height: number;
    targetWidth: number;
    targetHeight: number;
    downloadUrl?: string;
    errorMessage?: string;
}

interface Props {
    imageState: ImageState;
    isSelected?: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    retry: [uuid: string];
    remove: [uuid: string];
    onSelect: [uuid: string];
    download: [uuid: string];
}>();
</script>

<template>
    <ImageCard
        :uuid="imageState.uuid"
        :selectable="true"
        :selected="isSelected"
        @select="emit('onSelect', $event)"
    >
        <ImageCardPreview
            :src="imageState.previewUrl"
            :alt="imageState.file.name"
        />

        <ImageCardContent>
            <ImageCardHeader>
                <ImageCardIcon />
                <ImageCardTitle>
                    <h3 class="truncate text-base font-semibold text-gray-900">{{ imageState.file.name }}</h3>
                    <ImageCardDimensions
                        :original-width="imageState.width"
                        :original-height="imageState.height"
                        :target-width="imageState.targetWidth"
                        :target-height="imageState.targetHeight"
                    />
                </ImageCardTitle>
            </ImageCardHeader>

            <ImageCardMetadata
                label="Images | Library"
                :date="getCurrentDate()"
            />

            <ImageCardStats>
                <ImageCardStat
                    icon="eye"
                    :value="0"
                />
                <ImageCardStat
                    icon="comment"
                    :value="0"
                />
                <ImageCardStat
                    icon="emoji"
                    :value="0"
                />
            </ImageCardStats>

            <ImageCardFooter>
                <ImageCardStatus :status="imageState.status" />

                <ImageCardActions>
                    <Button
                        v-if="imageState.status === 'completed' && imageState.downloadUrl"
                        class="border border-green-500 bg-white text-green-600 hover:bg-green-500 hover:text-white"
                        @click.stop="emit('download', imageState.uuid)"
                    >
                        Download
                    </Button>
                    <Button
                        v-if="imageState.status === 'error'"
                        class="border border-blue-500 bg-white text-blue-500 hover:bg-blue-500 hover:text-white"
                        @click.stop="emit('retry', imageState.uuid)"
                    >
                        Retry
                    </Button>
                    <Button
                        class="group border border-red-500 bg-white text-red-500 hover:bg-red-500 hover:text-white"
                        @click.stop="emit('remove', imageState.uuid)"
                    >
                        Delete
                    </Button>
                </ImageCardActions>
            </ImageCardFooter>

            <ImageCardError
                v-if="imageState.errorMessage"
                :message="imageState.errorMessage"
            />
        </ImageCardContent>
    </ImageCard>
</template>
