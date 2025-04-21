<script setup lang="ts">
import type { ImageRecord, Image as TypeImage } from './index';
import Image from '../Image.vue';
import { computed } from 'vue';

interface Props {
    name: string;
    params: Record<string, ImageRecord>;
    class: string;
}

const props = defineProps<Props>();

const images = computed<TypeImage[]>(() => {
    const imageObject = props.params?.[props.name] as ImageRecord | null;

    if (! imageObject) {
        return [];
    }

    return Array.isArray(imageObject?.image?.url)
        ? imageObject?.image?.url.map((url) => {
              return {
                  ...imageObject.image,
                  url,
              };
          })
        : [imageObject?.image];
});

const hiddenImagesCount = computed<number|null>(() => {
    const imageObject = props.params?.[props.name] as ImageRecord | null;
    if (! imageObject) {
        return null;
    }

    return imageObject?.image?.hiddenImagesCount ?? null;
})
</script>

<template>
    <Image v-if="images.length === 1" :image="images[0] as TypeImage" />
    <div v-else class="flex items-center space-x-2">
        <div class="flex -space-x-[0.6rem]">
            <Image v-for="(image, index) in images"
                   class="size-8 ring-background rounded-full ring-2"
                   :image="image as TypeImage"
                   :key="index"
            />
        </div>

        <span v-if="hiddenImagesCount" class="text-sm text-muted-foreground font-bold">+{{ hiddenImagesCount }}</span>
    </div>

</template>
