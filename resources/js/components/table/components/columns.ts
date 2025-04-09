export { default as TextColumn } from './columns/TextColumn.vue'
export { default as BadgeColumn } from './columns/BadgeColumn.vue'
export { default as BooleanColumn } from './columns/BooleanColumn.vue'

export interface Icon {
    ['icon']: {
        name: string
        class?: string
        style?: string
        alt?: string
        title?: string
        position?: string
    }
}

export interface Image {
    ['image']: {
        src: string
        class?: string
        style?: string
        alt?: string
        title?: string
        position?: string
    }
}

export interface Link {
    ['link']: {
        href: string
        class?: string
        style?: string
        target?: string
        title?: string
    }
}
