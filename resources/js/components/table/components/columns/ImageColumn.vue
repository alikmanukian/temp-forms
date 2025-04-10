<script setup lang="ts">
import type { ImageRecord } from '../columns';
import Image from '../Image.vue';
import { computed } from 'vue';

interface Props {
    name: string;
    params: Record<string, ImageRecord>;
    class: string;
}

const props = defineProps<Props>();

const images = computed(() => {
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
</script>

<template>
    <Image v-if="images.length === 1" :image="images[0] as Image" />
    <div v-else class="flex -space-x-[0.6rem]">
        <Image v-for="(image, index) in images"
               class="size-8 ring-background rounded-full ring-2"
               :image="image as Image"
               :key="index"
        />
    </div>

</template>
