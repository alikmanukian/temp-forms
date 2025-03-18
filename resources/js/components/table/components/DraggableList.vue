<script generic="T extends object" lang="ts" setup>
import { Container, Draggable } from 'vue3-smooth-dnd'
import { ref } from 'vue'

interface DDResult {
    removedIndex: number | null
    addedIndex: number | null
    payload: T
}

const props = withDefaults(
    defineProps<{
        list: T[]
        orientation?: 'vertical' | 'horizontal' // Define accepted values
    }>(),
    { orientation: 'vertical' } // Default value
)

defineSlots<{
    default(props: { item: T }): any
}>()

const emit = defineEmits<{
    (e: 'updated', items: T[]): any
}>()

const items = ref<T[]>(props.list as T[])

function getItemKey(item: T, index: number): string | number {
    if ('id' in item) return (item as { id: string | number }).id
    return index
}

const applyDrag = (arr: T[], dragResult: DDResult) => {
    const { removedIndex, addedIndex, payload } = dragResult

    if (removedIndex === null && addedIndex === null) return arr
    const result = [...arr]
    let itemToAdd = payload

    if (removedIndex !== null) {
        itemToAdd = result.splice(removedIndex, 1)[0]
    }
    if (addedIndex !== null) {
        result.splice(addedIndex, 0, itemToAdd)
    }
    return result
}

const onDrop = (dropResult: DDResult) => {
    items.value = applyDrag(items.value as T[], dropResult)
    emit('updated', items.value as T[])
}
</script>

<template>
    <Container :orientation="props.orientation" @drop="onDrop">
        <Draggable v-for="(item, i) in items" :key="getItemKey(item as T, i)">
            <slot :item="item as T" />
        </Draggable>
    </Container>
</template>
