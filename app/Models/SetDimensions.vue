<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Input } from '../ui/input';
import Label from '../ui/label/Label.vue';
import { useImageStore } from '@/stores/imageStore';
import { storeToRefs } from 'pinia';
import ImageStateCard from './ImageStateCard.vue';

const { images: imagesState } = storeToRefs(useImageStore())

interface ImageDimensions {
    uuid: string;
    file: File;
    width?: number;
    height?: number;
}

const form = useForm({
    defaultWidth: 100,
    defaultHeight: 100,
    images: [] as ImageDimensions[],
});

const submit = (event: Event) => {
    console.log('EVENT ->', event);
};
</script>

<template>
    <div class="flex h-full">
        <div class="fixed left-0 h-full w-70 flex-shrink-0 border border-red-500">
            <div class="w-[90%] mx-auto">
              <h1 class="mb-6 text-xl font-semibold">Resize Settings</h1>
              <form id="resize-form" @submit.prevent="submit">
                  <div class="flex items-center gap-5">
                      <div class="flex-[50%]">
                          <Label>Width</Label>
                          <Input
                              type="number"
                              class="mt-0.5"
                              name="width"
                              :value="form.defaultWidth"
                              @input="form.defaultWidth = Number($event.target.value)"
                          />
                      </div>
                      <div class="flex-[50%]">
                          <Label>Height</Label>
                          <Input
                              type="number"
                              class="mt-0.5"
                              placeholder="100"
                              name="height"
                              :value="form.defaultHeight"
                              @input="form.defaultHeight = Number($event.target.value)"
                          />
                      </div>
                  </div>
              </form>
            </div>
        </div>
        <div class="ml-70 flex-1 overflow-y-auto bg-blue-500">
          <div class="flex items-center gap-5">
              <ImageStateCard
                  v-for="state in imagesState"
                  :key="state.uuid"
                  :image-state="state"
                  @retry="retryImageUpload"
                  @remove="removeImage"
              />
          </div>
        </div>
    </div>
</template>
