export { default as TextColumn } from './columns/TextColumn.vue'
export { default as BadgeColumn } from './columns/BadgeColumn.vue'
export { default as BooleanColumn } from './columns/BooleanColumn.vue'

export interface IconOrImage {
    src?: string
    name?: string
    class?: string
    style?: string
    alt?: string
    title?: string
    position?: string
}
