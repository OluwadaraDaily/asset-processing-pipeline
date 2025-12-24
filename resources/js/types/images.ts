export interface ImageDimensions {
    width: number;
    height: number;
}

export interface ImageState {
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

export type ImageResizeStage = 'upload' | 'set-dimensions' | 'process';
